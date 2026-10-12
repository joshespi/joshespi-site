<?php

use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\SitemapController;
use App\Livewire\Admin\IntakeIndex;
use App\Livewire\Admin\IntakeShow;
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

// Back office. Accounts come from `php artisan admin:user`; there is no sign-up.
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [LoginController::class, 'show'])->name('login');
        Route::post('/login', [LoginController::class, 'store']);
    });

    Route::middleware('auth')->group(function () {
        Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
        Route::livewire('/', IntakeIndex::class)->name('intakes.index');
        Route::livewire('/intakes/{intake}', IntakeShow::class)->name('intakes.show');
    });
});
