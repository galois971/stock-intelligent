@extends('layouts.app')

@section('content')
<div class="bg-white shadow rounded p-6 max-w-3xl mx-auto border border-gray-200">
    <h2 class="text-2xl font-bold mb-6 text-white">Détails de la catégorie</h2>

    <div>
        <h3 class="font-semibold">Nom</h3>
        <p>{{ $category->name }}</p>
    </div>

    <div class="mt-4">
        <a href="{{ route('categories.index') }}" class="bg-gray-300 px-4 py-2 rounded">Retour</a>
        <a href="{{ route('categories.edit', $category->id) }}" class="bg-amber-600 text-white px-4 py-2 rounded">Modifier</a>
    </div>
</div>
@endsection
