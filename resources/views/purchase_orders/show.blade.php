<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-3xl text-gray-900 leading-tight">Commande fournisseur #{{ $purchaseOrder->id }}</h2>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8 bg-transparent min-h-screen">
        <div class="max-w-4xl mx-auto bg-white rounded-lg shadow p-6">
            <div class="mb-4">
                <a href="{{ route('purchase_orders.index') }}" class="text-sm text-gray-600 hover:underline">← Retour aux commandes</a>
            </div>

            <div class="mb-4">
                <p><strong>Produit:</strong> {{ $purchaseOrder->product?->name }}</p>
                <p><strong>Quantité:</strong> {{ $purchaseOrder->quantity }}</p>
                <p><strong>Statut:</strong> <span id="poStatusText">{{ ucfirst($purchaseOrder->status) }}</span></p>
                <p><strong>Créée par:</strong> {{ $purchaseOrder->user?->name ?? '—' }}</p>
                <p><strong>Notes:</strong> {{ $purchaseOrder->notes ?? '—' }}</p>
            </div>

            <div class="flex items-center gap-3">
                <select id="statusSelect" class="border rounded px-2 py-1">
                    <option value="pending" {{ $purchaseOrder->status == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ $purchaseOrder->status == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="completed" {{ $purchaseOrder->status == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ $purchaseOrder->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                <button id="updateStatusBtn" class="bg-emerald-600 text-white px-3 py-1 rounded">Mettre à jour</button>
                <span id="updateMsg" class="text-sm text-gray-600"></span>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('updateStatusBtn')?.addEventListener('click', async function(){
            const status = document.getElementById('statusSelect').value;
            const tokenMeta = document.querySelector('meta[name="csrf-token"]');
            const csrf = tokenMeta ? tokenMeta.getAttribute('content') : '';
            const id = {{ $purchaseOrder->id }};
            const res = await fetch("{{ route('purchase_orders.update_status', ['purchaseOrder' => $purchaseOrder->id]) }}", {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf
                },
                body: JSON.stringify({ status })
            });
            const txt = document.getElementById('updateMsg');
            if (!res.ok) { txt.innerText = 'Erreur'; return; }
            const data = await res.json();
            document.getElementById('poStatusText').innerText = data.status;
            txt.innerText = 'Mis à jour';
        });
    </script>
    @endpush
</x-app-layout>
