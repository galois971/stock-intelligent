@extends('layouts.app')

@section('content')
<div class="bg-white shadow rounded p-6 max-w-3xl mx-auto border border-gray-200">
    <h2 class="text-2xl font-bold mb-4 text-white">Ajouter une entrée d'inventaire</h2>

    @if($errors->any())
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4 border border-red-500/30">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('inventories.store') }}" method="POST">
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

        <div class="mb-4">
            <label class="block mb-1 text-gray-700">Quantité constatée</label>
            <input type="number" name="quantity" class="w-full border border-gray-300 bg-white text-gray-900 p-2 rounded focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" required>
        </div>

        <div class="mb-4">
            <label class="block mb-1 text-gray-700">Date de l'inventaire</label>
            <input type="date" name="inventory_date" value="{{ date('Y-m-d') }}" class="w-full border border-gray-300 bg-white text-gray-900 p-2 rounded focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
        </div>

        <div class="mb-4">
            <label class="block mb-1 text-gray-700">Référence (optionnel)</label>
            <input type="text" name="reference" class="w-full border border-gray-300 bg-white text-gray-900 p-2 rounded focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
        </div>

        <div class="flex space-x-2 mt-6">
            <button type="submit" class="inline-flex items-center px-6 py-3 bg-teal-600 text-white font-medium rounded-lg hover:bg-teal-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Enregistrer
            </button>
            <a href="{{ route('inventories.index') }}" class="inline-flex items-center px-6 py-3 bg-gray-700 text-gray-900 font-medium rounded-lg hover:bg-gray-600 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Annuler
            </a>
        </div>
    </form>
</div>
@endsection
