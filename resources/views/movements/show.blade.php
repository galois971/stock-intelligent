@extends('layouts.app')

@section('content')
<div>
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Détails du Mouvement</h1>
            <p class="mt-2 text-gray-600">Informations complètes du mouvement de stock</p>
        </div>
    </div>

    <!-- Details Card -->
    <div class="bg-white rounded-lg shadow overflow-hidden border border-gray-200 p-6">
        <div class="grid grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Produit</label>
                <p class="text-gray-900 font-medium">{{ optional($movement->product)->name }}</p>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Type</label>
                <p>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $movement->type === 'entry' ? 'bg-green-100 text-green-800 border border-green-300' : 'bg-red-100 text-red-800 border border-red-300' }}">
                        {{ $movement->type === 'entry' ? '📥 Entrée' : '📤 Sortie' }}
                    </span>
                </p>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Sous-type</label>
                <p class="text-gray-900">{{ $movement->subtype_label ?? '-' }}</p>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Quantité</label>
                <p class="text-lg font-bold text-gray-900">{{ $movement->quantity }}</p>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Utilisateur</label>
                <p class="text-gray-900">{{ optional($movement->user)->name ?? 'N/A' }}</p>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Date</label>
                <p class="text-gray-900">{{ $movement->movement_date ? $movement->movement_date->format('d/m/Y H:i') : '-' }}</p>
            </div>
            @if($movement->motif)
            <div class="col-span-2">
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Motif / Raison</label>
                <div class="bg-gray-50 border border-gray-200 p-3 rounded text-gray-900">{{ $movement->motif }}</div>
            </div>
            @endif
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Référence</label>
                <p class="text-gray-900">{{ $movement->reference ?? '-' }}</p>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Fournisseur / Client</label>
                <p class="text-gray-900">{{ $movement->supplier ?? $movement->customer ?? '-' }}</p>
            </div>
        </div>

        <div class="mt-8 flex gap-4">
            <a href="{{ route('movements.index') }}" class="inline-flex items-center px-6 py-2 bg-gray-200 text-gray-900 font-medium rounded-lg hover:bg-gray-300 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Retour à la liste
            </a>
        </div>
    </div>
</div>
@endsection
