<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use App\Models\Post;
use App\Models\Project;
use App\Models\SiteText;
use Illuminate\View\View;

class BerandaController extends Controller
{
    /**
     * Beranda memanggil data dari tiga fase sebelumnya: project dan
     * tulisan yang ditandai dipin, serta foto terbaru galeri.
     *
     * Post dipin tetap disaring scopeTerbit() — post dipin yang masih
     * draf tidak boleh muncul di halaman publik, aturan yang sama
     * dengan halaman journal sejak Fase 2B. "Foto terbaru" berarti
     * foto yang terakhir ditambahkan (id menurun), bukan urutan
     * manual yang dipakai grid galeri, lihat docs/keputusan.md.
     */
    public function index(): View
    {
        $teks = SiteText::whereIn('kunci', ['perkenalan', 'penutup'])
            ->get()
            ->keyBy('kunci');

        return view('beranda', [
            'perkenalan' => $teks->get('perkenalan'),
            'penutup' => $teks->get('penutup'),
            'projects' => Project::with('post')
                ->where('dipin', true)
                ->orderBy('urutan')
                ->get(),
            'posts' => Post::terbit()
                ->where('dipin', true)
                ->orderByDesc('terbit_pada')
                ->get(),
            'photos' => Photo::orderByDesc('id')
                ->limit(12)
                ->get(),
        ]);
    }
}
