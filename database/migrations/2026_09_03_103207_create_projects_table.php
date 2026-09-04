<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Project. Kolom mengikuti docs/PRD.md bagian 6.
 * Nama project sengaja tidak punya versi Inggris, lihat PRD bagian 7.6.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('ringkasan');
            $table->string('ringkasan_en')->nullable();
            $table->json('gambar');
            $table->string('tahun');
            $table->foreignId('post_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('dipin')->default(false);
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
