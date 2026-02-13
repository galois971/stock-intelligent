# Installation des packages d'export

Pour activer les exports PDF et Excel, vous devez installer les packages suivants :

## 1. Export Excel (maatwebsite/excel)

```bash
composer require maatwebsite/excel
```

Puis publier la configuration :
```bash
php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider" --tag=config
```

## 2. Export PDF (barryvdh/laravel-dompdf)

```bash
composer require barryvdh/laravel-dompdf
```

Puis publier la configuration :
```bash
php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"
```

## 3. Installer Chart.js (déjà ajouté au package.json)

```bash
npm install
npm run build
```
