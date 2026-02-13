<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-3xl text-gray-900 leading-tight">Prévision : {{ $forecast->product?->name ?? '—' }}</h2>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8 bg-transparent min-h-screen">
        <div class="max-w-5xl mx-auto bg-white rounded-lg shadow p-6">
            <div class="mb-4">
                <p class="text-sm text-gray-600">Méthode : <strong>{{ $forecast->method }}</strong></p>
                <p class="text-sm text-gray-600">Historique (jours) : <strong>{{ $forecast->history_days }}</strong></p>
                <p class="text-sm text-gray-600">Prévision (jours) : <strong>{{ $forecast->forecast_days }}</strong></p>
                <p class="text-sm text-gray-600">RMSE : <strong>{{ $forecast->rmse !== null ? number_format($forecast->rmse, 2, ',', ' ') : '—' }}</strong></p>
                <p class="text-sm text-gray-600">MAPE : <strong>{{ $forecast->mape !== null ? number_format($forecast->mape, 2, ',', ' ') . ' %' : '—' }}</strong></p>
            </div>

            <div class="mb-6 grid grid-cols-1 lg:grid-cols-3 gap-4">
                <div class="col-span-2">
                    <h3 class="text-md font-semibold mb-2">Contrôles</h3>
                    <div class="flex items-center gap-3">
                        <label class="text-sm text-gray-600">Méthode</label>
                        <select id="methodFilter" class="border rounded px-2 py-1">
                            <option value="">Toutes</option>
                            @foreach($methods as $m)
                                <option value="{{ $m }}">{{ $m }}</option>
                            @endforeach
                        </select>

                        <label class="text-sm text-gray-600">Date</label>
                        <input id="dateFilter" type="date" class="border rounded px-2 py-1" />
                    </div>
                </div>

                <div class="col-span-1">
                    <h3 class="text-md font-semibold mb-2">Runs disponibles</h3>
                    <div id="runsList" class="max-h-48 overflow-y-auto border rounded p-2">
                        @foreach($relatedForecasts as $rf)
                            <div class="run-item flex items-center justify-between p-2 rounded hover:bg-gray-50" data-method="{{ $rf->method }}" data-date="{{ $rf->created_at->toDateString() }}">
                                <div>
                                    <div class="text-sm font-medium text-gray-800">{{ $rf->created_at->format('Y-m-d H:i') }}</div>
                                    <div class="text-xs text-gray-500">RMSE: {{ $rf->rmse !== null ? number_format($rf->rmse,2,',',' ') : '—' }} • MAPE: {{ $rf->mape !== null ? number_format($rf->mape,2,',',' ') . ' %' : '—' }}</div>
                                </div>
                                <div>
                                    <a href="{{ route('forecasts.show', ['forecast' => $rf->id]) }}" class="inline-block bg-emerald-600 text-white px-2 py-1 rounded text-sm">Voir</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <h3 class="text-md font-semibold mb-2">Action rapide</h3>
                <div class="flex items-center gap-3">
                    <label class="text-sm text-gray-600">Quantité suggérée</label>
                    <input id="poQuantity" type="number" min="1" class="border rounded px-2 py-1" />
                    <button id="createPoBtn" class="bg-emerald-600 text-white px-3 py-1 rounded">Générer commande</button>
                    <span id="poStatus" class="text-sm text-gray-600"></span>
                </div>
            </div>

            <div class="mb-6">
                <canvas id="forecastChart" height="200"></canvas>
            </div>

            <div>
                <h3 class="text-lg font-semibold mb-2">Historique</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Quantité</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($forecast->history as $h)
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-700">{{ $h['date'] }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $h['quantity'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function(){
            const history = {!! json_encode($forecast->history ?: []) !!};
            const forecast = {!! json_encode($forecast->forecast ?: []) !!};

            const historyLabels = history.map(h => h.date);
            const historyData = history.map(h => h.quantity);

            // build future labels as dates following last history date if available
            let futureLabels = [];
            if (history.length > 0) {
                const lastDate = new Date(history[history.length - 1].date);
                for (let i = 1; i <= forecast.length; i++) {
                    const d = new Date(lastDate);
                    d.setDate(d.getDate() + i);
                    futureLabels.push(d.toISOString().slice(0,10));
                }
            } else {
                for (let i = 1; i <= forecast.length; i++) futureLabels.push('T+'+i);
            }

            const labels = historyLabels.concat(futureLabels);
            const forecastData = new Array(historyData.length).fill(null).concat(forecast);

            const ctx = document.getElementById('forecastChart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Historique',
                                data: historyData.concat(new Array(forecast.length).fill(null)),
                                borderColor: 'rgb(59,130,246)',
                                backgroundColor: 'rgba(59,130,246,0.1)',
                                tension: 0.3,
                                fill: false,
                                spanGaps: true
                            },
                            {
                                label: 'Prévision',
                                data: forecastData,
                                borderColor: 'rgb(16,185,129)',
                                backgroundColor: 'rgba(16,185,129,0.08)',
                                borderDash: [6,4],
                                tension: 0.3,
                                fill: false,
                                spanGaps: true
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                });
            }

            // Filtering runs list by method and date
            const methodFilter = document.getElementById('methodFilter');
            const dateFilter = document.getElementById('dateFilter');
            const runsList = document.getElementById('runsList');

            function filterRuns(){
                const m = methodFilter.value;
                const d = dateFilter.value;
                const items = runsList.querySelectorAll('.run-item');
                items.forEach(it => {
                    const im = it.getAttribute('data-method') || '';
                    const idate = it.getAttribute('data-date') || '';
                    let show = true;
                    if (m && im !== m) show = false;
                    if (d && idate !== d) show = false;
                    it.style.display = show ? '' : 'none';
                });
            }

            methodFilter?.addEventListener('change', filterRuns);
            dateFilter?.addEventListener('change', filterRuns);

            // Quick create Purchase Order
            const createPoBtn = document.getElementById('createPoBtn');
            const poQuantity = document.getElementById('poQuantity');
            const poStatus = document.getElementById('poStatus');

            // default suggested quantity = sum of forecast values (rounded)
            try {
                const forecastVals = {!! json_encode($forecast->forecast ?: []) !!};
                let suggested = 0;
                for (let v of forecastVals) suggested += Number(v || 0);
                suggested = Math.max(1, Math.round(suggested));
                if (poQuantity) poQuantity.value = suggested;
            } catch(e) { /* ignore */ }

            createPoBtn?.addEventListener('click', async function(){
                const qty = parseInt(poQuantity.value || '0', 10);
                if (!qty || qty < 1) { poStatus.innerText = 'Quantité invalide'; return; }

                const tokenMeta = document.querySelector('meta[name="csrf-token"]');
                const csrf = tokenMeta ? tokenMeta.getAttribute('content') : '';

                poStatus.innerText = 'Envoi...';
                try {
                    const res = await fetch('{{ route('purchase_orders.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrf
                        },
                        body: JSON.stringify({
                            product_id: {{ $forecast->product_id }},
                            forecast_id: {{ $forecast->id }},
                            quantity: qty
                        })
                    });

                    if (!res.ok) {
                        const txt = await res.text();
                        poStatus.innerText = 'Erreur: ' + res.status;
                        console.debug('po error', txt);
                        return;
                    }

                    const data = await res.json();
                    poStatus.innerText = 'Commande créée (ID: ' + data.purchase_order.id + ')';
                    createPoBtn.disabled = true;
                } catch (e) {
                    console.error(e);
                    poStatus.innerText = 'Erreur réseau';
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
