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
        if (!Schema::hasTable('bja_resi')) {
            Schema::create('bja_resi', function (Blueprint $table) {
                $table->id();
                $table->string('resi_num', 100)->unique();
                $table->string('kota_asal', 50)->default('Jabodetabek');
                $table->string('kota_tujuan', 100)->nullable();
                $table->string('layanan', 50)->nullable();
                $table->date('estimasi_tiba')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('bja_resi_status')) {
            Schema::create('bja_resi_status', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('resi_id');
                $table->string('status', 150);
                $table->text('keterangan')->nullable();
                $table->text('catatan')->nullable();
                $table->dateTime('waktu');
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamp('created_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('bja_resi_status');
        Schema::dropIfExists('bja_resi');
    }
};
