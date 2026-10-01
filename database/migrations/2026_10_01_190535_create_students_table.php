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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('nis')->nullable()->unique();
            $table->string('nama');
            $table->string('kelas');
            $table->string('osis_mpk')->nullable()->default('Bukan');
            $table->integer('class_sort_order')->default(999);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['kelas', 'class_sort_order']);
            $table->index('nama');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
