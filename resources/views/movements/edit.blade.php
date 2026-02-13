@extends('layouts.app')

@section('content')
<div>
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Modifier le Mouvement</h1>
            <p class="mt-2 text-gray-600">{{ $movement->product->name }}</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow overflow-hidden border border-gray-200 max-w-3xl">
        <div class="p-6">
            @if($errors->any())
                <div class="mb-6 bg-red-100 border-l-4 border-red-500 p-4 rounded-lg">
                    <p class="text-red-700 font-semibold mb-2">Erreurs de validation:</p>
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach($errors->all() as $error)
                            <li class="text-red-700 text-sm">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('movements.update', $movement->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Produit</label>
                    <select name="product_id" class="w-full border border-gray-300 bg-white text-gray-900 p-3 rounded-lg shadow-sm focus:shadow-md transition-shadow focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" required>
                        <option value="">-- Choisir --</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}" {{ old('product_id', $movement->product_id) == $p->id ? 'selected' : '' }}>{{ $p->name }} ({{ $p->barcode }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Type</label>
                        <select name="type" id="type" class="w-full border border-gray-300 bg-white text-gray-900 p-3 rounded-lg shadow-sm focus:shadow-md transition-shadow focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" required onchange="updateSubtypes()">
                            <option value="entry" {{ old('type', $movement->type) == 'entry' ? 'selected' : '' }}>Entrée</option>
                            <option value="exit" {{ old('type', $movement->type) == 'exit' ? 'selected' : '' }}>Sortie</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Sous-type</label>
                        <select name="subtype" id="subtype" class="w-full border border-gray-300 bg-white text-gray-900 p-3 rounded-lg shadow-sm focus:shadow-md transition-shadow focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                            <option value="">-- Choisir --</option>
                            <option value="achat" class="subtype-entry" {{ old('subtype', $movement->subtype) == 'achat' ? 'selected' : '' }}>Achat</option>
                            <option value="retour" class="subtype-entry" {{ old('subtype', $movement->subtype) == 'retour' ? 'selected' : '' }}>Retour</option>
                            <option value="correction" class="subtype-entry" {{ old('subtype', $movement->subtype) == 'correction' ? 'selected' : '' }}>Correction</option>
                            <option value="vente" class="subtype-exit" {{ old('subtype', $movement->subtype) == 'vente' ? 'selected' : '' }}>Vente</option>
                            <option value="perte" class="subtype-exit" {{ old('subtype', $movement->subtype) == 'perte' ? 'selected' : '' }}>Perte</option>
                            <option value="casse" class="subtype-exit" {{ old('subtype', $movement->subtype) == 'casse' ? 'selected' : '' }}>Casse</option>
                            <option value="expiration" class="subtype-exit" {{ old('subtype', $movement->subtype) == 'expiration' ? 'selected' : '' }}>Expiration</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Quantité</label>
                        <input type="number" name="quantity" min="1" value="{{ old('quantity', $movement->quantity) }}" class="w-full border border-gray-300 bg-white text-gray-900 p-3 rounded-lg shadow-sm focus:shadow-md transition-shadow focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" required>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Date</label>
                    <input type="date" name="movement_date" value="{{ old('movement_date', $movement->movement_date ? $movement->movement_date->format('Y-m-d') : date('Y-m-d')) }}" class="w-full border border-gray-300 bg-white text-gray-900 p-3 rounded-lg shadow-sm focus:shadow-md transition-shadow focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Motif / Raison du mouvement</label>
                    <textarea name="motif" rows="3" placeholder="Expliquez la raison de ce mouvement..." class="w-full border border-gray-300 bg-white text-gray-900 p-3 rounded-lg shadow-sm focus:shadow-md transition-shadow focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">{{ old('motif', $movement->motif) }}</textarea>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Référence / Fournisseur / Client (optionnel)</label>
                    <input type="text" name="reference" placeholder="Référence" value="{{ old('reference', $movement->reference) }}" class="w-full border border-gray-300 bg-white text-gray-900 p-3 rounded-lg shadow-sm focus:shadow-md transition-shadow focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 mb-3">
                    <input type="text" name="supplier" placeholder="Fournisseur" value="{{ old('supplier', $movement->supplier) }}" class="w-full border border-gray-300 bg-white text-gray-900 p-3 rounded-lg shadow-sm focus:shadow-md transition-shadow focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 mb-3">
                    <input type="text" name="customer" placeholder="Client" value="{{ old('customer', $movement->customer) }}" class="w-full border border-gray-300 bg-white text-gray-900 p-3 rounded-lg shadow-sm focus:shadow-md transition-shadow focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                </div>

                <div class="flex gap-4 pt-4">
                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-emerald-600 text-white shadow-md hover:shadow-lg hover:bg-emerald-700 font-medium rounded-lg transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Mettre à jour
                    </button>
                    <a href="{{ route('movements.index') }}" class="inline-flex items-center px-6 py-3 bg-gray-700 text-gray-900 font-medium rounded-lg hover:bg-gray-600 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function updateSubtypes() {
    const typeSelect = document.getElementById('type');
    const subtypeSelect = document.getElementById('subtype');
    const type = typeSelect.value;

    // Hide all options first
    Array.from(subtypeSelect.options).forEach(option => {
        if (option.value === '') return;
        option.style.display = 'none';
    });

    // Show relevant options
    if (type === 'entry') {
        document.querySelectorAll('.subtype-entry').forEach(option => {
            option.style.display = 'block';
        });
    } else if (type === 'exit') {
        document.querySelectorAll('.subtype-exit').forEach(option => {
            option.style.display = 'block';
        });
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', updateSubtypes);
</script>
@endsection
