<?php

namespace Tests\Unit;

use App\Models\StockMovement;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockMovementValidationTest extends TestCase
{
    use RefreshDatabase;

    private Product $product;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->product = Product::factory()->create();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function entry_movement_can_have_achat_subtype()
    {
        $movement = StockMovement::factory()->create([
            'product_id' => $this->product->id,
            'user_id' => $this->user->id,
            'type' => 'entry',
            'subtype' => 'achat',
            'quantity' => 10,
        ]);

        $this->assertEquals('entry', $movement->type);
        $this->assertEquals('achat', $movement->subtype);
    }

    /** @test */
    public function entry_movement_can_have_retour_subtype()
    {
        $movement = StockMovement::factory()->create([
            'product_id' => $this->product->id,
            'type' => 'entry',
            'subtype' => 'retour',
            'quantity' => 5,
        ]);

        $this->assertEquals('retour', $movement->subtype);
    }

    /** @test */
    public function entry_movement_can_have_correction_subtype()
    {
        $movement = StockMovement::factory()->create([
            'product_id' => $this->product->id,
            'type' => 'entry',
            'subtype' => 'correction',
            'quantity' => 2,
        ]);

        $this->assertEquals('correction', $movement->subtype);
    }

    /** @test */
    public function exit_movement_can_have_vente_subtype()
    {
        $movement = StockMovement::factory()->create([
            'product_id' => $this->product->id,
            'type' => 'exit',
            'subtype' => 'vente',
            'quantity' => 15,
        ]);

        $this->assertEquals('exit', $movement->type);
        $this->assertEquals('vente', $movement->subtype);
    }

    /** @test */
    public function exit_movement_can_have_perte_subtype()
    {
        $movement = StockMovement::factory()->create([
            'product_id' => $this->product->id,
            'type' => 'exit',
            'subtype' => 'perte',
            'quantity' => 3,
        ]);

        $this->assertEquals('perte', $movement->subtype);
    }

    /** @test */
    public function exit_movement_can_have_casse_subtype()
    {
        $movement = StockMovement::factory()->create([
            'product_id' => $this->product->id,
            'type' => 'exit',
            'subtype' => 'casse',
            'quantity' => 2,
        ]);

        $this->assertEquals('casse', $movement->subtype);
    }

    /** @test */
    public function exit_movement_can_have_expiration_subtype()
    {
        $movement = StockMovement::factory()->create([
            'product_id' => $this->product->id,
            'type' => 'exit',
            'subtype' => 'expiration',
            'quantity' => 1,
        ]);

        $this->assertEquals('expiration', $movement->subtype);
    }

    /** @test */
    public function movement_stores_user_reference()
    {
        $movement = StockMovement::factory()->create([
            'product_id' => $this->product->id,
            'user_id' => $this->user->id,
            'type' => 'entry',
            'subtype' => 'achat',
        ]);

        $this->assertEquals($this->user->id, $movement->user_id);
        $this->assertEquals($this->user->name, $movement->user->name);
    }

    /** @test */
    public function movement_stores_motif()
    {
        $motif = "Achat pour réapprovisionner suite à vente importante";
        $movement = StockMovement::factory()->create([
            'product_id' => $this->product->id,
            'type' => 'entry',
            'subtype' => 'achat',
            'motif' => $motif,
        ]);

        $this->assertEquals($motif, $movement->motif);
    }

    /** @test */
    public function movement_stores_movement_date()
    {
        $date = Carbon::parse('2026-02-10');
        $movement = StockMovement::factory()->create([
            'product_id' => $this->product->id,
            'type' => 'exit',
            'subtype' => 'vente',
            'movement_date' => $date,
        ]);

        $this->assertTrue($movement->movement_date->isSameDay($date));
    }

    /** @test */
    public function movement_needs_valid_type()
    {
        // Should fail with invalid type
        $this->expectExceptionMessage('type');

        StockMovement::factory()->create([
            'product_id' => $this->product->id,
            'type' => 'invalid_type',
            'subtype' => 'achat',
        ]);
    }

    /** @test */
    public function movement_tracks_quantity()
    {
        $movement = StockMovement::factory()->create([
            'product_id' => $this->product->id,
            'type' => 'entry',
            'quantity' => 100,
        ]);

        $this->assertEquals(100, $movement->quantity);
    }

    /** @test */
    public function multiple_subtypes_per_type_are_distinct()
    {
        $achat = StockMovement::factory()->create([
            'product_id' => $this->product->id,
            'type' => 'entry',
            'subtype' => 'achat',
        ]);

        $retour = StockMovement::factory()->create([
            'product_id' => $this->product->id,
            'type' => 'entry',
            'subtype' => 'retour',
        ]);

        $this->assertNotEquals($achat->subtype, $retour->subtype);
        $this->assertEquals($achat->type, $retour->type);
    }
}
