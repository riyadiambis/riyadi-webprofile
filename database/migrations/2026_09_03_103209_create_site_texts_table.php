<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Teks beranda dwibahasa. Kolom mengikuti docs/PRD.md bagian 6.
 * Hanya dua kunci di v1: perkenalan dan penutup.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_texts', function (Blueprint $table) {
            $table->id();
            $table->string('kunci')->unique();
            $table->text('nilai_id');
            $table->text('nilai_en')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_texts');
    }
};
