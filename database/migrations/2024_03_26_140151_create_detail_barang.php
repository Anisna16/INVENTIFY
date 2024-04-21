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
        Schema::create('detail_barang', function (Blueprint $table) {
            $table->id('id_barang');
            $table->unsignedBigInteger('id_kode_barang')->index();
            $table->string('merk_barang');
            $table->string('NUP');
            $table->string('satuan');
            $table->string('harga_perolehan');
            $table->dateTime('tanggal_pencatatan');

            $table->foreign('id_kode_barang')->references('id_kode_barang')->on('detail_kode_barang');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_barang');
    }
};
