<?php

/*
|--------------------------------------------------------------------------
| Static Brochure Pages
|--------------------------------------------------------------------------
|
| The one page list. routes/web.php registers them, SitemapController lists
| them, the layout builds its header and footer navs from them, the 404 page
| builds its "all pages" grid from them, and RoutesTest walks them — so none
| of those can drift apart.
|
| cache  => send public cache headers. /services is excluded because it
|           renders the Livewire intake form and must not be cached.
| nav    => show in the header nav.
| footer => show in the footer nav.
| blurb  => one-line description, used by the 404 page's grid.
|
*/

return [
    [
        'path'   => '/',
        'view'   => 'home',
        'label'  => 'Home',
        'cache'  => true,
        'nav'    => true,
        'footer' => false,
        'blurb'  => 'Fixed-price WordPress, Laravel, and DevOps work.',
    ],
    [
        'path'   => '/services',
        'view'   => 'services',
        'label'  => 'Services',
        'cache'  => false,
        'nav'    => true,
        'footer' => true,
        'blurb'  => 'The full fixed-price menu, FAQ, and the intake form.',
    ],
    [
        'path'   => '/work',
        'view'   => 'work',
        'label'  => 'Work',
        'cache'  => true,
        'nav'    => true,
        'footer' => true,
        'blurb'  => "Apps and projects I've built and shipped.",
    ],
    [
        'path'   => '/about',
        'view'   => 'about',
        'label'  => 'About',
        'cache'  => true,
        'nav'    => true,
        'footer' => true,
        'blurb'  => '15 years of PHP, Laravel, WordPress, Docker, and Linux.',
    ],
    [
        'path'   => '/privacy',
        'view'   => 'privacy',
        'label'  => 'Privacy',
        'cache'  => true,
        'nav'    => false,
        'footer' => true,
        'blurb'  => 'What this site collects, and what it does not.',
    ],
    [
        'path'   => '/terms',
        'view'   => 'terms',
        'label'  => 'Terms',
        'cache'  => true,
        'nav'    => false,
        'footer' => true,
        'blurb'  => 'The terms that govern a fixed-price engagement.',
    ],
];
