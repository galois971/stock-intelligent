@extends('layouts.app')

@section('content')
<div>
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Modifier le Produit</h1>
            <p class="mt-2 text-gray-600">{{ $product->name }}</p>
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

            <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nom du produit</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" class="w-full border border-gray-300 bg-white text-gray-900 p-3 rounded-lg shadow-sm focus:shadow-md transition-shadow focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" required>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Code-barres</label>
                    <input type="text" name="barcode" value="{{ old('barcode', $product->barcode) }}" class="w-full border border-gray-300 bg-white text-gray-900 p-3 rounded-lg shadow-sm focus:shadow-md transition-shadow focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" required>
                </div>

                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Prix (€)</label>
                        <input type="number" step="0.01" name="price" value="{{ old('price', $product->price) }}" class="w-full border border-gray-300 bg-white text-gray-900 p-3 rounded-lg shadow-sm focus:shadow-md transition-shadow focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Stock min</label>
                        <input type="number" name="stock_min" value="{{ old('stock_min', $product->stock_min) }}" class="w-full border border-gray-300 bg-white text-gray-900 p-3 rounded-lg shadow-sm focus:shadow-md transition-shadow focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Stock optimal</label>
                        <input type="number" name="stock_optimal" value="{{ old('stock_optimal', $product->stock_optimal) }}" class="w-full border border-gray-300 bg-white text-gray-900 p-3 rounded-lg shadow-sm focus:shadow-md transition-shadow focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" required>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Catégorie</label>
                    <select name="category_id" class="w-full border border-gray-300 bg-white text-gray-900 p-3 rounded-lg shadow-sm focus:shadow-md transition-shadow focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                        <option value="">-- Aucune --</option>
                        @foreach(App\Models\Category::all() as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Fiche technique (PDF/DOC)</label>
                    @if($product->technical_sheet)
                        <div class="mb-3 p-3 bg-white border border-gray-300 rounded-lg">
                            <p class="text-sm text-gray-600">Fichier actuel:</p>
                            <a href="{{ asset('storage/' . $product->technical_sheet) }}" target="_blank" class="text-emerald-600 hover:text-blue-300">Télécharger le fichier</a>
                        </div>
                    @endif
                    <input type="file" name="technical_sheet" accept=".pdf,.doc,.docx" class="w-full border border-gray-300 bg-white text-gray-700 p-3 rounded-lg file:border file:border-gray-300 file:bg-gray-700 file:text-gray-700 file:px-3 file:py-2 file:rounded-lg">
                </div>

                <div class="flex gap-4 pt-4">
                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-emerald-600 text-white shadow-md hover:shadow-lg hover:bg-emerald-700 font-medium rounded-lg transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Mettre à jour
                    </button>
                    <a href="{{ route('products.index') }}" class="inline-flex items-center px-6 py-3 bg-gray-700 text-gray-900 font-medium rounded-lg hover:bg-gray-600 transition">
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
@endsection
