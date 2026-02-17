<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\Auth;
use App\Services\SupplierIntegrationService;
use Illuminate\Http\Response;

class PurchaseOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a newly created purchase order (AJAX)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id'   => 'required|integer|exists:products,id',
            'forecast_id'  => 'nullable|integer|exists:forecasts,id',
            'quantity'     => 'required|integer|min:1',
            'notes'        => 'nullable|string',
        ]);

        $data['user_id'] = Auth::id();

        $po = PurchaseOrder::create($data);

        // Envoi au fournisseur
        $svc    = app(SupplierIntegrationService::class);
        $result = $svc->sendOrder($po);

        $note = "Supplier send: status=" . ($result['status'] ?? 0)
              . " success=" . ($result['success'] ? '1' : '0')
              . " body=" . substr((string)($result['body'] ?? ''), 0, 1000);

        $po->notes = trim(($po->notes ? $po->notes . "\n" : '') . $note);
        if ($result['success']) {
            $po->status = 'sent';
        }
        $po->save();

        return response()->json([
            'success'        => true,
            'purchase_order' => $po,
            'supplier'       => $result
        ], 201);
    }

    /**
     * Admin index - list purchase orders
     */
    public function index(Request $request)
    {
        $orders = PurchaseOrder::with(['product', 'forecast', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        return view('purchase_orders.index', compact('orders'));
    }

    /**
     * Show purchase order
     */
    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['product', 'forecast', 'user']);
        return view('purchase_orders.show', compact('purchaseOrder'));
    }

    /**
     * Update status (AJAX)
     */
    public function updateStatus(Request $request, PurchaseOrder $purchaseOrder)
    {
        $this->validate($request, [
            'status' => 'required|string|in:pending,approved,completed,cancelled'
        ]);

        $purchaseOrder->status = $request->input('status');
        $purchaseOrder->save();

        // Si approuvé, envoyer au fournisseur
        if ($purchaseOrder->status === 'approved') {
            $svc    = app(SupplierIntegrationService::class);
            $result = $svc->sendOrder($purchaseOrder);

            $note = "Supplier send on approval: status=" . ($result['status'] ?? 0)
                  . " success=" . ($result['success'] ? '1' : '0')
                  . " body=" . substr((string)($result['body'] ?? ''), 0, 1000);

            $purchaseOrder->notes = trim(($purchaseOrder->notes ? $purchaseOrder->notes . "\n" : '') . $note);
            if ($result['success']) {
                $purchaseOrder->status = 'sent';
            }
            $purchaseOrder->save();
        }

        return response()->json([
            'success' => true,
            'status'  => $purchaseOrder->status
        ]);
    }
}