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

        // Flash sekali pakai murni untuk animasi CSS penanda pengalih
        // bahasa di header — dibaca sekali oleh partial header lalu
        // hilang sendiri. Tidak mengubah mekanisme cookie/redirect.
        return back()
            ->withCookie(cookie()->forever('bahasa', $pilihan))
            ->with('bahasa_baru_ditekan', true);
    }
}
