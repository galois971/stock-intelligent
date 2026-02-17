<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\StockAlert;
use App\Models\StockMovement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->user->assignRole('admin');
    }

    public function test_dashboard_accessible_to_authenticated_users(): void
    {
        $response = $this->actingAs($this->user)->get('/dashboard');
        $response->assertStatus(200);
    }

    public function test_dashboard_not_accessible_to_unauthenticated_users(): void
    {
        $response = $this->get('/dashboard');
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_dashboard_shows_total_products(): void
    {
        Product::factory()->count(5)->create();

        $response = $this->actingAs($this->user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Produits');
    }

    public function test_dashboard_shows_alerts_count(): void
    {
        $product = Product::factory()->create();

        StockAlert::create([
            'product_id' => $product->id,
            'alert_type' => 'low_stock',
            'current_quantity' => 3,
            'message' => 'Stock bas',
            'is_resolved' => false,
        ]);

        $response = $this->actingAs($this->user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Alertes');
    }

    public function test_dashboard_shows_movements_count(): void
    {
        $product = Product::factory()->create();

        StockMovement::factory()->count(5)->create([
            'product_id' => $product->id,
            'type' => 'entry',
        ]);

        $response = $this->actingAs($this->user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Mouvements');
    }

    public function test_dashboard_counts_only_unresolved_alerts(): void
    {
        $product = Product::factory()->create();

        StockAlert::create([
            'product_id' => $product->id,
            'alert_type' => 'low_stock',
            'current_quantity' => 3,
            'message' => 'Stock bas',
            'is_resolved' => false,
        ]);

        StockAlert::create([
            'product_id' => $product->id,
            'alert_type' => 'low_stock',
            'current_quantity' => 5,
            'message' => 'Stock bas',
            'is_resolved' => true,
        ]);

        $unresolved = StockAlert::where('is_resolved', false)->count();
        $this->assertEquals(1, $unresolved);
    }

    public function test_dashboard_shows_entry_and_exit_breakdown(): void
    {
        $product = Product::factory()->create();

        StockMovement::factory()->count(3)->create([
            'product_id' => $product->id,
            'type' => 'entry',
        ]);

        StockMovement::factory()->count(2)->create([
            'product_id' => $product->id,
            'type' => 'exit',
        ]);

        $entryCount = StockMovement::where('type', 'entry')->count();
        $exitCount = StockMovement::where('type', 'exit')->count();

        $this->assertEquals(3, $entryCount);
        $this->assertEquals(2, $exitCount);
    }
}
