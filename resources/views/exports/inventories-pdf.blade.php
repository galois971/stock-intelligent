<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Inventaires</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h1 { text-align: center; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #4CAF50; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Inventaires - {{ date('d/m/Y') }}</h1>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Produit</th>
                <th>Quantité Constatée</th>
                <th>Stock Calculé</th>
                <th>Écart</th>
            </tr>
        </thead>
        <tbody>
            @foreach($inventories as $inventory)
                @php
                    $calculatedStock = $inventory->product ? $inventory->product->currentStock() : 0;
                    $difference = $inventory->quantity - $calculatedStock;
                @endphp
                <tr>
                    <td>{{ $inventory->inventory_date ? $inventory->inventory_date->format('d/m/Y') : '-' }}</td>
                    <td>{{ $inventory->product ? $inventory->product->name : '-' }}</td>
                    <td>{{ $inventory->quantity }}</td>
                    <td>{{ $calculatedStock }}</td>
                    <td>{{ $difference }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
