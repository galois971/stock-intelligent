<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-3xl text-gray-900 leading-tight">Importer des Mouvements (CSV / Excel)</h2>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8 bg-transparent min-h-screen">
        <div class="max-w-3xl mx-auto bg-white rounded-lg shadow p-6">
            @if(session('error'))
                <div class="mb-4 text-red-600">{{ session('error') }}</div>
            @endif
            <form action="{{ route('movements.import') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Fichier (xlsx, xls, csv)</label>
                    <input type="file" name="file" accept=".xlsx,.xls,.csv" required class="mt-1" />
                </div>
                <div class="flex items-center gap-3">
                    <button type="submit" class="bg-emerald-600 text-white px-4 py-2 rounded">Importer</button>
                    <a href="{{ route('movements.index') }}" class="text-sm text-gray-600 hover:underline">Annuler</a>
                </div>
            </form>
            <div class="mt-6 text-sm text-gray-600">
                <p>Colonnes acceptées : <strong>product_id</strong> ou <strong>sku</strong>, <strong>type</strong>, <strong>subtype</strong>, <strong>quantity</strong>, <strong>motif</strong>, <strong>reference</strong>, <strong>supplier</strong>, <strong>customer</strong>, <strong>movement_date</strong>.</p>
            </div>
        </div>
    </div>
</x-app-layout>
