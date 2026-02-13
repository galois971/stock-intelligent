@extends('layouts.app')

@section('content')
<div class="bg-white shadow rounded p-6">
    <h2 class="text-2xl font-bold mb-4">Détails du produit</h2>

    <div class="mb-4">
        <strong>ID :</strong> {{ $product->id }}
    </div>

    <div class="mb-4">
        <strong>Nom :</strong> {{ $product->name }}
    </div>

    <div class="mb-4">
        <strong>Code-barres :</strong> {{ $product->barcode }}
    </div>

    <div class="mb-4">
        <strong>Prix :</strong> {{ $product->price }} FCFA
    </div>

    <div class="mb-4">
        <strong>Stock minimum :</strong> {{ $product->stock_min }}
    </div>

    <div class="mb-4">
        <strong>Stock optimal :</strong> {{ $product->stock_optimal }}
    </div>

    <div class="mb-4">
        <strong>Catégorie :</strong> {{ $product->category->name ?? '—' }}
    </div>

    <div class="flex space-x-4 mt-6">
        <a href="{{ route('products.edit', $product->id) }}" 
           class="bg-amber-600 text-white px-4 py-2 rounded hover:bg-amber-700">
            Modifier
        </a>
        <a href="{{ route('products.index') }}" 
           class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
            Retour à la liste
        </a>
    </div>
</div>
@endsection