<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead;
use Illuminate\Support\Facades\Auth;
use App\Models\Penjualan;
use App\Models\User;
use Carbon\Carbon;
class LeadController extends Controller
{
    


public function index(Request $request)
{
    $userId = Auth::id();
    $query = Lead::where('user_id', $userId);

    if ($request->filled('daterange')) {
        $range = explode(' - ', $request->daterange);

        try {
            $start = Carbon::parse(trim($range[0]))->startOfDay();
            $end = Carbon::parse(trim($range[1]))->endOfDay();
            $query->whereBetween('tanggal', [$start, $end]);
        } catch (\Exception $e) {
            // format salah, abaikan
        }
    }

    $lead = $query->orderBy('tanggal', 'desc')->paginate(100)->withQueryString();

    // Tambahkan jumlah penjualan dan persentase ke setiap item
    $lead->getCollection()->transform(function ($item) use ($userId) {
        $jumlahPenjualan = Penjualan::where('id_user', $userId)
                                     ->whereDate('tanggal', $item->tanggal)
                                     ->count();

        $item->jumlah_penjualan = $jumlahPenjualan;
        $item->persentase = $item->jumlah_lead > 0
            ? round(($jumlahPenjualan / $item->jumlah_lead) * 100, 2)
            : 0;

        return $item;
    });

    return view('lead.index', compact('lead'));
}




     public function create()
    {
        return view('lead.create');
    }
    public function store(Request $request)
{
    $request->validate([
        'jumlah_lead' => 'required|integer|min:1',
        'tanggal' => 'required|date',
    ]);

    $userId = Auth::id();
    $tanggal = $request->tanggal;

    // Cek apakah data dengan user_id dan tanggal yang sama sudah ada
    $cekDuplikat = Lead::where('user_id', $userId)
                        ->where('tanggal', $tanggal)
                        ->first();

    if ($cekDuplikat) {
        return redirect()->back()->with('error', 'Data lead untuk tanggal tersebut sudah ada!');
    }

    // Simpan data jika belum ada duplikat
    Lead::create([
        'user_id' => $userId,
        'jumlah_lead' => $request->jumlah_lead,
        'tanggal' => $tanggal,
    ]);

    return redirect()->back()->with('success', 'Data lead berhasil ditambahkan!');
}

public function edit($id)
{
    $lead = Lead::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

    return view('lead.edit', compact('lead'));
}

public function update(Request $request, $id)
{
    $request->validate([
        'jumlah_lead' => 'required|integer|min:1',
        'tanggal' => 'required|date',
    ]);

    $lead = Lead::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

    // Cek duplikat tanggal lain selain data ini sendiri
    $duplikat = Lead::where('user_id', Auth::id())
                    ->where('tanggal', $request->tanggal)
                    ->where('id', '!=', $id)
                    ->exists();

    if ($duplikat) {
        return redirect()->back()->with('error', 'Tanggal tersebut sudah digunakan untuk data lead lain!');
    }

    $lead->update([
        'jumlah_lead' => $request->jumlah_lead,
        'tanggal' => $request->tanggal,
    ]);

    return redirect()->route('lead.index')->with('success', 'Data lead berhasil diperbarui!');
}

public function destroy($id)
{
    $lead = Lead::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

    $lead->delete();

    return redirect()->route('lead.index')->with('success', 'Data lead berhasil dihapus!');
}

public function laporan()
{
    // Ambil data dengan user relasi
    $semuaLead = Lead::with('user')->orderBy('tanggal', 'desc')->paginate(20);

    // Transformasi data untuk menambahkan jumlah_penjualan & persentase
    $semuaLead->getCollection()->transform(function ($lead) {
        if (!empty($lead->user_id) && !empty($lead->tanggal)) {
            $lead->jumlah_penjualan = Penjualan::where('id_user', $lead->user_id)
                                               ->whereDate('tanggal', $lead->tanggal)
                                               ->count();
        } else {
            $lead->jumlah_penjualan = 0;
        }

        $lead->persentase = $lead->jumlah_lead > 0
            ? round(($lead->jumlah_penjualan / $lead->jumlah_lead) * 100, 2)
            : 0;

        return $lead;
    });

    return view('lead.laporan', compact('semuaLead'));
}

}
