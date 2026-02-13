@extends('layouts.app')

@section('content')
<div class="bg-white shadow rounded p-6 max-w-3xl mx-auto border border-gray-200">
    <h2 class="text-2xl font-bold mb-4 text-white">Créer un mouvement</h2>

    @if($errors->any())
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4 border border-red-500/30">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('movements.store') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label class="block mb-1 text-gray-700">Produit</label>
            <select name="product_id" class="w-full border border-gray-300 bg-white text-gray-900 p-2 rounded focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" required>
                <option value="">-- Choisir --</option>
                @foreach($products as $p)
                    <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->barcode }})</option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block mb-1 text-gray-700">Type</label>
                <select name="type" id="type" class="w-full border border-gray-300 bg-white text-gray-900 p-2 rounded focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" required onchange="updateSubtypes()">
                    <option value="entry">Entrée</option>
                    <option value="exit">Sortie</option>
                </select>
            </div>
            <div>
                <label class="block mb-1 text-gray-700">Sous-type</label>
                <select name="subtype" id="subtype" class="w-full border border-gray-300 bg-white text-gray-900 p-2 rounded focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                    <option value="">-- Choisir --</option>
                    <option value="achat" class="subtype-entry">Achat</option>
                    <option value="retour" class="subtype-entry">Retour</option>
                    <option value="correction" class="subtype-entry">Correction</option>
                    <option value="vente" class="subtype-exit">Vente</option>
                    <option value="perte" class="subtype-exit">Perte</option>
                    <option value="casse" class="subtype-exit">Casse</option>
                    <option value="expiration" class="subtype-exit">Expiration</option>
                </select>
            </div>
            <div>
                <label class="block mb-1 text-gray-700">Quantité</label>
                <input type="number" name="quantity" min="1" class="w-full border border-gray-300 bg-white text-gray-900 p-2 rounded focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" required>
            </div>
        </div>

        <div class="mb-4">
            <label class="block mb-1 text-gray-700">Date</label>
            <input type="date" name="movement_date" value="{{ date('Y-m-d') }}" class="w-full border border-gray-300 bg-white text-gray-900 p-2 rounded focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
        </div>

        <div class="mb-4">
            <label class="block mb-1 text-gray-700">Motif / Raison du mouvement</label>
            <textarea name="motif" rows="3" placeholder="Expliquez la raison de ce mouvement..." class="w-full border border-gray-300 bg-white text-gray-900 p-2 rounded focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"></textarea>
        </div>

        <div class="mb-4">
            <label class="block mb-1 text-gray-700">Référence / Fournisseur / Client (optionnel)</label>
            <input type="text" name="reference" placeholder="Référence" class="w-full border border-gray-300 bg-white text-gray-900 p-2 rounded focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 mb-2">
            <input type="text" name="supplier" placeholder="Fournisseur" class="w-full border border-gray-300 bg-white text-gray-900 p-2 rounded focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 mb-2">
            <input type="text" name="customer" placeholder="Client" class="w-full border border-gray-300 bg-white text-gray-900 p-2 rounded focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
        </div>

        <div class="flex space-x-2 mt-6">
            <button type="submit" class="inline-flex items-center px-6 py-3 bg-teal-600 text-white font-medium rounded-lg hover:bg-teal-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Enregistrer
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

<script>
function updateSubtypes() {
    const type = document.getElementById('type').value;
    const subtypeSelect = document.getElementById('subtype');
    const options = subtypeSelect.querySelectorAll('option');
    
    // Réinitialiser
    subtypeSelect.value = '';
    
    // Afficher/masquer les options selon le type
    options.forEach(option => {
        if (option.value === '') {
            option.style.display = 'block';
            return;
        }
        
        if (type === 'entry' && option.classList.contains('subtype-entry')) {
            option.style.display = 'block';
        } else if (type === 'exit' && option.classList.contains('subtype-exit')) {
            option.style.display = 'block';
        } else {
            option.style.display = 'none';
        }
    });
}

// Initialiser au chargement
document.addEventListener('DOMContentLoaded', updateSubtypes);
</script>
@endsection
