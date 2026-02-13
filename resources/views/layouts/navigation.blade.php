<nav x-data="{ sidebarOpen: false }" class="bg-white/95 backdrop-blur-md border-b border-gray-200 shadow-md sticky top-0 z-40">
    <!-- Top Navigation Bar -->
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo and Brand -->
            <div class="flex items-center space-x-3">
                <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 rounded-md hover:bg-gray-100 transition">
                    <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-emerald-600 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4z"></path>
                            <path d="M3 10a1 1 0 011-1h12a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6z"></path>
                        </svg>
                    </div>
                    <span class="text-xl font-bold text-gray-900 hidden sm:inline">Stock Manager</span>
                </a>
            </div>

            <!-- User Menu -->
            <div class="flex items-center space-x-4">
                <div class="relative group hidden sm:block">
                    <button class="flex items-center space-x-2 px-4 py-2 rounded-lg hover:bg-gray-100 transition">
                        <div class="w-8 h-8 bg-emerald-600 rounded-full flex items-center justify-center">
                            <span class="text-white font-bold text-sm">{{ substr(Auth::user()->name, 0, 1) }}</span>
                        </div>
                        <span class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</span>
                        <svg class="w-4 h-4 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div class="absolute right-0 mt-0 w-48 bg-white text-gray-800 rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 border border-gray-200">
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 hover:bg-gray-50 first:rounded-t-lg transition">
                            <div class="font-medium text-gray-900">Mon Profil</div>
                            <div class="text-xs text-gray-600">{{ Auth::user()->email }}</div>
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="border-t border-gray-200">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-50 last:rounded-b-lg transition flex items-center space-x-2 text-gray-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                <span>Déconnexion</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Desktop Sidebar -->
    <div class="hidden lg:flex bg-gradient-to-r from-white via-gray-50 to-gray-100 border-t border-gray-200/50">
        <div class="flex-1 px-4 sm:px-6 lg:px-8 flex items-center space-x-1">
            <nav class="flex space-x-1">
                <a href="{{ route('dashboard') }}" @class([
                    'px-4 py-2 rounded-md text-sm font-medium transition',
                    'bg-emerald-50 text-emerald-700' => request()->routeIs('dashboard'),
                    'text-gray-700 hover:bg-gray-200' => !request()->routeIs('dashboard'),
                ])>
                    📊 Dashboard
                </a>
                <a href="{{ route('products.index') }}" @class([
                    'px-4 py-2 rounded-md text-sm font-medium transition',
                    'bg-emerald-50 text-emerald-700' => request()->routeIs('products.*'),
                    'text-gray-700 hover:bg-gray-200' => !request()->routeIs('products.*'),
                ])>
                    📦 Produits
                </a>
                <a href="{{ route('categories.index') }}" @class([
                    'px-4 py-2 rounded-md text-sm font-medium transition',
                    'bg-emerald-50 text-emerald-700' => request()->routeIs('categories.*'),
                    'text-gray-700 hover:bg-gray-200' => !request()->routeIs('categories.*'),
                ])>
                    🏷️ Catégories
                </a>
                <a href="{{ route('movements.index') }}" @class([
                    'px-4 py-2 rounded-md text-sm font-medium transition',
                    'bg-emerald-50 text-emerald-700' => request()->routeIs('movements.*'),
                    'text-gray-700 hover:bg-gray-200' => !request()->routeIs('movements.*'),
                ])>
                    ↔️ Mouvements
                </a>
                <a href="{{ route('inventories.index') }}" @class([
                    'px-4 py-2 rounded-md text-sm font-medium transition',
                    'bg-emerald-50 text-emerald-700' => request()->routeIs('inventories.*'),
                    'text-gray-700 hover:bg-gray-200' => !request()->routeIs('inventories.*'),
                ])>
                    📋 Inventaires
                </a>
                <a href="{{ route('alerts.index') }}" @class([
                    'px-4 py-2 rounded-md text-sm font-medium transition relative',
                    'bg-emerald-50 text-emerald-700' => request()->routeIs('alerts.*'),
                    'text-gray-700 hover:bg-gray-200' => !request()->routeIs('alerts.*'),
                ])>
                    🚨 Alertes
                </a>
            </nav>
        </div>
    </div>

    <!-- Mobile Sidebar -->
    <div x-show="sidebarOpen" @click.outside="sidebarOpen = false" class="lg:hidden bg-gradient-to-r from-gray-50 to-gray-100 border-t border-gray-200">
        <div class="px-4 py-3 space-y-2">
            <a href="{{ route('dashboard') }}" @class([
                'block px-4 py-2 rounded-md text-sm font-medium transition',
                'bg-emerald-50 text-emerald-700' => request()->routeIs('dashboard'),
                'text-gray-700 hover:bg-gray-200' => !request()->routeIs('dashboard'),
            ])>
                📊 Dashboard
            </a>
            <a href="{{ route('products.index') }}" @class([
                'block px-4 py-2 rounded-md text-sm font-medium transition',
                'bg-emerald-50 text-emerald-700' => request()->routeIs('products.*'),
                'text-gray-700 hover:bg-gray-200' => !request()->routeIs('products.*'),
            ])>
                📦 Produits
            </a>
            <a href="{{ route('categories.index') }}" @class([
                'block px-4 py-2 rounded-md text-sm font-medium transition',
                'bg-emerald-50 text-emerald-700' => request()->routeIs('categories.*'),
                'text-gray-700 hover:bg-gray-200' => !request()->routeIs('categories.*'),
            ])>
                🏷️ Catégories
            </a>
            <a href="{{ route('movements.index') }}" @class([
                'block px-4 py-2 rounded-md text-sm font-medium transition',
                'bg-emerald-50 text-emerald-700' => request()->routeIs('movements.*'),
                'text-gray-700 hover:bg-gray-200' => !request()->routeIs('movements.*'),
            ])>
                ↔️ Mouvements
            </a>
            <a href="{{ route('inventories.index') }}" @class([
                'block px-4 py-2 rounded-md text-sm font-medium transition',
                'bg-emerald-50 text-emerald-700' => request()->routeIs('inventories.*'),
                'text-gray-700 hover:bg-gray-200' => !request()->routeIs('inventories.*'),
            ])>
                📋 Inventaires
            </a>
            <a href="{{ route('alerts.index') }}" @class([
                'block px-4 py-2 rounded-md text-sm font-medium transition',
                'bg-emerald-50 text-emerald-700' => request()->routeIs('alerts.*'),
                'text-gray-700 hover:bg-gray-200' => !request()->routeIs('alerts.*'),
            ])>
                🚨 Alertes
            </a>
            <hr class="border-gray-300 my-2">
            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-200 rounded-md text-sm font-medium transition">
                👤 Mon Profil
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-200 rounded-md text-sm font-medium transition">
                    🚪 Déconnexion
                </button>
            </form>
        </div>
    </div>
</nav>
