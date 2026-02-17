<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\StockAlert;
use App\Services\ExpirationAlertService;
use Carbon\Carbon;
use Tests\TestCase;

class ExpirationAlertTest extends TestCase
{
    protected ExpirationAlertService $expirationService;

    public function setUp(): void
    {
        parent::setUp();
        $this->expirationService = new ExpirationAlertService();
    }

    /** @test */
    public function test_product_with_future_expiration_is_not_expired()
    {
        $product = Product::factory()->create([
            'name' => 'Produit Frais',
            'expiration_date' => Carbon::now()->addDays(30),
        ]);

        $this->assertFalse($product->isExpired());
    }

    /** @test */
    public function test_product_with_past_expiration_is_expired()
    {
        $product = Product::factory()->create([
            'name' => 'Produit Expiré',
            'expiration_date' => Carbon::now()->subDays(5),
        ]);

        $this->assertTrue($product->isExpired());
    }

    /** @test */
    public function test_product_without_expiration_is_not_expired()
    {
        $product = Product::factory()->create([
            'name' => 'Produit Sans Expiration',
            'expiration_date' => null,
        ]);

        $this->assertFalse($product->isExpired());
    }

    /** @test */
    public function test_product_expiring_within_30_days()
    {
        $product = Product::factory()->create([
            'expiration_date' => Carbon::now()->addDays(15),
        ]);

        $this->assertTrue($product->isExpiringWithin(30));
        $this->assertTrue($product->isExpiringWithin(20));
        $this->assertFalse($product->isExpiringWithin(10));
    }

    /** @test */
    public function test_product_without_expiration_is_not_expiring_within()
    {
        $product = Product::factory()->create([
            'expiration_date' => null,
        ]);

        $this->assertFalse($product->isExpiringWithin(30));
    }

    /** @test */
    public function test_days_until_expiration_calculation()
    {
        $product = Product::factory()->create([
            'expiration_date' => Carbon::now()->addDays(10),
        ]);

        $daysLeft = $product->daysUntilExpiration();
        // Allowance pour timing - daysLeft peut être 9 ou 10 dépendant du moment d'exécution
        $this->assertGreaterThanOrEqual(9, $daysLeft);
        $this->assertLessThanOrEqual(10, $daysLeft);
    }

    /** @test */
    public function test_scope_expired_returns_only_expired_products()
    {
        // Créer un produit expiré
        Product::factory()->create([
            'expiration_date' => Carbon::now()->subDays(5),
        ]);

        // Créer un produit frais
        Product::factory()->create([
            'expiration_date' => Carbon::now()->addDays(30),
        ]);

        // Créer un produit sans expiration
        Product::factory()->create([
            'expiration_date' => null,
        ]);

        $expired = Product::expired()->get();

        $this->assertEquals(1, $expired->count());
        $this->assertTrue($expired->first()->isExpired());
    }

    /** @test */
    public function test_scope_expiring_within_returns_products_expiring_soon()
    {
        // Créer un produit expirant dans 15 jours
        Product::factory()->create([
            'expiration_date' => Carbon::now()->addDays(15),
        ]);

        // Créer un produit déjà expiré
        Product::factory()->create([
            'expiration_date' => Carbon::now()->subDays(5),
        ]);

        // Créer un produit expirant dans 60 jours
        Product::factory()->create([
            'expiration_date' => Carbon::now()->addDays(60),
        ]);

        $expiring = Product::expiringWithin(30)->get();

        $this->assertEquals(1, $expiring->count());
    }

    /** @test */
    public function test_expiration_alert_service_creates_alert_for_expiring_product()
    {
        // Créer un produit expirant dans 20 jours
        $product = Product::factory()->create([
            'expiration_date' => Carbon::now()->addDays(20),
        ]);

        // Vérifier qu'aucune alerte n'existe
        $this->assertEquals(0, StockAlert::count());

        // Lancer le service
        $this->expirationService->checkAndGenerateExpirationAlerts();

        // Vérifier qu'une alerte a été créée
        $alert = StockAlert::where('product_id', $product->id)
            ->where('alert_type', 'expiration')
            ->first();

        $this->assertNotNull($alert);
        $this->assertStringContainsString('expire', $alert->message);
    }

    /** @test */
    public function test_expiration_alert_service_creates_alert_for_expired_product()
    {
        // Créer un produit déjà expiré
        $product = Product::factory()->create([
            'expiration_date' => Carbon::now()->subDays(5),
        ]);

        // Lancer le service
        $this->expirationService->checkAndGenerateExpirationAlerts();

        // Vérifier qu'une alerte a été créée
        $alert = StockAlert::where('product_id', $product->id)
            ->where('alert_type', 'expiration')
            ->first();

        $this->assertNotNull($alert);
        $this->assertStringContainsString('EXPIRÉ', $alert->message);
    }

    /** @test */
    public function test_expiration_alert_not_duplicated()
    {
        // Créer un produit expirant
        $product = Product::factory()->create([
            'expiration_date' => Carbon::now()->addDays(20),
        ]);

        // Vérifier qu'aucune alerte n'existe initialement
        $this->assertEquals(0, StockAlert::where('product_id', $product->id)->count());

        // Lancer le service une fois
        \Log::info("=== FIRST SERVICE CALL ===");
        $this->expirationService->checkAndGenerateExpirationAlerts();
        
        // Compter les alertes créées
        $alertsAfterFirst = StockAlert::where('product_id', $product->id)
            ->where('alert_type', 'expiration')
            ->get();
        
        \Log::info("After first call, alerts count: {$alertsAfterFirst->count()}");
        foreach ($alertsAfterFirst as $alert) {
            \Log::info("Alert ID: {$alert->id}, message: {$alert->message}");
        }
        
        $this->assertEquals(1, $alertsAfterFirst->count());

        // Lancer le service une deuxième fois (ne devrait pas créer de doublon)
        \Log::info("=== SECOND SERVICE CALL ===");
        $this->expirationService->checkAndGenerateExpirationAlerts();

        // Vérifier qu'il y a toujours qu'une seule alerte (pas de doublon)
        $alertsAfterSecond = StockAlert::where('product_id', $product->id)
            ->where('alert_type', 'expiration')
            ->get();

        \Log::info("After second call, alerts count: {$alertsAfterSecond->count()}");
        foreach ($alertsAfterSecond as $alert) {
            \Log::info("Alert ID: {$alert->id}, message: {$alert->message}");
        }

        $this->assertEquals(1, $alertsAfterSecond->count());
    }

    /** @test */
    public function test_scope_without_expiration()
    {
        // Créer 3 produits sans expiration
        Product::factory(3)->create(['expiration_date' => null]);

        // Créer 2 produits avec expiration
        Product::factory(2)->create(['expiration_date' => Carbon::now()->addDays(30)]);

        $withoutExpiration = Product::withoutExpiration()->count();
        $this->assertEquals(3, $withoutExpiration);
    }

    /** @test */
    public function test_scope_with_expiration()
    {
        // Créer 3 produits sans expiration
        Product::factory(3)->create(['expiration_date' => null]);

        // Créer 2 produits avec expiration
        Product::factory(2)->create(['expiration_date' => Carbon::now()->addDays(30)]);

        $withExpiration = Product::withExpiration()->count();
        $this->assertEquals(2, $withExpiration);
    }
}
