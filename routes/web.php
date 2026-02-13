<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\StockAlertController;
use App\Http\Controllers\PredictionController;
use App\Http\Controllers\ForecastController;
use App\Http\Controllers\Auth\JWTAuthController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\ProfileController;

// Authentication Routes
Route::middleware('guest')->group(function () {
	Route::get('register', [RegisteredUserController::class, 'create'])
		->name('register');

	Route::post('register', [RegisteredUserController::class, 'store']);

	Route::get('login', [AuthenticatedSessionController::class, 'create'])
		->name('login');

	Route::post('login', [AuthenticatedSessionController::class, 'store']);

	Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
		->name('password.request');

	Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
		->name('password.email');

	Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
		->name('password.reset');

	Route::post('reset-password', [NewPasswordController::class, 'store'])
		->name('password.store');
});

Route::middleware('auth')->group(function () {
	Route::get('verify-email', [EmailVerificationPromptController::class, '__invoke'])
		->name('verification.notice');

	Route::get('verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
		->middleware(['signed', 'throttle:6,1'])
		->name('verification.verify');

	Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
		->middleware('throttle:6,1')
		->name('verification.send');

	Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
		->name('password.confirm');

	Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

	Route::put('password', [PasswordController::class, 'update'])->name('password.update');

	Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
		->name('logout');
});

/* Basic web routes/resources */
Route::get('/', function () {
	return redirect()->route('products.index');
});

// Dashboard route
Route::middleware('auth')->get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Protected routes requiring authentication
Route::middleware('auth')->group(function () {
	Route::resource('products', ProductController::class);

	// Product import (web UI) - uses session auth and role check
	Route::middleware('role:admin,gestionnaire')->group(function () {
		Route::get('products/import', [\App\Http\Controllers\ProductImportController::class, 'show'])->name('products.import.show');
		Route::post('products/import', [\App\Http\Controllers\ProductImportController::class, 'import'])->name('products.import');
		Route::get('products/import/status/{importJob}', [\App\Http\Controllers\ProductImportController::class, 'statusPage'])->name('products.import.status');
		Route::get('products/import/status/{importJob}/json', [\App\Http\Controllers\ProductImportController::class, 'statusJson'])->name('products.import.status.json');
	});

	// Movement import (web UI)
	Route::middleware('role:admin,gestionnaire')->group(function () {
		Route::get('movements/import', [\App\Http\Controllers\StockMovementImportController::class, 'show'])->name('movements.import.show');
		Route::post('movements/import', [\App\Http\Controllers\StockMovementImportController::class, 'import'])->name('movements.import');
		Route::get('movements/import/status/{importJob}', [\App\Http\Controllers\StockMovementImportController::class, 'statusPage'])->name('movements.import.status');
		Route::get('movements/import/status/{importJob}/json', [\App\Http\Controllers\StockMovementImportController::class, 'statusJson'])->name('movements.import.status.json');
	});
	// Exports for products
	Route::get('products/export/excel', [ProductController::class, 'exportExcel'])->name('products.export.excel');
	Route::get('products/export/pdf', [ProductController::class, 'exportPdf'])->name('products.export.pdf');
	Route::get('products/{product}/export/pdf', [ProductController::class, 'exportProductPdf'])->name('products.export.product_pdf');

	Route::resource('categories', CategoryController::class);
	Route::resource('movements', StockMovementController::class);
	// Exports for movements
	Route::get('movements/export/excel', [StockMovementController::class, 'exportExcel'])->name('movements.export.excel');
	Route::get('movements/export/pdf', [StockMovementController::class, 'exportPdf'])->name('movements.export.pdf');

	Route::resource('inventories', InventoryController::class);
	// Exports for inventories
	Route::get('inventories/export/excel', [InventoryController::class, 'exportExcel'])->name('inventories.export.excel');
	Route::get('inventories/export/pdf', [InventoryController::class, 'exportPdf'])->name('inventories.export.pdf');

	Route::resource('alerts', StockAlertController::class)->only(['index', 'show', 'destroy']);

	// JSON endpoint used by dashboard to fetch live counters
	Route::get('dashboard/counts', function () {
		return response()->json([
			'products' => \App\Models\Product::count(),
			'categories' => \App\Models\Category::count(),
			'movements' => \App\Models\StockMovement::count(),
			'inventories' => \App\Models\Inventory::count(),
			'alerts' => \App\Models\StockAlert::where('resolved', false)->count(),
		]);
	})->name('dashboard.counts');

	// Profile routes
	Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
	Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
	Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

		// Web UI for forecasts (stored results)
		Route::get('forecasts/{forecast}/show', [\App\Http\Controllers\ForecastController::class, 'view'])->name('forecasts.show');

			// Purchase orders
			Route::post('purchase-orders', [\App\Http\Controllers\PurchaseOrderController::class, 'store'])->name('purchase_orders.store');

			// Admin purchase orders
			Route::middleware('role:admin,gestionnaire')->group(function () {
				Route::get('purchase-orders', [\App\Http\Controllers\PurchaseOrderController::class, 'index'])->name('purchase_orders.index');
				Route::get('purchase-orders/{purchaseOrder}', [\App\Http\Controllers\PurchaseOrderController::class, 'show'])->name('purchase_orders.show');
				Route::put('purchase-orders/{purchaseOrder}/status', [\App\Http\Controllers\PurchaseOrderController::class, 'updateStatus'])->name('purchase_orders.update_status');
			});
});

/* Simple API endpoint for prediction (versioned) */
Route::prefix('api/v1')->group(function () {
	Route::post('login', [JWTAuthController::class, 'login']);

	// Protected API routes (require JWT)
	Route::middleware('auth.jwt')->group(function () {
		// Forecasts listing (stored results)
		Route::get('forecasts', [ForecastController::class, 'index']);
		Route::get('forecasts/{forecast}', [ForecastController::class, 'show']);
		Route::get('prediction/{product}', [PredictionController::class, 'predict']);
		Route::apiResource('products', ProductController::class)->only(['index','show']);
		Route::apiResource('movements', StockMovementController::class)->only(['index','show']);
		Route::apiResource('inventories', InventoryController::class)->only(['index','show']);
		Route::get('me', [JWTAuthController::class, 'me']);

		// Routes requiring gestionnaire or admin for write operations
		Route::middleware('role:admin,gestionnaire')->group(function () {
			Route::post('products', [ProductController::class, 'store']);
			Route::put('products/{product}', [ProductController::class, 'update']);
			Route::delete('products/{product}', [ProductController::class, 'destroy']);

			Route::post('categories', [CategoryController::class, 'store']);
			Route::put('categories/{category}', [CategoryController::class, 'update']);
			Route::delete('categories/{category}', [CategoryController::class, 'destroy']);

			Route::post('inventories', [InventoryController::class, 'store']);
			Route::delete('inventories/{inventory}', [InventoryController::class, 'destroy']);
		});

		// Product import (CSV/Excel)
		Route::middleware('role:admin,gestionnaire')->group(function () {
			Route::get('products/import', [\App\Http\Controllers\ProductImportController::class, 'show'])->name('products.import.show');
			Route::post('products/import', [\App\Http\Controllers\ProductImportController::class, 'import'])->name('products.import');
		});

		// Import job status page
		Route::middleware('role:admin,gestionnaire')->get('products/import/status/{importJob}', [\App\Http\Controllers\ProductImportController::class, 'statusPage'])->name('products.import.status');
		Route::middleware('role:admin,gestionnaire')->get('products/import/status/{importJob}/json', [\App\Http\Controllers\ProductImportController::class, 'statusJson'])->name('products.import.status.json');

		// Movements can be created by gestionnaire or admin
		Route::middleware('role:admin,gestionnaire')->group(function () {
			Route::post('movements', [StockMovementController::class, 'store']);
			Route::delete('movements/{movement}', [StockMovementController::class, 'destroy']);
		});
	});
});

