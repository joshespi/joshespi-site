<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = array_map(function (array $page): array {
            $view = resource_path("views/{$page['view']}.blade.php");

            // A missing view or an unreadable mtime used to emit a warning and
            // a lastmod of 1970-01-01, which crawlers read as "never changes".
            // Fall back to today instead: wrong but harmless.
            $mtime = is_file($view) ? filemtime($view) : false;

            return [
                'loc'     => url($page['path']),
                'lastmod' => date('Y-m-d', $mtime !== false ? $mtime : time()),
            ];
        }, config('pages'));

        return response()
            ->view('sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml');
    }
}
