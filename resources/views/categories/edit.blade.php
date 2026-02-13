@extends('layouts.app')

@section('content')
<div>
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Modifier la Catégorie</h1>
            <p class="mt-2 text-gray-600">{{ $category->name }}</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow overflow-hidden border border-gray-200 max-w-2xl">
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

            <form action="{{ route('categories.update', $category->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nom de la catégorie</label>
                    <input type="text" name="name" value="{{ old('name', $category->name) }}" class="w-full border border-gray-300 bg-white text-gray-900 p-3 rounded-lg shadow-sm focus:shadow-md transition-shadow focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" placeholder="Ex: Électronique, Vêtements..." required>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Catégorie parente (optionnel)</label>
                    <select name="parent_id" class="w-full border border-gray-300 bg-white text-gray-900 p-3 rounded-lg shadow-sm focus:shadow-md transition-shadow focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                        <option value="">-- Aucune (catégorie principale) --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('parent_id', $category->parent_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-600 mt-2">Laissez vide pour une catégorie principale, ou sélectionnez une catégorie pour en faire une sous-catégorie</p>
                </div>

                <div class="flex gap-4 pt-4">
                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-emerald-600 text-white shadow-md hover:shadow-lg hover:bg-emerald-700 font-medium rounded-lg transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Mettre à jour
                    </button>
                    <a href="{{ route('categories.index') }}" class="inline-flex items-center px-6 py-3 bg-gray-700 text-gray-900 font-medium rounded-lg hover:bg-gray-600 transition">
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
