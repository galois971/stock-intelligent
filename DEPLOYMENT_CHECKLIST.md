# Configuration Laravel pour hébergement sur Render + Aiven

## 📋 Résumé de la configuration

Votre `.env` a été configuré avec :

| Paramètre | Valeur |
|-----------|--------|
| **APP_ENV** | production |
| **APP_URL** | https://stock-intelligent.onrender.com |
| **DB_CONNECTION** | pgsql (Aiven PostgreSQL) |
| **MAIL_MAILER** | smtp (Gmail/Mailtrap) |
| **CACHE_STORE** | database |
| **SESSION_DRIVER** | database |
| **QUEUE_CONNECTION** | database |

---

## 🚀 Déploiement rapide

### Étape 1 : Configuration Aiven

1. Créez/connectez-vous à [Aiven](https://aiven.io/)
2. Créez un service **PostgreSQL**
3. Récupérez les credentials dans **Connection Information** :
   ```
   Host: pg-xxxx.c.aivencloud.com
   Port: 13357
   Database: stock_db
   User: avnadmin
   Password: ****
   ```
4. Mettez à jour `.env` (section DATABASE)

### Étape 2 : Configuration Render

1. Poussez votre code vers GitHub
2. Allez sur [Render](https://render.com/) → Create → Web Service
3. Connectez votre repo GitHub
4. Configuration :
   - **Runtime** : Python 3.11 (ou supérieur)
   - **Build Command** : `chmod +x build.sh && ./build.sh`
   - **Start Command** : `php artisan serve --host=0.0.0.0 --port=10000`

5. Définissez les **Environment Variables** :
   ```
   APP_KEY=base64:votre_clé_générée
   DB_HOST=pg-xxxx.c.aivencloud.com
   DB_PORT=13357
   DB_DATABASE=stock_db
   DB_USERNAME=avnadmin
   DB_PASSWORD=mot_de_passe
   MAIL_USERNAME=votre@gmail.com
   MAIL_PASSWORD=app_password
   ```

6. Déployez !

---

## 📁 Fichiers créés

- **`.env`** – Configuration mise à jour pour production
- **`DEPLOYMENT_RENDER_AIVEN.md`** – Guide détaillé (ce fichier)
- **`build.sh`** – Script de build pour Render
- **`render.yaml`** – Infrastructure as Code (optionnel)
- **`Procfile`** – Configuration des processus

---

## ✅ Checklist pré-déploiement

```
[ ] APP_KEY généré : php artisan key:generate --show
[ ] Aiven PostgreSQL créé et accessible
[ ] Credentials Aiven copiées dans .env
[ ] Archive `.git` pas trop grosse (< 100 MB)
[ ] Variables sensibles NOT commitées dans .env
[ ] Mail configuré (Gmail app password ou Mailtrap)
[ ] Migrations testées localement
[ ] Tests unitaires passants (php artisan test)
```

---

## 🛠️ Commandes post-déploiement utiles

```bash
# Vérifier les logs Render
render logs <service-id>

# Redéployer manuellement
git push  # déclenche automatiquement le redéploiement

# Connection SSH à Render (optionnel)
# Disponible dans le panneau Settings → Shell
```

---

## 🐛 Dépannage

| Erreur | Solution |
|--------|----------|
| `SQLSTATE[connection failed]` | Vérifiez `DB_HOST`, `DB_PASSWORD`, `DB_SSLMODE=require` |
| `502 Bad Gateway` | Attendez 2-3 min après déploiement (Render initialise) |
| `No such file: Procfile` | Assurez-vous que Procfile est à la racine du projet |
| Mail non envoyé | Vérifiez credentials Gmail (app password, pas password compte) |

---

## 📞 Support

- **Aiven Support** : https://support.aiven.io/
- **Render Docs** : https://render.com/docs
- **Laravel Docs** : https://laravel.com/docs

