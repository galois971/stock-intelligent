# Stock Intelligence API - Documentation

## 📚 Accès à la Documentation

- **Interface Interactive Swagger UI** : [http://localhost/api/docs](http://localhost/api/docs)
- **Fichier OpenAPI (JSON)** : [http://localhost/api/docs.json](http://localhost/api/docs.json)

## 🔐 Authentification

Tous les endpoints (sauf `/api/docs`) nécessitent une authentification **Bearer Token (JWT)**.

### Obtenir un Token

```bash
curl -X POST http://localhost/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "password"
  }'
```

**Réponse :**
```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "user": {
    "id": 1,
    "name": "Admin",
    "email": "admin@example.com",
    "roles": ["admin"]
  }
}
```

### Utiliser le Token

```bash
curl http://localhost/api/v1/products \
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc..."
```

## 📋 Endpoints Principaux

### 🏭 Produits

#### Lister les produits
```bash
GET /api/v1/products?page=1&per_page=15
```

**Réponse :**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Laptop Dell",
      "barcode": "8712345678901",
      "price": 1299.99,
      "stock_min": 10,
      "stock_optimal": 50,
      "category_id": null,
      "created_at": "2026-02-13T10:00:00Z",
      "updated_at": "2026-02-13T10:00:00Z"
    }
  ],
  "meta": {
    "total": 150,
    "count": 15,
    "per_page": 15,
    "current_page": 1,
    "total_pages": 10
  }
}
```

#### Créer un produit (Admin/Gestionnaire)
```bash
POST /api/v1/products
Content-Type: application/json

{
  "name": "Laptop Dell",
  "barcode": "8712345678901",
  "price": 1299.99,
  "stock_min": 10,
  "stock_optimal": 50,
  "category_id": null
}
```

#### Obtenir un produit
```bash
GET /api/v1/products/{id}
```

#### Mettre à jour un produit (Admin/Gestionnaire)
```bash
PUT /api/v1/products/{id}
```

#### Supprimer un produit (Admin)
```bash
DELETE /api/v1/products/{id}
```

---

### 📦 Mouvements de Stock

#### Lister les mouvements
```bash
GET /api/v1/stock-movements?product_id=1&type=entry&page=1
```

#### Créer un mouvement
```bash
POST /api/v1/stock-movements
Content-Type: application/json

{
  "product_id": 1,
  "type": "entry",
  "subtype": "achat",
  "quantity": 50,
  "motif": "Achat auprès du fournisseur X",
  "movement_date": "2026-02-13"
}
```

**Types de mouvement :**
- **entry** (entrée)
  - `achat` - Achat auprès d'un fournisseur
  - `retour` - Retour client
  - `correction` - Correction d'inventaire
  
- **exit** (sortie)
  - `vente` - Vente
  - `perte` - Perte/vol
  - `casse` - Produit cassé
  - `expiration` - Produit expiré

---

### 📊 Prédictions

#### Obtenir les prédictions
```bash
GET /api/v1/forecasts?product_id=1&days=30
```

**Réponse :**
```json
{
  "data": [
    {
      "id": 1,
      "product_id": 1,
      "forecasted_demand": 45.5,
      "forecast_date": "2026-02-14",
      "method": "moving_average",
      "rmse": 5.2,
      "mape": 0.12
    }
  ]
}
```

**Méthodes disponibles :**
- `moving_average` - Moyenne mobile (simple)
- `linear_regression` - Régression linéaire
- `exponential_smoothing` - Lissage exponentiel (à venir)

---

### 🚨 Alertes de Stock

#### Lister les alertes
```bash
GET /api/v1/stock-alerts?status=active
```

**Types d'alerte :**
- `low_stock` - Stock inférieur au minimum
- `overstock` - Stock supérieur à l'optimal
- `risk_of_rupture` - Risque de rupture (seuil critique)
- `expiration` - Produit expiré

#### Obtenir les détails d'une alerte
```bash
GET /api/v1/stock-alerts/{id}
```

#### Résoudre une alerte (Admin/Gestionnaire)
```bash
PATCH /api/v1/stock-alerts/{id}/resolve
```

---

### 📈 Tableau de Bord

#### Obtenir les KPIs
```bash
GET /api/v1/dashboard
```

**Réponse :**
```json
{
  "total_products": 150,
  "total_stock_value": 50000.50,
  "active_alerts": 5,
  "low_stock_products": 3,
  "overstock_products": 2,
  "alerts_by_type": {
    "low_stock": 3,
    "overstock": 2,
    "risk_of_rupture": 0,
    "expiration": 0
  },
  "top_products_by_value": [
    {
      "id": 1,
      "name": "Laptop Dell",
      "total_value": 12999.90
    }
  ],
  "stock_movements_today": 45
}
```

---

## 👥 Contrôle d'Accès par Rôle

| Endpoint | Admin | Gestionnaire | Observateur |
|----------|-------|--------------|------------|
| **Produits** | | | |
| GET /products | ✅ | ✅ | ✅ (lecture) |
| POST /products | ✅ | ✅ | ❌ |
| PUT /products/{id} | ✅ | ✅ | ❌ |
| DELETE /products/{id} | ✅ | ❌ | ❌ |
| **Mouvements** | | | |
| GET /stock-movements | ✅ | ✅ | ✅ (lecture) |
| POST /stock-movements | ✅ | ✅ | ❌ |
| **Alertes** | | | |
| GET /stock-alerts | ✅ | ✅ | ✅ (lecture) |
| PATCH /stock-alerts/{id}/resolve | ✅ | ✅ | ❌ |
| **Dashboard** | | | |
| GET /dashboard | ✅ | ✅ | ✅ |

---

## 🔄 Imports Asynchrones

#### Importer des produits (CSV)
```bash
POST /api/v1/imports/products
Content-Type: multipart/form-data

file: products.csv
```

#### Consulter le statut d'import
```bash
GET /api/v1/imports/{job_id}
```

**Réponse :**
```json
{
  "id": "550e8400-e29b-41d4-a716-446655440000",
  "type": "products",
  "status": "processing",
  "filename": "products.csv",
  "total_rows": 1000,
  "processed_rows": 500,
  "failed_rows": 2,
  "progress": 50,
  "created_at": "2026-02-13T10:00:00Z",
  "updated_at": "2026-02-13T10:05:00Z"
}
```

---

## ⚠️ Gestion des Erreurs

### Types d'erreurs

**400 Bad Request** - Données invalides
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "name": ["The name field is required."],
    "barcode": ["The barcode has already been taken."]
  }
}
```

**401 Unauthorized** - Token manquant ou invalide
```json
{
  "message": "Unauthenticated."
}
```

**403 Forbidden** - Accès refusé
```json
{
  "message": "This action is unauthorized."
}
```

**404 Not Found** - Ressource non trouvée
```json
{
  "message": "Resource not found."
}
```

**422 Unprocessable Entity** - Erreur de validation
```json
{
  "message": "The quantity must be at least 1.",
  "errors": {
    "quantity": ["The quantity must be at least 1."]
  }
}
```

---

## 🧪 Exemples cURL

### 1. Login et obtenir le token
```bash
curl -X POST http://localhost/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "password"
  }'
```

### 2. Lister les produits avec le token
```bash
TOKEN="eyJ0eXAiOiJKV1QiLCJhbGc..."
curl -X GET http://localhost/api/v1/products \
  -H "Authorization: Bearer $TOKEN"
```

### 3. Créer un produit
```bash
TOKEN="eyJ0eXAiOiJKV1QiLCJhbGc..."
curl -X POST http://localhost/api/v1/products \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "iPhone 15",
    "barcode": "1234567890123",
    "price": 999.99,
    "stock_min": 5,
    "stock_optimal": 20
  }'
```

### 4. Créer un mouvement de stock
```bash
TOKEN="eyJ0eXAiOiJKV1QiLCJhbGc..."
curl -X POST http://localhost/api/v1/stock-movements \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "product_id": 1,
    "type": "entry",
    "subtype": "achat",
    "quantity": 100,
    "motif": "Achat chez fournisseur principal",
    "movement_date": "2026-02-13"
  }'
```

---

## 📞 Support

Pour toute question ou problème avec l'API :
1. Consultez la documentation interactive : `/api/docs`
2. Vérifiez votre authentification (token valide et non expiré)
3. Vérifiez votre rôle (admin/gestionnaire/observateur)
4. Consultez les logs : `storage/logs/laravel.log`

---

## 📝 Version

- **API Version** : 1.0.0
- **OpenAPI Version** : 3.0.0
- **Dernière mise à jour** : 2026-02-13
