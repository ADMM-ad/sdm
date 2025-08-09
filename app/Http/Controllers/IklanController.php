<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Iklan;
use App\Models\Kampanye;
use App\Models\JenisLead;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Spatie\SimpleExcel\SimpleExcelReader;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\Carbon;

class IklanController extends Controller
{
    public function showImportForm(Request $request)
{
    $query = Iklan::with('kampanye')->orderBy('awal_pelaporan', 'desc');

      // Filter berdasarkan rentang tanggal
   if ($request->filled('daterange')) {
    try {
        [$start, $end] = explode(' - ', $request->daterange);
        $startDate = \Carbon\Carbon::createFromFormat('Y-m-d', trim($start))->startOfDay();
        $endDate = \Carbon\Carbon::createFromFormat('Y-m-d', trim($end))->endOfDay();
        $query->whereBetween('awal_pelaporan', [$startDate, $endDate]);
    } catch (\Exception $e) {
        // Abaikan jika format salah
    }
}

    $iklans = $query->get();
    $jenisLeads = JenisLead::all();
return view('iklan.import', compact('iklans', 'jenisLeads'));

   
}
public function import(Request $request)
{
    $request->validate([
        'file' => 'required|file|mimes:xlsx,xls,csv',
    ]);

    try {
        $uploadedFile = $request->file('file');
        $extension = $uploadedFile->getClientOriginalExtension();
        $filePath = storage_path('app/temp_upload.' . $extension);

        $uploadedFile->move(storage_path('app'), 'temp_upload.' . $extension);

        $rows = iterator_to_array(SimpleExcelReader::create($filePath)->getRows());

        // Inisialisasi counter
        $totalRows = count($rows);
        $sukses = 0;
        $gagal = 0;

        // Proses data per chunk (20 per batch)
        collect($rows)->chunk(20)->each(function ($chunk) use (&$sukses, &$gagal) {
            foreach ($chunk as $row) {
                try {
                    $awalPelaporan = Carbon::parse($row['Awal Pelaporan'])->format('Y-m-d');

                    // Bersihkan nama kampanye dari spasi berlebihan
                    $namaKampanye = preg_replace('/\s+/', ' ', trim($row['Nama Kampanye']));

                    $hasil = (int) $row['Hasil'];
                    $jumlahDibelanjakan = (int) preg_replace('/[^\d]/', '', $row['Jumlah yang dibelanjakan (IDR)']);

                    // Cocokkan nama kampanye (tanpa spasi ganda, ignore case)
                    $kampanye = Kampanye::with('jenisLead')
    ->whereRaw('LOWER(TRIM(kode_kampanye)) = ?', [strtolower($namaKampanye)])
    ->first();


                    if (!$kampanye) {
                        $gagal++;
                        continue;
                    }

                    $existing = Iklan::where('awal_pelaporan', $awalPelaporan)
                        ->where('kode_kampanye_id', $kampanye->id)
                        ->first();

                    if ($existing) {
                        $existing->update([
                            'hasil' => $hasil,
                            'jumlah_dibelanjakan' => $jumlahDibelanjakan,
                            'jenis_lead_id' => $kampanye->jenis_lead_id, 

                        ]);
                    } else {
                        Iklan::create([
                            'awal_pelaporan' => $awalPelaporan,
                            'kode_kampanye_id' => $kampanye->id,
                            'hasil' => $hasil,
                            'jumlah_dibelanjakan' => $jumlahDibelanjakan,
                            'jenis_lead_id' => $kampanye->jenis_lead_id, 

                        ]);
                    }

                    $sukses++;
                } catch (\Throwable $e) {
                    $gagal++;
                    continue;
                }
            }
        });

        // Hapus file setelah selesai
        try {
            unlink($filePath);
        } catch (\Throwable $e) {
            // Abaikan error
        }

        return redirect()->back()->with('success', "Data iklan berhasil diimpor. {$sukses} berhasil, {$gagal} gagal dari total {$totalRows} baris.");
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Gagal mengimpor data: ' . $e->getMessage());
    }
}



public function bulkUpdateJenisLead(Request $request)
{
    $request->validate([
        'selected_ids' => 'required|string',
        'jenis_lead_id' => 'required|exists:jenis_lead,id',
    ]);

    $ids = explode(',', $request->selected_ids);

    Iklan::whereIn('id', $ids)->update([
        'jenis_lead_id' => $request->jenis_lead_id,
    ]);

    return redirect()->back()->with('success', 'Jenis lead berhasil diperbarui pada iklan yang dipilih.');
}


public function downloadTemplate(): StreamedResponse
{
    $fileName = 'template_import_iklan.xlsx';

    return SimpleExcelWriter::streamDownload($fileName)
        ->addHeader([
            'Awal Pelaporan',
            'Nama Kampanye',
            'Hasil',
            'Jumlah yang dibelanjakan (IDR)',
        ]);
}


public function create()
{
    $jenisLeads = JenisLead::all();
    $kampanyes = Kampanye::all(); // modelnya sesuai yang kamu buat
    return view('iklan.createiklan', compact('jenisLeads', 'kampanyes'));
}

public function store(Request $request)
{
    $request->validate([
        'awal_pelaporan' => 'required|date',
        'kode_kampanye_id' => 'required|exists:kode_kampanye_cs,id',
        'jenis_lead_id' => 'nullable|exists:jenis_lead,id',
        'hasil' => 'required|integer',
        'jumlah_dibelanjakan' => 'required|integer',
    ]);

    Iklan::create([
        'awal_pelaporan' => $request->awal_pelaporan,
        'kode_kampanye_id' => $request->kode_kampanye_id,
        'jenis_lead_id' => $request->jenis_lead_id,
        'hasil' => $request->hasil,
        'jumlah_dibelanjakan' => $request->jumlah_dibelanjakan,
    ]);

    return redirect()->route('iklan.import')->with('success', 'Iklan berhasil ditambahkan.');
}


public function edit($id)
{
    $iklan = Iklan::findOrFail($id);
    $jenisLeads = JenisLead::all();
    $kampanyes = Kampanye::all();

    return view('iklan.editiklan', compact('iklan', 'jenisLeads', 'kampanyes'));
}


public function update(Request $request, $id)
{
    $request->validate([
        'awal_pelaporan' => 'required|date',
        'kode_kampanye_id' => 'required|exists:kode_kampanye_cs,id',
        'hasil' => 'required|integer',
        'jumlah_dibelanjakan' => 'required|integer',
        'jenis_lead_id' => 'nullable|exists:jenis_lead,id',
    ]);

    $iklan = Iklan::findOrFail($id);
    $iklan->update($request->only([
        'awal_pelaporan',
        'kode_kampanye_id',
        'hasil',
        'jumlah_dibelanjakan',
        'jenis_lead_id',
    ]));

    return redirect()->route('iklan.import')->with('success', 'Data iklan berhasil diperbarui.');
}

public function destroy($id)
{
    $iklan = Iklan::findOrFail($id);
    $iklan->delete();

    return redirect()->route('iklan.import', request()->query())
                     ->with('success', 'Data iklan berhasil dihapus.');
}


}
