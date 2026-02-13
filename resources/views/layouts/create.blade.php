@extends('layouts.app')

@section('content')
<div class="bg-white shadow rounded p-6">
    <h2 class="text-2xl font-bold mb-4">Ajouter une entrée d'inventaire</h2>

    @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('inventories.store') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label for="product_id" class="block font-semibold">Produit</label>
            <select name="product_id" id="product_id" class="w-full border rounded px-3 py-2" required>
                <option value="">-- Choisir un produit --</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" 
                        {{ old('product_id') == $product->id ? 'selected' : '' }}>
                        {{ $product->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label for="quantity" class="block font-semibold">Quantité</label>
            <input type="number" name="quantity" id="quantity" 
                   class="w-full border rounded px-3 py-2" 
                   value="{{ old('quantity') }}" required>
        </div>

        <div class="flex space-x-4">
            <button type="submit" class="bg-teal-600 text-white px-4 py-2 rounded hover:bg-teal-700">
                Enregistrer
            </button>
            <a href="{{ route('inventories.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                Annuler
            </a>
        </div>
    </form>
</div>
@endsection