<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Iklan;
use App\Models\Kampanye;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Spatie\SimpleExcel\SimpleExcelReader;
use Carbon\Carbon;

class IklanController extends Controller
{
    public function showImportForm()
{
    $iklans = Iklan::with('kampanye')->latest()->get();
    return view('iklan.import', compact('iklans'));
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

        foreach ($rows as $index => $row) {
            $awalPelaporan = Carbon::parse($row['Awal Pelaporan'])->format('Y-m-d');
            $namaKampanye = $row['Nama Kampanye'];
            $hasil = (int) $row['Hasil'];
            $jumlahDibelanjakan = (int) preg_replace('/[^\d]/', '', $row['Jumlah yang dibelanjakan (IDR)']);

            $kampanye = Kampanye::where('kode_kampanye', $namaKampanye)->first();
            if (!$kampanye) continue;

            $existing = Iklan::where('awal_pelaporan', $awalPelaporan)
                ->where('kode_kampanye_id', $kampanye->id)
                ->first();

            if ($existing) {
                $existing->update([
                    'hasil' => $hasil,
                    'jumlah_dibelanjakan' => $jumlahDibelanjakan,
                ]);
            } else {
                Iklan::create([
                    'awal_pelaporan' => $awalPelaporan,
                    'kode_kampanye_id' => $kampanye->id,
                    'hasil' => $hasil,
                    'jumlah_dibelanjakan' => $jumlahDibelanjakan,
                ]);
            }
        }

        try {
    unlink($filePath);
} catch (\Throwable $e) {
    // Abaikan error, jangan tampilkan
}

        return redirect()->back()->with('success', 'Data iklan berhasil diimpor.');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Gagal mengimpor data: ' . $e->getMessage());
    }
}
}
