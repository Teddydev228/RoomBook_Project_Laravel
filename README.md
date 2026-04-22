# RoomBook — Lot 1 : Gestion des Utilisateurs & Authentification

Système de gestion des utilisateurs avec authentification par token (Laravel Sanctum) et contrôle d'accès basé sur les rôles (RBAC).

---

## 📋 Prérequis

| Outil | Version minimum |
|-------|----------------|
| PHP | 8.2+ |
| Composer | 2.x |
| MySQL / MariaDB | 8.0+ (ou SQLite pour les tests) |

---

## 🚀 Installation

### 1. Cloner / Extraire le projet

```bash
unzip roombook-lot1.zip
cd roombook
```

### 2. Installer les dépendances

```bash
composer install
```

### 3. Configurer l'environnement

```bash
cp .env.example .env
php artisan key:generate
```

Ouvrir `.env` et configurer la base de données :

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=roombook
DB_USERNAME=votre_utilisateur
DB_PASSWORD=votre_mot_de_passe
```

> **SQLite (test rapide)** :
> ```env
> DB_CONNECTION=sqlite
> # Créer le fichier : touch database/database.sqlite
> ```

### 4. Créer la base de données et lancer les migrations

```bash
# Créer la base de données dans MySQL
mysql -u root -p -e "CREATE DATABASE roombook CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Lancer les migrations
php artisan migrate

# (Optionnel) Insérer les données de test
php artisan db:seed
```

### 5. Démarrer le serveur

```bash
php artisan serve
```

L'API est disponible sur : **http://localhost:8000/api**

---

## 📡 Endpoints API

### 🔓 Routes publiques

| Méthode | URL | Description |
|---------|-----|-------------|
| `GET` | `/api/health` | Santé de l'API |
| `POST` | `/api/login` | Connexion |

### 🔒 Routes authentifiées (Header : `Authorization: Bearer {token}`)

| Méthode | URL | Description | Rôle requis |
|---------|-----|-------------|-------------|
| `POST` | `/api/logout` | Déconnexion | Tous |
| `GET` | `/api/me` | Profil connecté | Tous |
| `GET` | `/api/users` | Liste des utilisateurs | Admin |
| `POST` | `/api/users` | Créer un utilisateur | Admin |
| `GET` | `/api/users/{id}` | Afficher un utilisateur | Admin |
| `PUT` | `/api/users/{id}` | Modifier un utilisateur | Admin |
| `DELETE` | `/api/users/{id}` | Supprimer un utilisateur | Admin |

---

## 🔑 Authentification

### Connexion

```http
POST /api/login
Content-Type: application/json

{
    "email": "admin@roombook.fr",
    "password": "password"
}
```

**Réponse :**
```json
{
    "message": "Connexion réussie.",
    "token": "1|abc123xyz...",
    "user": {
        "id": 1,
        "name": "Admin Principal",
        "email": "admin@roombook.fr",
        "role": "admin"
    }
}
```

### Utiliser le token

Inclure dans toutes les requêtes suivantes :
```http
Authorization: Bearer 1|abc123xyz...
```

### Déconnexion

```http
POST /api/logout
Authorization: Bearer 1|abc123xyz...
```

---

## 👥 Gestion des utilisateurs (Admin uniquement)

### Créer un utilisateur

```http
POST /api/users
Authorization: Bearer {token_admin}
Content-Type: application/json

{
    "name": "Jean Dupont",
    "email": "jean.dupont@roombook.fr",
    "password": "motdepasse123",
    "password_confirmation": "motdepasse123",
    "role": "enseignant"
}
```

### Modifier un utilisateur

```http
PUT /api/users/2
Authorization: Bearer {token_admin}
Content-Type: application/json

{
    "role": "responsable"
}
```

---

## 👤 Rôles disponibles

| Rôle | Description | Accès |
|------|-------------|-------|
| `admin` | Administrateur système | CRUD complet sur les utilisateurs, salles, matériels |
| `responsable` | Responsable pédagogique | Valide/refuse les demandes de réservation |
| `enseignant` | Enseignant | Crée et consulte ses demandes de réservation |

---

## 🧪 Comptes de test (après `db:seed`)

| Rôle | Email | Mot de passe |
|------|-------|--------------|
| Admin | admin@roombook.fr | password |
| Responsable | responsable@roombook.fr | password |
| Responsable | martin.pierre@roombook.fr | password |
| Enseignant | enseignant@roombook.fr | password |
| Enseignant | leroy.thomas@roombook.fr | password |
| Enseignant | moreau.julie@roombook.fr | password |

---

## 🏗️ Structure du projet (Lot 1)

```
roombook/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php       # Login, logout, me
│   │   │   └── UserController.php       # CRUD utilisateurs
│   │   └── Middleware/
│   │       └── CheckRole.php            # Vérification des rôles
│   └── Models/
│       └── User.php                     # Modèle utilisateur
├── bootstrap/
│   └── app.php                          # Config middleware & exceptions
├── database/
│   ├── migrations/
│   │   ├── ..._create_users_table.php
│   │   └── ..._create_personal_access_tokens_table.php
│   └── seeders/
│       └── DatabaseSeeder.php           # Données de test
├── routes/
│   └── api.php                          # Toutes les routes API
├── .env.example
└── composer.json
```

---

## 🔒 Sécurité implémentée

- **Hachage des mots de passe** : Laravel `bcrypt` via cast `hashed`
- **Token Sanctum** : révoqué à chaque déconnexion
- **Middleware `CheckRole`** : protège les routes par rôle
- **Validation stricte** : toutes les entrées sont validées
- **Protection auto-suppression** : un admin ne peut pas se supprimer lui-même
- **Messages d'erreur JSON** : toutes les exceptions retournent du JSON pour les routes `/api/*`

---

## 📦 Lots suivants

| Lot | Description | Dépend de |
|-----|-------------|-----------|
| **Lot 2** | Gestion des Salles & Matériels | Lot 1 |
| **Lot 3** | Système de Réservation | Lots 1 & 2 |
| **Lot 4** | Interface Frontend | Lots 1, 2 & 3 |
