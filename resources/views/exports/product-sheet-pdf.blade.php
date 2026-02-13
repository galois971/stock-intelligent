<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Fiche Produit - {{ $product->name }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; padding: 20px; }
        h1 { color: #333; border-bottom: 2px solid #4CAF50; padding-bottom: 10px; }
        .info-section { margin: 20px 0; }
        .info-row { margin: 10px 0; }
        .label { font-weight: bold; display: inline-block; width: 150px; }
        .value { display: inline-block; }
        .stock-warning { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Fiche Produit</h1>
    
    <div class="info-section">
        <div class="info-row">
            <span class="label">ID :</span>
            <span class="value">{{ $product->id }}</span>
        </div>
        <div class="info-row">
            <span class="label">Nom :</span>
            <span class="value">{{ $product->name }}</span>
        </div>
        <div class="info-row">
            <span class="label">Code-barres :</span>
            <span class="value">{{ $product->barcode }}</span>
        </div>
        <div class="info-row">
            <span class="label">Catégorie :</span>
            <span class="value">{{ $product->category ? $product->category->name : '-' }}</span>
        </div>
        <div class="info-row">
            <span class="label">Prix unitaire :</span>
            <span class="value">{{ number_format($product->price, 2, ',', ' ') }} €</span>
        </div>
    </div>

    <div class="info-section">
        <h2>Informations de Stock</h2>
        <div class="info-row">
            <span class="label">Stock actuel :</span>
            <span class="value {{ $product->currentStock() < $product->stock_min ? 'stock-warning' : '' }}">
                {{ $product->currentStock() }}
            </span>
        </div>
        <div class="info-row">
            <span class="label">Stock minimum :</span>
            <span class="value">{{ $product->stock_min }}</span>
        </div>
        <div class="info-row">
            <span class="label">Stock optimal :</span>
            <span class="value">{{ $product->stock_optimal }}</span>
        </div>
        <div class="info-row">
            <span class="label">Valeur du stock :</span>
            <span class="value">{{ number_format($product->currentStock() * $product->price, 2, ',', ' ') }} €</span>
        </div>
    </div>

    @if($product->technical_sheet)
    <div class="info-section">
        <div class="info-row">
            <span class="label">Fiche technique :</span>
            <span class="value">Disponible</span>
        </div>
    </div>
    @endif

    <div style="margin-top: 30px; font-size: 10px; color: #666;">
        Généré le {{ date('d/m/Y à H:i') }}
    </div>
</body>
</html>
