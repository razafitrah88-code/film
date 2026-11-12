<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cast extends Model
{
    use HasFactory;
    protected $table = 'casts';
    protected $fillable = ['name', 'age', 'bio'];

    /**
     * Relationship to Peran
     */
    public function perans()
    {
        return $this->hasMany(Peran::class, 'cast_id');
    }

    /**
     * Many-to-many relationship to Film via perans table
     */
    public function films()
    {
        return $this->belongsToMany(Film::class, 'perans', 'cast_id', 'film_id')
                    ->withPivot('id', 'actor')
                    ->withTimestamps();
    }
}
