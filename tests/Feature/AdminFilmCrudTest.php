<?php

namespace Tests\Feature;

use App\Models\Cast;
use App\Models\Film;
use App\Models\Genre;
use App\Models\Peran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminFilmCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Genre $genre;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin Film Test',
            'email' => 'admin-film-test@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $this->genre = Genre::create(['name' => 'Genre Film Test']);
    }

    public function test_admin_can_edit_a_film(): void
    {
        $film = Film::create([
            'title' => 'Film Lama',
            'genre_id' => $this->genre->id,
            'year' => 2020,
            'poster' => 'images/old.jpg',
            'sinopsis' => 'Sinopsis lama.',
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.films.update', $film), [
            'title' => 'Film Baru',
            'genre_id' => $this->genre->id,
            'year' => 2024,
            'poster' => 'images/new.jpg',
            'sinopsis' => 'Sinopsis baru.',
        ]);

        $response->assertRedirect(route('admin.films.edit', $film));
        $this->assertDatabaseHas('films', [
            'id' => $film->id,
            'title' => 'Film Baru',
            'year' => 2024,
            'poster' => 'images/new.jpg',
            'sinopsis' => 'Sinopsis baru.',
        ]);
    }

    public function test_admin_can_delete_a_film_and_its_cast_assignments(): void
    {
        $film = Film::create([
            'title' => 'Film Akan Dihapus',
            'genre_id' => $this->genre->id,
            'year' => 2020,
            'poster' => 'images/delete.jpg',
            'sinopsis' => 'Sinopsis film.',
        ]);
        $cast = Cast::create([
            'name' => 'Pemeran Test',
            'age' => 30,
            'bio' => 'Bio test.',
        ]);
        $peran = Peran::create([
            'film_id' => $film->id,
            'cast_id' => $cast->id,
            'actor' => 'Karakter Test',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.films.destroy', $film));

        $response->assertRedirect(route('admin.films.index'));
        $this->assertDatabaseMissing('films', ['id' => $film->id]);
        $this->assertDatabaseMissing('perans', ['id' => $peran->id]);
    }
}
