<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Models\Genre;
use App\Models\User;
use App\Models\Kritik;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        $totalFilms = Film::count();
        $totalGenres = Genre::count();
        $totalUsers = User::count();
        $totalKritik = Kritik::count();

        $recentUsers = User::latest()->limit(8)->get();
        $recentFilms = Film::with('perans')->latest()->limit(8)->get();

        return view('admin.dashboard', compact(
            'totalFilms',
            'totalGenres',
            'totalUsers',
            'totalKritik',
            'recentUsers',
            'recentFilms'
        ));
    }
}
