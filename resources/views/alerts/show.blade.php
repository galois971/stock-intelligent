@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Détails de l'alerte</h1>

    <div class="bg-white rounded-lg shadow p-8 border border-gray-200">
        <div>
            <h5 class="text-2xl font-bold text-gray-900 mb-6">{{ $alert->product->name ?? '—' }}</h5>
            
            <div class="space-y-4 mb-8">
                <div class="flex justify-between items-center p-4 bg-white rounded-lg border border-gray-300">
                    <span class="text-gray-600 font-medium">Type :</span>
                    <span class="text-gray-900">{{ $alert->alert_type }}</span>
                </div>
                <div class="flex justify-between items-center p-4 bg-white rounded-lg border border-gray-300">
                    <span class="text-gray-600 font-medium">Quantité actuelle :</span>
                    <span class="text-gray-900">{{ $alert->current_quantity }}</span>
                </div>
                <div class="flex justify-between items-center p-4 bg-white rounded-lg border border-gray-300">
                    <span class="text-gray-600 font-medium">Message :</span>
                    <span class="text-gray-900">{{ $alert->message }}</span>
                </div>
                <div class="flex justify-between items-center p-4 bg-white rounded-lg border border-gray-300">
                    <span class="text-gray-600 font-medium">Créée le :</span>
                    <span class="text-gray-900">{{ $alert->created_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>

            <form action="{{ route('alerts.destroy', $alert) }}" method="POST" class="flex space-x-4">
                @csrf
                @method('DELETE')
                <button class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition">Marquer comme résolue</button>
                <a href="{{ route('alerts.index') }}" class="px-6 py-2 bg-white hover:bg-gray-700 text-gray-900 font-medium rounded-lg transition border border-gray-300">Retour</a>
            </form>
        </div>
    </div>
</div>
@endsection
