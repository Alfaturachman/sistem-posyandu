<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('petugas', function (Blueprint $table) {
            $table->id();
            $table->string('username', 100)->unique();
            $table->string('password');
            $table->string('nama_petugas', 100);
            $table->string('no_hp', 15)->nullable();
            $table->string('alamat', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('petugas');
    }
};
// Compare this snippet from database/migrations/2025_02_18_143604_create_petugas_table.php: