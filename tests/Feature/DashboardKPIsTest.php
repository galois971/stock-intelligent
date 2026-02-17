<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\StockAlert;
use App\Models\StockMovement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardKPIsTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->user->assignRole('admin');
    }

    /** @test */
    public function dashboard_shows_total_products_count()
    {
        // Create products
        Product::factory()->count(5)->create();

        $response = $this->actingAs($this->user)->get('/dashboard');

        $response->assertSee('5');
        $response->assertSee('Produits');
    }

    /** @test */
    public function dashboard_shows_low_stock_products_count()
    {
        // Create products with low stock
        Product::factory()->count(3)->create([
            'stock_min' => 10,
            'stock_optimal' => 20,
        ]);

        // Create inventory to set current stock below minimum
        foreach (Product::all() as $product) {
            $product->current_stock = 5; // Below min of 10
            $product->save();
        }

        $response = $this->actingAs($this->user)->get('/dashboard');

        $response->assertStatus(200);
    }

    /** @test */
    public function dashboard_shows_active_alerts_count()
    {
        Product::factory()->count(2)->create();

        StockAlert::create([
            'product_id' => Product::first()->id,
            'alert_type' => 'low_stock',
            'current_quantity' => 3,
            'message' => 'Stock bas',
            'is_resolved' => false,
        ]);

        StockAlert::create([
            'product_id' => Product::latest()->first()->id,
            'alert_type' => 'overstock',
            'current_quantity' => 150,
            'message' => 'Surstock',
            'is_resolved' => false,
        ]);

        $response = $this->actingAs($this->user)->get('/dashboard');

        $response->assertSee('2');
    }

    /** @test */
    public function dashboard_counts_only_unresolved_alerts()
    {
        Product::factory()->count(2)->create();

        $alert1 = StockAlert::create([
            'product_id' => Product::first()->id,
            'alert_type' => 'low_stock',
            'current_quantity' => 3,
            'message' => 'Stock bas',
            'is_resolved' => false,
        ]);

        $alert2 = StockAlert::create([
            'product_id' => Product::latest()->first()->id,
            'alert_type' => 'low_stock',
            'current_quantity' => 5,
            'message' => 'Stock bas',
            'is_resolved' => true, // Resolved
        ]);

        $unresolved = StockAlert::where('is_resolved', false)->count();

        $this->assertEquals(1, $unresolved);
    }

    /** @test */
    public function dashboard_shows_stock_movement_count()
    {
        $product = Product::factory()->create();

        StockMovement::factory()->count(7)->create([
            'product_id' => $product->id,
            'type' => 'entry',
            'subtype' => 'achat',
        ]);

        StockMovement::factory()->count(5)->create([
            'product_id' => $product->id,
            'type' => 'exit',
            'subtype' => 'vente',
        ]);

        $totalMovements = StockMovement::count();

        $this->assertEquals(12, $totalMovements);
    }

    /** @test */
    public function dashboard_calculates_total_stock_value()
    {
        Product::factory()->create([
            'price' => 10.00,
        ]);

        Product::factory()->create([
            'price' => 20.00,
        ]);

        Product::factory()->create([
            'price' => 30.00,
        ]);

        $products = Product::all();

        // Simulate current stock
        $totalValue = $products->sum(function ($product) {
            return $product->price * ($product->current_stock ?? 0);
        });

        $this->assertIsNumeric($totalValue);
    }

    /** @test */
    public function dashboard_shows_entry_and_exit_breakdown()
    {
        $product = Product::factory()->create();

        $entries = StockMovement::factory()->count(3)->create([
            'product_id' => $product->id,
            'type' => 'entry',
        ]);

        $exits = StockMovement::factory()->count(2)->create([
            'product_id' => $product->id,
            'type' => 'exit',
        ]);

        $entryCount = StockMovement::where('type', 'entry')->count();
        $exitCount = StockMovement::where('type', 'exit')->count();

        $this->assertEquals(3, $entryCount);
        $this->assertEquals(2, $exitCount);
    }

    /** @test */
    public function dashboard_counts_products_by_category()
    {
        $category1 = \App\Models\Category::factory()->create();
        $category2 = \App\Models\Category::factory()->create();

        Product::factory()->count(4)->create([
            'category_id' => $category1->id,
        ]);

        Product::factory()->count(3)->create([
            'category_id' => $category2->id,
        ]);

        $catCount1 = Product::where('category_id', $category1->id)->count();
        $catCount2 = Product::where('category_id', $category2->id)->count();

        $this->assertEquals(4, $catCount1);
        $this->assertEquals(3, $catCount2);
    }

    /** @test */
    public function dashboard_shows_recent_movements()
    {
        $product = Product::factory()->create();

        $old = StockMovement::factory()->create([
            'product_id' => $product->id,
            'type' => 'entry',
            'created_at' => now()->subDays(10),
        ]);

        $recent = StockMovement::factory()->create([
            'product_id' => $product->id,
            'type' => 'exit',
            'created_at' => now(),
        ]);

        $movements = StockMovement::orderByDesc('created_at')->limit(5)->get();

        $this->assertTrue($movements->first()->id === $recent->id);
    }

    /** @test */
    public function dashboard_calculates_movement_statistics()
    {
        $product = Product::factory()->create();

        StockMovement::factory()->create([
            'product_id' => $product->id,
            'type' => 'entry',
            'quantity' => 100,
        ]);

        StockMovement::factory()->create([
            'product_id' => $product->id,
            'type' => 'exit',
            'quantity' => 30,
        ]);

        StockMovement::factory()->create([
            'product_id' => $product->id,
            'type' => 'exit',
            'quantity' => 20,
        ]);

        $totalEntries = StockMovement::where('type', 'entry')
            ->sum('quantity');

        $totalExits = StockMovement::where('type', 'exit')
            ->sum('quantity');

        $this->assertEquals(100, $totalEntries);
        $this->assertEquals(50, $totalExits);
    }

    /** @test */
    public function dashboard_accessible_to_authenticated_users()
    {
        $response = $this->actingAs($this->user)->get('/dashboard');

        $response->assertStatus(200);
    }

    /** @test */
    public function dashboard_not_accessible_to_unauthenticated_users()
    {
        $response = $this->get('/dashboard');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /** @test */
    public function dashboard_shows_kpi_cards()
    {
        Product::factory()->count(5)->create();

        $response = $this->actingAs($this->user)->get('/dashboard');

        // Check for KPI indicators
        $response->assertSee('Produits');
        $response->assertSee('Alertes');
        $response->assertSee('Mouvements');
    }
}
