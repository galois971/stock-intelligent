@extends('layouts.app')

@section('content')
<div>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">📦 Gestion des Produits</h1>
            <p class="mt-2 text-gray-600">Gérez vos produits, stocks et prix</p>
        </div>
        <a href="{{ route('products.create') }}"
           class="mt-4 sm:mt-0 inline-flex items-center px-6 py-3 bg-emerald-600 text-white shadow-md hover:shadow-lg hover:bg-emerald-700 font-medium rounded-lg transition shadow-lg">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouveau Produit
        </a>
    </div>

    <!-- CONTENEUR DASHBOARD -->
    <div class="bg-white text-gray-900 rounded-lg p-6 shadow">

        <!-- Success message -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg animate-fade-in">
                <p class="text-sm font-medium text-green-700">
                    ✓ {{ session('success') }}
                </p>
            </div>
        @endif

        <!-- Search & Filter Section -->
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 p-6 rounded-lg border border-gray-200 mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Search Input -->
                <div class="lg:col-span-2">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Rechercher</label>
                    <input type="text" id="search" placeholder="Nom, code-barres, catégorie..." 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
                </div>
                <!-- Price Range -->
                <div>
                    <label for="minPrice" class="block text-sm font-medium text-gray-700 mb-2">Prix min</label>
                    <input type="number" id="minPrice" placeholder="0" step="0.01" 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                </div>
                <!-- Stock Status -->
                <div>
                    <label for="stockStatus" class="block text-sm font-medium text-gray-700 mb-2">Stock</label>
                    <select id="stockStatus" 
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                        <option value="">Tous</option>
                        <option value="low">Stock bas</option>
                        <option value="excess">Stock excès</option>
                        <option value="ok">Stock OK</option>
                    </select>
                </div>
                <!-- Reset Button -->
                <div class="flex items-end">
                    <button onclick="document.getElementById('search').value=''; document.getElementById('minPrice').value=''; document.getElementById('stockStatus').value='';"
                            class="w-full px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                        ↻ Réinitialiser
                    </button>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-md">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-gray-600" id="productsTable">
                <thead class="bg-gradient-to-r from-emerald-50 to-teal-50 border-b border-gray-200 text-gray-700 uppercase text-xs font-bold">
                    <tr>
                        <th class="px-6 py-4 text-left">Produit</th>
                        <th class="px-6 py-4 text-left">Code-barres</th>
                        <th class="px-6 py-4 text-left">Catégorie</th>
                        <th class="px-6 py-4 text-left">Prix</th>
                        <th class="px-6 py-4 text-left">Stock</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($products as $product)
                        <tr class="hover:bg-emerald-50/30 transition duration-200 border-b border-gray-100">
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="font-medium text-gray-900">{{ $product->name }}</span>
                                    <span class="text-xs text-gray-600">ID: {{ $product->id }}</span>
                                </div>
                            </td>

                            <td class="px-6 py-4 font-mono text-gray-600">
                                {{ $product->barcode ?? '-' }}
                            </td>

                            <td class="px-6 py-4">
                                <span class="px-3 py-1 text-xs font-medium bg-blue-500/20 text-emerald-600 rounded-full">
                                    {{ optional($product->category)->name ?? 'Aucune' }}
                                </span>
                            </td>

                            <td class="px-6 py-4 font-semibold text-gray-900">
                                {{ number_format($product->price, 2, ',', ' ') }} €
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="font-medium">{{ $product->currentStock() }}</span>

                                    @if($product->currentStock() <= $product->stock_min)
                                        <span class="px-2 py-1 text-xs bg-red-500/20 text-red-600 rounded-full">Bas</span>
                                    @elseif($product->currentStock() > $product->stock_optimal)
                                        <span class="px-2 py-1 text-xs bg-orange-500/20 text-orange-400 rounded-full">Excès</span>
                                    @else
                                        <span class="px-2 py-1 text-xs bg-green-500/20 text-teal-600 rounded-full">OK</span>
                                    @endif
                                </div>
                            </td>

                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('products.show', $product->id) }}"
                                       class="p-2 text-gray-600 hover:text-emerald-600 hover:bg-blue-500/10 rounded-lg transition">
                                        👁
                                    </a>
                                    <a href="{{ route('products.edit', $product->id) }}"
                                       class="p-2 text-gray-600 hover:text-amber-600 hover:bg-amber-600/10 rounded-lg transition">
                                        ✏️
                                    </a>
                                    <form action="{{ route('products.destroy', $product->id) }}" method="POST"
                                          onsubmit="return confirm('Supprimer ce produit ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="p-2 text-gray-600 hover:text-red-600 hover:bg-red-500/10 rounded-lg transition">
                                            🗑
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-600">
                                Aucun produit trouvé
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-xl border border-blue-200 shadow-md hover:shadow-lg transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-600 text-sm font-semibold uppercase">Total Produits</p>
                        <p class="text-3xl font-bold text-blue-900 mt-2">{{ $products->count() }}</p>
                    </div>
                    <div class="bg-blue-600 rounded-full p-4 text-white text-2xl">📦</div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-red-50 to-red-100 p-6 rounded-xl border border-red-200 shadow-md hover:shadow-lg transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-600 text-sm font-semibold uppercase">Stock Bas</p>
                        <p class="text-3xl font-bold text-red-900 mt-2">
                            {{ $products->where('stock_min', '>', 0)->count() }}
                        </p>
                    </div>
                    <div class="bg-red-600 rounded-full p-4 text-white text-2xl">⚠️</div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-xl border border-green-200 shadow-md hover:shadow-lg transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-600 text-sm font-semibold uppercase">Valeur Totale</p>
                        <p class="text-2xl font-bold text-green-900 mt-2">
                            {{ number_format($products->sum(fn($p) => $p->price * $p->currentStock()), 2, ',', ' ') }} €
                        </p>
                    </div>
                    <div class="bg-green-600 rounded-full p-4 text-white text-2xl">💰</div>
                </div>
            </div>
        </div>

        <!-- Search Script -->
        <script>
            const searchInput = document.getElementById('search');
            const minPriceInput = document.getElementById('minPrice');
            const stockStatusSelect = document.getElementById('stockStatus');
            const table = document.getElementById('productsTable');
            const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

            function filterTable() {
                const searchValue = searchInput.value.toLowerCase();
                const minPrice = parseFloat(minPriceInput.value) || 0;
                const stockStatus = stockStatusSelect.value;

                Array.from(rows).forEach(row => {
                    if (row.textContent.includes('Aucun produit trouvé')) return;
                    
                    const productName = row.cells[0].textContent.toLowerCase();
                    const barcode = row.cells[1].textContent.toLowerCase();
                    const category = row.cells[2].textContent.toLowerCase();
                    const price = parseFloat(row.cells[3].textContent.replace(/[^0-9.]/g, ''));
                    const stockText = row.cells[4].textContent.toLowerCase();
                    
                    let searchMatch = !searchValue || 
                        productName.includes(searchValue) || 
                        barcode.includes(searchValue) || 
                        category.includes(searchValue);
                    
                    let priceMatch = price >= minPrice;
                    let stockMatch = !stockStatus || stockText.includes(stockStatus);
                    
                    row.style.display = (searchMatch && priceMatch && stockMatch) ? '' : 'none';
                });
            }

            searchInput.addEventListener('keyup', filterTable);
            minPriceInput.addEventListener('change', filterTable);
            stockStatusSelect.addEventListener('change', filterTable);
        </script>

</div>
@endsection
