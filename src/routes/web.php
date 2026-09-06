<?php

use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

// Page list lives in config/pages.php so the sitemap stays in sync with it.
foreach (config('pages') as $page) {
    $route = Route::view($page['path'], $page['view']);

    if ($page['cache']) {
        $route->middleware('cache.headers:public;max_age=3600;etag');
    }
}

Route::get('/sitemap.xml', SitemapController::class)
    ->middleware('cache.headers:public;max_age=86400;etag');
