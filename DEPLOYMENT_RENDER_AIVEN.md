# Déploiement sur Render + Aiven PostgreSQL

## 1. Configuration Aiven PostgreSQL

### Récupérer les informations de connexion :

1. Allez sur https://console.aiven.io/
2. Sélectionnez votre service PostgreSQL
3. Cliquez sur **Connection information**
4. Copiez les paramètres et remplacez dans `.env` :

```
DB_HOST=pg-xxxxxxxx.c.aivencloud.com      # Host (sans le port)
DB_PORT=13357                               # Port (habituellement 13357)
DB_DATABASE=stock_db                        # Nom de la base de données
DB_USERNAME=avnadmin                        # Utilisateur par défaut
DB_PASSWORD=votre_mot_de_passe_aiven       # Mot de passe
```

> **Note** : Aiven fournit aussi une chaîne de connexion complète (Connection string) que vous pouvez utiliser directement si préféré.

---

## 2. Configuration Render

### Variable d'environnement APP_KEY

Générez une clé d'application Laravel (exécutez localement d'abord) :

```bash
php artisan key:generate --show
```

Copiez la clé (format `base64:xxxxx...`) et mettez-la dans `.env` :

```
APP_KEY=base64:votre_clé_générée_ici
```

### Déploiement sur Render

1. **Créer le service** :
   - Allez sur https://render.com/
   - Créez un nouveau **Web Service**
   - Connectez votre repo GitHub
   - Sélectionnez la branche `main` (ou `master`)

2. **Configuration du build** :
   - **Build command** :
     ```bash
     composer install && php artisan migrate --force
     ```
   
   - **Start command** :
     ```bash
     php artisan serve --host 0.0.0.0 --port 10000
     ```

3. **Variables d'environnement** sur Render (dans Settings → Environment) :
   - Collez l'intégralité de votre `.env` ou configurez chaque variable individuellement
   - Assurez-vous que :
     - `APP_ENV=production`
     - `APP_DEBUG=false`
     - `APP_URL=https://votre-domaine-render.onrender.com`

---

## 3. Configuration Mail (optionnel mais recommandé)

### Pour Gmail :
1. Activez **2-Factor Authentication** sur votre compte Google
2. Générez un **App Password** : https://myaccount.google.com/apppasswords
3. Utilisez ce mot de passe dans `.env` :
   ```
   MAIL_USERNAME=votre-email@gmail.com
   MAIL_PASSWORD=xxxx xxxx xxxx xxxx  # App password (16 caractères)
   ```

### Alternative : Mailtrap
1. Créez un compte sur https://mailtrap.io/
2. Créez une boîte de réception (Inbox)
3. Copiez les identifiants SMTP dans `.env`

---

## 4. Fichiers de configuration supplémentaires

Créez un fichier `Procfile` à la racine du projet pour Render :

```procfile
worker: php artisan queue:work --sleep=3 --tries=3
scheduler: php artisan schedule:run >> /dev/null 2>&1
```

Créez un fichier `render.yaml` (optionnel, pour l'infrastructure as code) :

```yaml
services:
  - type: web
    name: stock-intelligent
    runtime: php
    buildCommand: composer install && php artisan migrate --force
    startCommand: php artisan serve --host 0.0.0.0 --port 10000
    envVars:
      - fromFile: .env
```

---

## 5. Checklist avant déploiement

- [ ] `.env` configuré avec Aiven et Render
- [ ] `APP_KEY` généré et défini
- [ ] Base de données Aiven créée et accessible
- [ ] Mail configuré (optionnel)
- [ ] Migrations exécutées localement manuellement (optionnel, pour test)
- [ ] Repo GitHub synchronisé avec les derniers changements

---

## 6. Dépannage

### Erreur de connexion PostgreSQL sur Render :
- Vérifiez que `DB_SSLMODE=require` est défini
- Vérifiez les pare-feu Aiven (Settings → IP ACL)

### Erreur "SQLSTATE[connection failed]" :
- Testez localement que la chaîne de connexion fonctionne
- Vérifiez le mot de passe Aiven

### Application lente au démarrage :
- Render met en cache les dépendances ; attendez quelques secondes pour le premier démarrage

---

## 7. Commandes utiles après déploiement

```bash
# Voir les logs en direct
render logs <service-id>

# Redéployer manuellement
render trigger-deploy <service-id>

# Exécuter des migrations manuellement
render rails db:migrate
```

