<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use App\Models\Produk;
use App\Models\JenisProduk;
use App\Models\User;
use App\Models\Iklan;
use Carbon\Carbon;
use App\Models\IndonesiaPostalCode;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Spatie\SimpleExcel\SimpleExcelReader;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
                       ->paginate(500)
                       ->onEachSide(1)
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



 $totalHargaProduk = DetailPenjualan::where('id_penjualan', $penjualan->id)->sum('total_harga');

    if ($totalHargaProduk > 0) {
        $selisih = $penjualan->total_bayar - $penjualan->ongkir - $totalHargaProduk;
        $pembagianOmset = $selisih / $totalHargaProduk;

        $pembagianOngkir = $penjualan->ongkir / $totalHargaProduk;
        $pembagianBiayaCOD = $penjualan->biaya_cod / $totalHargaProduk;
        $pembagianCashback = $penjualan->cashback / $totalHargaProduk;

        $details = DetailPenjualan::where('id_penjualan', $penjualan->id)->get();

        foreach ($details as $detail) {
            // OMSET
            $hasil = $detail->total_harga * $pembagianOmset;
                    $shasil_pembagian_omset = $hasil + $detail->total_harga;
                    $detail->hasil_pembagian_omset = $shasil_pembagian_omset;


            // ONGKIR
            $hasilOngkir = $detail->total_harga * $pembagianOngkir;
            $detail->hasil_pembagian_ongkir = $hasilOngkir;

            // BIAYA COD
            $hasilBiayaCOD = $detail->total_harga * $pembagianBiayaCOD;
            $detail->hasil_pembagian_biayacod = $hasilBiayaCOD;

            // CASHBACK
            $hasilCashback = $detail->total_harga * $pembagianCashback;
            $detail->hasil_pembagian_cashback = $hasilCashback;

            $detail->save();
        }
    }

    return redirect()->route('penjualan.index')->with('success', 'Penjualan berhasil disimpan.');
}





public function edit($id, Request $request)
{
    $penjualan = Penjualan::findOrFail($id);
    $produk = Produk::all();
    $detail = DetailPenjualan::where('id_penjualan', $id)->get();

    // Ambil daftar provinsi unik
    $provinsiList = IndonesiaPostalCode::select('province')
        ->distinct()
        ->orderBy('province')
        ->pluck('province');

    // Ambil semua user dengan role 'customerservice'
    $customerServices = User::where('role', 'customerservice')->get();

    // Cek jika user yang terhubung ke penjualan tidak termasuk CS
    $currentUser = User::find($penjualan->id_user);
    if ($currentUser && $currentUser->role !== 'customerservice') {
        // Gabungkan user saat ini ke dalam koleksi
        $customerServices->push($currentUser);
    } 
if ($request->has('from')) {
        session(['return_to_url' => $request->get('from')]);
    }

    return view('penjualan.edit', compact('penjualan', 'produk', 'detail', 'provinsiList','customerServices'));
}

public function update(Request $request, $id)
{
    $request->validate([
        
        'nama_pembeli' => 'required',
        'no_hp' => 'required',
        
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

$totalHargaProduk = DetailPenjualan::where('id_penjualan', $penjualan->id)->sum('total_harga');

    if ($totalHargaProduk > 0) {
        $selisih = $penjualan->total_bayar - $penjualan->ongkir - $totalHargaProduk;
        $pembagianOmset = $selisih / $totalHargaProduk;

        $pembagianOngkir = $penjualan->ongkir / $totalHargaProduk;
        $pembagianBiayaCOD = $penjualan->biaya_cod / $totalHargaProduk;
        $pembagianCashback = $penjualan->cashback / $totalHargaProduk;

        $details = DetailPenjualan::where('id_penjualan', $penjualan->id)->get();

        foreach ($details as $detail) {
            // OMSET
            $hasil = $detail->total_harga * $pembagianOmset;
                    $shasil_pembagian_omset = $hasil + $detail->total_harga;
                    $detail->hasil_pembagian_omset = round($shasil_pembagian_omset);

            // ONGKIR
            $hasilOngkir = $detail->total_harga * $pembagianOngkir;
            $detail->hasil_pembagian_ongkir = round($hasilOngkir);

            // BIAYA COD
            $hasilBiayaCOD = $detail->total_harga * $pembagianBiayaCOD;
            $detail->hasil_pembagian_biayacod = round($hasilBiayaCOD);

            // CASHBACK
            $hasilCashback = $detail->total_harga * $pembagianCashback;
            $detail->hasil_pembagian_cashback = round($hasilCashback);

            $detail->save();
        }
    }

$filters = $request->_filter ?? [];
$queryString = http_build_query($filters);

if (auth()->user()->role === 'admin') {
       $redirectUrl = session('return_to_url', route('penjualan.laporan')); // fallback kalau gak ada
        session()->forget('return_to_url'); // bersihkan session setelah dipakai

        return redirect()->to($redirectUrl)->with('success', 'Data penjualan berhasil diperbarui.');
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
        // Memulai query untuk data penjualan utama yang akan ditampilkan dan dipaginasi
        $query = Penjualan::with(['user', 'detailPenjualan.produk'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('nama_pembeli', 'desc')
            ->orderBy('id', 'asc');

        // Menerapkan filter berdasarkan input dari request
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

        if ($request->filled('jenis_produk')) {
    $query->whereHas('detailPenjualan.produk', function ($q) use ($request) {
        $q->where('jenis_produk_id', $request->jenis_produk); // ← BENAR
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

        // Ambil data penjualan dengan paginasi
        $penjualan = $query->paginate(500)->onEachSide(1)->withQueryString();

      foreach ($penjualan as $p) {
    $tanggal = Carbon::parse($p->tanggal)->toDateString();
    $idUser = $p->id_user;

    // Ambil semua jenis_lead_id dari detail penjualan untuk penjualan ini
    $jenisLeadIds = $p->detailPenjualan()
        ->join('produk', 'detail_penjualan.id_produk', '=', 'produk.id')
        ->whereNotNull('produk.jenis_lead_id')
        ->pluck('produk.jenis_lead_id')
        ->unique();

    $totalBiayaIklanPerTransaksi = 0;

    foreach ($jenisLeadIds as $jenisLeadId) {
        // 1. Hitung total iklan untuk jenis_lead_id tertentu di hari itu
        $totalIklan = Iklan::whereDate('awal_pelaporan', $tanggal)
            ->where('jenis_lead_id', $jenisLeadId)
            ->whereHas('kampanye', function ($q) use ($idUser) {
                $q->where('user_id', $idUser);
            })
            ->sum('jumlah_dibelanjakan');

        // 2. Hitung jumlah closing (penjualan) untuk produk dengan jenis_lead_id ini
        $jumlahClosing = DB::table('penjualan as p')
            ->join('detail_penjualan as dp', 'p.id', '=', 'dp.id_penjualan')
            ->join('produk as pr', 'dp.id_produk', '=', 'pr.id')
            ->whereDate('p.tanggal', $tanggal)
            ->where('p.id_user', $idUser)
            ->where('pr.jenis_lead_id', $jenisLeadId)
            ->select('p.id')
            ->distinct()
            ->count(); // hitung jumlah penjualan unik

        $biayaIklanJenisIni = $jumlahClosing > 0 ? ($totalIklan / $jumlahClosing) : 0;

        $totalBiayaIklanPerTransaksi += $biayaIklanJenisIni;
    }

    $p->biayaIklanPerTransaksi = $totalBiayaIklanPerTransaksi;

    // Hitung profit sesuai rumus
    $p->profit = $p->total_bayar
                - $totalBiayaIklanPerTransaksi
                - $p->total_hpp
                - $p->ongkir
                - $p->biaya_cod;
}

        // Ambil semua user dengan role 'customerservice' untuk filter di view
        $semuaUser = User::where('role', 'customerservice')->get();
 $jenisProdukOptions = JenisProduk::all();
        // Kirim data ke view
        return view('penjualan.laporan', compact('penjualan', 'semuaUser','jenisProdukOptions'));
    }

public function pembagian(Request $request)
    {
        // Memulai query untuk data penjualan utama yang akan ditampilkan dan dipaginasi
        $query = Penjualan::with(['user', 'detailPenjualan.produk'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('nama_pembeli', 'desc')
            ->orderBy('id', 'asc');

        // Menerapkan filter berdasarkan input dari request
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

        if ($request->filled('jenis_produk')) {
    $query->whereHas('detailPenjualan.produk', function ($q) use ($request) {
        $q->where('jenis_produk_id', $request->jenis_produk); // ← BENAR
    });
}


        if ($request->filled('nama_pembeli')) {
            $query->where('nama_pembeli', 'like', '%' . $request->nama_pembeli . '%');
        }

        if ($request->filled('waktu_input')) {
            $query->where(function ($q) use ($request) {
                if ($request->waktu_input == 'pagi') {
                    $q->whereTime('tanggal', '<', '11:00:00');
                } elseif ($request->waktu_input == 'siang') {
                    $q->whereTime('tanggal', '>=', '11:00:00');
                }
            });
        }

        // Ambil data penjualan dengan paginasi
        $penjualan = $query->paginate(500)->onEachSide(1)->withQueryString();

        foreach ($penjualan as $p) {
    $tanggal = Carbon::parse($p->tanggal)->toDateString();
    $idUser = $p->id_user;

    // Ambil semua jenis_lead_id dari detail penjualan untuk penjualan ini
    $jenisLeadIds = $p->detailPenjualan()
        ->join('produk', 'detail_penjualan.id_produk', '=', 'produk.id')
        ->whereNotNull('produk.jenis_lead_id')
        ->pluck('produk.jenis_lead_id')
        ->unique();

    $totalBiayaIklanPerTransaksi = 0;

    foreach ($jenisLeadIds as $jenisLeadId) {
        // 1. Hitung total iklan untuk jenis_lead_id tertentu di hari itu
        $totalIklan = Iklan::whereDate('awal_pelaporan', $tanggal)
            ->where('jenis_lead_id', $jenisLeadId)
            ->whereHas('kampanye', function ($q) use ($idUser) {
                $q->where('user_id', $idUser);
            })
            ->sum('jumlah_dibelanjakan');

        // 2. Hitung jumlah closing (penjualan) untuk produk dengan jenis_lead_id ini
        $jumlahClosing = DB::table('penjualan as p')
            ->join('detail_penjualan as dp', 'p.id', '=', 'dp.id_penjualan')
            ->join('produk as pr', 'dp.id_produk', '=', 'pr.id')
            ->whereDate('p.tanggal', $tanggal)
            ->where('p.id_user', $idUser)
            ->where('pr.jenis_lead_id', $jenisLeadId)
            ->select('p.id')
            ->distinct()
            ->count(); // hitung jumlah penjualan unik

        $biayaIklanJenisIni = $jumlahClosing > 0 ? ($totalIklan / $jumlahClosing) : 0;

        $totalBiayaIklanPerTransaksi += $biayaIklanJenisIni;
    }

    $p->biayaIklanPerTransaksi = $totalBiayaIklanPerTransaksi;

    // Hitung profit sesuai rumus
    $p->profit = $p->total_bayar
                - $totalBiayaIklanPerTransaksi
                - $p->total_hpp
                - $p->ongkir
                - $p->biaya_cod;
}


        // Ambil semua user dengan role 'customerservice' untuk filter di view
        $semuaUser = User::where('role', 'customerservice')->get();
 $jenisProdukOptions = JenisProduk::all();
        // Kirim data ke view
        return view('penjualan.pembagian', compact('penjualan', 'semuaUser','jenisProdukOptions'));
    }

public function ExportPembagianExcel(Request $request)
{
    $fileName = 'pembagian_penjualan.xlsx';
    $filePath = storage_path($fileName);

      $penjualans = Penjualan::with(['detailPenjualan', 'user'])
        ->when($request->cs, function ($query) use ($request) {
            $query->where('id_user', $request->cs);
        })
        ->when($request->daterange, function ($query) use ($request) {
            $dates = explode(' - ', $request->daterange);
            if (count($dates) == 2) {
                try {
    $start = \Carbon\Carbon::parse(str_replace('/', '-', trim($dates[0])))->startOfDay();
    $end = \Carbon\Carbon::parse(str_replace('/', '-', trim($dates[1])))->endOfDay();
    $query->whereBetween('tanggal', [$start, $end]);
} catch (\Exception $e) {
    // Handle error jika parsing gagal
    report($e);
}

            }
        })
        ->when($request->platform, function ($query) use ($request) {
            if ($request->platform == 'shopee') {
                $query->whereNotNull('order_id');
            } elseif ($request->platform == 'nonshopee') {
                $query->whereNull('order_id');
            }
        })
        ->when($request->nama_pembeli, function ($query) use ($request) {
            $query->where('nama_pembeli', 'like', '%' . $request->nama_pembeli . '%');
        })
        ->when($request->waktu_input, function ($query) use ($request) {
            if ($request->waktu_input == 'pagi') {
                $query->whereTime('created_at', '<', '11:00:00');
            } elseif ($request->waktu_input == 'siang') {
                $query->whereTime('created_at', '>=', '11:00:00');
            }
        })
        ->when($request->jenis_produk, function ($query) use ($request) {
            $query->whereHas('detailPenjualan.produk', function ($q) use ($request) {
                $q->where('id_jenis_produk', $request->jenis_produk);
            });
        })
        ->orderBy('tanggal', 'asc')
        ->get();

    $writer = SimpleExcelWriter::create($filePath)
        ->addHeader([
            'Tanggal',
            'Nama Pembeli',
            'Total Omset',
            'Pembagian Omset',
            'Total Ongkir',
            'Pembagian Ongkir',
            'Total Biaya COD',
            'Pembagian Biaya COD',
            'Total Cashback',
            'Pembagian Cashback',
        ]);

    foreach ($penjualans as $penjualan) {
        $pembagianOmset = $penjualan->detailPenjualan->sum(function ($item) {
    return ($item->hasil_pembagian_omset ?? 0) + ($item->hasil_pembagian_ongkir ?? 0);
});

        $pembagianOngkir = $penjualan->detailPenjualan->sum('hasil_pembagian_ongkir');
        $pembagianBiayaCod = $penjualan->detailPenjualan->sum('hasil_pembagian_biayacod');
        $pembagianCashback = $penjualan->detailPenjualan->sum('hasil_pembagian_cashback');

        $writer->addRow([
            $penjualan->tanggal,
            $penjualan->nama_pembeli,
            $penjualan->total_bayar,
            $pembagianOmset,
            $penjualan->ongkir,
            $pembagianOngkir,
            $penjualan->biaya_cod,
            $pembagianBiayaCod,
            $penjualan->cashback,
            $pembagianCashback,
        ]);
    }

    return response()->download($filePath)->deleteFileAfterSend(true);
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

        // Logika baru untuk filter ekspor
        if ($request->has('selected_ids') && is_array($request->selected_ids) && !empty($request->selected_ids)) {
            // Jika ada ID yang dipilih, filter berdasarkan ID tersebut
            $query->whereIn('id', $request->selected_ids);
        } else {
            // Jika tidak ada ID yang dipilih, terapkan semua filter yang ada
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

            if ($request->filled('platform')) {
                if ($request->platform === 'nonshopee') {
                    $query->whereIn('metode_pengiriman', ['COD', 'TRANSFER']);
                } elseif ($request->platform === 'shopee') {
                    $query->where('metode_pengiriman', 'SHOPEE');
                }
            }

               if ($request->filled('jenis_produk')) {
    $query->whereHas('detailPenjualan.produk', function ($q) use ($request) {
        $q->where('jenis_produk_id', $request->jenis_produk); // ← BENAR
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
            // Tambahkan juga filter metode pengiriman default jika tidak ada selected_ids
            
        }


        $penjualans = $query->orderBy('tanggal', 'asc')->get();


       $writer = SimpleExcelWriter::create($filePath)
        ->addHeader([
            'Tanggal',
            'CS',
            'METODE',
            'EKSPEDISI',
            'NAMA',
            'NO. HP',
            'ALAMAT',
            'NAMA PRODUK',
            'DETAIL',
            'BERAT',
            'TOTAL PEMBAYARAN',
            'JUMLAH PRODUK',
            'ONGKIR',
            'BIAYA COD',
            'CASHBACK',
            'PENCAIRAN',
            'HPP',
            'KETERANGAN',
            'STATUS PENGIRIMAN',
            'TANGGAL RESI',
            'NO. RESI',
            'NO. RESI (KB WD)',
            'PROFIT',
            'Iklan',
            'CASHBACK',
            'EST. CASHBACK',
            'STATUS WD',
            'TF NON COD',
            'Bukti TF',
        ]);


        foreach ($penjualans as $penjualan) {
        $jumlahBarang = $penjualan->detailPenjualan->sum('jumlah');

        $produkList = $penjualan->detailPenjualan
            ->map(fn($d) => $d->produk ? "{$d->jumlah}x {$d->produk->nama_produk}" : "{$d->jumlah}x Produk tidak ditemukan")
            ->implode(', ');

        $variasiList = $penjualan->detailPenjualan
            ->map(fn($d) => $d->nama_variasi ? "{$d->jumlah}x {$d->produk->nama_produk} (Variasi: {$d->nama_variasi})" : '')
            ->filter()
            ->implode(', ');

$buktiUrl = $penjualan->bukti
                ? url($penjualan->bukti)
                : '-';
// --- Logika biaya iklan per transaksi ---
    $tanggal = \Carbon\Carbon::parse($penjualan->tanggal)->toDateString();
    $idUser = $penjualan->id_user;

    $jenisLeadIds = $penjualan->detailPenjualan()
        ->join('produk', 'detail_penjualan.id_produk', '=', 'produk.id')
        ->whereNotNull('produk.jenis_lead_id')
        ->pluck('produk.jenis_lead_id')
        ->unique();

    $totalBiayaIklanPerTransaksi = 0;

    foreach ($jenisLeadIds as $jenisLeadId) {
        $totalIklan = \App\Models\Iklan::whereDate('awal_pelaporan', $tanggal)
            ->where('jenis_lead_id', $jenisLeadId)
            ->whereHas('kampanye', function ($q) use ($idUser) {
                $q->where('user_id', $idUser);
            })
            ->sum('jumlah_dibelanjakan');

        $jumlahClosing = DB::table('penjualan as p')
            ->join('detail_penjualan as dp', 'p.id', '=', 'dp.id_penjualan')
            ->join('produk as pr', 'dp.id_produk', '=', 'pr.id')
            ->whereDate('p.tanggal', $tanggal)
            ->where('p.id_user', $idUser)
            ->where('pr.jenis_lead_id', $jenisLeadId)
            ->select('p.id')
            ->distinct()
            ->count();

        $biayaIklanJenisIni = $jumlahClosing > 0 ? ($totalIklan / $jumlahClosing) : 0;
        $totalBiayaIklanPerTransaksi += $biayaIklanJenisIni;
    }

    // Hitung profit
    $profit = $penjualan->total_bayar
        - $totalBiayaIklanPerTransaksi
        - $penjualan->total_hpp
        - $penjualan->ongkir
        - $penjualan->biaya_cod;

        $writer->addRow([
            $penjualan->tanggal,
            $penjualan->user->name ?? '-',
            $penjualan->metode_pengiriman === 'TRANSFER' ? 'NON COD' : $penjualan->metode_pengiriman,
            $penjualan->kurir,
            $penjualan->nama_pembeli,
            $penjualan->no_hp,
            $penjualan->alamat,
            $produkList,
            $penjualan->detail . ($variasiList ? " | $variasiList" : ''),
            0.5,
            $penjualan->total_bayar,
            $jumlahBarang,
            $penjualan->ongkir,
            $penjualan->biaya_cod,
            $penjualan->cashback,
            '', // PENCAIRAN
            $penjualan->total_hpp,
            '', // KETERANGAN
            $penjualan->status_pesanan,
            '', // TANGGAL RESI
            $penjualan->no_resi,
            $penjualan->metode_pengiriman === 'SHOPEE' ? $penjualan->order_id : $penjualan->no_resi,
            $profit, // PROFIT
        $totalBiayaIklanPerTransaksi, // IKLAN
            $penjualan->cashback,
            '', // EST. CASHBACK
            '', // STATUS WD
            $penjualan->metode_pengiriman === 'TRANSFER' ? $penjualan->total_bayar : '',
            $buktiUrl,
        ]);
    }

        return response()->download($filePath)->deleteFileAfterSend(true);
    }




public function resiExcel(Request $request)
    {
        $fileName = 'resi_penjualan.xlsx';
        $filePath = storage_path($fileName);

        $query = Penjualan::with(['detailPenjualan.produk', 'user'])
            ->whereNull('no_resi'); // Tetap hanya mengambil yang belum punya resi

        // Logika baru untuk filter ekspor berdasarkan checkbox atau filter umum
        if ($request->has('selected_ids') && is_array($request->selected_ids) && !empty($request->selected_ids)) {
            // Jika ada ID yang dipilih, filter berdasarkan ID tersebut
            $query->whereIn('id', $request->selected_ids);
        } else {
            // Jika tidak ada ID yang dipilih, terapkan semua filter yang ada
            // (dan tambahkan kondisi metode_pengiriman jika belum ada di filter umum)
            $query->whereIn('metode_pengiriman', ['COD', 'TRANSFER']);

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

              if ($request->filled('platform')) {
                if ($request->platform === 'nonshopee') {
                    $query->whereIn('metode_pengiriman', ['COD', 'TRANSFER']);
                } elseif ($request->platform === 'shopee') {
                    $query->where('metode_pengiriman', 'SHOPEE');
                }
            }

               if ($request->filled('jenis_produk')) {
    $query->whereHas('detailPenjualan.produk', function ($q) use ($request) {
        $q->where('jenis_produk_id', $request->jenis_produk); // ← BENAR
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

              // ✅ Tambahkan juga kolom 'detail' ke dalam deskripsi paket
        $isiPaketan = trim($produkList . ($penjualan->detail ? " {$penjualan->detail}" : ''));

        // ✅ Gunakan kolom 'catatan' untuk instruksi pengiriman
        $instruksiPengiriman = $penjualan->catatan ?? '';

            $assigneeName = $penjualan->user->name ?? '-';

$dp = $penjualan->dp ?? 0;
$hargaSetelahDp = max(0, $totalHarga - $dp);


            $writer->addRow([
                $penjualan->nama_pembeli,
                $penjualan->alamat,
                $penjualan->no_hp,
                $penjualan->kodepos,
                0.5, // Berat default
                $penjualan->metode_pengiriman === 'TRANSFER' ? $hargaSetelahDp : '',
                $penjualan->metode_pengiriman === 'COD' ? $hargaSetelahDp : '',
                $isiPaketan, // Hanya produk, tanpa detail tambahan di sini
                $penjualan->wilayah, // Desa/Kelurahan
                $penjualan->kecamatan,
                $instruksiPengiriman, // Kolom Instruksi Pengiriman
                $assigneeName,
                $assigneeName, // Remark 1
                '',
                 '',
            ]);
        }

        return response()->download($filePath)->deleteFileAfterSend(true);
    }


public function cekShopee(Request $request)
{
    $adminIds = \App\Models\User::where('role', 'admin')->pluck('id');

    $query = Penjualan::with(['user', 'detailPenjualan.produk'])
        ->where('metode_pengiriman', 'SHOPEE')
        ->whereIn('id_user', $adminIds);

    if ($request->filled('nama_pembeli')) {
        $query->where('nama_pembeli', 'like', '%' . $request->nama_pembeli . '%');
    }

    if ($request->filled('daterange')) {
        $dates = explode(' - ', $request->daterange);
        if (count($dates) === 2) {
            $startDate = Carbon::createFromFormat('Y-m-d', $dates[0])->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $dates[1])->endOfDay();

            $query->whereBetween('tanggal', [$startDate, $endDate]);
        }
    }

    $penjualan = $query->orderBy('tanggal', 'desc')
        ->paginate(500)
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

public function importView()
{
    return view('penjualan.import');
}

public function importLama(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:xlsx,xls',
    ]);

    try {
        $path = $request->file('file')->store('temp');
        $fullPath = storage_path('app/' . $path);

        $rows = SimpleExcelReader::create($fullPath)->getRows();
        $grouped = collect($rows)->groupBy('No Pesanan');

        DB::beginTransaction();

        foreach ($grouped as $orderId => $items) {
            $first = $items->first();

            $namaCs = trim($first['Nama CS'] ?? '');
$userId = User::where('name', $namaCs)->value('id');
if (!$userId) {
    $userId = 1;
}


            // Normalisasi status dan hitung total bayar final
            $statusPesanan = $first['Status Pesanan'] ?? '';
            $normalizedStatus = strtolower(trim(preg_replace('/\s+/', ' ', $statusPesanan)));
            $statusInvalid = ['rts', 'batal', 'undelivered', 'delivery return', 'deliveri return'];
            $setTotalBayarToZero = in_array($normalizedStatus, $statusInvalid);
            $finalTotalBayar = $setTotalBayarToZero ? 0 : (int) $first['Total Bayar'];

            // Cek apakah order_id sudah ada
            $penjualan = Penjualan::where('order_id', $orderId)->first();

            if ($penjualan) {
                // Update data
                $penjualan->update([
                    'id_user' => $userId,
                    'tanggal' => \Carbon\Carbon::parse($first['Tanggal']),
                    'metode_pengiriman' => strtoupper(trim($first['Metode Pengiriman'])) === 'NON COD' ? 'TRANSFER' : $first['Metode Pengiriman'],
                    'metode_pembayaran' => $first['Metode Pembayaran'],
                    'nama_pembeli' => $first['Nama Pembeli'],
                    'no_hp' => $first['Nomor Telepon'],
                    'alamat' => $first['Alamat'],
                    'kodepos' => $first['Kode Pos'],
                    'provinsi' => $first['Provinsi'],
                    'kota' => $first['Kota'],
                    'kecamatan' => $first['Kecamatan'],
                    'wilayah' => $first['Wilayah'],
                    'ongkir' => (int) $first['Ongkir'],
                    'detail' => $first['Detail'],
                    'total_bayar' => $finalTotalBayar,
                    'total_hpp' => (int) $first['Total HPP'],
                    'dp' => (int) $first['Dp'],
                    'cashback' => (int) $first['Cashback'],
                    'biaya_cod' => (int) $first['Biaya COD'],
                    'kurir' => $first['Kurir'],
                    'no_resi' => $first['No Resi'],
                    'status_pesanan' => $first['Status Pesanan'],
                ]);

                DetailPenjualan::where('id_penjualan', $penjualan->id)->delete();
            } else {
                // Buat baru
                $penjualan = Penjualan::create([
                    'id_user' => $userId,
                    'tanggal' => \Carbon\Carbon::parse($first['Tanggal']),
                    'order_id' => $orderId,
                    'metode_pengiriman' => strtoupper(trim($first['Metode Pengiriman'])) === 'NON COD' ? 'TRANSFER' : $first['Metode Pengiriman'],
                    'metode_pembayaran' => $first['Metode Pembayaran'],
                    'nama_pembeli' => $first['Nama Pembeli'],
                    'no_hp' => $first['Nomor Telepon'],
                    'alamat' => $first['Alamat'],
                    'kodepos' => $first['Kode Pos'],
                    'provinsi' => $first['Provinsi'],
                    'kota' => $first['Kota'],
                    'kecamatan' => $first['Kecamatan'],
                    'wilayah' => $first['Wilayah'],
                    'ongkir' => (int) $first['Ongkir'],
                    'detail' => $first['Detail'],
                    'total_bayar' => $finalTotalBayar,
                    'total_hpp' => (int) $first['Total HPP'],
                    'dp' => (int) $first['Dp'],
                    'cashback' => (int) $first['Cashback'],
                    'biaya_cod' => (int) $first['Biaya COD'],
                    'kurir' => $first['Kurir'],
                    'no_resi' => $first['No Resi'],
                    'status_pesanan' => $first['Status Pesanan'],
                ]);
            }

            foreach ($items as $item) {
                $produk = Produk::where('nama_produk', $item['Nama Produk'])->first();

                if ($produk) {
                    $totalHarga = $produk->harga_jual * (int) $item['jumlah'];

                    DetailPenjualan::create([
                        'id_produk' => $produk->id,
                        'id_penjualan' => $penjualan->id,
                        'jumlah' => (int) $item['jumlah'],
                        'total_harga' => $totalHarga,
                        'nama_variasi' => null,
                    ]);
                }
            }
        }

        DB::commit();
        return back()->with('success', 'Import data berhasil!');
    } catch (\Exception $e) {
        DB::rollback();
        return back()->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
    }
}


public function downloadTemplate(): StreamedResponse
{
    $fileName = 'template_import_penjualan.xlsx';

    return SimpleExcelWriter::streamDownload($fileName)
        ->addHeader([
            'Nama CS',
            'Tanggal',
            'No Pesanan',
            'Metode Pengiriman',
            'Metode Pembayaran',
            'Nama Pembeli',
            'Nomor Telepon',
            'Alamat',
            'Kode Pos',
            'Provinsi',
            'Kota',
            'Kecamatan',
            'Wilayah',
            'Ongkir',
            'Detail',
            'Total Bayar',
            'Total HPP',
            'Dp',
            'Cashback',
            'Biaya COD',
            'Kurir',
            'No Resi',
            'Status Pesanan',
            'Nama Produk',
            'jumlah'
        ]);
}



 public function lapshopee(Request $request)
    {
        // Memulai query untuk data penjualan Shopee
        $query = Penjualan::with(['user', 'detailPenjualan.produk'])
            ->where('metode_pengiriman', 'SHOPEE') // Filter khusus untuk Shopee
            ->orderBy('tanggal', 'desc')
            ->orderBy('nama_pembeli', 'desc')
            ->orderBy('id', 'asc');
// HANYA set default 'batal' saat semua filter kosong (halaman pertama dibuka)
    if (!$request->hasAny(['cs', 'daterange', 'jenis_produk', 'nama_pembeli', 'status_filter'])) {
        $request->merge(['status_filter' => 'batal']);
    }
        // Menerapkan filter berdasarkan input dari request (sama seperti fungsi laporan)
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

        // Filter jenis produk
        if ($request->filled('jenis_produk')) {
            $query->whereHas('detailPenjualan.produk', function ($q) use ($request) {
                $q->where('jenis_produk_id', $request->jenis_produk);
            });
        }

        if ($request->filled('nama_pembeli')) {
            $query->where('nama_pembeli', 'like', '%' . $request->nama_pembeli . '%');
        }

       if ($request->filled('status_filter') && $request->status_filter === 'batal') {
    $query->whereRaw("LOWER(status_pesanan) IN (?, ?, ?, ?, ?)", [
        'rts', 'batal', 'undelivered', 'delivery return', 'deliveri return'
    ]);
}


        // Ambil data penjualan dengan paginasi
        $penjualan = $query->paginate(500)->onEachSide(1)->withQueryString();

     

        $semuaUser = User::where('role', 'customerservice')->get();
        $jenisProdukOptions = JenisProduk::all();

        // Mengarahkan ke view yang sama atau view baru khusus Shopee
        return view('penjualan.lapshopee', compact('penjualan', 'semuaUser', 'jenisProdukOptions'));
    }


public function setWithoutHpp(Request $request)
    {
        $request->validate([
            'selected_ids' => 'required|json', // Pastikan input adalah JSON string
        ]);

        $selectedIds = json_decode($request->selected_ids, true);

        if (empty($selectedIds)) {
            return back()->with('error', 'Tidak ada data penjualan yang dipilih.');
        }

        try {
            Penjualan::whereIn('id', $selectedIds)->update([
                'total_hpp' => 0,
                'ongkir'=>0,
                'kunci_hpp' => 'ya',
            ]);

            return back()->with('success', 'HPP dan Ongkir untuk penjualan yang dipilih telah diatur menjadi 0.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat mengatur HPP dan Ongkir: ' . $e->getMessage());
        }
    }    

public function editKunci($id)
{
    $penjualan = Penjualan::findOrFail($id);
    return view('penjualan.kunci', compact('penjualan'));
}

public function updateKunci(Request $request, $id)
    {
        $request->validate([
            'kunci_hpp' => 'nullable|in:ya,tidak',
        ]);

        $penjualan = Penjualan::findOrFail($id);

        // --- Logika Baru Dimulai Di Sini ---
        $oldKunciHpp = $penjualan->kunci_hpp; // Simpan nilai kunci_hpp sebelumnya

        // Perbarui kunci_hpp terlebih dahulu
        $penjualan->kunci_hpp = $request->kunci_hpp;

        if ($penjualan->kunci_hpp === 'ya') {
            // Jika kunci_hpp disetel ke 'ya', ubah total_hpp menjadi 0
            $penjualan->total_hpp = 0;
            $message = 'Kunci HPP berhasil diaktifkan, dan Total HPP diatur menjadi 0.';
        } elseif ($penjualan->kunci_hpp === 'tidak') {
            // Jika kunci_hpp disetel ke 'tidak', hitung ulang total_hpp
            $totalHppBaru = 0;

            // Ambil semua detail penjualan yang terkait dengan penjualan ini
            // Pastikan model DetailPenjualan memiliki relasi ke Produk dan Penjualan
            // dan Produk memiliki kolom 'hpp'
            $details = DetailPenjualan::where('id_penjualan', $penjualan->id)->get();

            foreach ($details as $detail) {
                // Pastikan $detail->produk berfungsi (relasi hasOne/belongsTo)
                // atau Anda perlu ambil produk secara manual: Produk::find($detail->id_produk)
                if ($detail->produk) { // Asumsi ada relasi 'produk' di model DetailPenjualan
                    $totalHppBaru += $detail->produk->hpp * $detail->jumlah;
                } else {
                    \Log::warning("Produk tidak ditemukan untuk DetailPenjualan ID: {$detail->id} pada Penjualan ID: {$penjualan->id}");
                }
            }
            $penjualan->total_hpp = (int) round($totalHppBaru);
            $message = 'Kunci HPP berhasil dinonaktifkan, dan Total HPP dihitung ulang.';
        } else {
            // Kasus jika $request->kunci_hpp adalah null (misalnya jika fieldnya dihapus dari form,
            // tapi validasi 'nullable' tetap mengizinkan).
            // Dalam kasus ini, kita bisa memilih untuk tidak melakukan apa-apa pada HPP
            // atau mengaturnya ke 'tidak' dan menghitung ulang.
            // Untuk skenario Anda, lebih baik asumsikan 'tidak' jika null.
            $totalHppBaru = 0;
            $details = DetailPenjualan::where('id_penjualan', $penjualan->id)->get();
            foreach ($details as $detail) {
                if ($detail->produk) {
                    $totalHppBaru += $detail->produk->hpp * $detail->jumlah;
                }
            }
            $penjualan->total_hpp = (int) round($totalHppBaru);
            $penjualan->kunci_hpp = 'tidak'; // Pastikan diset ke 'tidak'
            $message = 'Kunci HPP disetel ke default, dan Total HPP dihitung ulang.';
        }
        // --- Logika Baru Berakhir Di Sini ---

        $penjualan->save();

        return redirect()->route('penjualan.lapshopee')->with('success', $message);
    }
public function beriHpp(Request $request)
{
    $ids = json_decode($request->selected_ids, true);
    $penjualans = Penjualan::whereIn('id', $ids)->get();

    foreach ($penjualans as $penjualan) {
        $totalHpp = 0;

        foreach ($penjualan->detailPenjualan as $detail) {
            $produk = $detail->produk;
            if (!$produk) continue;

            $jumlah = $detail->jumlah;
            $hpp = $produk->hpp * $jumlah;
            $totalHpp += $hpp;
        }

        $penjualan->update([
            'total_hpp' => $totalHpp,
            'kunci_hpp' => 'tidak',
            'tanpa_hpp' => null
        ]);
    }

    return redirect()->route('penjualan.lapshopee')->with('success', 'HPP berhasil dihitung ulang dan kunci HPP dibuka.');
}


public function hitungOmsetSemua()
{
    DB::transaction(function () {
        Penjualan::chunk(100, function ($penjualans) {
            foreach ($penjualans as $penjualan) {
                $totalHargaProduk = DetailPenjualan::where('id_penjualan', $penjualan->id)->sum('total_harga');

                if ($totalHargaProduk == 0) {
                    continue; // Lewati jika tidak ada produk
                }

                $selisih = $penjualan->total_bayar - $penjualan->ongkir - $totalHargaProduk;
                $pembagianOmset = $selisih / $totalHargaProduk;

                $pembagianOngkir = $penjualan->ongkir / $totalHargaProduk;
              
                    $pembagianBiayaCOD = $penjualan->biaya_cod / $totalHargaProduk;
                
                $pembagianCashback = $penjualan->cashback / $totalHargaProduk;


                $details = DetailPenjualan::where('id_penjualan', $penjualan->id)->get();

                foreach ($details as $detail) {
                    $hasil = $detail->total_harga * $pembagianOmset;
                    $shasil_pembagian_omset = $hasil + $detail->total_harga;
                    $detail->hasil_pembagian_omset = $shasil_pembagian_omset; // bisa juga pakai ceil/floor

                    // ONGKIR
                    $hasilOngkir = $detail->total_harga * $pembagianOngkir;
                    $detail->hasil_pembagian_ongkir = $hasilOngkir;

                    // BIAYA COD
                    $hasilBiayaCOD = $detail->total_harga * $pembagianBiayaCOD;
                    $detail->hasil_pembagian_biayacod = $hasilBiayaCOD;

                    // CASHBACK
                    $hasilCashback = $detail->total_harga * $pembagianCashback;
                    $detail->hasil_pembagian_cashback = $hasilCashback;


                    $detail->save();
                }
            }
        });
    });

    return back()->with('success', 'Berhasil menghitung hasil pembagian omset untuk semua penjualan.');
}

    public function resetHpp()
{
    DB::transaction(function () {
        Penjualan::where(function ($query) {
                $query->whereNull('kunci_hpp')
                      ->orWhere('kunci_hpp', 'tidak');
            })
            ->where('total_hpp', '>', 0)
            ->chunk(100, function ($penjualans) {
                foreach ($penjualans as $penjualan) {
                    $totalHpp = 0;

                    foreach ($penjualan->detailPenjualan as $detail) {
                        $produk = $detail->produk;

                        if (!$produk) {
                            continue; // lewati jika produk tidak ditemukan
                        }

                        $jumlah = $detail->jumlah;
                        $totalHpp += $produk->hpp * $jumlah;
                    }

                    $penjualan->update([
                        'total_hpp' => $totalHpp,
                    ]);
                }
            });
    });

    return redirect()->back()->with('success', 'HPP berhasil dihitung ulang untuk penjualan yang tidak terkunci dan sebelumnya sudah memiliki HPP.');
}

}
