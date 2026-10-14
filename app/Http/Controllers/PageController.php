<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Contracts\View\View;

final class PageController extends Controller
{
    public function home(): View
    {
        $page = Page::findByKey('home');

        abort_if($page === null, 404);

        return view('pages.home', ['page' => $page]);
    }
}
