<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Services\RendererKonten;
use Illuminate\View\View;

class JournalController extends Controller
{
    public function index(): View
    {
        $posts = Post::terbit()
            ->latest('terbit_pada')
            ->paginate(9);

        return view('journal', ['posts' => $posts]);
    }

    /**
     * Penyaringan status ada tepat di baris query ini, bukan di
     * tampilan. Draf mengembalikan 404 lewat firstOrFail().
     */
    public function tulisan(string $slug, RendererKonten $renderer): View
    {
        $post = Post::terbit()->where('slug', $slug)->firstOrFail();

        return view('journal-tulisan', [
            'post' => $post,
            'konten' => $renderer->render($post->konten),
        ]);
    }
}
