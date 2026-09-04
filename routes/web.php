<?php

use Illuminate\Support\Facades\Route;

/*
 | Rute publik. URL dan nama rute memakai bahasa Indonesia sesuai
 | docs/PRD.md bagian 7. Isi tiap halaman menyusul di fasenya sendiri.
 */

Route::view('/', 'beranda')->name('beranda');
Route::view('/project', 'project')->name('project');
Route::view('/journal', 'journal')->name('journal');
Route::view('/galeri', 'galeri')->name('galeri');
