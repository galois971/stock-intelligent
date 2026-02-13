@extends('layouts.app')

@section('content')
<div>
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Détails de l'Inventaire</h1>
            <p class="mt-2 text-gray-600">Informations complètes du comptage d'inventaire</p>
        </div>
    </div>

    <!-- Details Card -->
    <div class="bg-white rounded-lg shadow overflow-hidden border border-gray-200 p-6">
        <div class="grid grid-cols-2 gap-6">
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Produit</label>
                <p class="text-gray-900 font-medium text-lg">{{ optional($inventory->product)->name }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Quantité</label>
                <p class="text-gray-900 font-bold text-lg">{{ $inventory->quantity }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Date</label>
                <p class="text-gray-900">{{ $inventory->inventory_date }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Référence</label>
                <p class="text-gray-900">{{ $inventory->reference ?? '-' }}</p>
            </div>
        </div>

        <div class="mt-8 flex gap-4">
            <a href="{{ route('inventories.index') }}" class="inline-flex items-center px-6 py-2 bg-gray-200 text-gray-900 font-medium rounded-lg hover:bg-gray-300 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Retour à la liste
            </a>
        </div>
    </div>
</div>
@endsection
