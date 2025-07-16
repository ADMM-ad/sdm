<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use App\Models\Produk;
use App\Models\User;
use Carbon\Carbon;
use App\Models\IndonesiaPostalCode;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Illuminate\Support\Facades\Storage;
class PenjualanController extends Controller
{

public function index(Request $request)
{
    $user = auth()->user();
    $query = Penjualan::with(['detailPenjualan.produk'])
                      ->where('id_user', $user->id);

    // Proses filter daterange dari Litepicker
    if ($request->filled('daterange')) {
        $tanggalRange = explode(' - ', $request->daterange);
        if (count($tanggalRange) === 2) {
            try {
                $start = Carbon::createFromFormat('Y-m-d', trim($tanggalRange[0]))->startOfDay();
                $end = Carbon::createFromFormat('Y-m-d', trim($tanggalRange[1]))->endOfDay();
                $query->whereBetween('tanggal', [$start, $end]);
            } catch (\Exception $e) {
                // Abaikan jika salah format
            }
        }
    }

      // Filter platform
    if ($request->filled('platform')) {
        if ($request->platform === 'nonshopee') {
            $query->whereIn('metode_pengiriman', ['COD', 'TRANSFER']);
        } elseif ($request->platform === 'shopee') {
            $query->where('metode_pengiriman', 'SHOPEE');
        }
    }
if ($request->filled('nama_pembeli')) {
    $query->where('nama_pembeli', 'like', '%' . $request->nama_pembeli . '%');
}


    $penjualan = $query->orderByDesc('tanggal')
                       ->orderByDesc('id')
                       ->paginate(100)
                       ->withQueryString();

    return view('penjualan.indexindividu', compact('penjualan'));
}


public function create()
{
    $produk = Produk::all(); // ambil semua produk dari database
    $provinsi = DB::table('indonesia_postal_codes')
            ->select('province')
            ->distinct()
            ->orderBy('province')
            ->get();

        return view('penjualan.create', compact('produk', 'provinsi'));
}

public function store(Request $request)
{
    $request->validate([
        'metode_pengiriman' => 'required',
        'nama_pembeli' => 'required',
        'metode_pembayaran' => 'nullable|in:dp,lunas',

        'no_hp' => 'required',
        'alamat' => 'required',
        'kodepos' => 'required',
        'provinsi' => 'required',
        'kota' => 'required',
        'kecamatan' => 'required',
        'bukti' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        'wilayah' => 'required',
        'ongkir' => 'required|numeric',
        'dp' => 'nullable|integer|min:0',
        'produk_id.*' => 'required|exists:produk,id',
        'jumlah.*' => 'required|numeric|min:1',
        ], [
    'metode_pengiriman.required' => 'Metode pengiriman wajib diisi.',
    'nama_pembeli.required' => 'Nama pembeli wajib diisi.',
    'no_hp.required' => 'Nomor HP wajib diisi.',
    'alamat.required' => 'Alamat wajib diisi.',
    'kodepos.required' => 'Kode pos wajib diisi.',
    'provinsi.required' => 'Provinsi wajib diisi.',
    'kota.required' => 'Kota/Kabupaten wajib diisi.',
    'kecamatan.required' => 'Kecamatan wajib diisi.',
    'bukti.image' => 'Bukti pembayaran harus berupa gambar.',
    'bukti.mimes' => 'Format gambar bukti harus jpeg, png, jpg, atau gif.',
    'bukti.max' => 'Ukuran gambar bukti maksimal 5MB.',
    'wilayah.required' => 'Wilayah wajib diisi.',
    'ongkir.required' => 'Ongkir wajib diisi.',
    'ongkir.numeric' => 'Ongkir harus berupa angka.',
    'produk_id.*.required' => 'Pilih produk terlebih dahulu.',
    'produk_id.*.exists' => 'Produk yang dipilih tidak valid.',
    'jumlah.*.required' => 'Jumlah produk wajib diisi.',
    'jumlah.*.numeric' => 'Jumlah produk harus berupa angka.',
    'jumlah.*.min' => 'Jumlah produk minimal 1.',
    ]);


// Hitung jumlah penjualan sebelumnya
$lastId = Penjualan::max('id');
$nomorUrut = ($lastId ?? 0) + 1;

    // Format nama pembeli dengan nomor urut
    $namaPembeli = $nomorUrut . '-' . $request->nama_pembeli;

    $penjualan = new Penjualan();
    $penjualan->id_user = auth()->id(); // ambil user login
    $penjualan->tanggal = now();
    $penjualan->fill($request->except('produk_id', 'jumlah', 'bukti', 'nama_pembeli'));
$penjualan->nama_pembeli = $namaPembeli;

   // Simpan file bukti ke folder public/bukti (tanpa storage:link)
    if ($request->hasFile('bukti')) {
        $file = $request->file('bukti');
        $namaFile = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('bukti'), $namaFile);
        $penjualan->bukti = 'bukti/' . $namaFile; // simpan path relatif
    }

    $penjualan->save();

    $total = 0;
    $totalHpp = 0;
    foreach ($request->produk_id as $index => $produkId) {
        $produk = Produk::find($produkId);
        $jumlah = $request->jumlah[$index];
        $totalHarga = $produk->harga_jual * $jumlah;

        DetailPenjualan::create([
            'id_penjualan' => $penjualan->id,
            'id_produk' => $produkId,
            'jumlah' => $jumlah,
            'total_harga' => $totalHarga,
        ]);

        $total += $totalHarga;
        $totalHpp += $produk->hpp * $jumlah;
    }

   if ($request->filled('total_bayar')) {
    $penjualan->total_bayar = str_replace(',', '', $request->total_bayar); // jika input berupa string format ribuan
} else {
    $penjualan->total_bayar = $total + $penjualan->ongkir;
}
$penjualan->total_hpp = $totalHpp;
$penjualan->save();


    return redirect()->route('penjualan.index')->with('success', 'Penjualan berhasil disimpan.');
}





public function edit($id)
{
    $penjualan = Penjualan::findOrFail($id);
    $produk = Produk::all();
    $detail = DetailPenjualan::where('id_penjualan', $id)->get();

    // Ambil daftar provinsi unik
    $provinsiList = IndonesiaPostalCode::select('province')
        ->distinct()
        ->orderBy('province')
        ->pluck('province');

    $customerServices = User::where('role', 'customerservice')->get();    

    return view('penjualan.edit', compact('penjualan', 'produk', 'detail', 'provinsiList','customerServices'));
}

public function update(Request $request, $id)
{
    $request->validate([
        
        'nama_pembeli' => 'required',
        'no_hp' => 'required',
        'alamat' => 'required',
        'kodepos' => 'required',
        'bukti' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        'produk_id.*' => 'required|exists:produk,id',
        'jumlah.*' => 'required|integer|min:1',
    ]);

    $penjualan = Penjualan::findOrFail($id);

 // Jika user upload bukti baru, hapus file lama di public/bukti
    if ($request->hasFile('bukti')) {
        // Hapus file lama jika ada
        if ($penjualan->bukti && file_exists(public_path($penjualan->bukti))) {
            unlink(public_path($penjualan->bukti));
        }

        // Simpan file baru ke public/bukti
        $file = $request->file('bukti');
        $namaFile = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('bukti'), $namaFile);

        // Update kolom bukti
        $penjualan->bukti = 'bukti/' . $namaFile;
    }
    
    $penjualan->update([
        'metode_pengiriman' => $request->metode_pengiriman,
        'metode_pembayaran' => $request->metode_pembayaran,
        'id_user' => $request->id_user,
        'nama_pembeli' => $request->nama_pembeli,
        'no_hp' => $request->no_hp,
        'alamat' => $request->alamat,
        'kodepos' => $request->kodepos,
        'provinsi' => $request->provinsi,
        'kota' => $request->kota,
        'kecamatan' => $request->kecamatan,
        'wilayah' => $request->wilayah,
        'kurir' => $request->kurir, 
        'dp' => $request->dp, 
        'ongkir' => $request->ongkir,
        'catatan' => $request->catatan,
        'detail' => $request->detail,
        'total_bayar' => $request->total_bayar,
         'order_id' => $request->order_id,
    'no_resi' => $request->no_resi,
    ]);

    // Hapus detail lama
    $penjualan->detailPenjualan()->delete();
$totalHpp = 0;
    // Simpan ulang detail
    foreach ($request->produk_id as $index => $produkId) {
        $jumlah = $request->jumlah[$index];
        $produk = Produk::find($produkId);
        $totalHarga = $produk->harga_jual * $jumlah;

        $penjualan->detailPenjualan()->create([
            'id_produk' => $produkId,
            'jumlah' => $jumlah,
            'total_harga' => $totalHarga,
        ]);
        $totalHpp += $produk->hpp * $jumlah;
    }
$penjualan->total_hpp = $totalHpp;
$penjualan->save();



$filters = $request->_filter ?? [];
$queryString = http_build_query($filters);

if (auth()->user()->role === 'admin') {
    return redirect()->to(route('penjualan.laporan') . '?' . $queryString)
        ->with('success', 'Data penjualan berhasil diperbarui.');
} else {
    return redirect()->to(route('penjualan.index') . '?' . $queryString)
        ->with('success', 'Data penjualan berhasil diperbarui.');
}


}


public function show($id)
{
    $penjualan = Penjualan::with('detailPenjualan.produk', 'user')->findOrFail($id);

    return view('penjualan.detail', compact('penjualan'));
}


public function laporan(Request $request)
{
    $query = Penjualan::with(['user', 'detailPenjualan.produk'])
        ->orderBy('tanggal', 'desc')
        ->orderBy('nama_pembeli', 'desc')
        ->orderBy('id', 'asc');

    if ($request->filled('cs')) {
        $query->where('id_user', $request->cs);
    }

    if ($request->filled('daterange')) {
        $range = explode(' - ', $request->daterange);
        $tanggal_awal = $range[0] ?? null;
        $tanggal_akhir = $range[1] ?? null;

        if ($tanggal_awal) {
            $query->whereDate('tanggal', '>=', $tanggal_awal);
        }
        if ($tanggal_akhir) {
            $query->whereDate('tanggal', '<=', $tanggal_akhir);
        }
    }

    // Filter platform
    if ($request->filled('platform')) {
        if ($request->platform === 'nonshopee') {
            $query->whereIn('metode_pengiriman', ['COD', 'TRANSFER']);
        } elseif ($request->platform === 'shopee') {
            $query->where('metode_pengiriman', 'SHOPEE');
        }
    }

// Filter berdasarkan jenis produk
if ($request->filled('jenis_produk')) {
    $query->whereHas('detailPenjualan.produk', function ($q) use ($request) {
        $q->where('jenis_produk', $request->jenis_produk);
    });
}

if ($request->filled('nama_pembeli')) {
    $query->where('nama_pembeli', 'like', '%' . $request->nama_pembeli . '%');
}

if ($request->filled('waktu_input')) {
    $query->where(function ($q) use ($request) {
        if ($request->waktu_input == 'pagi') {
            $q->whereTime('created_at', '<', '11:00:00');
        } elseif ($request->waktu_input == 'siang') {
            $q->whereTime('created_at', '>=', '11:00:00');
        }
    });
}


    $penjualan = $query->paginate(100)->onEachSide(1)->withQueryString();
    $semuaUser = \App\Models\User::where('role', 'customerservice')->get();

    return view('penjualan.laporan', compact('penjualan', 'semuaUser'));
}




public function destroy($id)
{
    $penjualan = Penjualan::findOrFail($id);
    
    // Hapus semua detail penjualan terkait
    $penjualan->detailPenjualan()->delete();

    // Hapus penjualan
    $penjualan->delete();

    // Ambil query string agar redirect tetap bawa filter
    $queryString = http_build_query(request()->except('_token', '_method'));

    if (Auth::user()->role === 'admin') {
        return redirect()->to(route('penjualan.laporan') . '?' . $queryString)
                         ->with('success', 'Data penjualan berhasil dihapus.');
    } else {
        return redirect()->to(route('penjualan.index') . '?' . $queryString)
                         ->with('success', 'Data penjualan berhasil dihapus.');
    }
}



public function ExportExcel(Request $request) 
{
    $fileName = 'penjualan.xlsx'; 
    $filePath = storage_path($fileName);

    $query = Penjualan::with(['detailPenjualan.produk', 'user']);

    if ($request->filled('cs')) {
        $query->where('id_user', $request->cs);
    }

    if ($request->filled('daterange')) {
        $tanggalRange = explode(' - ', $request->daterange);
        if (count($tanggalRange) == 2) {
            try {
                $startDate = Carbon::createFromFormat('Y-m-d', $tanggalRange[0])->startOfDay();
                $endDate = Carbon::createFromFormat('Y-m-d', $tanggalRange[1])->endOfDay();
                $query->whereBetween('tanggal', [$startDate, $endDate]);
            } catch (\Exception $e) {
                // Format salah, skip
            }
        }
    }

    if ($request->filled('jenis_produk')) {
        $query->whereHas('detailPenjualan.produk', function ($q) use ($request) {
            $q->where('jenis_produk', $request->jenis_produk);
        });
    }

    if ($request->filled('nama_pembeli')) {
        $query->where('nama_pembeli', 'like', '%' . $request->nama_pembeli . '%');
    }

    if ($request->filled('waktu_input')) {
        $query->where(function ($q) use ($request) {
            if ($request->waktu_input == 'pagi') {
                $q->whereTime('created_at', '<', '11:00:00');
            } elseif ($request->waktu_input == 'siang') {
                $q->whereTime('created_at', '>=', '11:00:00');
            }
        });
    }
$query->whereIn('metode_pengiriman', ['COD', 'TRANSFER']);
    $penjualans = $query->orderBy('tanggal', 'asc')->get();


    $writer = SimpleExcelWriter::create($filePath)
        ->addHeader([
            'Tipe Pengiriman',
            'Kurir',
            'Nama Penerima',
            'No. Telp. Penerima',
            'Alamat Penerima',
            'Kode Pos Penerima',
            'Berat (kg)',
            'Jumlah Barang',
            'Detail Paket (Gabungan dari Produk dan Detail)',
            'Harga Barang (Jika NON-COD)',
            'Nilai COD (Jika COD)',
            'Kelurahan',
            'Kecamatan',
            'Nama CS',
            'Bukti TF',
            'Total HPP',
        ]);

    foreach ($penjualans as $penjualan) {
        $totalHarga = $penjualan->total_bayar;
        $jumlahBarang = $penjualan->detailPenjualan->sum('jumlah');

        $produkList = $penjualan->detailPenjualan
            ->map(function ($d) {
                return $d->produk ? "{$d->jumlah}x {$d->produk->nama_produk}" : "{$d->jumlah}x Produk tidak ditemukan";
            })
            ->implode(', ');

        $detailPaket = $produkList . "\n" . ($penjualan->detail ?? '');

        $buktiUrl = $penjualan->bukti 
    ? url($penjualan->bukti) 
    : '-';

        $writer->addRow([
            $penjualan->metode_pengiriman === 'TRANSFER' ? 'NON COD' : $penjualan->metode_pengiriman,
            $penjualan->kurir,
            $penjualan->nama_pembeli,
            $penjualan->no_hp,
            $penjualan->alamat,
            $penjualan->kodepos,
            0.5,
            $jumlahBarang,
            $detailPaket,
            $penjualan->metode_pengiriman === 'TRANSFER' ? $totalHarga : '',
            $penjualan->metode_pengiriman === 'COD' ? $totalHarga : '',
            $penjualan->wilayah,
            $penjualan->kecamatan,
            $penjualan->user->name ?? '-',
            $buktiUrl, // 👉 Tambahan data link bukti
             $penjualan->total_hpp,
        ]);
    }

    return response()->download($filePath)->deleteFileAfterSend(true);
}



public function resiExcel(Request $request)
{
    $fileName = 'resi_penjualan.xlsx';
    $filePath = storage_path($fileName);

    $query = Penjualan::with(['detailPenjualan.produk', 'user'])
        ->whereNull('no_resi')
        ->whereIn('metode_pengiriman', ['COD', 'TRANSFER']);

    if ($request->filled('cs')) {
        $query->where('id_user', $request->cs);
    }

    if ($request->filled('daterange')) {
        $tanggalRange = explode(' - ', $request->daterange);
        if (count($tanggalRange) == 2) {
            try {
                $startDate = Carbon::createFromFormat('Y-m-d', $tanggalRange[0])->startOfDay();
                $endDate = Carbon::createFromFormat('Y-m-d', $tanggalRange[1])->endOfDay();
                $query->whereBetween('tanggal', [$startDate, $endDate]);
            } catch (\Exception $e) {
                // Skip jika format salah
            }
        }
    }

    if ($request->filled('jenis_produk')) {
        $query->whereHas('detailPenjualan.produk', function ($q) use ($request) {
            $q->where('jenis_produk', $request->jenis_produk);
        });
    }

    if ($request->filled('nama_pembeli')) {
        $query->where('nama_pembeli', 'like', '%' . $request->nama_pembeli . '%');
    }

    if ($request->filled('waktu_input')) {
        $query->where(function ($q) use ($request) {
            if ($request->waktu_input == 'pagi') {
                $q->whereTime('created_at', '<', '11:00:00');
            } elseif ($request->waktu_input == 'siang') {
                $q->whereTime('created_at', '>=', '11:00:00');
            }
        });
    }

    $penjualans = $query->orderBy('tanggal', 'asc')->get();

    // Header baru sesuai permintaan
    $writer = SimpleExcelWriter::create($filePath)
        ->addHeader([
            'Nama Penerima',
            'Alamat Penerima',
            'Nomor Telepon',
            'Kode Pos',
            'Berat',
            'Harga Barang (Jika NON-COD)',
            'Nilai COD (Jika COD)',
            'Isi Paketan (Nama Produk)',
            '*Desa',
            '**Kecamatan',
            '*Instruksi Pengiriman',
            'Assignee name',
            '**Remark 1',
            '**Remark 2',
            '*Remark 3',
        ]);

    foreach ($penjualans as $penjualan) {
        $totalHarga = $penjualan->total_bayar;
        $jumlahBarang = $penjualan->detailPenjualan->sum('jumlah');

        $produkList = $penjualan->detailPenjualan
            ->map(function ($d) {
                return $d->produk ? "{$d->jumlah}x {$d->produk->nama_produk}" : "{$d->jumlah}x Produk tidak ditemukan";
            })
            ->implode(', ');

        $detailPaket = $produkList . "\n" . ($penjualan->detail ?? '');

        $assigneeName = $penjualan->user->name ?? '-';

        $writer->addRow([
            $penjualan->nama_pembeli,
            $penjualan->alamat,
            $penjualan->no_hp,
            $penjualan->kodepos,
            0.5,
            $penjualan->metode_pengiriman === 'TRANSFER' ? $totalHarga : '',
            $penjualan->metode_pengiriman === 'COD' ? $totalHarga : '',
            $detailPaket,
            $penjualan->wilayah,
            $penjualan->kecamatan,
            $penjualan->catatan ?? '',
            $assigneeName,
            $assigneeName,
            '',
            '',
        ]);
    }

    return response()->download($filePath)->deleteFileAfterSend(true);
}



public function cekShopee(Request $request)
{
    // Ambil semua user dengan role 'admin'
    $adminIds = \App\Models\User::where('role', 'admin')->pluck('id');

    // Filter penjualan metode SHOPEE dan user admin
    $penjualan = Penjualan::with(['user', 'detailPenjualan.produk'])
        ->where('metode_pengiriman', 'SHOPEE')
        ->whereIn('id_user', $adminIds)
        ->orderBy('tanggal', 'desc')
        ->paginate(100)
        ->onEachSide(1);

    return view('penjualan.shopee', compact('penjualan'));
}

public function ambilPenjualan($id)
{
    $penjualan = Penjualan::findOrFail($id);
    $penjualan->id_user = Auth::id();
    $penjualan->save();

    return back()->with('success', 'Penjualan berhasil ditandai sebagai milik Anda.');
}
}
