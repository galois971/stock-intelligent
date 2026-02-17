<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleBasedAccessControlTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $gestionnaire;
    private User $observateur;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        // Create users with different roles
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->gestionnaire = User::factory()->create();
        $this->gestionnaire->assignRole('gestionnaire');

        $this->observateur = User::factory()->create();
        $this->observateur->assignRole('observateur');

        // Create test product
        $this->product = Product::factory()->create();
    }

    public function test_admin_can_access_product_index(): void
    {
        $this->actingAs($this->admin)
            ->get('/products')
            ->assertStatus(200);
    }

    public function test_gestionnaire_can_access_product_index(): void
    {
        $this->actingAs($this->gestionnaire)
            ->get('/products')
            ->assertStatus(200);
    }

    public function test_observateur_can_access_product_index(): void
    {
        $this->actingAs($this->observateur)
            ->get('/products')
            ->assertStatus(200);
    }

    /** @test */
    public function admin_can_create_product()
    {
        $this->actingAs($this->admin)
            ->get('/products/create')
            ->assertStatus(200);
    }

    /** @test */
    public function gestionnaire_can_create_product()
    {
        $this->actingAs($this->gestionnaire)
            ->get('/products/create')
            ->assertStatus(200);
    }

    /** @test */
    public function observateur_cannot_create_product()
    {
        $this->actingAs($this->observateur)
            ->get('/products/create')
            ->assertStatus(403);
    }

    /** @test */
    public function admin_can_edit_product()
    {
        $this->actingAs($this->admin)
            ->get("/products/{$this->product->id}/edit")
            ->assertStatus(200);
    }

    /** @test */
    public function gestionnaire_can_edit_product()
    {
        $this->actingAs($this->gestionnaire)
            ->get("/products/{$this->product->id}/edit")
            ->assertStatus(200);
    }

    /** @test */
    public function observateur_cannot_edit_product()
    {
        $this->actingAs($this->observateur)
            ->get("/products/{$this->product->id}/edit")
            ->assertStatus(403);
    }

    /** @test */
    public function admin_can_delete_product()
    {
        $product = Product::factory()->create();

        $this->actingAs($this->admin)
            ->delete("/products/{$product->id}")
            ->assertStatus(302); // Redirect after success
    }

    /** @test */
    public function gestionnaire_can_delete_product()
    {
        $product = Product::factory()->create();

        $this->actingAs($this->gestionnaire)
            ->delete("/products/{$product->id}")
            ->assertStatus(302);
    }

    /** @test */
    public function observateur_cannot_delete_product()
    {
        $product = Product::factory()->create();

        $this->actingAs($this->observateur)
            ->delete("/products/{$product->id}")
            ->assertStatus(403);
    }

    /** @test */
    public function admin_can_access_movements_create()
    {
        $this->actingAs($this->admin)
            ->get('/movements/create')
            ->assertStatus(200);
    }

    /** @test */
    public function gestionnaire_can_access_movements_create()
    {
        $this->actingAs($this->gestionnaire)
            ->get('/movements/create')
            ->assertStatus(200);
    }

    /** @test */
    public function observateur_cannot_access_movements_create()
    {
        $this->actingAs($this->observateur)
            ->get('/movements/create')
            ->assertStatus(403);
    }

    /** @test */
    public function all_roles_can_view_movement_details()
    {
        $movement = StockMovement::factory()->create([
            'product_id' => $this->product->id,
        ]);

        $this->actingAs($this->admin)
            ->get("/movements/{$movement->id}")
            ->assertStatus(200);

        $this->actingAs($this->gestionnaire)
            ->get("/movements/{$movement->id}")
            ->assertStatus(200);

        $this->actingAs($this->observateur)
            ->get("/movements/{$movement->id}")
            ->assertStatus(200);
    }

    /** @test */
    public function admin_can_access_alerts_destroy()
    {
        $this->actingAs($this->admin)
            ->delete('/alerts/1')
            ->assertStatus(302); // Will redirect even if alert doesn't exist
    }

    /** @test */
    public function gestionnaire_can_access_alerts_destroy()
    {
        $this->actingAs($this->gestionnaire)
            ->delete('/alerts/1')
            ->assertStatus(302);
    }

    /** @test */
    public function observateur_cannot_destroy_alerts()
    {
        $this->actingAs($this->observateur)
            ->delete('/alerts/1')
            ->assertStatus(403);
    }

    /** @test */
    public function unauthenticated_user_is_redirected_to_login()
    {
        $this->get('/products/create')
            ->assertStatus(302)
            ->assertRedirect('/login');
    }

    /** @test */
    public function observateur_sees_read_only_message_on_products_page()
    {
        $this->actingAs($this->observateur)
            ->get('/products')
            ->assertSee('Mode lecture seule');
    }

    /** @test */
    public function admin_does_not_see_read_only_message()
    {
        $this->actingAs($this->admin)
            ->get('/products')
            ->assertDontSee('Mode lecture seule');
    }

    /** @test */
    public function gestionnaire_does_not_see_read_only_message()
    {
        $this->actingAs($this->gestionnaire)
            ->get('/products')
            ->assertDontSee('Mode lecture seule');
    }
}
