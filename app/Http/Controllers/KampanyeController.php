<?php

namespace App\Http\Controllers;
use App\Models\Kampanye;
use App\Models\User;
use Illuminate\Http\Request;

class KampanyeController extends Controller
{
   public function index()
{
    $kampanye = Kampanye::with('user')->latest()->get();
    return view('iklan.index', compact('kampanye'));
}

public function create()
{
    $users = User::where('role', 'customerservice')->get(); // khusus CS
    return view('iklan.create', compact('users'));
}

public function store(Request $request)
{
    $request->validate([
        'user_id' => 'required|exists:users,id',
        'kode_kampanye' => 'required|string|max:255',
    ]);

    Kampanye::create([
        'user_id' => $request->user_id,
        'kode_kampanye' => $request->kode_kampanye,
    ]);

    return redirect()->route('kampanye.index')->with('success', 'Kode kampanye berhasil ditambahkan.');
}

public function edit($id)
{
    $kampanye = Kampanye::findOrFail($id);
    $users = User::where('role', 'customerservice')->get(); // hanya CS
    return view('iklan.edit', compact('kampanye', 'users'));
}

public function update(Request $request, $id)
{
    $request->validate([
        'user_id' => 'required|exists:users,id',
        'kode_kampanye' => 'required|string|max:255',
    ]);

    $kampanye = Kampanye::findOrFail($id);
    $kampanye->update([
        'user_id' => $request->user_id,
        'kode_kampanye' => $request->kode_kampanye,
    ]);

    return redirect()->route('kampanye.index')->with('success', 'Data kampanye berhasil diperbarui.');
}
public function destroy($id)
{
    $kampanye = Kampanye::findOrFail($id);
    $kampanye->delete();

    return redirect()->route('kampanye.index')->with('success', 'Data kampanye berhasil dihapus.');
}
}
