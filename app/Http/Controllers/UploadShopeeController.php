<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use App\Models\Produk;
use App\Models\User;
use Spatie\SimpleExcel\SimpleExcelReader;
use Illuminate\Support\Facades\Auth;

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

            // Simpan Detail Produk
            foreach ($groupedItems as $row) {
                $skuInduk = trim($this->getValue($row, 'SKU Induk'));
                $produk = Produk::where('nama_produk', $skuInduk)->first();

                if (!$produk) {
                    \Log::warning("Produk tidak ditemukan untuk SKU Induk: [{$skuInduk}] pada Order ID: {$orderId}");
                    continue;
                }

                $detail = DetailPenjualan::where('id_penjualan', $penjualan->id)
                    ->where('id_produk', $produk->id)
                    ->first();

                $jumlah = (int) $this->getValue($row, 'Jumlah Produk di Pesan');
                $harga = (int) str_replace('.', '', $this->getValue($row, 'Total Harga Produk'));
                $variasi = $this->getValue($row, 'Nama Variasi');

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
}
