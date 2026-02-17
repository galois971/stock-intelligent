@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto text-center py-24">
        <div class="card inline-block p-8 text-left">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 flex items-center justify-center rounded-full bg-gradient-to-br from-var(--primary-600) to-var(--primary-500) text-white text-2xl font-bold">
                    ⚠
                </div>
                <div>
                    <h1 class="text-2xl font-bold">Accès refusé</h1>
                    <p class="muted mt-1">Vous n'avez pas les permissions nécessaires pour accéder à cette page.</p>
                </div>
            </div>

            <div class="mt-6">
                <p class="text-sm">Si vous pensez que c'est une erreur, contactez l'administrateur ou vérifiez que vous êtes connecté avec un compte disposant des droits requis.</p>
                <div class="mt-6 flex items-center justify-center gap-3">
                    <a href="{{ url()->previous() ?: route('dashboard') }}" class="px-4 py-2 rounded-lg border bg-transparent hover:bg-gray-100 transition">Retour</a>
                    @auth
                        @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('gestionnaire'))
                            <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-lg bg-gradient-to-r" style="background:linear-gradient(90deg,var(--primary-600),var(--primary-500));color:white">Tableau de bord</a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>
@endsection
