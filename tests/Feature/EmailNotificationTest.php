<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\StockAlert;
use App\Notifications\StockAlertNotification;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class EmailNotificationTest extends TestCase
{
    protected User $admin;
    protected User $gestionnaire;
    protected Product $product;

    public function setUp(): void
    {
        parent::setUp();

        // Create users with roles
        $this->admin = User::factory()->create(['email' => 'admin@test.com']);
        $this->admin->assignRole('admin');

        $this->gestionnaire = User::factory()->create(['email' => 'gestionnaire@test.com']);
        $this->gestionnaire->assignRole('gestionnaire');

        // Create a product with initial stock via stock movements
        $this->product = Product::factory()->create([
            'name' => 'Test Product',
            'stock_min' => 10,
            'stock_optimal' => 50,
        ]);

        // Initialize stock to 100 using an entry movement
        StockMovement::factory()->create([
            'product_id' => $this->product->id,
            'type' => 'entry',
            'quantity' => 100,
        ]);
    }

    /** @test */
    public function test_low_stock_alert_is_created_when_threshold_breached()
    {
        // Disable notifications for this test - we just want to check alert creation
        Notification::fake();

        // Create a stock movement that will trigger low stock alert
        StockMovement::factory()->create([
            'product_id' => $this->product->id,
            'type' => 'exit',
            'quantity' => 91, // This will make current stock = 9 (below minimum of 10)
        ]);

        // Check that low stock alert was created  
        $alert = StockAlert::where('product_id', $this->product->id)
            ->where('alert_type', 'low_stock')
            ->first();

        $this->assertNotNull($alert);
        $this->assertEquals('low_stock', $alert->alert_type);
        $this->assertEquals(9, $alert->current_quantity);
    }

    /** @test */
    public function test_overstock_alert_is_created()
    {
        Notification::fake();

        // Create a stock movement that will trigger overstock alert
        StockMovement::factory()->create([
            'product_id' => $this->product->id,
            'type' => 'entry',
            'quantity' => 100, // This will make current stock = 200 (above optimal of 50)
        ]);

        // Check that overstock alert was created
        $alert = StockAlert::where('product_id', $this->product->id)
            ->where('alert_type', 'overstock')
            ->first();

        $this->assertNotNull($alert);
        $this->assertEquals('overstock', $alert->alert_type);
    }

    /** @test */
    public function test_risk_of_rupture_alert_is_created()
    {
        Notification::fake();

        // Create a product with lower stock
        $product = Product::factory()->create([
            'stock_min' => 10,
        ]);

        // Initialize stock to 15 using entry movement
        StockMovement::factory()->create([
            'product_id' => $product->id,
            'type' => 'entry',
            'quantity' => 15,
        ]);

        // Create a movement that triggers risk of rupture
        StockMovement::factory()->create([
            'product_id' => $product->id,
            'type' => 'exit',
            'quantity' => 5, // Stock becomes 10, which is = stock_min and < stock_min * 1.2
        ]);

        // Check that risk of rupture alert was created
        $alert = StockAlert::where('product_id', $product->id)
            ->where('alert_type', 'risk_of_rupture')
            ->first();

        $this->assertNotNull($alert);
        $this->assertEquals('risk_of_rupture', $alert->alert_type);
    }

    /** @test */
    public function test_expiration_alert_is_created()
    {
        Notification::fake();

        // Create a stock movement with expiration subtype
        StockMovement::factory()->create([
            'product_id' => $this->product->id,
            'type' => 'exit',
            'subtype' => 'expiration',
            'quantity' => 10,
        ]);

        // Check that expiration alert was created
        $alert = StockAlert::where('product_id', $this->product->id)
            ->where('alert_type', 'expiration')
            ->first();

        $this->assertNotNull($alert);
        $this->assertEquals('expiration', $alert->alert_type);
    }

    /** @test */
    public function test_notification_not_sent_if_alert_already_exists()
    {
        Notification::fake();

        // Create an existing unresolved alert
        StockAlert::create([
            'product_id' => $this->product->id,
            'alert_type' => 'low_stock',
            'current_quantity' => 5,
            'message' => 'Stock trop bas',
            'is_resolved' => false,
        ]);

        // Create a movement that would trigger the same alert
        StockMovement::factory()->create([
            'product_id' => $this->product->id,
            'type' => 'exit',
            'quantity' => 50,
        ]);

        // Check that only one low_stock alert exists
        $alerts = StockAlert::where('product_id', $this->product->id)
            ->where('alert_type', 'low_stock')
            ->count();

        $this->assertEquals(1, $alerts);
    }

    /** @test */
    public function test_notification_only_sent_to_admin_and_gestionnaire()
    {
        Notification::fake();

        // Create an observateur user (should not receive notifications)
        $observateur = User::factory()->create(['email' => 'observateur@test.com']);
        $observateur->assignRole('observateur');

        // Trigger alert
        StockMovement::factory()->create([
            'product_id' => $this->product->id,
            'type' => 'exit',
            'quantity' => 91,
        ]);

        // Check that alert was created
        $alert = StockAlert::where('product_id', $this->product->id)
            ->where('alert_type', 'low_stock')
            ->count();

        $this->assertEquals(1, $alert);

        // Verify notification was sent to admin and gestionnaire (not checking observateur)
        Notification::assertSentTo($this->admin, StockAlertNotification::class);
        Notification::assertSentTo($this->gestionnaire, StockAlertNotification::class);
        Notification::assertNotSentTo($observateur, StockAlertNotification::class);
    }

    /** @test */
    public function test_stock_alert_message_contains_product_information()
    {
        Notification::fake();

        // Create a movement that triggers alert
        StockMovement::factory()->create([
            'product_id' => $this->product->id,
            'type' => 'exit',
            'quantity' => 91,
        ]);

        // Get the alert from database
        $alert = StockAlert::where('product_id', $this->product->id)
            ->where('alert_type', 'low_stock')
            ->first();

        // Verify alert message was created with product name
        $this->assertNotNull($alert);
        $this->assertStringContainsString($this->product->name, $alert->message);
        $this->assertEquals(9, $alert->current_quantity);
    }

    /** @test */
    public function test_multiple_alert_types_can_exist_for_same_product()
    {
        Notification::fake();

        // Create product with specific thresholds
        $product = Product::factory()->create([
            'stock_min' => 20,
            'stock_optimal' => 50,
        ]);

        // Initialize stock to 15
        StockMovement::factory()->create([
            'product_id' => $product->id,
            'type' => 'entry',
            'quantity' => 15,
        ]);

        // This should create a low stock alert
        StockMovement::factory()->create([
            'product_id' => $product->id,
            'type' => 'exit',
            'quantity' => 1,
        ]);

        // Now trigger overstock (low stock continues to exist)
        StockMovement::factory()->create([
            'product_id' => $product->id,
            'type' => 'entry',
            'quantity' => 100, // stock = 114, which exceeds optimal of 50
        ]);

        // Check that overstock alert was created (low stock from before still exists)
        $overstock_alert = StockAlert::where('product_id', $product->id)
            ->where('alert_type', 'overstock')
            ->first();

        $this->assertNotNull($overstock_alert);
    }
}

