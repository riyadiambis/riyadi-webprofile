<?php

use App\Http\Controllers\JournalController;
use Illuminate\Support\Facades\Route;

/*
 | Rute publik. URL dan nama rute memakai bahasa Indonesia sesuai
 | docs/PRD.md bagian 7. Isi tiap halaman menyusul di fasenya sendiri.
 */

Route::view('/', 'beranda')->name('beranda');
Route::view('/project', 'project')->name('project');
Route::get('/journal', [JournalController::class, 'index'])->name('journal');
Route::get('/journal/{slug}', [JournalController::class, 'tulisan'])->name('journal.tulisan');
Route::view('/galeri', 'galeri')->name('galeri');
