<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Models\Kritik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KritikController extends Controller
{
    /**
     * Store a newly created comment on a film in storage.
     */
    public function store(Request $request, Film $film)
    {
        // Backend protection: reject if not authenticated
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->guest(route('login', ['redirect' => url()->previous()]))
                ->with('error', 'Silakan login terlebih dahulu untuk menambahkan komentar.');
        }

        // Validate comment: not empty, maximum 500 characters
        $validated = $request->validate([
            'comment' => 'required|string|max:500',
        ], [
            'comment.required' => 'Isi komentar tidak boleh kosong.',
            'comment.max' => 'Komentar tidak boleh melebihi 500 karakter.',
        ]);

        // Save comment strictly using authenticated user's ID
        Kritik::create([
            'film_id' => $film->id,
            'user_id' => Auth::id(),
            'comment' => trim($validated['comment']),
            'rating' => (int) $request->input('rating', 5),
        ]);

        return back()->with('success', 'Komentar Anda berhasil dikirim!');
    }
}
