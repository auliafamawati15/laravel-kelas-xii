<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\Profile;

class AuthController extends Controller
{
    /**
     * Menampilkan form login (jika diakses via URL langsung).
     * Jika user sudah login, arahkan ke home. Jika belum, buka modal login.
     */
    /**
     * Menampilkan form login (jika diakses via URL langsung).
     * Jika user sudah login, arahkan ke home. Jika belum, buka modal login.
     */
    public function showLoginForm(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        // Simpan target URL ke intended session jika ada parameter redirect_to
        if ($request->filled('redirect_to')) {
            session(['url.intended' => $request->input('redirect_to')]);
        }

        return redirect()->route('home')
            ->with('auth_modal', true)
            ->with('auth_form', 'login');
    }

    /**
     * Menampilkan form register (jika diakses via URL langsung).
     */
    public function showRegisterForm(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        // Simpan target URL ke intended session jika ada parameter redirect_to
        if ($request->filled('redirect_to')) {
            session(['url.intended' => $request->input('redirect_to')]);
        }

        return redirect()->route('home')
            ->with('auth_modal', true)
            ->with('auth_form', 'register');
    }

    /**
     * Memproses autentikasi Login.
     * Mendukung login menggunakan Email maupun Username.
     */
    public function login(Request $request)
    {
        // 1. Validasi input form
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ], [
            'login.required'    => 'Email atau Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // 2. Deteksi apakah input login adalah format email atau username
        $loginInput = $request->input('login');
        $fieldType  = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        $credentials = [
            $fieldType => $loginInput,
            'password'  => $request->input('password'),
        ];

        // 3. Cek kredensial ke database (Password diverifikasi dengan hash bcrypt)
        // Prepared statements digunakan secara otomatis oleh Eloquent/Auth Laravel
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // Regenerate session ID untuk mencegah serangan Session Fixation
            $request->session()->regenerate();

            /** @var User $user */
            $user = Auth::user();
            $roleName = $user->role ? strtolower($user->role->name) : 'user';

            // Simpan data user ke session sesuai spesifikasi tugas (id, nama, role)
            $request->session()->put('user_id', $user->id);
            $request->session()->put('user_name', $user->name);
            $request->session()->put('user_role', $roleName);

            // 4. Redirect kembali ke halaman asal (misal halaman film) jika ada parameter redirect_to
            if ($request->filled('redirect_to')) {
                $redirectTo = $request->input('redirect_to');
                $loginUrl = route('login');
                $registerUrl = route('register');

                if ($redirectTo !== $loginUrl && $redirectTo !== $registerUrl) {
                    $parsedHost = parse_url($redirectTo, PHP_URL_HOST);
                    // Validasi domain internal untuk mencegah celah keamanan Open Redirect
                    if (empty($parsedHost) || $parsedHost === $request->getHost()) {
                        return redirect()->to($redirectTo)
                            ->with('success', 'Halo ' . e($user->name) . ', Anda berhasil login!');
                    }
                }
            }

            // Redirect sesuai role jika tidak ada redirect_to spesifik
            if ($roleName === 'admin') {
                return redirect()->route('admin.dashboard')
                    ->with('success', 'Selamat datang kembali di Dashboard Admin, ' . e($user->name) . '!');
            }

            return redirect()->intended(route('home'))
                ->with('success', 'Halo ' . e($user->name) . ', Anda berhasil login!');
        }

        // 5. Jika gagal: tampilkan pesan kesalahan umum (jangan beri tahu field mana yang salah demi keamanan)
        return back()
            ->withInput($request->only('login'))
            ->withErrors(['login' => 'Email/username atau password salah.'])
            ->with('auth_modal', true)
            ->with('auth_form', 'login');
    }

    /**
     * Memproses pendaftaran pengguna baru (Register).
     */
    public function register(Request $request)
    {
        // 1. Validasi data register
        $validated = $request->validate([
            'name'                  => 'required|string|min:3|max:100|unique:users,name',
            'email'                 => 'required|string|email|max:255|unique:users,email',
            'password'              => 'required|string|min:8|confirmed',
        ], [
            'name.required'         => 'Nama / username wajib diisi.',
            'name.min'              => 'Nama / username minimal 3 karakter.',
            'name.unique'           => 'Username ini sudah digunakan, silakan pilih yang lain.',
            'email.required'        => 'Email wajib diisi.',
            'email.email'           => 'Format email tidak valid.',
            'email.unique'          => 'Email ini sudah terdaftar.',
            'password.required'     => 'Password wajib diisi.',
            'password.min'          => 'Password minimal harus 8 karakter.',
            'password.confirmed'    => 'Konfirmasi password tidak cocok dengan password.',
        ]);

        // 2. Buat profil default otomatis untuk memenuhi foreign key profile_id
        $profile = Profile::create([
            'umur'   => 0,
            'bio'    => 'Anggota baru OneMovies',
            'alamat' => '-',
        ]);

        // 3. Ambil role default "user" (ID 1)
        $role = Role::where('name', 'user')->first();
        $roleId = $role ? $role->id : 1;

        // 4. Buat akun user baru dengan password yang di-hash menggunakan bcrypt
        $user = User::create([
            'name'       => $validated['name'],
            'email'      => $validated['email'],
            'password'   => Hash::make($validated['password']), // Hash password (keamanan)
            'role_id'    => $roleId,                           // Role default "user"
            'profile_id' => $profile->id,
        ]);

        // 5. Login otomatis setelah registrasi berhasil
        Auth::login($user);
        $request->session()->regenerate();

        // Simpan data session user
        $request->session()->put('user_id', $user->id);
        $request->session()->put('user_name', $user->name);
        $request->session()->put('user_role', 'user');

        // Kembalikan ke halaman sebelumnya jika ada parameter redirect_to
        if ($request->filled('redirect_to')) {
            $redirectTo = $request->input('redirect_to');
            $loginUrl = route('login');
            $registerUrl = route('register');

            if ($redirectTo !== $loginUrl && $redirectTo !== $registerUrl) {
                $parsedHost = parse_url($redirectTo, PHP_URL_HOST);
                if (empty($parsedHost) || $parsedHost === $request->getHost()) {
                    return redirect()->to($redirectTo)
                        ->with('success', 'Registrasi berhasil! Selamat datang, ' . e($user->name) . '.');
                }
            }
        }

        return redirect()->route('home')
            ->with('success', 'Registrasi berhasil! Selamat datang, ' . e($user->name) . '.');
    }

    /**
     * Memproses Logout pengguna dan menghapus session secara aman.
     */
    public function logout(Request $request)
    {
        // 1. Keluarkan user dari autentikasi Laravel
        Auth::logout();

        // 2. Hapus seluruh data session dan buat ulang ID session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('success', 'Anda telah berhasil logout. Sampai jumpa!');
    }
}
