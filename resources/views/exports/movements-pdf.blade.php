<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Mouvements de Stock</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; }
        h1 { text-align: center; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #4CAF50; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .entry { color: green; }
        .exit { color: red; }
    </style>
</head>
<body>
    <h1>Mouvements de Stock - {{ date('d/m/Y') }}</h1>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Produit</th>
                <th>Type</th>
                <th>Sous-type</th>
                <th>Quantité</th>
                <th>Utilisateur</th>
            </tr>
        </thead>
        <tbody>
            @foreach($movements as $movement)
                <tr>
                    <td>{{ $movement->movement_date ? $movement->movement_date->format('d/m/Y') : '-' }}</td>
                    <td>{{ $movement->product ? $movement->product->name : '-' }}</td>
                    <td class="{{ $movement->type === 'entry' ? 'entry' : 'exit' }}">
                        {{ $movement->type === 'entry' ? 'Entrée' : 'Sortie' }}
                    </td>
                    <td>{{ $movement->subtype_label ?? '-' }}</td>
                    <td>{{ $movement->quantity }}</td>
                    <td>{{ $movement->user ? $movement->user->name : '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
