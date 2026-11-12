<?php

namespace Tests\Feature;

use App\Models\Film;
use App\Models\Genre;
use App\Models\Kritik;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;
    protected Film $film;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name' => 'Komentator ' . uniqid(),
            'email' => 'komentator_' . uniqid() . '@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'role_id' => 1,
        ]);

        $genre = Genre::firstOrCreate(['name' => 'Drama']);

        $this->film = Film::create([
            'title' => 'Film Uji Komentar ' . uniqid(),
            'genre_id' => $genre->id,
            'year' => 2024,
            'poster' => 'images/comment-test.jpg',
            'sinopsis' => 'Sinopsis film untuk pengujian komentar.',
        ]);
    }

    public function test_guest_sees_login_prompt_instead_of_comment_form(): void
    {
        $response = $this->get('/movies/' . $this->film->id);

        $response->assertStatus(200);
        $response->assertSee('Login untuk berkomentar');
        $response->assertSee('Anda harus login terlebih dahulu');
        $response->assertDontSee('KIRIM KOMENTAR');
    }

    public function test_authenticated_user_sees_comment_form_and_their_name(): void
    {
        $response = $this->actingAs($this->user)->get('/movies/' . $this->film->id);

        $response->assertStatus(200);
        $response->assertSee('Berkomentar sebagai:');
        $response->assertSee($this->user->name);
        $response->assertSee('KIRIM KOMENTAR');
        $response->assertDontSee('Login untuk berkomentar');
    }

    public function test_guest_cannot_post_comment_directly_and_is_redirected(): void
    {
        $response = $this->post('/movies/' . $this->film->id . '/comments', [
            'comment' => 'Komentar liar dari pengunjung tanpa login',
        ]);

        $response->assertRedirect(route('login'));

        $this->assertDatabaseMissing('kritiks', [
            'film_id' => $this->film->id,
            'comment' => 'Komentar liar dari pengunjung tanpa login',
        ]);
    }

    public function test_guest_ajax_post_comment_returns_401_unauthorized(): void
    {
        $response = $this->postJson('/movies/' . $this->film->id . '/comments', [
            'comment' => 'Komentar AJAX liar',
        ]);

        $response->assertStatus(401);

        $this->assertDatabaseMissing('kritiks', [
            'film_id' => $this->film->id,
            'comment' => 'Komentar AJAX liar',
        ]);
    }

    public function test_authenticated_user_can_post_valid_comment(): void
    {
        $commentText = 'Film ini sangat luar biasa dan plot twist-nya tidak terduga!';

        $response = $this->actingAs($this->user)->post('/movies/' . $this->film->id . '/comments', [
            'comment' => $commentText,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('kritiks', [
            'film_id' => $this->film->id,
            'user_id' => $this->user->id,
            'comment' => $commentText,
        ]);
    }

    public function test_comment_user_id_is_strictly_from_auth_and_ignores_tampered_input(): void
    {
        $commentText = 'Komentar dengan percobaan memalsukan user_id';

        $response = $this->actingAs($this->user)->post('/movies/' . $this->film->id . '/comments', [
            'user_id' => 99999, // Tampered input
            'comment' => $commentText,
        ]);

        $response->assertRedirect();

        // Must be saved with $this->user->id, NEVER 99999
        $this->assertDatabaseHas('kritiks', [
            'film_id' => $this->film->id,
            'user_id' => $this->user->id,
            'comment' => $commentText,
        ]);

        $this->assertDatabaseMissing('kritiks', [
            'user_id' => 99999,
        ]);
    }

    public function test_comment_validation_rejects_empty_comment(): void
    {
        $response = $this->actingAs($this->user)->post('/movies/' . $this->film->id . '/comments', [
            'comment' => '',
        ]);

        $response->assertSessionHasErrors(['comment']);

        $this->assertDatabaseMissing('kritiks', [
            'film_id' => $this->film->id,
            'user_id' => $this->user->id,
        ]);
    }

    public function test_comment_validation_rejects_comment_exceeding_500_characters(): void
    {
        $longComment = str_repeat('a', 501);

        $response = $this->actingAs($this->user)->post('/movies/' . $this->film->id . '/comments', [
            'comment' => $longComment,
        ]);

        $response->assertSessionHasErrors(['comment']);

        $this->assertDatabaseMissing('kritiks', [
            'film_id' => $this->film->id,
            'comment' => $longComment,
        ]);
    }

    public function test_user_is_returned_to_film_page_after_login(): void
    {
        $email = 'intended_user_' . uniqid() . '@example.com';
        User::create([
            'name' => 'Intended User',
            'email' => $email,
            'password' => Hash::make('password123'),
            'role' => 'user',
            'role_id' => 1,
        ]);

        $filmUrl = url('/movies/' . $this->film->id);

        // User clicked login link with redirect to film page
        $this->get('/login?redirect=' . urlencode($filmUrl));

        // Submit login credentials
        $response = $this->post('/login', [
            'login' => $email,
            'password' => 'password123',
            'redirect_to' => $filmUrl,
        ]);

        $response->assertRedirect($filmUrl);
        $this->assertAuthenticated();
    }
}
