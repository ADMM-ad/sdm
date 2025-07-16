<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penjualan;
use Spatie\SimpleExcel\SimpleExcelReader;

class UploadMengantarController extends Controller
{
   public function import(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:csv,txt,xlsx,xls'
    ]);

    $path = $request->file('file')->store('temp');

    SimpleExcelReader::create(storage_path("app/{$path}"))
        ->useDelimiter("\t") // Ganti ke ',' jika file kamu pakai koma
        ->getRows()
        ->each(function (array $row) {
            try {
                $orderId        = $row['Order ID'] ?? null;
                $noResi         = $row['Tracking ID'] ?? null;
                $lastStatus     = $row['Last Status'] ?? null;
                $customerName   = $row['Customer Name'] ?? null;

                $shippingFee       = isset($row['Shipping Fee']) ? (int) $row['Shipping Fee'] : 0;
                $shippingDiscount  = isset($row['Shipping Discount']) ? (int) $row['Shipping Discount'] : 0;
                $codFee            = isset($row['COD Fee (Inc VAT)']) ? (int) floatval($row['COD Fee (Inc VAT)']) : 0;

                if (!$orderId || !$noResi || !$lastStatus || !$customerName) {
                    \Log::warning('Data tidak lengkap: ' . json_encode($row));
                    return;
                }


   // ✅ Cek apakah status menyebabkan total_bayar di-nol-kan
                $statusInvalid = ['rts', 'batal', 'undelivered', 'delivery return', 'deliveri return'];
                $normalizedStatus = strtolower(trim($lastStatus));
                $setTotalBayarToZero = in_array($normalizedStatus, $statusInvalid);

                $penjualan = Penjualan::where('order_id', $orderId)->first();

                if ($penjualan) {
                    $penjualan->update([
                        'no_resi'       => $noResi,
                        'status_pesanan'=> $lastStatus,
                        'ongkir'        => $shippingFee,      // 👉 TANPA pengurangan
                        'cashback'      => $shippingDiscount,
                        'biaya_cod'     => $codFee,
                        'total_bayar'    => $setTotalBayarToZero ? 0 : $penjualan->total_bayar,
                    ]);
                    \Log::info("Update by order_id: {$orderId}");
                    return;
                }

                // Jika tidak ditemukan berdasarkan order_id
                $cleanedName = trim($customerName);
                $penjualanByName = Penjualan::where('nama_pembeli', $cleanedName)->first();

                if ($penjualanByName) {
                    $penjualanByName->update([
                        'no_resi'        => $noResi,
                        'status_pesanan' => $lastStatus,
                        'order_id'       => $orderId,
                        'ongkir'         => $shippingFee,
                        'cashback'       => $shippingDiscount,
                        'biaya_cod'      => $codFee,
                        'total_bayar'    => $setTotalBayarToZero ? 0 : $penjualanByName->total_bayar,
                    ]);
                    \Log::info("Update by nama_pembeli: {$cleanedName} → order_id updated to {$orderId}");
                    return;
                }

                \Log::warning("Data tidak cocok (abaikan): Order ID: {$orderId}, Nama: {$customerName}");

            } catch (\Exception $e) {
                \Log::error("Error baris CSV: " . $e->getMessage());
            }
        });

    return back()->with('success', 'Data berhasil diperbarui sesuai yang cocok.');
}


}

