<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Menentukan bahasa aktif dari cookie `bahasa` pilihan pengunjung.
 *
 * Ini satu-satunya tempat di seluruh aplikasi yang membaca cookie
 * itu — Blade dan controller tidak pernah menyentuhnya langsung,
 * mereka cukup memakai app()->getLocale(). Alasan lengkapnya di
 * docs/keputusan.md (keputusan Fase 5).
 */
class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $pilihan = $request->cookie('bahasa');

        // Hanya id dan en yang sah, lihat docs/fitur/05-dwibahasa.md.
        // Selain itu (termasuk cookie tidak ada) jatuh ke bahasa
        // Indonesia, bahasa utama situs ini.
        $bahasa = in_array($pilihan, ['id', 'en'], true) ? $pilihan : 'id';

        app()->setLocale($bahasa);

        return $next($request);
    }
}
