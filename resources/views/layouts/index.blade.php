@extends('layouts.app')

@section('content')
<div class="bg-white shadow rounded p-6">
    <h2 class="text-2xl font-bold mb-4">Inventaire des Produits</h2>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-4">
        <a href="{{ route('inventories.create') }}" 
           class="bg-emerald-600 text-white hover:bg-emerald-700 px-4 py-2 rounded hover:bg-emerald-200">
            + Ajouter une entrée d'inventaire
        </a>
    </div>

    <table class="w-full border-collapse border border-gray-300">
        <thead class="bg-gray-200">
            <tr>
                <th class="border p-2">ID</th>
                <th class="border p-2">Produit</th>
                <th class="border p-2">Quantité</th>
                <th class="border p-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($inventories as $inventory)
                <tr>
                    <td class="border p-2">{{ $inventory->id }}</td>
                    <td class="border p-2">{{ $inventory->product->name }}</td>
                    <td class="border p-2">{{ $inventory->quantity }}</td>
                    <td class="border p-2 flex space-x-2">
                        <a href="{{ route('inventories.edit', $inventory->id) }}" 
                           class="bg-amber-600 text-white px-3 py-1 rounded hover:bg-amber-700">
                            Modifier
                        </a>
                        <form action="{{ route('inventories.destroy', $inventory->id) }}" method="POST" onsubmit="return confirm('Supprimer cette entrée ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">
                                Supprimer
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center p-4">Aucune entrée d'inventaire trouvée.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection