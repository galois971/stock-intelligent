<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-3xl text-gray-900 leading-tight">Import en cours</h2>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8 bg-transparent min-h-screen">
        <div class="max-w-3xl mx-auto bg-white rounded-lg shadow p-6">
            <p class="mb-4">Fichier: <strong>{{ $importJob->filename }}</strong></p>
            <div class="w-full bg-gray-200 rounded h-4 mb-4">
                <div id="progressBar" class="bg-emerald-600 h-4 rounded" style="width:0%"></div>
            </div>
            <p id="progressText" class="text-sm text-gray-600 mb-2">Statut: {{ $importJob->status }}</p>
            <pre id="errorLog" class="text-xs text-red-600" style="display:none"></pre>
            <div>
                <a href="{{ route('products.index') }}" class="text-sm text-gray-600 hover:underline">Retour produits</a>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const jobId = {{ $importJob->id }};
        const statusUrl = "{{ route('products.import.status.json', ['importJob' => $importJob->id]) }}";

        async function updateStatus(){
            try{
                const res = await fetch(statusUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                if(!res.ok) return;
                const data = await res.json();
                const total = data.total_rows || 0;
                const processed = data.processed_rows || 0;
                const status = data.status || 'pending';

                const pct = total > 0 ? Math.round((processed / total) * 100) : (status === 'completed' ? 100 : 0);
                document.getElementById('progressBar').style.width = pct + '%';
                document.getElementById('progressText').innerText = `Statut: ${status} — ${processed}/${total}`;

                if (data.error) {
                    const el = document.getElementById('errorLog');
                    el.style.display = 'block';
                    el.innerText = data.error;
                }

                if (status === 'completed' || status === 'failed') {
                    clearInterval(interval);
                }
            }catch(e){ console.debug(e); }
        }

        const interval = setInterval(updateStatus, 2000);
        updateStatus();
    </script>
    @endpush
</x-app-layout>
