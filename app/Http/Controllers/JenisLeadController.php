<?php

namespace App\Http\Controllers;

use App\Models\JenisLead;
use Illuminate\Http\Request;

class JenisLeadController extends Controller
{

public function index(Request $request)
{
    $query = JenisLead::query();

    // Filter pencarian berdasarkan kolom 'jenis'
    if ($request->has('search') && $request->search != '') {
        $query->where('jenis', 'like', '%' . $request->search . '%');
    }

    $jenislead = $query->paginate(10);

    return view('jenislead.index', compact('jenislead'));
}


    public function create()
{
    return view('jenislead.create');
}

public function store(Request $request)
{
    $request->validate([
        'jenis' => 'required|string|max:100|unique:jenis_lead,jenis',
    ], [
        'jenis.required' => 'Jenis lead tidak boleh kosong',
        'jenis.unique' => 'Jenis lead sudah ada',
    ]);

    JenisLead::create([
        'jenis' => $request->jenis,
    ]);

    return redirect()->route('jenislead.index')->with('success', 'Jenis lead berhasil ditambahkan.');
}

public function edit(JenisLead $jenislead)
{
    return view('jenislead.edit', compact('jenislead'));
}

public function update(Request $request, JenisLead $jenislead)
{
    $request->validate([
        'jenis' => 'required|string|max:100|unique:jenis_lead,jenis,' . $jenislead->id,
    ], [
        'jenis.required' => 'Jenis lead tidak boleh kosong',
        'jenis.unique' => 'Jenis lead sudah ada',
    ]);

    $jenislead->update([
        'jenis' => $request->jenis,
    ]);

    return redirect()->route('jenislead.index')
                     ->with('success', 'Jenis lead berhasil diperbarui.');
}

public function destroy(JenisLead $jenislead)
{
    $jenislead->delete();

    return redirect()->route('jenislead.index')
                     ->with('success', 'Jenis lead berhasil dihapus.');
}
}
