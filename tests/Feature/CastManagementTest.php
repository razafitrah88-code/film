<?php

namespace Tests\Feature;

use App\Models\Cast;
use App\Models\Film;
use App\Models\Genre;
use App\Models\Peran;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CastManagementTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;
    protected User $user;
    protected Genre $genre;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin_test@example.com'],
            [
                'name' => 'Admin Tester',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'role_id' => 2,
            ]
        );

        $this->user = User::firstOrCreate(
            ['email' => 'user_test@example.com'],
            [
                'name' => 'Regular Tester',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'role_id' => 1,
            ]
        );

        $this->genre = Genre::firstOrCreate(
            ['name' => 'Action Test']
        );
    }

    public function test_detail_film_displays_cast_name_and_role(): void
    {
        $film = Film::create([
            'title' => 'Film Uji Cast ' . uniqid(),
            'genre_id' => $this->genre->id,
            'year' => 2024,
            'poster' => 'images/test.jpg',
            'sinopsis' => 'Sinopsis film uji cast',
        ]);

        $cast = Cast::create([
            'name' => 'Aktor Bintang ' . uniqid(),
            'age' => 35,
            'bio' => 'Aktor profesional',
        ]);

        Peran::create([
            'film_id' => $film->id,
            'cast_id' => $cast->id,
            'actor' => 'Pahlawan Ksatria',
        ]);

        $response = $this->get('/movies/' . $film->id);
        $response->assertStatus(200);
        $response->assertSee($cast->name);
        $response->assertSee('Pahlawan Ksatria');
        $response->assertDontSee('Data cast belum tersedia');
    }

    public function test_detail_film_without_cast_displays_appropriate_message(): void
    {
        $film = Film::create([
            'title' => 'Film Tanpa Cast ' . uniqid(),
            'genre_id' => $this->genre->id,
            'year' => 2024,
            'poster' => 'images/empty.jpg',
            'sinopsis' => 'Sinopsis film tanpa cast',
        ]);

        $response = $this->get('/movies/' . $film->id);
        $response->assertStatus(200);
        $response->assertSee('Data cast belum tersedia');
    }

    public function test_regular_user_cannot_access_admin_film_management(): void
    {
        $response = $this->actingAs($this->user)->get('/admin/films');
        $response->assertRedirect(route('home'));

        $responseCreate = $this->actingAs($this->user)->get('/admin/films/create');
        $responseCreate->assertRedirect(route('home'));
    }

    public function test_admin_can_view_films_list_and_create_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/films');
        $response->assertStatus(200);
        $response->assertSee('Kelola');
        $response->assertSee('Film & Cast', false);

        $responseCreate = $this->actingAs($this->admin)->get('/admin/films/create');
        $responseCreate->assertStatus(200);
        $responseCreate->assertSee('Tambah');
        $responseCreate->assertSee('Pilih & Tambah Cast', false);
    }

    public function test_admin_can_create_film_with_cast(): void
    {
        $cast = Cast::create([
            'name' => 'Cast Awal ' . uniqid(),
            'age' => 28,
            'bio' => 'Bio cast awal',
        ]);

        $filmTitle = 'Film Baru dengan Cast ' . uniqid();

        $response = $this->actingAs($this->admin)->post('/admin/films', [
            'title' => $filmTitle,
            'genre_id' => $this->genre->id,
            'year' => 2025,
            'poster' => 'images/m1.jpg',
            'sinopsis' => 'Sinopsis film baru lengkap dengan cast.',
            'casts' => [
                [
                    'cast_id' => $cast->id,
                    'actor' => 'Komandan Skuadron',
                ],
            ],
        ]);

        $film = Film::where('title', $filmTitle)->first();
        $this->assertNotNull($film);
        $response->assertRedirect(route('admin.films.edit', $film->id));

        $this->assertDatabaseHas('perans', [
            'film_id' => $film->id,
            'cast_id' => $cast->id,
            'actor' => 'Komandan Skuadron',
        ]);
    }

    public function test_admin_can_add_cast_to_existing_film(): void
    {
        $film = Film::create([
            'title' => 'Film Target Cast ' . uniqid(),
            'genre_id' => $this->genre->id,
            'year' => 2024,
            'poster' => 'images/target.jpg',
            'sinopsis' => 'Sinopsis film target',
        ]);

        $cast = Cast::create([
            'name' => 'Cast Baru ' . uniqid(),
            'age' => 32,
            'bio' => 'Bio cast baru',
        ]);

        $response = $this->actingAs($this->admin)->post('/admin/films/' . $film->id . '/cast', [
            'cast_id' => $cast->id,
            'actor' => 'Penasihat Raja',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('perans', [
            'film_id' => $film->id,
            'cast_id' => $cast->id,
            'actor' => 'Penasihat Raja',
        ]);
    }

    public function test_admin_can_remove_cast_from_film(): void
    {
        $film = Film::create([
            'title' => 'Film Hapus Cast ' . uniqid(),
            'genre_id' => $this->genre->id,
            'year' => 2024,
            'poster' => 'images/target.jpg',
            'sinopsis' => 'Sinopsis',
        ]);

        $cast = Cast::create([
            'name' => 'Cast Hapus ' . uniqid(),
            'age' => 40,
            'bio' => 'Bio',
        ]);

        $peran = Peran::create([
            'film_id' => $film->id,
            'cast_id' => $cast->id,
            'actor' => 'Karakter Dihapus',
        ]);

        $response = $this->actingAs($this->admin)->delete('/admin/films/' . $film->id . '/cast/' . $peran->id);
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('perans', [
            'id' => $peran->id,
        ]);
    }
}
