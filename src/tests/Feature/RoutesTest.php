<?php

namespace Tests\Feature;

use Tests\TestCase;

class RoutesTest extends TestCase
{
    public function test_every_page_returns_200(): void
    {
        foreach (config('pages') as $page) {
            $this->get($page['path'])->assertStatus(200);
        }
    }

    public function test_unknown_route_renders_the_custom_404_page(): void
    {
        // Status alone would still pass on Laravel's stock error page.
        $this->get('/does-not-exist')
            ->assertStatus(404)
            ->assertSee('That page doesn\'t exist.', escape: false)
            ->assertSee('All pages', escape: false);
    }

    public function test_the_404_page_links_every_nav_page_except_home(): void
    {
        $response = $this->get('/does-not-exist')->assertStatus(404);

        foreach (config('pages') as $page) {
            if (! $page['nav'] || $page['path'] === '/') {
                continue;
            }

            // Escaped needle: blurbs are Blade-escaped output, so an apostrophe
            // arrives as &#039; and a raw comparison would miss it.
            $response->assertSee($page['blurb']);
        }
    }

    public function test_cacheable_pages_send_cache_headers_and_services_does_not(): void
    {
        // The whole point of the 'cache' flag: /services renders the Livewire
        // intake form and must never be served from a shared cache.
        foreach (config('pages') as $page) {
            $response = $this->get($page['path'])->assertStatus(200);

            if ($page['cache']) {
                $this->assertStringContainsString(
                    'max-age=3600',
                    $response->headers->get('Cache-Control') ?? '',
                    "Expected {$page['path']} to be cacheable."
                );
            } else {
                $this->assertStringNotContainsString(
                    'max-age=3600',
                    $response->headers->get('Cache-Control') ?? '',
                    "Expected {$page['path']} not to be cacheable."
                );
            }
        }
    }

    public function test_sitemap_lists_every_page(): void
    {
        $response = $this->get('/sitemap.xml')
            ->assertStatus(200)
            ->assertHeader('Content-Type', 'application/xml');

        foreach (config('pages') as $page) {
            $response->assertSee(url($page['path']), escape: false);
        }
    }

    public function test_sitemap_lastmod_is_a_real_recent_date(): void
    {
        // A missing view used to fall through filemtime() to 1970-01-01, which
        // crawlers read as "this never changes".
        $body = $this->get('/sitemap.xml')->assertStatus(200)->getContent();

        preg_match_all('/<lastmod>([^<]+)<\/lastmod>/', $body, $matches);

        $this->assertNotEmpty($matches[1]);

        foreach ($matches[1] as $lastmod) {
            $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $lastmod);
            $this->assertGreaterThan('2000-01-01', $lastmod, 'lastmod fell back to the epoch.');
        }
    }

    public function test_robots_points_at_the_sitemap(): void
    {
        $this->assertStringContainsString(
            'Sitemap: https://joshespi.com/sitemap.xml',
            file_get_contents(public_path('robots.txt'))
        );
    }

    public function test_pages_expose_canonical_and_open_graph_tags(): void
    {
        $this->get('/work')
            ->assertSee('<link rel="canonical" href="' . url('/work') . '">', escape: false)
            ->assertSee('property="og:title"', escape: false)
            ->assertSee('name="twitter:card"', escape: false);
    }

    public function test_page_titles_come_from_the_page_section(): void
    {
        // Covers the View::getSection() swap away from Blade internals.
        $this->get('/about')
            ->assertSee('<title>About Josh Espinoza — Full Stack Engineer</title>', escape: false);
    }

    public function test_mobile_nav_is_present(): void
    {
        $this->get('/')
            ->assertSee('id="mobile-nav"', escape: false)
            ->assertSee('aria-label="Toggle navigation menu"', escape: false);
    }

    public function test_services_page_emits_faq_structured_data(): void
    {
        $this->get('/services')
            ->assertSee('"@type":"FAQPage"', escape: false)
            ->assertSee('Do I have to get on a call?', escape: false);
    }
}
