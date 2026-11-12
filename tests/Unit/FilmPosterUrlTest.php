<?php

namespace Tests\Unit;

use App\Models\Film;
use Tests\TestCase;

class FilmPosterUrlTest extends TestCase
{
    public function test_bare_image_paths_use_the_public_storage_directory(): void
    {
        $film = new Film(['poster' => 'images/m1.jpg']);

        $this->assertSame(asset('storage/images/m1.jpg'), $film->poster_url);
    }

    public function test_storage_paths_and_external_urls_remain_usable(): void
    {
        $storageFilm = new Film(['poster' => 'storage/images/m1.jpg']);
        $externalFilm = new Film(['poster' => 'https://example.com/poster.jpg']);

        $this->assertSame(asset('storage/images/m1.jpg'), $storageFilm->poster_url);
        $this->assertSame('https://example.com/poster.jpg', $externalFilm->poster_url);
    }
}
