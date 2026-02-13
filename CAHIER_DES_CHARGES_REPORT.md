## Rapport de conformité — Cahier des charges : Gestion de Stock Intelligente

Date: 2026-02-13

Résumé rapide
- Objectif: cartographier les fonctionnalités demandées du cahier des charges vers l'implémentation actuelle et lister ce qui manque.

État des fonctionnalités (implémentées / partiellement / manquantes)

- Gestion des produits & stocks: **Implémenté**
  - Modèles: [app/Models/Product.php](app/Models/Product.php), [app/Models/Inventory.php](app/Models/Inventory.php), [app/Models/StockMovement.php](app/Models/StockMovement.php)
  - Contrôleurs / UI: [app/Http/Controllers/ProductController.php](app/Http/Controllers/ProductController.php), [resources/views/products](resources/views/products)

- Alertes & notifications: **Implémenté**
  - Observer & création d'alertes: [app/Observers/StockMovementObserver.php](app/Observers/StockMovementObserver.php)
  - Notification queued: [app/Notifications/StockAlertNotification.php](app/Notifications/StockAlertNotification.php)

- Exports (Excel / PDF): **Implémenté**
  - Exports et routes: [app/Exports](app/Exports) et [routes/web.php](routes/web.php)

- Authentification & rôles: **Implémenté**
  - Routes d'auth, middleware `role:` utilisé, Spatie présent: [routes/web.php](routes/web.php), [vendor/spatie/laravel-permission](vendor/spatie/laravel-permission)

- Tableau de bord décisionnel: **Implémenté**
  - Backend: [app/Http/Controllers/DashboardController.php](app/Http/Controllers/DashboardController.php)
  - Vue: [resources/views/dashboard.blade.php](resources/views/dashboard.blade.php)

- Prédictions & modèles basiques: **Implémenté (statistiques simples)**
  - Service de forecasting: [app/Services/ForecastService.php](app/Services/ForecastService.php)
  - Endpoint API prédiction: [app/Http/Controllers/PredictionController.php](app/Http/Controllers/PredictionController.php)
  - Commande batch & persistence des runs: [app/Console/Commands/RunPredictions.php](app/Console/Commands/RunPredictions.php)
  - Modèle / table: [app/Models/Forecast.php](app/Models/Forecast.php), [database/migrations/2026_02_13_000000_create_forecasts_table.php](database/migrations/2026_02_13_000000_create_forecasts_table.php)
  - Métriques RMSE / MAPE calculées et stockées: migration [2026_02_13_000001_add_mape_to_forecasts.php](database/migrations/2026_02_13_000001_add_mape_to_forecasts.php)

- UI prévisions: **Implémenté**
  - Liste et section sur le dashboard: [resources/views/dashboard.blade.php](resources/views/dashboard.blade.php)
  - Vue détaillée (graphe + historique + contrôles): [resources/views/forecasts/show.blade.php](resources/views/forecasts/show.blade.php)
  - Controller web/API pour forecasts: [app/Http/Controllers/ForecastController.php](app/Http/Controllers/ForecastController.php)

- Scheduler / automatisation: **Implémenté**
  - `app/Console/Kernel.php` schedule la commande `predictions:run`

Éléments manquants ou à améliorer (priorité recommandée)

1. Purchase Order / workflow achat (convertir recommandations en commandes) — *à implémenter*.
   - Raison: nécessaire pour boucler le processus (de la détection de besoin → commande fournisseur).

2. Multi-entrepôts / localisation des stocks — *à implémenter si besoin métier*.

3. Import CSV/Excel massifs pour produits / historique — *à implémenter (facilite la mise en production)*.

4. Documentation API (OpenAPI/Swagger) — *à implémenter (utile aux intégrations mobile/tiers)*.

5. Tests unitaires et d'intégration (ForecastService, PredictionController, commandes) — *à implémenter*.

6. Politique de déploiement / vérification queues & workers (supervisor, cron, .env) — *à compléter*.

7. Observabilité des modèles: stockage historique des runs (déjà présent) + dashboard qualité (RMSE/MAPE) — *amélioration possible*.

Recommandation de la prochaine étape (prioritaire)
- Implémenter le module minimal `PurchaseOrder` (modèle + endpoints + UI) pour permettre de transformer une recommandation en commande fournisseur.
  - Pourquoi: cela boucle la valeur métier du système (prévision → action d'achat).
  - Livrables attendus: modèle `PurchaseOrder`, controller API/web, migration, petite UI pour créer/visualiser commandes depuis le dashboard/prévision.

Si tu confirmes, je commence l'implémentation du module `PurchaseOrder` minimal (modèle, migration, contrôleur API + bouton « Générer commande » depuis la page prévision). Ou je peux prioriser une autre tâche listée ci‑dessus selon ton besoin.
