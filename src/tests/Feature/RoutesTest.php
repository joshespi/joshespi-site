<?php

namespace Tests\Feature;

use Tests\TestCase;

class RoutesTest extends TestCase
{
    public function test_home_returns_200(): void
    {
        $this->get('/')->assertStatus(200);
    }

    public function test_services_returns_200(): void
    {
        $this->get('/services')->assertStatus(200);
    }

    public function test_about_returns_200(): void
    {
        $this->get('/about')->assertStatus(200);
    }

    public function test_privacy_returns_200(): void
    {
        $this->get('/privacy')->assertStatus(200);
    }

    public function test_unknown_route_returns_404(): void
    {
        $this->get('/does-not-exist')->assertStatus(404);
    }
}
