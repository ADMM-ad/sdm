<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penjualan;
use App\Models\Lead;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
   public function indexadmin(Request $request)
{
    $queryPenjualan = Penjualan::query();
    $queryLead = Lead::query();

    // === FILTER UNTUK DASHBOARD DAN TABEL ALL TIME ===
    $startDate = null;
    $endDate = null;

    if ($request->filled('daterange')) {
        [$start, $end] = explode(' - ', $request->daterange);
        try {
            $startDate = Carbon::createFromFormat('Y-m-d', trim($start))->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', trim($end))->endOfDay();

            $queryPenjualan->whereBetween('tanggal', [$startDate, $endDate]);
            $queryLead->whereBetween('tanggal', [$startDate, $endDate]);
        } catch (\Exception $e) {
            // log jika diperlukan
        }
    }

    $jumlahPenjualan = $queryPenjualan->count();
    $totalLead = $queryLead->sum('jumlah_lead');
    $totalHpp = $queryPenjualan->sum('total_hpp');
    $totalOmset = $queryPenjualan->sum('total_bayar');
    $totalBiayaCod = $queryPenjualan->sum('biaya_cod');
    $totalOngkir = $queryPenjualan->sum('ongkir');
    $totalCashback = $queryPenjualan->sum('cashback');


    
// Ambil total jumlah dibelanjakan dari iklan
$totalDibelanjakan = \App\Models\Iklan::when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
    return $query->whereBetween('awal_pelaporan', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
})->sum('jumlah_dibelanjakan');

// Hindari pembagian dengan nol
$rataDibelanjakanPerPenjualan = $jumlahPenjualan > 0 ? ($totalDibelanjakan / $jumlahPenjualan) : 0;

// Hitung profit
$profit = $totalOmset - $rataDibelanjakanPerPenjualan - $totalHpp - $totalBiayaCod - $totalOngkir + $totalCashback;



    // === TABEL 1: DATA CS ALL TIME ATAU SESUAI FILTER ===
    $dataCSAllTime = User::where('role', 'customerservice')->get()->map(function ($user) use ($startDate, $endDate, $request) {
        $leadQuery = Lead::where('user_id', $user->id);
        $penjualanQuery = Penjualan::where('id_user', $user->id);

        if ($request->filled('daterange') && $startDate && $endDate) {
            $leadQuery->whereBetween('tanggal', [$startDate, $endDate]);
            $penjualanQuery->whereBetween('tanggal', [$startDate, $endDate]);
        }

        $leadCount = $leadQuery->sum('jumlah_lead');
        $penjualanCount = $penjualanQuery->count();
        $totalHpp = $penjualanQuery->sum('total_hpp');
        $totalOmset = $penjualanQuery->sum('total_bayar');
        $persentase = $leadCount > 0 ? round(($penjualanCount / $leadCount) * 100, 2) : 0;

$totalBiayaCod = $penjualanQuery->sum('biaya_cod');
$totalOngkir = $penjualanQuery->sum('ongkir');
$totalCashback = $penjualanQuery->sum('cashback');

// Ambil total iklan berdasarkan relasi user -> kampanye -> iklan
$totalIklan = \App\Models\Iklan::whereHas('kampanye', function ($q) use ($user) {
    $q->where('user_id', $user->id);
});
if ($startDate && $endDate) {
    $totalIklan->whereBetween('awal_pelaporan', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
}
$totalIklan = $totalIklan->sum('jumlah_dibelanjakan');

// Hitung profit masing-masing CS
$avgIklanPerPenjualan = $penjualanCount > 0 ? ($totalIklan / $penjualanCount) : 0;
$profit = $totalOmset - $avgIklanPerPenjualan - $totalHpp - $totalBiayaCod - $totalOngkir + $totalCashback;



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
        ];
    });

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

$avgIklanPerPenjualan = $penjualanCount > 0 ? ($totalIklan / $penjualanCount) : 0;

$profit = $totalOmset - $avgIklanPerPenjualan - $totalHpp - $totalBiayaCod - $totalOngkir + $totalCashback;


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
        'dataCSAllTime',
        'dailyData',
        'filterTanggal'
    ));
}



    public function indexcustomerservice(Request $request)
{
    $user = auth()->user(); // Ambil user yang sedang login

    $queryPenjualan = Penjualan::where('id_user', $user->id);
    $queryLead = Lead::where('user_id', $user->id); // Sesuaikan jika perlu

    $startDate = null;
    $endDate = null;

    if ($request->filled('daterange')) {
        [$start, $end] = explode(' - ', $request->daterange);
        try {
            $startDate = Carbon::createFromFormat('Y-m-d', trim($start))->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', trim($end))->endOfDay();

            $queryPenjualan->whereBetween('tanggal', [$startDate, $endDate]);
            $queryLead->whereBetween('tanggal', [$startDate, $endDate]);
        } catch (\Exception $e) {
            // log jika perlu
        }
    }

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

    $profit = $totalOmset - $rataDibelanjakanPerPenjualan - $totalHpp - $totalBiayaCod - $totalOngkir + $totalCashback;

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
        'endDate'
    ));
}


public function indexeditor()
{
    return view('dashboard.editor');
}

}
