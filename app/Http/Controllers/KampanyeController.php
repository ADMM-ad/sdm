<?php

namespace App\Http\Controllers;
use App\Models\Kampanye;
use App\Models\User;
use App\Models\JenisLead;
use Illuminate\Http\Request;

class KampanyeController extends Controller
{
   public function index(Request $request)
{
    // Ambil semua CS untuk dropdown
    $customerservices = User::where('role', 'customerservice')->get();

    $selectedUser = $request->input('user_id');
    $searchKode   = $request->input('search');

    $kampanye = Kampanye::with(['user', 'jenisLead'])
        ->when($selectedUser, function ($query, $selectedUser) {
            return $query->where('user_id', $selectedUser);
        })
        ->when($searchKode, function ($query, $searchKode) {
            return $query->where('kode_kampanye', 'like', "%{$searchKode}%");
        })
        ->latest()
        ->get();

    return view('iklan.index', compact('kampanye', 'customerservices', 'selectedUser', 'searchKode'));
}



public function create()
{
    $users = User::where('role', 'customerservice')->get(); // khusus CS
     $jenisLeads = JenisLead::all();
    return view('iklan.create', compact('users', 'jenisLeads'));
}

public function store(Request $request)
{
    $request->validate([
        'user_id' => 'required|exists:users,id',
        'kode_kampanye' => 'required|string|max:255',
        'jenis_lead_id' => 'nullable|exists:jenis_lead,id',
    ]);

    Kampanye::create([
        'user_id' => $request->user_id,
        'kode_kampanye' => $request->kode_kampanye,
        'jenis_lead_id' => $request->jenis_lead_id,
    ]);

    return redirect()->route('kampanye.index')->with('success', 'Kode kampanye berhasil ditambahkan.');
}

public function edit($id)
{
    $kampanye = Kampanye::findOrFail($id);
    $users = User::where('role', 'customerservice')->get(); // hanya CS
    $jenisLeads = JenisLead::all(); 
    return view('iklan.edit', compact('kampanye', 'users','jenisLeads'));
}

public function update(Request $request, $id)
{
    $request->validate([
        'user_id' => 'required|exists:users,id',
        'kode_kampanye' => 'required|string|max:255',
         'jenis_lead_id' => 'nullable|exists:jenis_lead,id',
    ]);

    $kampanye = Kampanye::findOrFail($id);
    $kampanye->update([
        'user_id' => $request->user_id,
        'kode_kampanye' => $request->kode_kampanye,
        'jenis_lead_id' => $request->jenis_lead_id,
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
