<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Film extends Model
{
    use HasFactory;
    protected $table = 'films';
    protected $fillable = ['title', 'sinopsis', 'year', 'poster', 'genre_id'];

    public function getPosterUrlAttribute(): string
    {
        $poster = $this->poster;

        if (str_starts_with($poster, '//')) {
            return $poster;
        }

        $poster = ltrim($poster, '/');

        if (str_starts_with($poster, 'images/')) {
            $poster = 'storage/' . $poster;
        }

        return asset($poster);
    }

    /**
     * Relationship to Peran (intermediate model connecting Film and Cast)
     */
    public function perans()
    {
        return $this->hasMany(Peran::class, 'film_id');
    }

    /**
     * Many-to-many relationship to Cast via perans table.
     * Note: named castMembers to avoid collision with Laravel Eloquent's built-in casts() method.
     */
    public function castMembers()
    {
        return $this->belongsToMany(Cast::class, 'perans', 'film_id', 'cast_id')
                    ->withPivot('id', 'actor')
                    ->withTimestamps();
    }

    /**
     * Relationship to Genre
     */
    public function genre()
    {
        return $this->belongsTo(Genre::class, 'genre_id');
    }

    /**
     * Relationship to Kritik (reviews)
     */
    public function kritiks()
    {
        return $this->hasMany(Kritik::class, 'film_id');
    }
}
