<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-3xl text-gray-900 leading-tight">Gestion des Commandes Fournisseurs</h2>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8 bg-transparent min-h-screen">
        <div class="max-w-7xl mx-auto bg-white rounded-lg shadow p-6">
            <div class="mb-4">
                <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 hover:underline">← Retour</a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">ID</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Produit</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Quantité</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Statut</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Créée par</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($orders as $order)
                            <tr>
                                <td class="px-4 py-2 text-sm text-gray-700">{{ $order->id }}</td>
                                <td class="px-4 py-2 text-sm text-gray-900">{{ $order->product?->name }}</td>
                                <td class="px-4 py-2 text-sm text-gray-800">{{ $order->quantity }}</td>
                                <td class="px-4 py-2 text-sm text-gray-800">{{ ucfirst($order->status) }}</td>
                                <td class="px-4 py-2 text-sm text-gray-800">{{ $order->user?->name ?? '—' }}</td>
                                <td class="px-4 py-2 text-sm">
                                    <a href="{{ route('purchase_orders.show', $order->id) }}" class="text-emerald-600 hover:underline">Voir</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
