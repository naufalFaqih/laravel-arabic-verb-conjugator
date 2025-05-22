<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    /**
     * Tampilkan halaman register.
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle register request.
     */
    public function register(Request $request)
    {
        // Validasi input dengan aturan yang kuat
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => [
                'required', 
                'confirmed', 
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
        ]);

        try {
            // Buat user baru dalam database
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'last_login_at' => Carbon::now(),
            ]);

            // Login user yang baru registrasi
            Auth::login($user);
            
            // Log user registration (opsional)
            \Illuminate\Support\Facades\Log::info('User registered', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);

            return redirect()->route('home')
                            ->with('success', 'Akun berhasil dibuat! Selamat datang, ' . $user->name);
                            
        } catch (\Exception $e) {
            // Log error dan tampilkan pesan error yang user-friendly
            \Illuminate\Support\Facades\Log::error('Registration failed', [
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Terjadi kesalahan saat membuat akun. Silahkan coba lagi.')
                        ->withInput($request->except('password'));
        }
    }
}