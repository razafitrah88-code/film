<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Show login form (redirect to home with modal opened).
     */
    public function showLogin(Request $request)
    {
        if (Auth::check()) {
            return Auth::user()->isAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('home');
        }

        if ($request->has('redirect')) {
            session(['url.intended' => $request->query('redirect')]);
        } elseif (!session()->has('url.intended') && url()->previous() !== url()->current()) {
            session(['url.intended' => url()->previous()]);
        }

        return redirect()->route('home')->with('show_modal', 'login');
    }

    /**
     * Show register form (redirect to home with modal opened on register tab).
     */
    public function showRegister()
    {
        if (Auth::check()) {
            return Auth::user()->isAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('home');
        }

        return redirect()->route('home')->with('show_modal', 'register');
    }

    /**
     * Handle user login.
     */
    public function login(Request $request)
    {
        $messages = [
            'login.required' => 'Email atau Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ];

        try {
            $request->validateWithBag('login', [
                'login' => 'required|string',
                'password' => 'required|string',
            ], $messages);
        } catch (ValidationException $e) {
            return back()
                ->withInput($request->only('login'))
                ->withErrors($e->errors(), 'login')
                ->with('show_modal', 'login');
        }

        $loginInput = $request->input('login');
        $loginField = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        $credentials = [
            $loginField => $loginInput,
            'password' => $request->input('password'),
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'))
                    ->with('success', 'Selamat datang di Admin Dashboard, ' . $user->name . '!');
            }

            $intended = session()->pull('url.intended', $request->input('redirect_to', route('home')));
            return redirect()->to($intended)
                ->with('success', 'Login berhasil! Selamat datang, ' . $user->name . '.');
        }

        // Generic error message without revealing whether email or password is wrong
        return back()
            ->withInput($request->only('login'))
            ->withErrors(['login' => 'Email/Username atau password yang Anda masukkan salah.'], 'login')
            ->with('show_modal', 'login');
    }

    /**
     * Handle user registration.
     */
    public function register(Request $request)
    {
        $messages = [
            'name.required' => 'Nama/Username wajib diisi.',
            'name.min' => 'Nama/Username minimal 3 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar. Silakan gunakan email lain.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ];

        try {
            $validated = $request->validateWithBag('register', [
                'name' => 'required|string|min:3|max:255',
                'email' => 'required|string|email|max:255|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
            ], $messages);
        } catch (ValidationException $e) {
            return back()
                ->withInput($request->except(['password', 'password_confirmation']))
                ->withErrors($e->errors(), 'register')
                ->with('show_modal', 'register');
        }

        // Password must be hashed (bcrypt/argon2)
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'user',
            'role_id' => 1,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('home')
            ->with('success', 'Registrasi berhasil! Selamat datang, ' . $user->name . '.');
    }

    /**
     * Handle user logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Anda telah berhasil logout.');
    }
}
