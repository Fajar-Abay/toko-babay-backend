<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request){
        $valid = $request->validate([
            "username" => "required",
            "password" => "required",
        ]);

        $user = User::where("name", $valid["username"])->first();

         // Cek apakah user ada dan password cocok
        if (!$user || !Hash::check($valid['password'], $user->password)) {
            return response()->json([
                'message' => 'Username atau password salah'
            ], 401);
        }

        // Buat token Sanctum
        $token = $user->createToken('apitoken')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }

    public function register(Request $request)
    {
        // Validasi data
        $valid = $request->validate([
            "username" => "required|unique:users,name",
            "email" => "required|email|unique:users,email",
            "password" => "required|min:6",
        ]);

        // Simpan user baru
        $user = User::create([
            'name' => $valid['username'],
            'email' => $valid['email'],
            'password' => Hash::make($valid['password']), // Hash password
        ]);

        // Buat token Sanctum
        $token = $user->createToken('apitoken')->plainTextToken;

        return response()->json([
            'message' => 'Register berhasil',
            'user' => $user,
            'token' => $token
        ], 201);
    }
}
