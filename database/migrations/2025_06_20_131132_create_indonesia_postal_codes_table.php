<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::create('indonesia_postal_codes', function (Blueprint $table) {
            $table->id();
            $table->string('postal_code', 5)->index(); // 'code' dari JSON, ditambah index
            $table->string('village');
            $table->string('district');
            $table->string('regency'); // 'regency' akan menjadi nama kolom 'city' atau 'kabupaten/kota'
            $table->string('province');
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            $table->integer('elevation')->nullable();
            $table->string('timezone')->nullable();
            $table->timestamps(); // Kolom created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indonesia_postal_codes');
    }
};
