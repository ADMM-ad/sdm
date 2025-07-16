<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\IndonesiaPostalCode; // Import model yang telah dibuat
use Illuminate\Support\Facades\File; // Untuk membaca file
use Illuminate\Support\Facades\DB;   // Untuk transaksi database

class KodeposJsonDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       // Path ke file JSON yang telah Anda unduh.
        // Pastikan path ini benar!
        $jsonPath = storage_path('app/public/data/kodepos.json');

        // Cek apakah file JSON ada
        if (!File::exists($jsonPath)) {
            $this->command->error("File kodepos.json tidak ditemukan di: " . $jsonPath);
            $this->command->warn("Pastikan Anda sudah mengunduh file 'kodepos.json' dan menempatkannya di 'storage/app/public/data/'");
            return;
        }

        // Baca konten file JSON
        $jsonContent = File::get($jsonPath);
        $data = json_decode($jsonContent, true); // Dekode JSON menjadi array asosiatif PHP

        if (empty($data)) {
            $this->command->warn("File kodepos.json kosong atau tidak valid.");
            return;
        }

        // Konfigurasi untuk impor data besar (opsional, tapi disarankan)
        ini_set('memory_limit', '512M'); // Tingkatkan batas memori PHP jika data sangat besar
        set_time_limit(300); // Tingkatkan batas waktu eksekusi skrip menjadi 5 menit

        $chunkSize = 1000; // Jumlah record yang akan dimasukkan dalam satu batch
        $recordsToInsert = [];
        $totalInserted = 0;

        // Mulai transaksi database untuk mempercepat proses insert
        DB::beginTransaction();

        try {
            // Iterasi setiap baris data dari JSON
            foreach ($data as $row) {
                $recordsToInsert[] = [
                    'postal_code' => $row['code'], // Sesuaikan 'code' dari JSON dengan 'postal_code' di DB
                    'village' => $row['village'],
                    'district' => $row['district'],
                    'regency' => $row['regency'], // 'regency' dari JSON ke 'regency' di DB
                    'province' => $row['province'],
                    'latitude' => $row['latitude'] ?? null, // Gunakan null jika data tidak ada di JSON
                    'longitude' => $row['longitude'] ?? null,
                    'elevation' => $row['elevation'] ?? null,
                    'timezone' => $row['timezone'] ?? null,
                    'created_at' => now(), // Tambahkan timestamp
                    'updated_at' => now(), // Tambahkan timestamp
                ];

                // Jika jumlah record mencapai chunkSize, lakukan insert batch
                if (count($recordsToInsert) >= $chunkSize) {
                    IndonesiaPostalCode::insert($recordsToInsert);
                    $totalInserted += count($recordsToInsert);
                    $recordsToInsert = []; // Reset array untuk batch selanjutnya
                    $this->command->info("Inserted " . $totalInserted . " records...");
                }
            }

            // Masukkan sisa record yang mungkin belum mencapai chunkSize terakhir
            if (!empty($recordsToInsert)) {
                IndonesiaPostalCode::insert($recordsToInsert);
                $totalInserted += count($recordsToInsert);
            }

            DB::commit(); // Commit transaksi jika semua berhasil
            $this->command->info("Data kodepos berhasil diimpor. Total: " . $totalInserted . " records.");

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaksi jika ada kesalahan
            $this->command->error("Gagal mengimpor data kodepos: " . $e->getMessage());
            $this->command->error("Line: " . $e->getLine());
        }
    }
    }

