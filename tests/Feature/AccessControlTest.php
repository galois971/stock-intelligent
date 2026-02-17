<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessControlTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $gestionnaire;
    private User $observateur;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->gestionnaire = User::factory()->create();
        $this->gestionnaire->assignRole('gestionnaire');

        $this->observateur = User::factory()->create();
        $this->observateur->assignRole('observateur');
    }

    public function test_all_roles_can_view_products(): void
    {
        $this->actingAs($this->admin)->get('/products')->assertStatus(200);
        $this->actingAs($this->gestionnaire)->get('/products')->assertStatus(200);
        $this->actingAs($this->observateur)->get('/products')->assertStatus(200);
    }

    public function test_admin_can_create_product(): void
    {
        $this->actingAs($this->admin)
            ->get('/products/create')
            ->assertStatus(200);
    }

    public function test_gestionnaire_can_create_product(): void
    {
        $this->actingAs($this->gestionnaire)
            ->get('/products/create')
            ->assertStatus(200);
    }

    public function test_observateur_cannot_create_product(): void
    {
        $this->actingAs($this->observateur)
            ->get('/products/create')
            ->assertStatus(403);
    }

    public function test_admin_can_edit_product(): void
    {
        $product = Product::factory()->create();
        
        $this->actingAs($this->admin)
            ->get("/products/{$product->id}/edit")
            ->assertStatus(200);
    }

    public function test_gestionnaire_can_edit_product(): void
    {
        $product = Product::factory()->create();
        
        $this->actingAs($this->gestionnaire)
            ->get("/products/{$product->id}/edit")
            ->assertStatus(200);
    }

    public function test_observateur_cannot_edit_product(): void
    {
        $product = Product::factory()->create();
        
        $this->actingAs($this->observateur)
            ->get("/products/{$product->id}/edit")
            ->assertStatus(403);
    }

    public function test_admin_can_delete_product(): void
    {
        $product = Product::factory()->create();

        $this->actingAs($this->admin)
            ->delete("/products/{$product->id}")
            ->assertStatus(302);
    }

    public function test_gestionnaire_can_delete_product(): void
    {
        $product = Product::factory()->create();

        $this->actingAs($this->gestionnaire)
            ->delete("/products/{$product->id}")
            ->assertStatus(302);
    }

    public function test_observateur_cannot_delete_product(): void
    {
        $product = Product::factory()->create();

        $this->actingAs($this->observateur)
            ->delete("/products/{$product->id}")
            ->assertStatus(403);
    }

    public function test_all_roles_can_view_movements(): void
    {
        $this->actingAs($this->admin)->get('/movements')->assertStatus(200);
        $this->actingAs($this->gestionnaire)->get('/movements')->assertStatus(200);
        $this->actingAs($this->observateur)->get('/movements')->assertStatus(200);
    }

    public function test_admin_can_create_movement(): void
    {
        $this->actingAs($this->admin)
            ->get('/movements/create')
            ->assertStatus(200);
    }

    public function test_gestionnaire_can_create_movement(): void
    {
        $this->actingAs($this->gestionnaire)
            ->get('/movements/create')
            ->assertStatus(200);
    }

    public function test_observateur_cannot_create_movement(): void
    {
        $this->actingAs($this->observateur)
            ->get('/movements/create')
            ->assertStatus(403);
    }

    public function test_unauthenticated_user_redirected_to_login(): void
    {
        $this->get('/products/create')
            ->assertStatus(302)
            ->assertRedirect('/login');
    }

    public function test_observateur_sees_read_only_message(): void
    {
        $response = $this->actingAs($this->observateur)->get('/products');
        
        $response->assertSee('Mode lecture seule');
    }

    public function test_admin_does_not_see_read_only_message(): void
    {
        $response = $this->actingAs($this->admin)->get('/products');
        
        $response->assertDontSee('Mode lecture seule');
    }
}
