@extends('layouts.app')

@section('content')
<div>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">🚨 Alertes de Stock</h1>
            <p class="mt-2 text-gray-600">Gérez les alertes de stock bas et surstock</p>
        </div>
        <div class="mt-4 sm:mt-0 px-6 py-3 bg-red-100 rounded-xl border-l-4 border-red-500">
            <p class="text-sm font-medium text-red-700">{{ $alerts->count() }} alerte{{ $alerts->count() !== 1 ? 's' : '' }} active{{ $alerts->count() !== 1 ? 's' : '' }}</p>
        </div>
    </div>

    @if($alerts->isEmpty())
        <div class="bg-white rounded-xl shadow p-12 text-center border border-gray-200">
            <div class="flex flex-col items-center justify-center">
                <svg class="w-16 h-16 text-teal-600 mb-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Aucune alerte en cours</h3>
                <p class="text-gray-600">Tous vos stocks sont dans les normes 👍</p>
            </div>
        </div>
    @else
        <!-- Alerts Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($alerts as $alert)
                <div class="bg-white rounded-xl shadow overflow-hidden border-l-4 border-gray-200 {{ $alert->alert_type === 'low_stock' ? 'border-l-red-500' : 'border-l-orange-500' }}">
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ $alert->product->name ?? '—' }}</h3>
                                <p class="text-sm text-gray-500 mt-1">ID: {{ $alert->product->id ?? '—' }}</p>
                            </div>
                            <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $alert->alert_type === 'low_stock' ? 'bg-red-900/50 text-red-700 border border-red-600' : 'bg-orange-900/50 text-orange-300 border border-orange-600' }}">
                                {{ $alert->alert_type === 'low_stock' ? '📉 Stock Bas' : '📈 Surstock' }}
                            </span>
                        </div>

                        <div class="bg-white rounded-xl p-4 mb-4 border border-gray-300">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs text-gray-600 uppercase tracking-wider">Quantité Actuelle</p>
                                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $alert->current_quantity }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600 uppercase tracking-wider">Message</p>
                                    <p class="text-sm text-gray-700 mt-1">{{ $alert->message }}</p>
                                </div>
                            </div>
                        </div>

                        <p class="text-sm text-gray-600 mb-4">{{ $alert->message }}</p>

                        <div class="flex space-x-2">
                            <a href="{{ route('alerts.show', $alert) }}" class="flex-1 inline-flex justify-center items-center px-4 py-2 bg-emerald-50 text-blue-300 font-medium rounded-xl hover:bg-emerald-50/50 transition border border-blue-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Détails
                            </a>
                            @if(auth()->user() && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('gestionnaire')))
                                <form action="{{ route('alerts.destroy', $alert) }}" method="POST" class="flex-1" onsubmit="return confirm('Marquer cette alerte comme résolue ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full px-4 py-2 bg-teal-100 text-green-700 font-medium rounded-xl hover:bg-green-900/50 transition border border-green-700">
                                        <svg class="w-4 h-4 mr-2 inline" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        Résoudre
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
