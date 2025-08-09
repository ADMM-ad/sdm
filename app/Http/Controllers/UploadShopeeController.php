<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use App\Models\Produk;
use App\Models\User;
use Spatie\SimpleExcel\SimpleExcelReader;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Spatie\SimpleExcel\SimpleExcelWriter;

class UploadShopeeController extends Controller
{
    public function form()
    {
        return view('upload-shopee');
    }

public function import(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:csv,xlsx,txt'
    ]);

    $path = $request->file('file')->store('temp');

    $penjualanCache = [];

    $rows = SimpleExcelReader::create(storage_path("app/{$path}"))
        ->useDelimiter(',')
        ->getRows()
        ->toArray();

    $groupedRows = collect($rows)->groupBy(function ($item) {
    return trim($item['No. Pesanan']);
});


    $groupedRows->each(function ($groupedItems, $orderId) use (&$penjualanCache) {
        try {
            $firstRow = $groupedItems[0];

$orderId = trim($firstRow['No. Pesanan'] ?? '');


            // Hitung total harga produk untuk semua baris dengan Order ID yang sama
            $totalHargaProduk = collect($groupedItems)->sum(function ($item) {
                return (int) str_replace('.', '', $item['Total Harga Produk'] ?? 0);
            });

            $ongkirRaw = (int) str_replace('.', '', $firstRow['Perkiraan Ongkos Kirim'] ?? 0);
            $potonganPengiriman = (int) str_replace('.', '', $firstRow['Estimasi Potongan Biaya Pengiriman'] ?? 0);
            $voucherDitanggung = (int) str_replace('.', '', $firstRow['Voucher Ditanggung Penjual'] ?? 0);
            $ongkir = $ongkirRaw - $potonganPengiriman;
            $totalBayar = $totalHargaProduk + $ongkir - $voucherDitanggung;

// Cek status pesanan
            $statusPesanan = $firstRow['Status Pesanan'] ?? '';
            $statusInvalid = ['rts', 'batal', 'undelivered', 'delivery return', 'deliveri return'];
            $normalizedStatus = strtolower(trim($statusPesanan));
            $setTotalBayarToZero = in_array($normalizedStatus, $statusInvalid);

            $finalTotalBayar = $setTotalBayarToZero ? 0 : $totalBayar;


            $user = User::where('kode_voucher', $voucherDitanggung)->first();
            $idUser = $user ? $user->id : 1;

            $namaPembeli = $firstRow['Username (Pembeli)'] ?? '-';

            // Jika penjualan sudah diproses
            if (isset($penjualanCache[$orderId])) {
                $penjualan = $penjualanCache[$orderId];
            } else {
                $penjualan = Penjualan::where('order_id', $orderId)->first();

                $dataPenjualan = [
                    'id_user'           => $idUser,
                    'tanggal'           => $this->parseDate($firstRow['Waktu Pesanan Dibuat'] ?? now()),
                    'order_id'          => $orderId,
                    'status_pesanan'    => $statusPesanan,
                    'alasan_batal'      => $firstRow['Alasan Pembatalan'] ?? '',
                    'status_pembatalan' => $firstRow['Status Pembatalan/ Pengembalian'] ?? '',
                    'no_resi'           => $firstRow['No. Resi'] ?? '',
                    'metode_pengiriman' => 'SHOPEE',
                    'kurir'             => $firstRow['Opsi Pengiriman'] ?? '',
                    'metode_pembayaran' => $firstRow['Metode Pembayaran'] ?? '',
                    'total_bayar'       => $finalTotalBayar,
                    'detail'            => $firstRow['Catatan dari Pembeli'] ?? '',
                    'nama_pembeli'      => $namaPembeli,
                    'no_hp'             => $firstRow['No. Telepon'] ?? '0',
                    'alamat'            => $firstRow['Alamat Pengiriman'] ?? '',
                    'provinsi'          => $firstRow['Provinsi'] ?? '',
                    'kota'              => $firstRow['Kota/Kabupaten'] ?? '',
                    'kecamatan'         => '-',
                    'kodepos'           => '-',
                    'wilayah'           => '-',
                    'ongkir'            => $ongkir,
                    'catatan_penjual'   => $firstRow['Catatan'] ?? '',
                ];

                if (!$penjualan) {
                    $penjualan = Penjualan::create($dataPenjualan);
                    \Log::info('Tambah penjualan baru order ID: ' . $orderId);
                } else {
                    $penjualan->update($dataPenjualan);
                    \Log::info("Update semua data penjualan untuk order ID: {$orderId}");
                }

                $penjualanCache[$orderId] = $penjualan;
            }

            $totalHpp = 0;
if ($penjualan->kunci_hpp !== 'ya') {
    // 1. Gabungkan item berdasarkan SKU Induk
    $groupedBySku = [];

    foreach ($groupedItems as $row) {
        $skuInduk = trim($this->getValue($row, 'SKU Induk'));
        $jumlah = (int) $this->getValue($row, 'Jumlah');

        // Jika sudah ada, jumlahkan 'Jumlah'-nya
        if (isset($groupedBySku[$skuInduk])) {
            $groupedBySku[$skuInduk]['jumlah'] += $jumlah;
        } else {
            // Simpan data pertama kali
            $groupedBySku[$skuInduk] = [
                'row' => $row,        // Simpan salah satu baris untuk ambil data lainnya
                'jumlah' => $jumlah, // Total jumlah awal
            ];
        }
    }

    // 2. Proses per SKU Induk
    foreach ($groupedBySku as $skuInduk => $data) {
        $row = $data['row'];
        $jumlah = $data['jumlah']; // jumlah sudah digabungkan dari semua baris

        $produk = Produk::where('nama_produk', $skuInduk)->first();

        if (!$produk) {
            \Log::warning("Produk tidak ditemukan untuk SKU Induk: [{$skuInduk}] pada Order ID: {$orderId}");
            continue;
        }
                $detail = DetailPenjualan::where('id_penjualan', $penjualan->id)
                    ->where('id_produk', $produk->id)
                    ->first();

                $harga = (int) str_replace('.', '', $this->getValue($row, 'Total Harga Produk'));
                $variasi = $this->getValue($row, 'Nama Variasi');

$totalHpp += $produk->hpp * $jumlah;

                if ($detail) {
                    $detail->update([
                        'jumlah' => $jumlah,
                        'total_harga' => $harga,
                        'nama_variasi' => $variasi,
                    ]);
                    \Log::info("Update detail penjualan untuk produk: {$produk->nama_produk} pada order ID: {$orderId}");
                } else {
                    DetailPenjualan::create([
                        'id_penjualan' => $penjualan->id,
                        'id_produk'    => $produk->id,
                        'jumlah'       => $jumlah,
                        'total_harga'  => $harga,
                        'nama_variasi' => $variasi,
                    ]);
                    \Log::info("Tambah detail penjualan baru untuk produk: {$produk->nama_produk} pada order ID: {$orderId}");
                }
            }
$penjualan->update([
'total_hpp' => (int) round($totalHpp),
'kunci_hpp' => $penjualan->kunci_hpp ?? 'tidak', 
]);}

$totalHargaProduk = DetailPenjualan::where('id_penjualan', $penjualan->id)->sum('total_harga');

if ($totalHargaProduk > 0) {
     $selisih = $penjualan->total_bayar - $penjualan->ongkir - $totalHargaProduk;
        $pembagianOmset = $selisih / $totalHargaProduk;
    $pembagianOngkir     = $penjualan->ongkir / $totalHargaProduk;
    $pembagianBiayaCOD   = $penjualan->biaya_cod / $totalHargaProduk;
    $pembagianCashback   = $penjualan->cashback / $totalHargaProduk;

    $details = DetailPenjualan::where('id_penjualan', $penjualan->id)->get();

    foreach ($details as $detail) {
          $hasil = $detail->total_harga * $pembagianOmset;
                    $shasil_pembagian_omset = $hasil + $detail->total_harga;
                    $detail->hasil_pembagian_omset = round($shasil_pembagian_omset);
        $detail->hasil_pembagian_ongkir     = round($detail->total_harga * $pembagianOngkir);
        $detail->hasil_pembagian_biayacod   = round($detail->total_harga * $pembagianBiayaCOD);
        $detail->hasil_pembagian_cashback   = round($detail->total_harga * $pembagianCashback);
        $detail->save();
    }
}
        } catch (\Exception $e) {
            \Log::error('Gagal simpan order ID: ' . $orderId . ' Error: ' . $e->getMessage());
        }
    });

    return back()->with('success', 'Data berhasil diimpor!');
}



    private function parseDate($value)
    {
        if (!$value || $value === '-') return now()->format('Y-m-d H:i:s');
    return \Carbon\Carbon::parse($value)->format('Y-m-d H:i:s');
    }

    private function normalizeKey($key)
    {
        return trim(str_replace("\u{00A0}", '', $key)); // hapus spasi tak terlihat
    }
private function getValue(array $row, string $expectedKey)
{
    foreach ($row as $key => $value) {
        if (strcasecmp($this->normalizeKey($key), $this->normalizeKey($expectedKey)) === 0) {
            return trim($value);
        }
    }

    \Log::warning("Kolom '{$expectedKey}' tidak ditemukan di row: " . json_encode($row));
    return null;
}

    private function convertToInt($value)
    {
        if (!$value || $value === '-') return 0;
        return (int) str_replace(['.', ','], '', preg_replace('/[^\d]/', '', $value));
    }

public function adminshopee(Request $request)
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

        $totalRows = count($rows);
        $sukses = 0;
        $gagal = 0;

        collect($rows)->chunk(100)->each(function ($chunk) use (&$sukses, &$gagal) {
            foreach ($chunk as $row) {
                try {
                    $orderId = trim($row['No. Pesanan']);

                    // Cari data penjualan berdasarkan order_id
                    $penjualan = Penjualan::where('order_id', $orderId)->first();

                    if (!$penjualan) {
                        $gagal++;
                        continue;
                    }

                    // Ambil dan konversi nilai biaya (bersihkan karakter non-digit)
                    $getValue = fn($field) => (int) preg_replace('/[^\d]/', '', $row[$field] ?? 0);

                    $biayaCod = 
                        $getValue('Ongkos Kirim Pengembalian Barang') +
                        $getValue('Kembali ke Biaya Pengiriman Pengirim') +
                        $getValue('Pengembalian Biaya Kirim') +
                        $getValue('Biaya Komisi AMS') +
                        $getValue('Biaya Administrasi') +
                        $getValue('Biaya Layanan') +
                        $getValue('Biaya Proses Pesanan') +
                        $getValue('Premi') +
                        $getValue('Biaya Program') +
                        $getValue('Biaya Transaksi') +
                        $getValue('Biaya Kampanye') +
                        $getValue('Bea Masuk, PPN & PPh');

                    $penjualan->update([
                        'biaya_cod' => $biayaCod
                    ]);

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

        return redirect()->back()->with('success', "Import selesai. Berhasil: {$sukses}, Gagal: {$gagal}, Total: {$totalRows}.");
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Gagal mengimpor data: ' . $e->getMessage());
    }
}

public function downloadTemplateImportAdmin(): StreamedResponse
{
    $fileName = 'template_import_baiyashopee.xlsx';

    return SimpleExcelWriter::streamDownload($fileName)
        ->addHeader([
            'No.',
            'No. Pesanan',
            'No. Pengajuan',
            'Username (Pembeli)',
            'Waktu Pesanan Dibuat',
            'Metode pembayaran pembeli',
            'Tanggal Dana Dilepaskan',
            'Harga Asli Produk',
            'Total Diskon Produk',
            'Jumlah Pengembalian Dana ke Pembeli',
            'Diskon Produk dari Shopee',
            'Diskon Voucher Ditanggung Penjual',
            'Cashback Koin yang Ditanggung Penjual',
            'Ongkir Dibayar Pembeli',
            'Diskon Ongkir Ditanggung Jasa Kirim',
            'Gratis Ongkir dari Shopee',
            'Ongkir yang Diteruskan oleh Shopee ke Jasa Kirim',
            'Ongkos Kirim Pengembalian Barang',
            'Kembali ke Biaya Pengiriman Pengirim',
            'Pengembalian Biaya Kirim',
            'Biaya Komisi AMS',
            'Biaya Administrasi',
            'Biaya Layanan',
            'Biaya Proses Pesanan',
            'Premi',
            'Biaya Program',
            'Biaya Transaksi',
            'Biaya Kampanye',
            'Bea Masuk, PPN & PPh',
            'Total Penghasilan',
            'Kode Voucher',
            'Kompensasi',
            'Promo Gratis Ongkir dari Penjual',
            'Jasa Kirim',
            'Nama Kurir',
        ]);
}

}
