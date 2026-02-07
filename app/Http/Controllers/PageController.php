<?php

namespace App\Http\Controllers;

use App\Models\CmsPage;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function show($slug)
    {
        $page = CmsPage::findBySlug($slug);

        if (!$page) {
            abort(404);
        }

        return view('pages.show', compact('page'));
    }
}
