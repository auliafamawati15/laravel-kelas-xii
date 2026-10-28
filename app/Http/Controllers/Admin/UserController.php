<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Menampilkan daftar seluruh pengguna.
     */
    public function index(Request $request)
    {
        $query = User::with('role')->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(10)->withQueryString();
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Memperbarui peran (role) pengguna (Admin / User).
     */
    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        // Cegah admin mengubah rolenya sendiri menjadi non-admin
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat mengubah role akun Anda sendiri!');
        }

        $user->update([
            'role_id' => $request->role_id,
        ]);

        return back()->with('success', 'Role untuk pengguna ' . $user->name . ' berhasil diperbarui!');
    }

    /**
     * Menghapus akun pengguna dari sistem.
     */
    public function destroy(User $user)
    {
        // Cegah admin menghapus akunnya sendiri
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda yang sedang aktif digunakan!');
        }

        $name = $user->name;

        DB::transaction(function () use ($user) {
            // Hapus kritik/komentar yang dibuat user ini
            $user->delete();
        });

        return back()->with('success', 'Pengguna "' . $name . '" berhasil dihapus dari sistem!');
    }
}
