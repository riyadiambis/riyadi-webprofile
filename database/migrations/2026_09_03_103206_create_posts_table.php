<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tulisan journal. Kolom mengikuti docs/PRD.md bagian 6.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('slug')->unique();
            $table->text('ringkasan');
            $table->longText('konten');
            $table->string('cover')->nullable();
            $table->string('tautan_project')->nullable();
            $table->string('label_tautan')->nullable();
            $table->enum('status', ['draf', 'terbit'])->default('draf');
            $table->boolean('dipin')->default(false);
            $table->dateTime('terbit_pada')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
