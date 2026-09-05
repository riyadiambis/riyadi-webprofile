<?php

use App\Http\Controllers\BahasaController;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\GaleriController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;

/*
 | Rute publik. URL dan nama rute memakai bahasa Indonesia sesuai
 | docs/PRD.md bagian 7. Isi tiap halaman menyusul di fasenya sendiri.
 */

Route::get('/', [BerandaController::class, 'index'])->name('beranda');
Route::get('/project', [ProjectController::class, 'index'])->name('project');
Route::get('/journal', [JournalController::class, 'index'])->name('journal');
Route::get('/journal/{slug}', [JournalController::class, 'tulisan'])->name('journal.tulisan');
Route::get('/galeri', [GaleriController::class, 'index'])->name('galeri');

/*
 | Pengalih bahasa ID/EN di header. Form POST tanpa JavaScript:
 | cookie ditulis di sini, dibaca satu kali oleh middleware
 | SetLocale pada permintaan berikutnya.
 */
Route::post('/bahasa', [BahasaController::class, 'atur'])->name('bahasa');
