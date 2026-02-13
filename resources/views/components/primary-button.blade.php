<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 border border-transparent rounded-lg font-bold text-sm text-white uppercase tracking-widest hover:from-emerald-700 hover:to-teal-700 focus:from-emerald-700 focus:to-teal-700 active:from-emerald-800 active:to-teal-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-200 shadow-md hover:shadow-lg']) }}>
    {{ $slot }}
</button>
