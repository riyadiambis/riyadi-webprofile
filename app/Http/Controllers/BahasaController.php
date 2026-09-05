<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BahasaController extends Controller
{
    /**
     * Menyimpan pilihan bahasa pengunjung ke cookie, lalu kembali ke
     * halaman asalnya. Cookie dibaca ulang oleh middleware SetLocale
     * pada permintaan berikutnya, jadi bahasa baru berlaku begitu
     * pengalihan selesai.
     */
    public function atur(Request $request): RedirectResponse
    {
        $pilihan = $request->validate(['pilihan' => ['required', 'in:id,en']])['pilihan'];

        return back()->withCookie(cookie()->forever('bahasa', $pilihan));
    }
}
