<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(): View
    {
        $projects = Project::with('post')
            ->orderBy('urutan')
            ->get();

        return view('project', ['projects' => $projects]);
    }
}
