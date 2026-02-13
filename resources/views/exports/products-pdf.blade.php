<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Liste des Produits</title>
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
    <h1>Liste des Produits - {{ date('d/m/Y') }}</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Code-barres</th>
                <th>Catégorie</th>
                <th>Prix (€)</th>
                <th>Stock</th>
                <th>Stock Min</th>
                <th>Stock Optimal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
                <tr>
                    <td>{{ $product->id }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->barcode }}</td>
                    <td>{{ $product->category ? $product->category->name : '-' }}</td>
                    <td>{{ number_format($product->price, 2, ',', ' ') }}</td>
                    <td>{{ $product->currentStock() }}</td>
                    <td>{{ $product->stock_min }}</td>
                    <td>{{ $product->stock_optimal }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
