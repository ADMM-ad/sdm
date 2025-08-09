<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penjualan;
use App\Models\Lead;
use App\Models\User;
use App\Models\Editor;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class DashboardController extends Controller
{
   public function indexadmin(Request $request)
{
    $queryPenjualan = Penjualan::query();
    $queryLead = Lead::query();

    $jenisLeadId = $request->input('jenis_lead_id');


    // === FILTER UNTUK DASHBOARD DAN TABEL ALL TIME ===
     // === Default Daterange: Bulan & Tahun Sekarang ===
    $startDate = null;
    $endDate = null;

    if ($request->filled('daterange')) {
        [$start, $end] = explode(' - ', $request->daterange);
        try {
            $startDate = Carbon::createFromFormat('Y-m-d', trim($start))->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', trim($end))->endOfDay();
        } catch (\Exception $e) {
            // log error
        }
    } else {
        // ⏱ Jika daterange tidak diisi, set ke awal & akhir bulan ini
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        // Set value default ke request agar tetap muncul di input daterange
        $request->merge([
            'daterange' => $startDate->format('Y-m-d') . ' - ' . $endDate->format('Y-m-d')
        ]);
    }

        // === FILTER JENIS LEAD PADA PENJUALAN ===
    if ($jenisLeadId) {
        $queryPenjualan->whereHas('detailPenjualan.produk', function ($q) use ($jenisLeadId) {
            $q->where('jenis_lead_id', $jenisLeadId);
        });
        // Filter semua query penjualan berdasarkan produk dengan jenis_lead_id tertentu
    }

    $statusInvalid = ['rts', 'batal', 'undelivered', 'delivery return', 'deliveri return'];

$jumlahPenjualan = $queryPenjualan
    ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
        $query->whereBetween('tanggal', [$startDate, $endDate]);
    })
    ->where(function ($query) use ($statusInvalid) {
        $query->whereNull('status_pesanan')
              ->orWhere('status_pesanan', '')
              ->orWhereRaw('LOWER(status_pesanan) NOT IN (' . implode(',', array_fill(0, count($statusInvalid), '?')) . ')', 
                array_map('strtolower', $statusInvalid));
    })
    ->count();


  $totalLead = $queryLead
    ->when($jenisLeadId, function ($query) use ($jenisLeadId) {
        return $query->where('tanggal', $jenisLeadId);
    })
    ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
        $query->whereBetween('created_at', [$startDate, $endDate]);
    })
    ->sum('jumlah_lead');


      
       // --- MODIFIED totalOmset, HPP, BiayaCod, Ongkir, Cashback CALCULATION ---
$totalHpp = 0.0;
$totalOmset = 0.0;
$totalBiayaCod = 0.0;
$totalOngkir = 0.0;
$totalCashback = 0.0;

if ($jenisLeadId) {

    // Base query for joining and filtering by jenis_lead_id
    $baseDetailPenjualanQuery = Penjualan::query()
        ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
            return $query->whereBetween('tanggal', [$startDate, $endDate]);
        })
        ->join('detail_penjualan', 'penjualan.id', '=', 'detail_penjualan.id_penjualan')
        ->join('produk', 'detail_penjualan.id_produk', '=', 'produk.id')
        ->where('produk.jenis_lead_id', $jenisLeadId);

    $detailOmsetItems = (clone $baseDetailPenjualanQuery)
        ->whereNotNull('detail_penjualan.hasil_pembagian_omset')
        ->where('detail_penjualan.hasil_pembagian_omset', '!=', 0)
        ->select(
            'penjualan.order_id',
            'produk.nama_produk',
            'detail_penjualan.hasil_pembagian_omset',
            'detail_penjualan.hasil_pembagian_ongkir'
        )
        ->get();

    $totalHpp = (double)$baseDetailPenjualanQuery->where('penjualan.total_hpp', '!=', 0)->sum(DB::raw('produk.hpp * detail_penjualan.jumlah'));
    $totalOmset = (double)$detailOmsetItems->sum(function($item) {
        return $item->hasil_pembagian_omset + ($item->hasil_pembagian_ongkir ?? 0);
    });
   
$totalBiayaCod = DB::table('penjualan')
    ->join('detail_penjualan', 'penjualan.id', '=', 'detail_penjualan.id_penjualan')
    ->join('produk', 'detail_penjualan.id_produk', '=', 'produk.id')
    ->where('produk.jenis_lead_id', $jenisLeadId)
    ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
        return $query->whereBetween('penjualan.tanggal', [$startDate, $endDate]);
    })
    ->sum('detail_penjualan.hasil_pembagian_biayacod');

$totalOngkir = DB::table('penjualan')
    ->join('detail_penjualan', 'penjualan.id', '=', 'detail_penjualan.id_penjualan')
    ->join('produk', 'detail_penjualan.id_produk', '=', 'produk.id')
    ->where('produk.jenis_lead_id', $jenisLeadId)
    ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
        return $query->whereBetween('penjualan.tanggal', [$startDate, $endDate]);
    })
    ->sum('detail_penjualan.hasil_pembagian_ongkir');

$totalCashback = DB::table('penjualan')
    ->join('detail_penjualan', 'penjualan.id', '=', 'detail_penjualan.id_penjualan')
    ->join('produk', 'detail_penjualan.id_produk', '=', 'produk.id')
    ->where('produk.jenis_lead_id', $jenisLeadId)
    ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
        return $query->whereBetween('penjualan.tanggal', [$startDate, $endDate]);
    })
    ->sum('detail_penjualan.hasil_pembagian_cashback');


} else {

    $baseGlobalCalculationQuery = Penjualan::query()
        ->join('detail_penjualan', 'penjualan.id', '=', 'detail_penjualan.id_penjualan')
        ->join('produk', 'detail_penjualan.id_produk', '=', 'produk.id')
        ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
            return $query->whereBetween('penjualan.tanggal', [$startDate, $endDate]);
        });

    $detailGlobalOmsetItems = (clone $baseGlobalCalculationQuery)
        ->whereNotNull('detail_penjualan.hasil_pembagian_omset')
        ->where('detail_penjualan.hasil_pembagian_omset', '!=', 0)
        ->select(
            'penjualan.order_id',
            'produk.nama_produk',
            'produk.jenis_lead_id',
            'detail_penjualan.hasil_pembagian_omset',
            'detail_penjualan.hasil_pembagian_ongkir'
        )
        ->get();

    $totalHpp = (double)(clone $baseGlobalCalculationQuery)->where('penjualan.total_hpp', '!=', 0)->sum(DB::raw('produk.hpp * detail_penjualan.jumlah'));
    $totalOmset = (double)$detailGlobalOmsetItems->sum(function($item) {
        return $item->hasil_pembagian_omset + ($item->hasil_pembagian_ongkir ?? 0);
    });
    $totalBiayaCod = (double)(clone $baseGlobalCalculationQuery)
        ->whereNotNull('detail_penjualan.hasil_pembagian_biayacod')
        ->sum('detail_penjualan.hasil_pembagian_biayacod');

    $totalOngkir = (double)(clone $baseGlobalCalculationQuery)
        ->whereNotNull('detail_penjualan.hasil_pembagian_ongkir')
        ->sum('detail_penjualan.hasil_pembagian_ongkir');

    $totalCashback = (double)(clone $baseGlobalCalculationQuery)
        ->whereNotNull('detail_penjualan.hasil_pembagian_cashback')
        ->sum('detail_penjualan.hasil_pembagian_cashback');
}
// --- END MODIFIED CALCULATION ---



    
// Ambil total jumlah dibelanjakan dari iklan
$totalDibelanjakan = \App\Models\Iklan::when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
    return $query->whereBetween('awal_pelaporan', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
})
->when($jenisLeadId, function ($query) use ($jenisLeadId) {
    return $query->where('jenis_lead_id', $jenisLeadId); // 🔧 modified: filter jenis_lead
})
->sum('jumlah_dibelanjakan');




// Hitung profit
$profit = $totalOmset - $totalDibelanjakan - $totalHpp - $totalBiayaCod - $totalOngkir;

// --- Tambahan: CPR, ROI, ROAS ---
$cpr = $jumlahPenjualan > 0 ? $totalDibelanjakan / $jumlahPenjualan : 0;
$roi = $totalDibelanjakan > 0 ? $profit / $totalDibelanjakan : 0;
$roas = $totalDibelanjakan > 0 ? $totalOmset / $totalDibelanjakan : 0;


    // === TABEL 1: DATA CS ALL TIME ATAU SESUAI FILTER ===
    $dataCSAllTime = User::where('role', 'customerservice')->get()->map(function ($user) use ($startDate, $endDate, $request, $jenisLeadId) {
        $leadQuery = Lead::where('user_id', $user->id);
        if ($jenisLeadId) {
    $leadQuery->where('jenis_lead_id', $jenisLeadId); // ✅ modified to filter by jenis_lead_id
}

        $penjualanQuery = Penjualan::where('id_user', $user->id);

        if ($request->filled('daterange') && $startDate && $endDate) {
            $leadQuery->whereBetween('tanggal', [$startDate, $endDate]);
            $penjualanQuery->whereBetween('tanggal', [$startDate, $endDate]);
        }

        // Filter jenis lead untuk setiap CS
        if ($jenisLeadId) {
            $penjualanQuery->whereHas('detailPenjualan.produk', function ($q) use ($jenisLeadId) {
                $q->where('jenis_lead_id', $jenisLeadId);
            });
        }

        $leadCount = $leadQuery->sum('jumlah_lead');
        $penjualanCount = $penjualanQuery->count();
     
        $persentase = $leadCount > 0 ? round(($penjualanCount / $leadCount) * 100, 2) : 0;

// === DIGANTI: Perhitungan HPP, Omset, BiayaCod, Ongkir, Cashback per CS ===
$baseDetailQuery = DB::table('penjualan')
    ->join('detail_penjualan', 'penjualan.id', '=', 'detail_penjualan.id_penjualan')
    ->join('produk', 'detail_penjualan.id_produk', '=', 'produk.id')
    ->where('penjualan.id_user', $user->id);

if ($startDate && $endDate) {
    $baseDetailQuery->whereBetween('penjualan.tanggal', [$startDate, $endDate]);
}

if ($jenisLeadId) {
    $baseDetailQuery->where('produk.jenis_lead_id', $jenisLeadId);
}

$totalHpp = (clone $baseDetailQuery)
    ->where('penjualan.total_hpp', '!=', 0)
    ->sum(DB::raw('produk.hpp * detail_penjualan.jumlah'));


$detailOmsetItems = (clone $baseDetailQuery)
    ->whereNotNull('detail_penjualan.hasil_pembagian_omset')
    ->select('detail_penjualan.hasil_pembagian_omset', 'detail_penjualan.hasil_pembagian_ongkir')
    ->get();

$totalOmset = (double)$detailOmsetItems->sum(function($item) {
    return $item->hasil_pembagian_omset + ($item->hasil_pembagian_ongkir ?? 0);
});

$totalBiayaCod = (clone $baseDetailQuery)
    ->whereNotNull('detail_penjualan.hasil_pembagian_biayacod')
    ->sum('detail_penjualan.hasil_pembagian_biayacod');

$totalOngkir = (clone $baseDetailQuery)
    ->whereNotNull('detail_penjualan.hasil_pembagian_ongkir')
    ->sum('detail_penjualan.hasil_pembagian_ongkir');

$totalCashback = (clone $baseDetailQuery)
    ->whereNotNull('detail_penjualan.hasil_pembagian_cashback')
    ->sum('detail_penjualan.hasil_pembagian_cashback');

// === END GANTIAN

// Ambil total iklan berdasarkan relasi user -> kampanye -> iklan
$totalIklan = \App\Models\Iklan::whereHas('kampanye', function ($q) use ($user) {
    $q->where('user_id', $user->id);
});

if ($startDate && $endDate) {
    $totalIklan->whereBetween('awal_pelaporan', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
}

if ($jenisLeadId) {
    $totalIklan->where('jenis_lead_id', $jenisLeadId); // 🔧 modified: filter jenis_lead
}

$totalIklan = $totalIklan->sum('jumlah_dibelanjakan');



$profit = $totalOmset - $totalIklan - $totalHpp - $totalBiayaCod - $totalOngkir;


$costclosing = $penjualanCount > 0 ? $totalIklan / $penjualanCount : 0;
$costlead = $leadCount > 0 ? $totalIklan / $leadCount : 0;


        return [
            'nama' => $user->name,
            'lead' => $leadCount,
            'penjualan' => $penjualanCount,
            'persentase' => $persentase,
            'total_hpp' => $totalHpp,
            'total_omset' => $totalOmset,
            'total_biaya_cod' => $totalBiayaCod,
'total_ongkir' => $totalOngkir,
'total_cashback' => $totalCashback,
'total_iklan' => $totalIklan,
'profit' => $profit,
'costclosing'=> $costclosing,
'costlead'=> $costlead,
        ];
    });

//diagram
$lineMetrics = [
    'total_bayar' => [],
    'total_hpp' => [],
    'ongkir' => [],
    'biaya_cod' => [],
    'cashback' => [],
    'profit' => [], // ✅ Tambahkan ini
];

$lineLabels = [];

$bulanInput = request('bulan');

if ($bulanInput) {
    try {
        $selectedDate = Carbon::createFromFormat('Y-m', $bulanInput);
    } catch (\Exception $e) {
        $selectedDate = now();
    }
} else {
    $selectedDate = now();
}

$monthlyPeriod = \Carbon\CarbonPeriod::create(
    $selectedDate->copy()->startOfMonth(),
    $selectedDate->copy()->endOfMonth()
);



foreach ($monthlyPeriod as $date) {
    $dateStr = $date->format('Y-m-d');
    $lineLabels[] = $date->format('d M');

    $total_bayar = (double) Penjualan::whereDate('tanggal', $dateStr)->sum('total_bayar');
    $total_hpp = (double) Penjualan::whereDate('tanggal', $dateStr)->sum('total_hpp');
    $ongkir = (double) Penjualan::whereDate('tanggal', $dateStr)->sum('ongkir');
    $biaya_cod = (double) Penjualan::whereDate('tanggal', $dateStr)->sum('biaya_cod');
    $cashback = (double) Penjualan::whereDate('tanggal', $dateStr)->sum('cashback');
    $iklan = (double) \App\Models\Iklan::whereDate('awal_pelaporan', $dateStr)->sum('jumlah_dibelanjakan');

    // Hitung profit: omset - hpp - iklan - ongkir - biaya cod
    $profit2 = $total_bayar - $total_hpp - $iklan - $ongkir - $biaya_cod;

    // Isi array
    $lineMetrics['total_bayar'][] = $total_bayar;
    $lineMetrics['total_hpp'][] = $total_hpp;
    $lineMetrics['ongkir'][] = $ongkir;
    $lineMetrics['biaya_cod'][] = $biaya_cod;
    $lineMetrics['cashback'][] = $cashback;
    $lineMetrics['iklan'][] = $iklan;
    $lineMetrics['profit'][] = $profit2;
}






    // === TABEL 2: DETAIL HARIAN BERDASARKAN filter_tanggal ===
    $filterTanggal = $request->input('filter_tanggal');
    if (!$filterTanggal) {
        $filterTanggal = Carbon::today()->format('Y-m-d') . ' - ' . Carbon::today()->format('Y-m-d');
    }

    [$startFilter, $endFilter] = explode(' - ', $filterTanggal);
    $startFilterDate = Carbon::createFromFormat('Y-m-d', trim($startFilter))->startOfDay();
    $endFilterDate = Carbon::createFromFormat('Y-m-d', trim($endFilter))->endOfDay();

    $dailyData = [];
    $period = Carbon::parse($startFilterDate)->toPeriod($endFilterDate);

    foreach ($period as $date) {
        $dateStr = $date->format('Y-m-d');

        $dataPerHari = User::where('role', 'customerservice')->get()->map(function ($user) use ($dateStr) {
         $leadCount = Lead::where('user_id', $user->id)
                ->whereDate('tanggal', $dateStr)
                ->sum('jumlah_lead');


            $penjualanCount = Penjualan::where('id_user', $user->id)
                ->whereDate('tanggal', $dateStr)
                ->count();

            $totalHpp = Penjualan::where('id_user', $user->id)
                ->whereDate('tanggal', $dateStr)
                ->sum('total_hpp');

            $totalOmset = Penjualan::where('id_user', $user->id)
                ->whereDate('tanggal', $dateStr)
                ->sum('total_bayar');

            $persentase = $leadCount > 0 ? round(($penjualanCount / $leadCount) * 100, 2) : 0;

$totalBiayaCod = Penjualan::where('id_user', $user->id)
    ->whereDate('tanggal', $dateStr)
    ->sum('biaya_cod');

$totalOngkir = Penjualan::where('id_user', $user->id)
    ->whereDate('tanggal', $dateStr)
    ->sum('ongkir');

$totalCashback = Penjualan::where('id_user', $user->id)
    ->whereDate('tanggal', $dateStr)
    ->sum('cashback');

// Iklan per hari
$totalIklan = \App\Models\Iklan::whereHas('kampanye', function ($q) use ($user) {
    $q->where('user_id', $user->id);
})->whereDate('awal_pelaporan', $dateStr)->sum('jumlah_dibelanjakan');



$profit = $totalOmset - $totalIklan - $totalHpp - $totalBiayaCod - $totalOngkir ;

$costclosing = $penjualanCount > 0 ? $totalIklan / $penjualanCount : 0;
$costlead = $leadCount > 0 ? $totalIklan / $leadCount : 0;
            return [
                'nama' => $user->name,
                'lead' => $leadCount,
                'penjualan' => $penjualanCount,
                'persentase' => $persentase,
                'total_hpp' => $totalHpp,
                'total_omset' => $totalOmset,
                'total_biaya_cod' => $totalBiayaCod,
'total_ongkir' => $totalOngkir,
'total_cashback' => $totalCashback,
'total_iklan' => $totalIklan,
'profit' => $profit,
'costclosing'=> $costclosing,
'costlead'=> $costlead,
            ];
        });

        $dailyData[$dateStr] = $dataPerHari;
    }

    return view('dashboard.admin', compact(
        'jumlahPenjualan',
        'totalLead',
        'totalHpp',
        'totalOmset',
        'totalBiayaCod',
        'totalOngkir',
        'totalCashback',
        'totalDibelanjakan',
        'profit',
        'profit2',
        'dataCSAllTime',
        'dailyData',
        'filterTanggal',
      'lineLabels',
'lineMetrics',
 'cpr',
    'roi',
    'roas'
    ));
}



    public function indexcustomerservice(Request $request)
{
    $user = auth()->user(); // Ambil user yang sedang login

    $queryPenjualan = Penjualan::where('id_user', $user->id);
    $queryLead = Lead::where('user_id', $user->id); // Sesuaikan jika perlu

   // Default: bulan ini
    if ($request->filled('daterange')) {
        [$start, $end] = explode(' - ', $request->daterange);
        try {
            $startDate = Carbon::createFromFormat('Y-m-d', trim($start))->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', trim($end))->endOfDay();
        } catch (\Exception $e) {
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
        }
    } else {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
    }

    // Terapkan filter tanggal
    $queryPenjualan->whereBetween('tanggal', [$startDate, $endDate]);
    $queryLead->whereBetween('tanggal', [$startDate, $endDate]);

    $jumlahPenjualan = $queryPenjualan->count();
    $totalLead = $queryLead->sum('jumlah_lead');
    $totalHpp = $queryPenjualan->sum('total_hpp');
    $totalOmset = $queryPenjualan->sum('total_bayar');
    $totalBiayaCod = $queryPenjualan->sum('biaya_cod');
    $totalOngkir = $queryPenjualan->sum('ongkir');
    $totalCashback = $queryPenjualan->sum('cashback');

    // Ambil total jumlah dibelanjakan iklan berdasarkan user login
    $totalDibelanjakan = \App\Models\Iklan::when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
        return $query->whereBetween('awal_pelaporan', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
    })->whereHas('kampanye', function ($query) use ($user) {
        $query->where('user_id', $user->id); // Pastikan ada relasi user_id di kode_kampanye_cs
    })->sum('jumlah_dibelanjakan');

    $rataDibelanjakanPerPenjualan = $jumlahPenjualan > 0 ? ($totalDibelanjakan / $jumlahPenjualan) : 0;

    $profit = $totalOmset - $totalDibelanjakan - $totalHpp - $totalBiayaCod - $totalOngkir ;
$daterange = $startDate->format('Y-m-d') . ' - ' . $endDate->format('Y-m-d');

    return view('dashboard.customerservice', compact(
        'jumlahPenjualan',
        'totalLead',
        'totalHpp',
        'totalOmset',
        'totalBiayaCod',
        'totalOngkir',
        'totalCashback',
        'totalDibelanjakan',
        'profit',
        'startDate',
        'endDate',
        'daterange'
    ));
}


public function indexeditor(Request $request)
{
   $tanggalDipilih = $request->input('tanggal') 
    ? Carbon::parse($request->input('tanggal')) 
    : Carbon::today();

$jamBatas = $tanggalDipilih->copy()->setTime(11, 0, 0);

// Sebelum jam 11
$produkSebelum11 = DB::table('penjualan')
    ->join('detail_penjualan', 'penjualan.id', '=', 'detail_penjualan.id_penjualan')
    ->join('produk', 'detail_penjualan.id_produk', '=', 'produk.id')
    ->join('jenis_produk', 'produk.jenis_produk_id', '=', 'jenis_produk.id')
    ->whereDate('penjualan.tanggal', $tanggalDipilih)
    ->whereTime('penjualan.tanggal', '<', $jamBatas->toTimeString())
    ->select('jenis_produk.kategori', DB::raw('SUM(detail_penjualan.jumlah) as total'))
    ->groupBy('jenis_produk.kategori')
    ->get();

// Setelah jam 11
$produkSetelah11 = DB::table('penjualan')
    ->join('detail_penjualan', 'penjualan.id', '=', 'detail_penjualan.id_penjualan')
    ->join('produk', 'detail_penjualan.id_produk', '=', 'produk.id')
    ->join('jenis_produk', 'produk.jenis_produk_id', '=', 'jenis_produk.id')
    ->whereDate('penjualan.tanggal', $tanggalDipilih)
    ->whereTime('penjualan.tanggal', '>=', $jamBatas->toTimeString())
    ->select('jenis_produk.kategori', DB::raw('SUM(detail_penjualan.jumlah) as total'))
    ->groupBy('jenis_produk.kategori')
    ->get();

   $userId = auth()->id(); // Langsung ID-nya saja

   
    $tanggal = $request->input('tanggal', Carbon::today()->toDateString());

    // Konversi ke format awal dan akhir hari
    $startDate = Carbon::parse($tanggal)->startOfDay();
    $endDate = Carbon::parse($tanggal)->endOfDay();
// Produk yang dikerjakan
    $donutData = DB::table('jobdesk_editor')
        ->join('penjualan', 'jobdesk_editor.penjualan_id', '=', 'penjualan.id')
        ->join('detail_penjualan', 'penjualan.id', '=', 'detail_penjualan.id_penjualan')
        ->join('produk', 'detail_penjualan.id_produk', '=', 'produk.id')
        ->join('jenis_produk', 'produk.jenis_produk_id', '=', 'jenis_produk.id')
        ->where('jobdesk_editor.user_id', $userId)
        ->whereBetween('jobdesk_editor.created_at', [$startDate, $endDate])
        ->select('jenis_produk.kategori', DB::raw('SUM(detail_penjualan.jumlah) as total'))
        ->groupBy('jenis_produk.kategori')
        ->get();

    // Statistik jobdesk
    $donutStatusData = DB::table('jobdesk_editor')
        ->where('user_id', $userId)
        ->whereBetween('created_at', [$startDate, $endDate])
        ->select('status', DB::raw('COUNT(*) as total'))
        ->groupBy('status')
        ->get();

    return view('dashboard.editor', compact('produkSebelum11', 'produkSetelah11','donutData','donutStatusData'));
}

}
