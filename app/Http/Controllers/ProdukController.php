<?php

namespace App\Http\Controllers;
use App\Models\Produk;
use App\Models\JenisProduk; 
use App\Models\JenisLead;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    public function index(Request $request)
    {
        $query = Produk::with(['jenisLead', 'jenisProduk']);
        // Filter pencarian berdasarkan nama_produk
        if ($request->has('search') && $request->search != '') {
            $query->where('nama_produk', 'like', '%' . $request->search . '%');
        }

        $produk = $query->paginate(20)->onEachSide(1);

        return view('produk.index', compact('produk'));
    }

   public function create()
{
    $jenisleadList = JenisLead::all(); // ambil semua jenis lead
     $jenisprodukList = JenisProduk::all();
    return view('produk.create', compact('jenisleadList','jenisprodukList'));
}


    public function store(Request $request)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:100|unique:produk,nama_produk',
            'hpp' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
             'jenis_produk_id' => 'required|exists:jenis_produk,id',
        ], [
            'nama_produk.required' => 'Nama produk tidak boleh kosong',
            'nama_produk.unique' => 'Nama produk sudah digunakan',
            'hpp.required' => 'HPP tidak boleh kosong',
            'harga_jual.required' => 'Harga jual tidak boleh kosong',
            'jenis_produk_id.required' => 'Jenis produk wajib dipilih', 
        ]);

        Produk::create([
    'nama_produk' => $request->nama_produk,
    'hpp' => $request->hpp,
    'harga_jual' => $request->harga_jual,
    'detail_produk' => $request->detail_produk,
    'jenis_produk_id' => $request->jenis_produk_id, 
    'jenis_lead_id' => $request->jenis_lead_id,
]);

        return redirect()->route('produk.index')
                         ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Produk $produk)
{
    $jenisleadList = JenisLead::all();
     $jenisprodukList = JenisProduk::all();
    return view('produk.edit', compact('produk', 'jenisleadList','jenisprodukList'));
}


    public function update(Request $request, Produk $produk)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:100|unique:produk,nama_produk,' . $produk->id,
            'hpp' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
           'jenis_produk_id' => 'required|exists:jenis_produk,id',
        ], [
            'nama_produk.unique' => 'Nama produk sudah digunakan',
            'jenis_produk_id.required' => 'Jenis produk wajib dipilih',
        ]);

       $produk->update([
    'nama_produk' => $request->nama_produk,
    'hpp' => $request->hpp,
    'harga_jual' => $request->harga_jual,
    'detail_produk' => $request->detail_produk,
    'jenis_produk_id' => $request->jenis_produk_id,
    'jenis_lead_id' => $request->jenis_lead_id,
]);

        return redirect()->route('produk.index')
                         ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Produk $produk)
    {
        $produk->delete();

        return redirect()->route('produk.index')
                         ->with('success', 'Produk berhasil dihapus.');
    }
}
