<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    /**
     * Tampilkan form login.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        // Validasi input
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Cek login dengan remember me
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // Regenerate session untuk keamanan
            $request->session()->regenerate();

            // Update last login time
            $user = Auth::user();
            if ($user instanceof \App\Models\User) {
                $user->last_login_at = Carbon::now();
                $user->save();
            }

            // Set informasi ke session
            Session::put('login_time', Carbon::now()->toDateTimeString());
            Session::put('user_name', $user->name);

            // Log login success (opsional)
            \Illuminate\Support\Facades\Log::info('User logged in', [
                'user_id' => $user->id,
                'email' => $user->email,
                'is_admin' => $user->is_admin,
            ]);
                        // Cek apakah user adalah admin
            if ($user->is_admin) {
                // Jika admin, arahkan ke dashboard admin
                return redirect()->route('admin.dashboard')
                                 ->with('success', 'Selamat datang Administrator, ' . $user->name . '!');
            }
            // Redirect ke halaman yang diinginkan, default ke home
            return redirect()->intended(route('home'))
                             ->with('success', 'Berhasil masuk! Selamat datang kembali, ' . $user->name);
        }

        // Jika gagal login
        return back()
                ->withErrors([
                    'email' => 'Email atau password salah.',
                ])
                ->onlyInput('email');
    }

    /**
     * Handle logout request.
     */
    public function logout(Request $request)
    {
      // Ambil data user sebelum logout
        $user = Auth::user();
        $userName = $user->name ?? 'User';
        $isAdmin = $user->is_admin ?? false;

        Auth::logout();

        // Invalidate session dan regenerate token untuk keamanan
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Log logout
        \Illuminate\Support\Facades\Log::info('User logged out', [
            'user_name' => $userName,
            'is_admin' => $isAdmin
        ]);

        return redirect()->route('home')
                         ->with('success', 'Berhasil keluar! Sampai jumpa lagi, ' . $userName);
    }
}
