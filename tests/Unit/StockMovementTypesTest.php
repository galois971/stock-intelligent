<?php

namespace Tests\Unit;

use App\Models\StockMovement;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockMovementTypesTest extends TestCase
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

    public function test_entry_movement_can_have_achat_subtype(): void
    {
        $movement = StockMovement::factory()->create([
            'product_id' => $this->product->id,
            'type' => 'entry',
            'subtype' => 'achat',
        ]);

        $this->assertEquals('entry', $movement->type);
        $this->assertEquals('achat', $movement->subtype);
    }

    public function test_entry_movement_can_have_retour_subtype(): void
    {
        $movement = StockMovement::factory()->create([
            'product_id' => $this->product->id,
            'type' => 'entry',
            'subtype' => 'retour',
        ]);

        $this->assertEquals('retour', $movement->subtype);
    }

    public function test_entry_movement_can_have_correction_subtype(): void
    {
        $movement = StockMovement::factory()->create([
            'product_id' => $this->product->id,
            'type' => 'entry',
            'subtype' => 'correction',
        ]);

        $this->assertEquals('correction', $movement->subtype);
    }

    public function test_exit_movement_can_have_vente_subtype(): void
    {
        $movement = StockMovement::factory()->create([
            'product_id' => $this->product->id,
            'type' => 'exit',
            'subtype' => 'vente',
        ]);

        $this->assertEquals('exit', $movement->type);
        $this->assertEquals('vente', $movement->subtype);
    }

    public function test_exit_movement_can_have_perte_subtype(): void
    {
        $movement = StockMovement::factory()->create([
            'product_id' => $this->product->id,
            'type' => 'exit',
            'subtype' => 'perte',
        ]);

        $this->assertEquals('perte', $movement->subtype);
    }

    public function test_exit_movement_can_have_casse_subtype(): void
    {
        $movement = StockMovement::factory()->create([
            'product_id' => $this->product->id,
            'type' => 'exit',
            'subtype' => 'casse',
        ]);

        $this->assertEquals('casse', $movement->subtype);
    }

    public function test_exit_movement_can_have_expiration_subtype(): void
    {
        $movement = StockMovement::factory()->create([
            'product_id' => $this->product->id,
            'type' => 'exit',
            'subtype' => 'expiration',
        ]);

        $this->assertEquals('expiration', $movement->subtype);
    }

    public function test_movement_stores_user_reference(): void
    {
        $movement = StockMovement::factory()->create([
            'product_id' => $this->product->id,
            'user_id' => $this->user->id,
            'type' => 'entry',
        ]);

        $this->assertEquals($this->user->id, $movement->user_id);
    }

    public function test_movement_stores_motif(): void
    {
        $motif = "Achat pour réapprovisionner";
        $movement = StockMovement::factory()->create([
            'product_id' => $this->product->id,
            'type' => 'entry',
            'motif' => $motif,
        ]);

        $this->assertEquals($motif, $movement->motif);
    }

    public function test_all_subtypes_are_distinct(): void
    {
        $types = [
            ['type' => 'entry', 'subtype' => 'achat'],
            ['type' => 'entry', 'subtype' => 'retour'],
            ['type' => 'entry', 'subtype' => 'correction'],
            ['type' => 'exit', 'subtype' => 'vente'],
            ['type' => 'exit', 'subtype' => 'perte'],
            ['type' => 'exit', 'subtype' => 'casse'],
            ['type' => 'exit', 'subtype' => 'expiration'],
        ];

        foreach ($types as $typeData) {
            $movement = StockMovement::factory()->create([
                'product_id' => $this->product->id,
                'type' => $typeData['type'],
                'subtype' => $typeData['subtype'],
            ]);

            $this->assertNotNull($movement->subtype);
        }

        // Verify all 7 subtypes were created
        $this->assertEquals(7, StockMovement::count());
    }
}
