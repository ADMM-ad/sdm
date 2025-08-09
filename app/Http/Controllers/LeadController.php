<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead;
use Illuminate\Support\Facades\Auth;
use App\Models\Penjualan;
use App\Models\User;
use App\Models\JenisLead;
use Carbon\Carbon;
use Spatie\SimpleExcel\SimpleExcelReader;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Illuminate\Support\Facades\DB;
class LeadController extends Controller
{
    


public function index(Request $request)
{
    $userId = Auth::id();
    $query = Lead::with('jenisLead')
                ->where('user_id', $userId);

    if ($request->filled('daterange')) {
        $range = explode(' - ', $request->daterange);

        try {
            $start = Carbon::parse(trim($range[0]))->startOfDay();
            $end = Carbon::parse(trim($range[1]))->endOfDay();
            $query->whereBetween('tanggal', [$start, $end]);
        } catch (\Exception $e) {
            // abaikan jika format salah
        }
    }

     if ($request->filled('jenis_lead')) {
        $query->where('jenis_lead_id', $request->jenis_lead);
    }

    $lead = $query->orderBy('tanggal', 'desc')->paginate(100)->withQueryString();

    $lead->getCollection()->transform(function ($item) use ($userId) {
        $jumlahPenjualan = Penjualan::where('penjualan.id_user', $userId)
            ->whereDate('penjualan.tanggal', $item->tanggal)
            ->whereHas('detailPenjualan.produk', function ($q) use ($item) {
                $q->where('produk.jenis_lead_id', $item->jenis_lead_id);
            })
            ->count();

        $item->jumlah_penjualan = $jumlahPenjualan;
        $item->persentase = $item->jumlah_lead > 0
            ? round(($jumlahPenjualan / $item->jumlah_lead) * 100, 2)
            : 0;

        return $item;
    });
$semuaJenisLead = JenisLead::orderBy('jenis')->get();
    return view('lead.index', compact('lead', 'semuaJenisLead'));
}




public function create()
{
    $jenis_lead = JenisLead::all();
    return view('lead.create', compact('jenis_lead'));
}


   public function store(Request $request)
{
    $request->validate([
        'jumlah_lead' => 'required|integer|min:1',
        'tanggal' => 'required|date',
        'jenis_lead_id' => 'required|exists:jenis_lead,id',
    ]);

    $userId = Auth::id();
    $tanggal = $request->tanggal;
    $jenisLeadId = $request->jenis_lead_id;

    $cekDuplikat = Lead::where('user_id', $userId)
                        ->where('tanggal', $tanggal)
                        ->where('jenis_lead_id', $jenisLeadId)
                        ->first();

    if ($cekDuplikat) {
        return redirect()->back()->with('error', 'Data lead untuk tanggal dan jenis lead tersebut sudah ada!');
    }

    Lead::create([
        'user_id' => $userId,
        'jumlah_lead' => $request->jumlah_lead,
        'tanggal' => $tanggal,
        'jenis_lead_id' => $jenisLeadId,
    ]);

    return redirect()->back()->with('success', 'Data lead berhasil ditambahkan!');
}


public function edit($id)
{
    $lead = Lead::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

    $jenisLeadList = JenisLead::all(); // Ambil daftar jenis lead

    return view('lead.edit', compact('lead', 'jenisLeadList'));
}

public function update(Request $request, $id)
{
    $request->validate([
        'jumlah_lead' => 'required|integer|min:1',
        'tanggal' => 'required|date',
        'jenis_lead_id' => 'required|exists:jenis_lead,id',
    ]);

    $lead = Lead::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

    // Cek duplikat tanggal & jenis_lead_id (selain data ini sendiri)
    $duplikat = Lead::where('user_id', Auth::id())
                    ->where('tanggal', $request->tanggal)
                    ->where('jenis_lead_id', $request->jenis_lead_id)
                    ->where('id', '!=', $id)
                    ->exists();

    if ($duplikat) {
        return redirect()->back()->with('error', 'Data dengan tanggal dan jenis lead tersebut sudah ada!');
    }

    $lead->update([
        'jumlah_lead' => $request->jumlah_lead,
        'tanggal' => $request->tanggal,
        'jenis_lead_id' => $request->jenis_lead_id,
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

public function laporan(Request $request)
{
    $query = Lead::with(['user', 'jenisLead'])->orderBy('tanggal', 'desc');

    // Filter berdasarkan user/CS
    if ($request->filled('cs')) {
        $query->where('user_id', $request->cs);
    }

    // Filter berdasarkan jenis lead
    if ($request->filled('jenis_lead')) {
        $query->where('jenis_lead_id', $request->jenis_lead);
    }

    // Filter berdasarkan rentang tanggal
   if ($request->filled('daterange')) {
    try {
        [$start, $end] = explode(' - ', $request->daterange);
        $startDate = \Carbon\Carbon::createFromFormat('Y-m-d', trim($start))->startOfDay();
        $endDate = \Carbon\Carbon::createFromFormat('Y-m-d', trim($end))->endOfDay();
        $query->whereBetween('tanggal', [$startDate, $endDate]);
    } catch (\Exception $e) {
        // Abaikan jika format salah
    }
}


    $semuaLead = $query->paginate(1000);

    $semuaLead->getCollection()->transform(function ($lead) {
        if (!empty($lead->user_id) && !empty($lead->tanggal) && !empty($lead->jenis_lead_id)) {
            $penjualanIds = Penjualan::where('id_user', $lead->user_id)
                ->whereDate('tanggal', $lead->tanggal)
                ->pluck('id');

            $jumlahPenjualan = \DB::table('detail_penjualan')
                ->join('produk', 'detail_penjualan.id_produk', '=', 'produk.id')
                ->whereIn('detail_penjualan.id_penjualan', $penjualanIds)
                ->where('produk.jenis_lead_id', $lead->jenis_lead_id)
                ->distinct('detail_penjualan.id_penjualan')
                ->count('detail_penjualan.id_penjualan');

            $lead->jumlah_penjualan = $jumlahPenjualan;
        } else {
            $lead->jumlah_penjualan = 0;
        }

        $lead->persentase = $lead->jumlah_lead > 0
            ? round(($lead->jumlah_penjualan / $lead->jumlah_lead) * 100, 2)
            : 0;

        return $lead;
    });

    // Semua user untuk filter dropdown
    $semuaUser = User::where('role', 'customerservice')->orderBy('name')->get();
  $semuaJenisLead = JenisLead::orderBy('jenis')->get();

    return view('lead.laporan', compact('semuaLead', 'semuaUser', 'semuaJenisLead'));
}

public function importForm()
{
    return view('lead.import');
}
public function import(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:xlsx,xls',
    ]);

    try {
        $path = $request->file('file')->store('temp');
        $fullPath = storage_path('app/' . $path);

        $rows = SimpleExcelReader::create($fullPath)->getRows();

        DB::beginTransaction();

        foreach ($rows as $row) {
            $namaCs = trim($row['Nama CS'] ?? '');
            $tanggalRaw = $row['Tanggal'] ?? '';
            $tanggal = $tanggalRaw instanceof \DateTimeInterface
                ? $tanggalRaw->format('Y-m-d')
                : trim((string) $tanggalRaw);
            $jumlahLead = (int) trim($row['Jumlah Lead'] ?? 0);
            $jenisLeadNama = trim($row['Jenis Lead'] ?? '');

            if (!$namaCs || !$tanggal || !$jumlahLead || !$jenisLeadNama) {
                continue; // Skip jika ada kolom kosong
            }

            // Ambil ID user
            $userId = User::where('name', $namaCs)->value('id');
            if (!$userId) {
                $userId = 1; // fallback jika CS tidak ditemukan
            }

            // Ambil jenis lead
            $jenisLead = JenisLead::where('jenis', $jenisLeadNama)->first();
            if (!$jenisLead) {
                continue; // skip jika jenis lead tidak ditemukan
            }

            // Parse tanggal
            try {
                $tanggalFormat = Carbon::parse($tanggal)->format('Y-m-d');
            } catch (\Exception $e) {
                continue; // skip jika tanggal tidak valid
            }

            // Cek apakah data sudah ada
            $existing = DB::table('lead')
                ->where('user_id', $userId)
                ->where('tanggal', $tanggalFormat)
                ->where('jenis_lead_id', $jenisLead->id)
                ->first();

            if ($existing) {
                // Update jika sudah ada
                DB::table('lead')
                    ->where('id', $existing->id)
                    ->update([
                        'jumlah_lead' => $jumlahLead,
                        'updated_at' => now(),
                    ]);
            } else {
                // Insert jika belum ada
                DB::table('lead')->insert([
                    'user_id' => $userId,
                    'tanggal' => $tanggalFormat,
                    'jumlah_lead' => $jumlahLead,
                    'jenis_lead_id' => $jenisLead->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        DB::commit();
        return back()->with('success', 'Import lead berhasil!');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
    }
}


public function downloadTemplate(): StreamedResponse
{
    $fileName = 'template_import_lead.xlsx';

    return SimpleExcelWriter::streamDownload($fileName)
        ->addHeader([
            'Nama CS',
            'Tanggal',
            'Jumlah Lead',
            'Jenis Lead',
        ]);
}


public function export(Request $request)
{
    $query = Lead::with(['user', 'jenisLead']);

    if ($request->filled('cs')) {
        $query->where('user_id', $request->cs);
    }

    if ($request->filled('jenis_lead')) {
        $query->where('jenis_lead_id', $request->jenis_lead);
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

    $data = $query->get();

    $filePath = storage_path('lead_export.xlsx');

    $writer = SimpleExcelWriter::create($filePath)->addHeader([
        'Tanggal',
        'Nama CS',
        'Jumlah Lead',
       
       
        'Jenis Lead',
    ]);

    foreach ($data as $item) {
        

        $writer->addRow([
            Carbon::parse($item->tanggal)->format('d-m-Y'),
            $item->user->name ?? 'Tidak Diketahui',
            $item->jumlah_lead,
           
       
            $item->jenisLead->jenis ?? '-',
        ]);
    }

    return response()->download($filePath)->deleteFileAfterSend(true);
}

}
