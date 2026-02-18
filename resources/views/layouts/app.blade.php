<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Stock Manager') }}</title>
    @php
        $manifestPath = public_path('build/manifest.json');
    @endphp

    @if (file_exists($manifestPath))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        @php
            $manifest = json_decode(@file_get_contents($manifestPath), true) ?: null;
        @endphp
        @if (is_array($manifest) && isset($manifest['resources/css/app.css']['file']))
            <link rel="stylesheet" href="{{ asset('build/' . $manifest['resources/css/app.css']['file']) }}">
        @else
            {{-- Fallback statique si manifest manquant ou corrompu --}}
            <link rel="stylesheet" href="{{ asset('build/assets/app-DPg4-Vjw.css') }}">
        @endif
        @if (is_array($manifest) && isset($manifest['resources/js/app.js']['file']))
            <script src="{{ asset('build/' . $manifest['resources/js/app.js']['file']) }}" defer></script>
        @else
            <script src="{{ asset('build/assets/app-CKl8NZMC.js') }}" defer></script>
        @endif
    @endif
</head>
<body class="bg-gradient-to-br from-white via-gray-50 to-gray-100 text-gray-900">

    @include('layouts.navigation')

    <div class="min-h-screen flex flex-col">
        <!-- Main Content -->
        <main class="flex-1 bg-transparent">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <!-- Flash Messages -->
                @if ($errors->any())
                    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-900">Erreur de validation</h3>
                                <ul class="mt-2 list-disc list-inside text-sm text-red-800">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                @if (session('status'))
                    <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-900">{{ session('status') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                {{ $slot ?? '' }}
                @yield('content')
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white/80 backdrop-blur-sm border-t border-gray-200 mt-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="text-center text-sm text-gray-600">
                    <p>&copy; {{ date('Y') }} Stock Manager. Tous droits réservés.</p>
                </div>
            </div>
        </footer>
    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('theme', localStorage.getItem('theme') || (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'));
            if (Alpine.store('theme') === 'dark') document.documentElement.classList.add('dark');
        });
    </script>
    @stack('scripts')
</body>
</html>
