<?php

namespace App\Http\Controllers;
use App\Models\JenisProduk;
use Illuminate\Http\Request;

class JenisProdukController extends Controller
{
public function index(Request $request)
{
    $query = JenisProduk::query();

    // Filter pencarian berdasarkan kolom 'kategori'
    if ($request->has('search') && $request->search != '') {
        $query->where('kategori', 'like', '%' . $request->search . '%');
    }

    $jenisproduk = $query->paginate(10);

    return view('jenisproduk.index', compact('jenisproduk'));
}


    public function create()
{
    return view('jenisproduk.create');
}

public function store(Request $request)
{
    $request->validate([
        'kategori' => 'required|string|max:100|unique:jenis_produk,kategori',
    ], [
        'kategori.required' => 'Kategori tidak boleh kosong',
        'kategori.unique' => 'Kategori sudah ada',
    ]);

    JenisProduk::create([
        'kategori' => $request->kategori,
    ]);

    return redirect()->route('jenisproduk.index')->with('success', 'Kategori berhasil ditambahkan.');
}
public function edit(JenisProduk $jenisproduk)
{
    return view('jenisproduk.edit', compact('jenisproduk'));
}

public function update(Request $request, JenisProduk $jenisproduk)
{
    $request->validate([
        'kategori' => 'required|string|max:100|unique:jenis_produk,kategori,' . $jenisproduk->id,
    ], [
        'kategori.required' => 'Kategori tidak boleh kosong',
        'kategori.unique' => 'Kategori sudah ada',
    ]);

    $jenisproduk->update([
        'kategori' => $request->kategori,
    ]);

    return redirect()->route('jenisproduk.index')
                     ->with('success', 'Kategori berhasil diperbarui.');
}

public function destroy(JenisProduk $jenisproduk)
{
    $jenisproduk->delete();

    return redirect()->route('jenisproduk.index')
                     ->with('success', 'Kategori berhasil dihapus.');
}

}
