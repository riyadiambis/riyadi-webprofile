<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use Illuminate\View\View;

class GaleriController extends Controller
{
    public function index(): View
    {
        $photos = Photo::orderBy('urutan')
            ->orderBy('id')
            ->get();

        return view('galeri', ['photos' => $photos]);
    }
}
