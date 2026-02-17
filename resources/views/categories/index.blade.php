@extends('layouts.app')

@section('content')
<div>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">🏷️ Gestion des Catégories</h1>
            <p class="mt-2 text-gray-600">Organisez vos produits par catégories</p>
            @if(auth()->user() && auth()->user()->hasRole('observateur'))
                <p class="mt-1 text-sm text-blue-600 font-medium">📖 Mode lecture seule (Observateur)</p>
            @endif
        </div>
        @if(auth()->user() && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('gestionnaire')))
            <a href="{{ route('categories.create') }}" class="mt-4 sm:mt-0 inline-flex items-center px-6 py-3 bg-emerald-600 text-white shadow-md hover:shadow-lg hover:bg-emerald-700 font-medium rounded-lg transition shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nouvelle Catégorie
            </a>
        @endif
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg animate-fade-in flex">
            <svg class="h-5 w-5 text-green-500 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
        </div>
    @endif

    <!-- Search & Filter Section -->
    <div class="bg-gradient-to-r from-gray-50 to-gray-100 p-6 rounded-lg border border-gray-200 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Search Input -->
            <div>
                <label for="categorySearch" class="block text-sm font-medium text-gray-700 mb-2">Rechercher</label>
                <input type="text" id="categorySearch" placeholder="Nom de la catégorie..." 
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
            </div>
            <!-- Reset Button -->
            <div class="flex items-end">
                <button onclick="document.getElementById('categorySearch').value=''; filterCategories();"
                        class="w-full px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                    ↻ Réinitialiser
                </button>
            </div>
        </div>
    </div>

    <!-- Categories Table -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-md">
        <div class="overflow-x-auto">
            <table class="w-full" id="categoriesTable">
                <thead class="bg-gradient-to-r from-teal-50 to-emerald-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Catégorie</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Chemin</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Sous-catégories</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($categories as $category)
                        @include('categories.partials.category-row', ['category' => $category, 'level' => 0])
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                    <p class="text-gray-600 font-medium text-lg">Aucune catégorie trouvée</p>
                                    <p class="text-sm text-gray-500 mt-1">Créez votre première catégorie</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Filter Script -->
    <script>
        function filterCategories() {
            const searchValue = document.getElementById('categorySearch')?.value.toLowerCase() || '';
            const rows = document.querySelectorAll('#categoriesTable tbody tr');
            
            rows.forEach(row => {
                const firstCell = row.querySelector('td');
                if (!firstCell) return;
                
                const categoryName = firstCell.textContent.toLowerCase();
                const isVisible = !searchValue || categoryName.includes(searchValue);
                row.style.display = isVisible ? '' : 'none';
            });
        }
        
        const searchInput = document.getElementById('categorySearch');
        if (searchInput) {
            searchInput.addEventListener('keyup', filterCategories);
        }
    </script>
</div>
@endsection
