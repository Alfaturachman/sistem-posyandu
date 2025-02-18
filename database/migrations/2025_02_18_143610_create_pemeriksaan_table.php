<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('pemeriksaan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_anak')->constrained('anak')->onDelete('cascade')->onUpdate('cascade');
            $table->dateTime('tanggal_periksa');
            $table->decimal('berat_badan', 5, 2);
            $table->decimal('tinggi_badan', 5, 2);
            $table->decimal('lingkar_lengan', 5, 2);
            $table->decimal('lingkar_kepala', 5, 2);
            $table->string('citra_telapak_kaki', 255)->nullable();
            $table->foreignId('id_petugas')->nullable()->constrained('petugas')->onDelete('set null')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('pemeriksaan');
    }
};
// Compare this snippet from database/migrations/2025_02_18_143610_create_pemeriksaan_table.php: