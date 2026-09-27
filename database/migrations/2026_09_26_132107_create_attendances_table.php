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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('nama');
            $table->string('kelas');
            $table->string('osis_mpk')->nullable()->default('bukan');
            $table->integer('class_sort_order')->default(999);
            $table->enum('status', ['hadir', 'sakit', 'izin', 'alfa']);
            $table->enum('kelengkapan', ['lengkap', 'tidak_lengkap'])->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index(['tanggal', 'class_sort_order']);
            $table->index('nama');
            $table->index('kelas');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
