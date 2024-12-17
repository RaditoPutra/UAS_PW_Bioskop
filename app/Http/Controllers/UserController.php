<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Enums\Role; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request)
    {
        // Validasi input
        $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'no_telp' => 'required|string|max:15',
        ]);

        // Tetapkan role berdasarkan username
        $role = $request->username === 'Admin' ? Role::Admin : Role::User;

        // Membuat user baru tanpa foto
        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'no_telp' => $request->no_telp,
            'role' => $role->value, // Simpan nilai role sebagai string
        ]);

        return response()->json([
            'user' => $user,
            'message' => 'User registered successfully',
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        // Validasi input
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Cek username dan password
        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Buat token berdasarkan role
            $tokenName = $user->role === Role::Admin ? 'AdminToken' : 'UserToken';
            $token = $user->createToken($tokenName)->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'user' => $user,
                'role' => $user->role->value, // Tampilkan nilai role sebagai string
                'token' => $token,
            ]);
        }

        // Jika login gagal
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    public function logout(Request $request)
    {
        // Hapus token akses personal
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }

    public function index()
    {
        $allUser = User::all();
        return response()->json($allUser);
    }

    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'nullable|string|min:8',
            'no_telp' => 'required|string|max:15',
        ]);

        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => "User tidak ditemukan"], 404);
        }

        // Update data user
        $user->username = $request->username;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->no_telp = $request->no_telp;

        $user->save();

        return response()->json([
            'user' => $user,
            'message' => 'User updated successfully',
        ]);
    }

    public function destroy(string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => "User tidak ditemukan"], 404);
        }

        $user->delete();

        return response()->json(['message' => "User berhasil dihapus."]);
    }

    public function profile()
    {
        // Ambil data user yang sedang login
        $user = Auth::user();

        // Periksa jika user tidak ditemukan (kasus edge)
        if (!$user) {
            return response()->json(['message' => 'User belum login atau tidak ditemukan'], 404);
        }

        return response()->json([
            'user' => $user,
            'message' => 'Profile user berhasil diambil',
        ]);
    }
}
