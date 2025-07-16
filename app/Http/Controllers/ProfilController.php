<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class ProfilController extends Controller
{
    
public function index()
{
    $user = Auth::user(); 
    return view('profil.index', compact('user'));
}

public function edit()
{
    $user = Auth::user(); 
    return view('profil.edit', compact('user'));
}
public function update(Request $request)
{
    $user = Auth::user();

    $request->validate([
        'name' => 'required|string|max:30',
        'username' => 'required|string|max:30|unique:users,username,' . $user->id,
        'password' => 'nullable|string|min:6|confirmed',
    ], [
    'name.required' => 'Nama wajib diisi.',
    'name.max' => 'Nama anda terlalu panjang, max 30 kata.',
    'username.required' => 'Username wajib diisi.',
    'username.max' => 'Username anda terlalu panjang, max 30 kata.',
    'username.unique' => 'Username sudah digunakan.',
    'password.min' => 'Password minimal 6 karakter.',
    'password.confirmed' => 'Konfirmasi password tidak cocok.',
    ]);

    $user->name = $request->name;
    $user->username = $request->username;

    if ($request->filled('password')) {
        $user->password = Hash::make($request->password);
    }

    $user->save();

    return redirect()->route('profil.index')->with('success', 'Profil berhasil diperbarui!');
}
}
