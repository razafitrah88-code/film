<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Models\Genre;
use App\Models\Cast;
use App\Models\Peran;
use Illuminate\Http\Request;

class AdminFilmController extends Controller
{
    /**
     * Display a listing of films for admin with pagination and search.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');

        $films = Film::with(['genre', 'perans.cast'])
            ->when($search, function ($query, $search) {
                return $query->where('title', 'like', "%{$search}%")
                             ->orWhere('year', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(15);

        return view('admin.films.index', compact('films', 'search'));
    }

    /**
     * Show form to create a new film with cast options.
     */
    public function create()
    {
        $genres = Genre::all();
        $casts = Cast::orderBy('name')->get();

        return view('admin.films.create', compact('genres', 'casts'));
    }

    /**
     * Store a new film along with initial cast members.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'genre_id' => 'required|exists:genres,id',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 5),
            'poster' => 'required|string',
            'sinopsis' => 'required|string',
            'casts' => 'nullable|array',
            'casts.*.cast_id' => 'nullable|exists:casts,id',
            'casts.*.new_cast_name' => 'nullable|string|max:255',
            'casts.*.actor' => 'nullable|string|max:255',
        ], [
            'title.required' => 'Judul film wajib diisi.',
            'genre_id.required' => 'Genre film wajib dipilih.',
            'year.required' => 'Tahun rilis wajib diisi.',
            'poster.required' => 'URL/Path poster film wajib diisi.',
            'sinopsis.required' => 'Sinopsis film wajib diisi.',
        ]);

        $film = Film::create([
            'title' => $validated['title'],
            'genre_id' => $validated['genre_id'],
            'year' => $validated['year'],
            'poster' => $validated['poster'],
            'sinopsis' => $validated['sinopsis'],
        ]);

        // Process cast rows if provided
        if (!empty($request->input('casts'))) {
            foreach ($request->input('casts') as $castData) {
                $castId = $castData['cast_id'] ?? null;
                $newCastName = trim($castData['new_cast_name'] ?? '');
                $role = trim($castData['actor'] ?? '');

                if (empty($castId) && !empty($newCastName)) {
                    $newCast = Cast::create([
                        'name' => $newCastName,
                        'age' => 30,
                        'bio' => 'Aktor / Aktris film',
                    ]);
                    $castId = $newCast->id;
                }

                if ($castId) {
                    Peran::create([
                        'film_id' => $film->id,
                        'cast_id' => $castId,
                        'actor' => $role ?: 'Pemeran',
                    ]);
                }
            }
        }

        return redirect()->route('admin.films.edit', $film->id)
            ->with('success', 'Film "' . $film->title . '" berhasil dibuat! Anda sekarang dapat mengelola cast pada film ini.');
    }

    /**
     * Show form to edit film and manage its cast members.
     */
    public function edit(Film $film)
    {
        $film->load(['genre', 'perans.cast']);
        $genres = Genre::all();
        $casts = Cast::orderBy('name')->get();

        return view('admin.films.edit', compact('film', 'genres', 'casts'));
    }

    /**
     * Update film details and optionally update existing cast roles.
     */
    public function update(Request $request, Film $film)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'genre_id' => 'required|exists:genres,id',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 5),
            'poster' => 'required|string',
            'sinopsis' => 'required|string',
        ], [
            'title.required' => 'Judul film wajib diisi.',
            'genre_id.required' => 'Genre film wajib dipilih.',
            'year.required' => 'Tahun rilis wajib diisi.',
            'poster.required' => 'URL/Path poster film wajib diisi.',
            'sinopsis.required' => 'Sinopsis film wajib diisi.',
        ]);

        $film->update($validated);

        // Update existing cast roles if provided
        if ($request->has('existing_roles')) {
            foreach ($request->input('existing_roles') as $peranId => $actorRole) {
                Peran::where('id', $peranId)->where('film_id', $film->id)->update([
                    'actor' => trim($actorRole) ?: 'Pemeran',
                ]);
            }
        }

        return redirect()->route('admin.films.edit', $film->id)
            ->with('success', 'Data film "' . $film->title . '" berhasil diperbarui!');
    }

    /**
     * Add a cast member to the film.
     */
    public function addCast(Request $request, Film $film)
    {
        $request->validate([
            'cast_id' => 'nullable|exists:casts,id',
            'new_cast_name' => 'nullable|string|max:255',
            'actor' => 'required|string|max:255',
        ], [
            'actor.required' => 'Nama peran / karakter wajib diisi.',
        ]);

        $castId = $request->input('cast_id');
        $newCastName = trim($request->input('new_cast_name') ?? '');

        if (empty($castId) && empty($newCastName)) {
            return back()->with('error', 'Silakan pilih cast dari daftar atau ketikkan nama cast baru.');
        }

        if (empty($castId) && !empty($newCastName)) {
            $newCast = Cast::create([
                'name' => $newCastName,
                'age' => 30,
                'bio' => 'Aktor / Aktris',
            ]);
            $castId = $newCast->id;
        }

        Peran::create([
            'film_id' => $film->id,
            'cast_id' => $castId,
            'actor' => $request->input('actor'),
        ]);

        return back()->with('success', 'Cast berhasil ditambahkan ke film "' . $film->title . '"!');
    }

    /**
     * Remove a cast member from the film.
     */
    public function removeCast(Film $film, Peran $peran)
    {
        if ($peran->film_id !== $film->id) {
            return back()->with('error', 'Data peran tidak sesuai dengan film.');
        }

        $peran->delete();

        return back()->with('success', 'Cast berhasil dihapus dari film!');
    }

    /**
     * Delete a film.
     */
    public function destroy(Film $film)
    {
        $title = $film->title;
        $film->perans()->delete();
        $film->delete();

        return redirect()->route('admin.films.index')
            ->with('success', 'Film "' . $title . '" berhasil dihapus!');
    }
}
