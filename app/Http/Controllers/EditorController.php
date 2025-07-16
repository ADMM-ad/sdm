<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\Editor;
use App\Models\Penjualan;
use App\Models\Produk;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EditorController extends Controller
{
    public function jobdesk(Request $request)
{
    $query = Penjualan::with(['user', 'detailPenjualan.produk']);
    // Filter berdasarkan platform (shopee / nonshopee)
    if ($request->filled('platform')) {
        if ($request->platform === 'nonshopee') {
            $query->whereIn('metode_pengiriman', ['COD', 'TRANSFER']);
        } elseif ($request->platform === 'shopee') {
            $query->where('metode_pengiriman', 'SHOPEE');
        }
    }

    if ($request->filled('daterange')) {
    [$start, $end] = explode(' - ', $request->daterange);

    try {
        $startDate = Carbon::createFromFormat('Y-m-d', trim($start))->startOfDay();
        $endDate = Carbon::createFromFormat('Y-m-d', trim($end))->endOfDay();

        $query->whereBetween('tanggal', [$startDate, $endDate]);
    } catch (\Exception $e) {
        // Optional: log error jika format salah
    }
}

 if ($request->filled('produk')) {
        $query->whereHas('detailPenjualan.produk', function ($q) use ($request) {
            $q->where('id', $request->produk);
        });
    }

    $penjualan = $query->orderByDesc('tanggal')->get();
       $produkList = Produk::orderBy('nama_produk')->get();


    return view('editor.jobdesk', compact('penjualan', 'produkList'));
}



public function ambilJobdesk(Request $request)
{
    $request->validate([
        'penjualan_id' => 'required|exists:penjualan,id',
    ]);

    // Cek apakah jobdesk untuk penjualan ini sudah diambil
    $exists = Editor::where('penjualan_id', $request->penjualan_id)->exists();

    if ($exists) {
        return redirect()->back()->with('error', 'Jobdesk sudah diambil.');
    }

    // Simpan jobdesk baru
    Editor::create([
        'user_id' => Auth::id(),
        'penjualan_id' => $request->penjualan_id,
        'status' => 'onproses',
    ]);

    return redirect()->back()->with('success', 'Jobdesk berhasil diambil.');
}


public function index()
{
    $user = Auth::user();

    // Ambil jobdesk user yang statusnya 'onproses' saja
        $jobdesks = Editor::with('penjualan')
        ->where('user_id', $user->id)
        ->where('status', 'onproses')
        ->join('penjualan', 'jobdesk_editor.penjualan_id', '=', 'penjualan.id')
        ->orderBy('penjualan.tanggal', 'asc') // ASC = tanggal terlama di atas
        ->select('jobdesk_editor.*') // hanya ambil kolom dari jobdesk_editor agar tidak bentrok
        ->get();

    return view('editor.index', compact('jobdesks'));
}

public function selesai(Request $request)
{
    $request->validate([
        'jobdesk_id' => 'required|exists:jobdesk_editor,id',
    ]);

    $jobdesk = Editor::where('id', $request->jobdesk_id)
        ->where('user_id', auth()->id())
        ->firstOrFail();

    $jobdesk->update(['status' => 'selesai']);

    return redirect()->back()->with('success', 'Jobdesk berhasil diselesaikan.');
}

public function hapus($id)
{
    $jobdesk = Editor::where('id', $id)
        ->where('user_id', auth()->id()) // hanya boleh hapus milik sendiri
        ->firstOrFail();

    $jobdesk->delete();

    return redirect()->back()->with('success', 'Jobdesk berhasil dihapus.');
}
public function done()
{
    $user = Auth::user();

    $jobdesks = Editor::with('penjualan.detailPenjualan.produk')
        ->where('user_id', $user->id)
        ->where('status', 'selesai')
        ->join('penjualan', 'jobdesk_editor.penjualan_id', '=', 'penjualan.id')
        ->orderBy('penjualan.tanggal', 'asc')
        ->select('jobdesk_editor.*')
        ->paginate(50)
->withQueryString();

    return view('editor.done', compact('jobdesks'));
}

public function laporan(Request $request)
{
    $query = Editor::with(['user', 'penjualan.detailPenjualan.produk'])->latest();

    // Filter tanggal (range)
    if ($request->filled('daterange')) {
            $range = explode(' - ', $request->daterange);
            $tanggal_awal = isset($range[0]) ? Carbon::parse($range[0])->startOfDay() : null;
            $tanggal_akhir = isset($range[1]) ? Carbon::parse($range[1])->endOfDay() : null;

            if ($tanggal_awal && $tanggal_akhir) {
                $query->whereBetween('created_at', [$tanggal_awal, $tanggal_akhir]);
            }
        }

    // Filter berdasarkan editor
    if ($request->filled('editor')) {
        $query->where('user_id', $request->editor);
    }

    // Filter status
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // Filter berdasarkan nama pembeli
    if ($request->filled('nama_pembeli')) {
        $query->whereHas('penjualan', function ($q) use ($request) {
            $q->where('nama_pembeli', 'like', '%' . $request->nama_pembeli . '%');
        });
    }

    $jobdesk = $query->paginate(50)->withQueryString();


    // Untuk dropdown nama editor
    $semuaEditor = User::where('role', 'editor')->get();


    return view('editor.laporan', compact('jobdesk', 'semuaEditor'));
}



}
