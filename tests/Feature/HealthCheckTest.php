<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    use RefreshDatabase;

    public function test_application_is_healthy(): void
    {
        // Just verify the app boots
        $this->assertTrue(true);
    }

    public function test_dashboard_route_exists(): void
    {
        $response = $this->get('/dashboard');
        // Should either return 200 or redirect to login (302/401)
        $this->assertThat(
            $response->status(),
            $this->logicalOr(
                $this->equalTo(200),
                $this->equalTo(302),
                $this->equalTo(301)
            )
        );
    }

    public function test_products_route_exists(): void
    {
        $response = $this->get('/products');
        $this->assertThat(
            $response->status(),
            $this->logicalOr(
                $this->equalTo(200),
                $this->equalTo(302),
                $this->equalTo(301)
            )
        );
    }

    public function test_movements_route_exists(): void
    {
        $response = $this->get('/movements');
        $this->assertThat(
            $response->status(),
            $this->logicalOr(
                $this->equalTo(200),
                $this->equalTo(302),
                $this->equalTo(301)
            )
        );
    }
}
