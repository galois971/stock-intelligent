@extends('layouts.app')

@section('content')
<div class="bg-white shadow rounded p-6 max-w-3xl mx-auto border border-gray-200">
    <h2 class="text-2xl font-bold mb-4">Détails du produit</h2>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <h3 class="font-semibold">Nom</h3>
            <p>{{ $product->name }}</p>
        </div>
        <div>
            <h3 class="font-semibold">Code-barres</h3>
            <p>{{ $product->barcode }}</p>
        </div>
        <div>
            <h3 class="font-semibold">Prix</h3>
            <p>{{ number_format($product->price,2,',',' ') }} €</p>
        </div>
        <div>
            <h3 class="font-semibold">Catégorie</h3>
            <p>{{ optional($product->category)->name }}</p>
        </div>
        <div>
            <h3 class="font-semibold">Stock courant</h3>
            <p>{{ $product->currentStock() }}</p>
        </div>
        <div>
            <h3 class="font-semibold">Seuils</h3>
            <p>Min: {{ $product->stock_min }} / Optimal: {{ $product->stock_optimal }}</p>
        </div>
    </div>

    @if($product->technical_sheet)
        <div class="mt-4">
            <a href="{{ asset('storage/' . $product->technical_sheet) }}" target="_blank" class="bg-emerald-600 text-white hover:bg-emerald-700 px-4 py-2 rounded">Télécharger la fiche technique</a>
        </div>
    @endif

    <div class="mt-6">
        <a href="{{ route('products.index') }}" class="bg-gray-300 px-4 py-2 rounded">Retour</a>
        <a href="{{ route('products.edit', $product->id) }}" class="bg-amber-600 text-white px-4 py-2 rounded">Modifier</a>
    </div>
</div>
@endsection
