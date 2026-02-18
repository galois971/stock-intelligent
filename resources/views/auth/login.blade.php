<x-guest-layout>
    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="flex justify-center mb-6">
            <x-application-logo class="w-16 h-16 fill-current text-emerald-600" />
        </div>

        <!-- Title -->
        <h1 class="text-3xl font-bold text-center text-gray-900 mb-2">Stock Intelligent</h1>
        <p class="text-center text-gray-600 text-sm mb-6">Connectez-vous à votre compte</p>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Login Form -->
        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Adresse Email')" />
                <x-text-input 
                    id="email" 
                    class="block mt-2 w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm 
                    focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
                    type="email" 
                    name="email" 
                    :value="old('email')" 
                    required 
                    autofocus 
                    autocomplete="username"
                    placeholder="admin@example.com"
                />
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-red-600" />
            </div>

            <!-- Password -->
            <div>
                <x-input-label for="password" :value="__('Mot de passe')" />
                <x-text-input 
                    id="password" 
                    class="block mt-2 w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm 
                    focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
                    type="password"
                    name="password"
                    required 
                    autocomplete="current-password"
                    placeholder="••••••••"
                />
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-600" />
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between">
                <label for="remember_me" class="inline-flex items-center">
                    <input 
                        id="remember_me" 
                        type="checkbox" 
                        class="rounded border-gray-300 text-emerald-600 shadow-sm focus:ring-emerald-500 cursor-pointer" 
                        name="remember"
                    >
                    <span class="ms-2 text-sm text-gray-600 cursor-pointer">{{ __('Se souvenir de moi') }}</span>
                </label>
                
                @if (Route::has('password.request'))
                    <a class="text-sm text-emerald-600 hover:text-emerald-700 font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500" href="{{ route('password.request') }}">
                        {{ __('Oubli du mot de passe ?') }}
                    </a>
                @endif
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-center pt-2">
                <x-primary-button class="w-full justify-center py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg transition-all duration-200 transform hover:scale-105">
                    <span id="label">{{ __('Se connecter') }}</span>
                    <svg id="spinner" class="hidden animate-spin h-5 w-5 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </x-primary-button>
            </div>

            <!-- Feedback Message for Errors -->
            @if ($errors->any())
                <div class="mt-4 p-4 bg-red-50 border-l-4 border-red-500 rounded">
                    <p class="text-sm font-semibold text-red-700">Erreur de connexion</p>
                    <p class="text-sm text-red-600 mt-1">Email ou mot de passe incorrect. Veuillez réessayer.</p>
                </div>
            @endif
        </form>

        <!-- Sign Up Link -->
        <p class="mt-6 text-center text-sm text-gray-600">
            Pas encore inscrit ? 
            <a href="{{ route('register') }}" class="text-emerald-600 hover:text-emerald-700 font-medium">
                S'inscrire
            </a>
        </p>

        <!-- Test Credentials Info (local only) -->
        @if (app('env') === 'local')
            <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <p class="text-xs font-semibold text-blue-700 mb-2">📋 Identifiants de test (Local)</p>
                <ul class="text-xs text-blue-600 space-y-1">
                    <li><strong>Admin:</strong> admin@example.com / password</li>
                    <li><strong>Gestionnaire:</strong> gestionnaire@example.com / password</li>
                    <li><strong>Observateur:</strong> observateur@example.com / password</li>
                </ul>
            </div>
        @endif
    </div>

    <script>
        // Add loading spinner on form submit
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const submitBtn = form.querySelector('button[type="submit"]');
            const spinner = document.getElementById('spinner');
            const label = document.getElementById('label');

            form.addEventListener('submit', function() {
                submitBtn.disabled = true;
                spinner.classList.remove('hidden');
                label.classList.add('hidden');
            });
        });
    </script>
</x-guest-layout>
