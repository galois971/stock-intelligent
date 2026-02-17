<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-6 py-3 border border-transparent rounded-lg font-bold text-sm text-white uppercase tracking-widest transition ease-in-out duration-200 shadow-md hover:shadow-lg']) }} style="background: linear-gradient(90deg,var(--primary-600),var(--primary-500));">
    {{ $slot }}
</button>
