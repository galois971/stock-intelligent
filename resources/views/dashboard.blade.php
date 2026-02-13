<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-3xl text-gray-900 leading-tight">
            📊 {{ __('Tableau de bord') }}
        </h2>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8 bg-transparent min-h-screen">
        <div class="max-w-7xl mx-auto">
            <!-- KPIs Cards - CoreUI Style -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Produits -->
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-md hover:shadow-xl hover:border-emerald-500 transition-all duration-300 hover:-translate-y-1.5 group cursor-pointer">                    <div class="h-1 bg-gradient-to-r from-emerald-400 to-emerald-600"></div>
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest mb-2">Total Produits</p>
                                <p class="text-4xl font-bold text-gray-900 group-hover:text-emerald-600 transition">{{ $totalProducts ?? \App\Models\Product::count() }}</p>
                                <p class="text-xs text-gray-500 mt-3">Articles en stock</p>
                            </div>
                            <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-full p-5 group-hover:scale-110 transition-transform duration-300">
                                <svg class="h-8 w-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Valeur du Stock -->
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-md hover:shadow-xl hover:border-teal-500 transition-all duration-300 hover:-translate-y-1.5 group cursor-pointer">                    <div class="h-1 bg-gradient-to-r from-teal-400 to-teal-600"></div>
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest mb-2">Valeur du Stock</p>
                                <p class="text-4xl font-bold text-gray-900 group-hover:text-teal-600 transition">{{ number_format($stockValue ?? 0, 2, ',', ' ') }} €</p>
                                <p class="text-xs text-gray-500 mt-3">Coût total</p>
                            </div>
                            <div class="bg-gradient-to-br from-teal-50 to-teal-100 rounded-full p-5 group-hover:scale-110 transition-transform duration-300">
                                <svg class="h-8 w-8 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alertes Actives -->
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-md hover:shadow-xl hover:border-red-500 transition-all duration-300 hover:-translate-y-1.5 group cursor-pointer">                    <div class="h-1 bg-gradient-to-r from-red-400 to-red-600"></div>
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest mb-2">Alertes Actives</p>
                                <p class="text-4xl font-bold text-gray-900 group-hover:text-red-600 transition">{{ $activeAlerts ?? \App\Models\StockAlert::where('resolved', false)->count() }}</p>
                                <p class="text-xs text-gray-500 mt-3">À traiter</p>
                            </div>
                            <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-full p-5 group-hover:scale-110 transition-transform duration-300">
                                <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mouvements Aujourd'hui -->
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-md hover:shadow-xl hover:border-purple-500 transition-all duration-300 hover:-translate-y-1.5 group cursor-pointer">                    <div class="h-1 bg-gradient-to-r from-purple-400 to-purple-600"></div>
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest mb-2">Mouvements</p>
                                <p class="text-4xl font-bold text-gray-900 group-hover:text-purple-600 transition">{{ $totalMovements ?? \App\Models\StockMovement::count() }}</p>
                                <p class="text-xs text-gray-500 mt-3">Total des mouvements</p>
                            </div>
                            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-full p-5 group-hover:scale-110 transition-transform duration-300">
                                <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Accès Rapide aux Modules -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Accès rapide</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
                    <!-- Produits -->
                    <a href="{{ route('products.index') }}" class="group bg-white rounded-xl border border-gray-200 p-5 hover:border-emerald-500 hover:shadow-lg transition-all duration-300 hover:-translate-y-1 cursor-pointer">
                        <div class="flex items-center justify-between mb-4">
                            <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-lg p-3 group-hover:scale-110 transition-transform duration-300">
                                <svg class="h-6 w-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4v10l8 4 8-4V7z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v18" /></svg>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">{{ $totalProducts ?? \App\Models\Product::count() }}</span>
                        </div>
                        <h4 class="text-sm font-bold text-gray-900 group-hover:text-emerald-600 transition">Produits</h4>
                        <p class="text-xs text-gray-500 mt-1.5">Gestion des produits</p>
                    </a>

                    <!-- Catégories -->
                    <a href="{{ route('categories.index') }}" class="group bg-white rounded-lg border border-gray-200 p-5 hover:border-teal-600 hover:shadow-md transition-all">
                        <div class="flex items-center justify-between mb-3">
                            <div class="bg-teal-100 rounded-lg p-3 group-hover:bg-teal-200 transition">
                                <svg class="h-6 w-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.59 13.41L13.41 20.59a2 2 0 01-2.83 0L3.41 13.41a2 2 0 010-2.83L10.59 3.41a2 2 0 012.83 0l6.17 6.17a2 2 0 010 2.83z" /></svg>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-teal-100 text-teal-700">{{ $totalCategories ?? \App\Models\Category::count() }}</span>
                        </div>
                        <h4 class="text-sm font-semibold text-gray-900">Catégories</h4>
                        <p class="text-xs text-gray-600 mt-1">Organisation</p>
                    </a>

                    <!-- Mouvements -->
                    <a href="{{ route('movements.index') }}" class="group bg-white rounded-lg border border-gray-200 p-5 hover:border-purple-600 hover:shadow-md transition-all">
                        <div class="flex items-center justify-between mb-3">
                            <div class="bg-purple-100 rounded-lg p-3 group-hover:bg-purple-200 transition">
                                <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H7" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h14" /></svg>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">{{ $totalMovements ?? \App\Models\StockMovement::count() }}</span>
                        </div>
                        <h4 class="text-sm font-semibold text-gray-900">Mouvements</h4>
                        <p class="text-xs text-gray-600 mt-1">Entrées & sorties</p>
                    </a>

                    <!-- Inventaires -->
                    <a href="{{ route('inventories.index') }}" class="group bg-white rounded-lg border border-gray-200 p-5 hover:border-blue-600 hover:shadow-md transition-all">
                        <div class="flex items-center justify-between mb-3">
                            <div class="bg-blue-100 rounded-lg p-3 group-hover:bg-blue-200 transition">
                                <svg class="h-6 w-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3h6l3 3v13a2 2 0 01-2 2H8a2 2 0 01-2-2V6l3-3z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6M9 16h6M9 8h6" /></svg>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">{{ $totalInventories ?? \App\Models\Inventory::count() }}</span>
                        </div>
                        <h4 class="text-sm font-semibold text-gray-900">Inventaires</h4>
                        <p class="text-xs text-gray-600 mt-1">Comptage</p>
                    </a>

                    <!-- Alertes -->
                    <a href="{{ route('alerts.index') }}" class="group bg-white rounded-lg border border-gray-200 p-5 hover:border-red-600 hover:shadow-md transition-all">
                        <div class="flex items-center justify-between mb-3">
                            <div class="bg-red-100 rounded-lg p-3 group-hover:bg-red-200 transition">
                                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1-9H6l-1 9h5" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01" /></svg>
                            </div>
                            @php $alertsCount = $activeAlerts ?? \App\Models\StockAlert::where('resolved', false)->count(); @endphp
                            @if($alertsCount > 0)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">{{ $alertsCount }}</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">0</span>
                            @endif
                        </div>
                        <h4 class="text-sm font-semibold text-gray-900">Alertes</h4>
                        <p class="text-xs text-gray-600 mt-1">À traiter</p>
                    </a>

                    <!-- Exports -->
                    <a href="{{ route('products.export.excel') }}" class="group bg-white rounded-lg border border-gray-200 p-5 hover:border-green-600 hover:shadow-md transition-all">
                        <div class="flex items-center justify-between mb-3">
                            <div class="bg-green-100 rounded-lg p-3 group-hover:bg-green-200 transition">
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v12m0 0l4-4m-4 4l-4-4" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 21H4a2 2 0 01-2-2V7a2 2 0 012-2h5" /></svg>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">{{ $totalProducts ?? \App\Models\Product::count() }}</span>
                        </div>
                        <h4 class="text-sm font-semibold text-gray-900">Exports</h4>
                        <p class="text-xs text-gray-600 mt-1">Télécharger</p>
                    </a>
                </div>
            </div>

            <!-- Graphiques -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Évolution du Stock -->
                <div class="bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300">
                    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="text-base font-semibold text-white">Évolution du Stock</h3>
                        <span class="text-xs text-gray-600">(30 derniers jours)</span>
                    </div>
                    <div class="p-6">
                        <canvas id="stockEvolutionChart" height="300"></canvas>
                    </div>
                </div>

                <!-- Mouvements par Type -->
                <div class="bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300">
                    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="text-base font-semibold text-white">Mouvements par Type</h3>
                        <span class="text-xs text-gray-600">(30 derniers jours)</span>
                    </div>
                    <div class="p-6">
                        <canvas id="movementsByTypeChart" height="300"></canvas>
                    </div>
                </div>
            </div>

            <!-- Tableaux d'informations -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Produits en Rupture -->
                <div class="bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-base font-semibold text-white flex items-center">
                            <svg class="h-5 w-5 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                            Produits en Rupture
                        </h3>
                    </div>
                    <div class="p-6">
                        @if(!empty($outOfStockProducts) && $outOfStockProducts->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-800">
                                    <thead>
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Produit</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Stock</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Min</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-800">
                                        @foreach($outOfStockProducts as $product)
                                            <tr class="hover:bg-gray-100/50">
                                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-white">{{ $product->name }}</td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-red-600 font-semibold">{{ $product->currentStock() }}</td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ $product->stock_min }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="h-12 w-12 text-teal-600 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                                <p class="text-sm text-gray-600 font-medium">Aucun produit en rupture</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Produits Proches de la Rupture -->
                <div class="bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-base font-semibold text-white flex items-center">
                            <svg class="h-5 w-5 text-amber-600 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                            Stock Bas
                        </h3>
                    </div>
                    <div class="p-6">
                        @if(!empty($lowStockProducts) && $lowStockProducts->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-800">
                                    <thead>
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Produit</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Stock</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Min</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-800">
                                        @foreach($lowStockProducts as $product)
                                            <tr class="hover:bg-gray-100/50">
                                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-white">{{ $product->name }}</td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-amber-600 font-semibold">{{ $product->currentStock() }}</td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ $product->stock_min }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="h-12 w-12 text-teal-600 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                                <p class="text-sm text-gray-600 font-medium">Aucun produit avec stock bas</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Mouvements par Catégorie -->
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300 mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-base font-semibold text-white">Mouvements par Catégorie</h3>
                    <p class="text-xs text-gray-600 mt-1">(30 derniers jours)</p>
                </div>
                <div class="p-6">
                    <canvas id="movementsByCategoryChart" height="200"></canvas>
                </div>
            </div>

            <!-- Recommandations de Commande -->
            @if(!empty($predictions) && count($predictions) > 0)
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-base font-semibold text-white flex items-center">
                        <svg class="h-5 w-5 text-emerald-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                        Recommandations de Commande
                    </h3>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-800">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Produit</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Stock Actuel</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Min</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">À Commander</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-800">
                                @foreach($predictions as $prediction)
                                    <tr class="hover:bg-gray-100/50">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-white">
                                            <a href="{{ route('forecasts.show', ['forecast' => $f->id ?? '#']) }}" class="text-emerald-400 hover:underline">{{ $prediction['product']->name }}</a>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-red-600 font-semibold">{{ $prediction['current_stock'] }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ $prediction['stock_min'] }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-emerald-600 font-semibold">{{ $prediction['suggested_order'] }} unités</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
    <!-- Prévisions Récentes -->
    <div class="max-w-7xl mx-auto mt-6">
        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-base font-semibold text-white">Prévisions Récentes</h3>
                <p class="text-xs text-gray-600 mt-1">Dernières prévisions calculées</p>
            </div>
            <div class="p-6">
                @if(!empty($latestForecasts) && $latestForecasts->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-800">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Produit</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Méthode</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Historique (jours)</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Prévision (prochains jours)</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">RMSE</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">MAPE</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-800">
                                @foreach($latestForecasts as $f)
                                    <tr class="hover:bg-gray-100/50">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-white">{{ $f->product?->name ?? '—' }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ $f->method }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ $f->history_days }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-emerald-600 font-semibold">{{ is_array($f->forecast) ? implode(', ', array_slice($f->forecast, 0, 5)) : $f->forecast }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ $f->rmse !== null ? number_format($f->rmse, 2, ',', ' ') : '—' }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ $f->mape !== null ? number_format($f->mape, 2, ',', ' ') . ' %' : '—' }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ $f->created_at->format('Y-m-d H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8">
                        <p class="text-sm text-gray-600 font-medium">Aucune prévision disponible</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
    <script>
        // Attendre que le DOM soit chargé et Chart.js disponible
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM chargé, Chart.js disponible:', typeof Chart !== 'undefined');
            
            setTimeout(function() {
                initCharts();
            }, 500);
        });

        function initCharts() {
            console.log('Initialisation des graphiques...');
            
            // Évolution du Stock
            try {
                const stockEvolutionCtx = document.getElementById('stockEvolutionChart');
                if (stockEvolutionCtx) {
                    console.log('Canvas stockEvolutionChart trouvé');
                    new Chart(stockEvolutionCtx, {
                        type: 'line',
                        data: {
                            labels: ['01/01', '05/01', '10/01', '15/01', '20/01', '25/01', '30/01', '04/02', '08/02', '10/02'],
                            datasets: [{
                                label: 'Stock Total',
                                data: [120, 140, 135, 160, 155, 180, 175, 190, 185, 200],
                                borderColor: 'rgb(59, 130, 246)',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                tension: 0.4,
                                fill: true,
                                borderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: true,
                                    labels: {
                                        color: 'rgba(255, 255, 255, 0.7)'
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        color: 'rgba(255, 255, 255, 0.5)'
                                    },
                                    grid: {
                                        color: 'rgba(255, 255, 255, 0.1)'
                                    }
                                },
                                x: {
                                    ticks: {
                                        color: 'rgba(255, 255, 255, 0.5)'
                                    },
                                    grid: {
                                        color: 'rgba(255, 255, 255, 0.1)'
                                    }
                                }
                            }
                        }
                    });
                } else {
                    console.log('Canvas stockEvolutionChart NOT trouvé');
                }
            } catch(e) {
                console.error('Erreur graphique Évolution du Stock:', e);
            }

            // Mouvements par Type
            try {
                const movementsByTypeCtx = document.getElementById('movementsByTypeChart');
                if (movementsByTypeCtx) {
                    console.log('Canvas movementsByTypeChart trouvé');
                    new Chart(movementsByTypeCtx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Entrées', 'Sorties'],
                            datasets: [{
                                data: [450, 320],
                                backgroundColor: [
                                    'rgba(34, 197, 94, 0.8)',
                                    'rgba(239, 68, 68, 0.8)'
                                ],
                                borderColor: [
                                    'rgb(34, 197, 94)',
                                    'rgb(239, 68, 68)'
                                ],
                                borderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    labels: {
                                        color: 'rgba(255, 255, 255, 0.7)'
                                    }
                                }
                            }
                        }
                    });
                } else {
                    console.log('Canvas movementsByTypeChart NOT trouvé');
                }
            } catch(e) {
                console.error('Erreur graphique Mouvements par Type:', e);
            }

            // Mouvements par Catégorie
            try {
                const movementsByCategoryCtx = document.getElementById('movementsByCategoryChart');
                if (movementsByCategoryCtx) {
                    console.log('Canvas movementsByCategoryChart trouvé');
                    new Chart(movementsByCategoryCtx, {
                        type: 'bar',
                        data: {
                            labels: ['Électronique', 'Bureautique', 'Pièces détachées', 'Accessoires'],
                            datasets: [{
                                label: 'Sorties (unités)',
                                data: [120, 95, 60, 45],
                                backgroundColor: 'rgba(239, 68, 68, 0.8)',
                                borderColor: 'rgb(239, 68, 68)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    labels: {
                                        color: 'rgba(255, 255, 255, 0.7)'
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        color: 'rgba(255, 255, 255, 0.5)'
                                    },
                                    grid: {
                                        color: 'rgba(255, 255, 255, 0.1)'
                                    }
                                },
                                x: {
                                    ticks: {
                                        color: 'rgba(255, 255, 255, 0.5)'
                                    },
                                    grid: {
                                        color: 'rgba(255, 255, 255, 0.1)'
                                    }
                                }
                            }
                        }
                    });
                } else {
                    console.log('Canvas movementsByCategoryChart NOT trouvé');
                }
            } catch(e) {
                console.error('Erreur graphique Mouvements par Catégorie:', e);
            }
        }
    </script>
    <script>
        (function(){
            const url = "{{ route('dashboard.counts') }}";
            const ids = {
                products: 'badge-products',
                categories: 'badge-categories',
                movements: 'badge-movements',
                inventories: 'badge-inventories',
                alerts: 'badge-alerts',
                exports: 'badge-exports'
            };

            async function updateCounts(){
                try{
                    const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    if(!res.ok) return;
                    const data = await res.json();
                    const setText = (id, value) => { const el = document.getElementById(id); if(el) el.innerText = value; };
                    setText(ids.products, data.products);
                    setText(ids.categories, data.categories);
                    setText(ids.movements, data.movements);
                    setText(ids.inventories, data.inventories);
                    setText(ids.exports, data.products);

                    const alertsEl = document.getElementById(ids.alerts);
                    if(alertsEl){
                        alertsEl.innerText = data.alerts;
                        if(data.alerts > 0){
                            alertsEl.classList.remove('bg-gray-100','text-gray-800');
                            alertsEl.classList.add('bg-red-600','text-white');
                        } else {
                            alertsEl.classList.remove('bg-red-600','text-white');
                            alertsEl.classList.add('bg-gray-100','text-gray-800');
                        }
                    }
                }catch(e){ console.debug('updateCounts error', e); }
            }

            updateCounts();
            setInterval(updateCounts, 30000);
        })();
    </script>
    @endpush
</x-app-layout>
