<?php

use Illuminate\Support\Facades\Route;

// Static brochure pages — publicly cacheable for 1 hour
Route::view('/', 'home')->middleware('cache.headers:public;max_age=3600;etag');
Route::view('/about', 'about')->middleware('cache.headers:public;max_age=3600;etag');
Route::view('/privacy', 'privacy')->middleware('cache.headers:public;max_age=3600;etag');

// Livewire form on this page — not cached
Route::view('/services', 'services');
