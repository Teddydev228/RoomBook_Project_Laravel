# RoomBook - Projet de Réservation de Salles et Matériels

## 📋 Contexte

RoomBook est un système complet de gestion de réservations de salles et de matériels, desarrollé dans le cadre d'un projet académique en 4 lots.

## 🏗️ Architecture

### Lots du projet

| Lot | Titre | Technologie | Statut |
|-----|-------|-------------|--------|
| 1 | Gestion des Utilisateurs & Authentification | Laravel + Sanctum | ✅ Terminé |
| 2 | Gestion des Ressources (Salles & Matériels) | Laravel | ✅ Terminé |
| 3 | Cœur du Système de Réservation | Laravel | ✅ Terminé |
| 4 | Interface Utilisateur & Vues Planning | Laravel (Blade) | ✅ **Terminé** |

###Architecture technique

```
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│   Lot 4 (Front) │────▶│ Lots 1-2-3 (API)│────▶│   Base de données │
│  roombook4/     │     │  RoomBook1/2/3 │     │     MySQL/...    │
│  Laravel 13     │     │  Laravel 11/13 │     │                 │
└─────────────────┘     └─────────────────┘     └─────────────────┘
```

## 📁 Dossier Lot 4 - Interface Utilisateur

### Fonctionnalités réalisées

#### Vue Enseignant
- ✅ Affichage du planning hebdomadaire avec navigation
- ✅ Formulaire de demande de réservation
- ✅ Tableau de suivi de ses demandes (statuts)
- ✅ Annulation des réservations acceptées

#### Vue Responsable
- ✅ Tableau de bord avec statistiques
- ✅ Liste des demandes en attente
- ✅ Acceptation/refus avec motif
- ✅ Planning global des réservations

#### Vue Administrateur
- ✅ Interface de gestion des utilisateurs (CRUD)
- ✅ Interface de gestion des salles (CRUD)
- ✅ Interface de gestion du matériel (CRUD)
- ✅ Tableaux de bord avec indicateurs

### Composants techniques

#### Contrôleurs (app/Http/Controllers/)
- `AuthController.php` - Connexion/déconnexion
- `DashboardController.php` - 3 tableaux de bord (enseignant, responsable, admin)
- `BookingController.php` - Réservations + décisions
- `RoomController.php` - CRUD salles
- `EquipmentController.php` - CRUD matériel
- `UserController.php` - CRUD utilisateurs

#### Services (app/Services/)
- `ApiService.php` - Service d'appel API avec fallback mock data

#### Middleware (app/Http/Middleware/)
- `ApiAuthMiddleware.php` - Authentification par token API

#### Vues (resources/views/)
- Layout principal dark theme (Bootstrap 5)
- Templates pour chaque rôle
- Planning interactif (JavaScript vanilla)
- Modals de confirmation

## 🔌 Intégration API

Le frontend Lot 4 communique avec les endpoints des Lots 1, 2 et 3 :

### Endpoints utilisés

```
# Authentification
POST   /api/login
POST   /api/logout
GET    /api/me

# Utilisateurs (Admin)
GET    /api/users
POST   /api/users
PUT    /api/users/{id}
DELETE /api/users/{id}

# Salles (Admin)
GET    /api/rooms
POST   /api/rooms
PUT    /api/rooms/{id}
DELETE /api/rooms/{id}

# Matériel (Admin)
GET    /api/equipment
POST   /api/equipment
PUT    /api/equipment/{id}
DELETE /api/equipment/{id}

# Réservations
GET    /api/bookings/my                    # Enseignant
POST   /api/bookings                        # Enseignant
POST   /api/bookings/{id}/cancel           # Enseignant
GET    /api/bookings/pending               # Responsable
POST   /api/bookings/{id}/decision         # Responsable
```

### Authentification

- Token Sanctum (Bearer Token)
- Stocké en session PHP
- Envoyé dans l'en-tête `Authorization: Bearer {token}`
- Rôle utilisateur partagé avec toutes les vues

## 🎨 Design & UX

### Palette de couleurs (Dark Theme)

```css
--bg-primary: #0d1117      /* Fond principal */
--bg-secondary: #161b22    /* Fond cartes, sidebar */
--bg-tertiary: #21262d     /* Fond headers */
--border-color: #30363d    /* Bordures */
--text-primary: #f0f6fc    /* Texte principal */
--text-secondary: #8b949e  /* Texte secondaire */
--primary: #238636         /* Boutons, succès */
--danger: #f85149          /* Erreurs, suppression */
--warning: #d29922         /* Alertes, en attente */
--info: #58a6ff            /* Infos */
```

### Framework CSS

- **Bootstrap 5.3** : Grid, composants, utilitaires
- **Bootstrap Icons** : Icônes
- Styles custom dans `resources/views/layouts/app.blade.php`

### Responsive

- Mobile-first design
- Navigation responsive avec navbar toggler
- Tables scrollables horizontalement sur mobile
- Cards adaptatives

## 📱 Fonctionnalités avancées

### Planning interactif

- Génération dynamique du calendrier semaine
- Navigation sans rechargement de page (JavaScript)
- Événements colorés par statut
- Adaptation au fuseau horaire du serveur

### Gestion des conflits

- Vérification automatique lors de la création
- Prévention des réservations doubles
- Messages d'erreur explicites

### Validation côté client

- HTML5 required attributes
- Vérification dates (ends > starts)
- Quantités matériel limitées au stock

### Feedback utilisateur

- Messages flash (success/error)
- Confirmations avant actions destructives
- Tooltips informatifs

## 🧪 Mode Mock/Demo

Pour faciliter le développement et la démonstration, un mode mock est disponible :

### Activation

```env
USE_MOCK_DATA=true
```

### Comportement

- Aucun appel API réel n'est effectué
- Données fictives retournées par `ApiService::getMockData()`
- Permet de tester toute l'UI sans backend

### Comptes de test

| Email | Rôle |
|-------|------|
| admin@test.com | Administrateur |
| responsable@test.com | Responsable |
| enseignant@test.com | Enseignant |

Mot de passe : n'importe lequel (non vérifié en mode mock)

## 🚀 Déploiement

### Production

1. **Variables d'environnement**
   ```env
   APP_ENV=production
   APP_DEBUG=false
   ROOMBOOK_API_URL=https://api.votre-domaine.com
   ```

2. **Optimisation**
   ```bash
   composer install --optimize-autoloader --no-dev
   npm run build
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

3. **Server**
   - Configurer Nginx/Apache avec `public/` comme DocumentRoot
   - Pointage vers `index.php`
   - Permissions `storage/` et `bootstrap/cache/` en écriture

### Configuration de l'API

Les URLs d'API doivent être accessibles depuis le frontend :
- Assurer la communication entre ports (8000 → 8001)
- Configurer CORS côté backend
- Activer HTTPS en production

## 🐛 Dépannage

### Erreur 302 sur la racine

La route `/` redirige vers `/dashboard` qui nécessite une authentification.
Se connecter d'abord ou modifier la route pour afficher la page d'accueil.

### Erreur CORS

1. Vérifier `config/cors.php`
2. S'assurer que le backend a aussi CORS configuré
3. Vérifier les en-têtes de réponse dans les outils développeurs

### Token non persistant

Vérifier que `SESSION_DRIVER=file` dans `.env` et que le dossier `storage/framework/sessions` est accessible en écriture.

### Assets non chargés

```bash
npm install
npm run dev  # ou npm run build
```

## ✅ Tests

Les tests par défaut de Laravel sont présents. Pour exécuter :

```bash
php artisan test --compact
```

Pour ajouter des tests :
- `tests/Feature/` - Tests fonctionnels
- `tests/Unit/` - Tests unitaires

## 📚 Ressources

### Documentation Laravel
- [Laravel 13 Documentation](https://laravel.com/docs/13.x)
- [Blade Templates](https://laravel.com/docs/13.x/blade)
- [Routing](https://laravel.com/docs/13.x/routing)
- [Middleware](https://laravel.com/docs/13.x/middleware)

### API Backend (Lots 1-2-3)
Consulter les fichiers README dans :
- `RoomBook1/`
- `RoomBook2/`
- `roombook-lot3/`

## 📝 Conventions

### Nommage

- Contrôleurs : `PascalCase` + `Controller`
- Routes : `snake_case` (ex: `bookings.pending`)
- Vues : `dossier/fichier.blade.php`
- Classes : `PascalCase` (PSR-4)

### Organisation du code

- Une action par méthode de contrôleur
- Validation avec `$request->validate()`
- Redirections avec `with()` pour messages flash
- API calls via `ApiService` uniquement

## 👥 Contact

Pour toute question concernant ce lot :
- Consulter la documentation interne du projet
- Examiner les commentaires dans le code
- Vérifier les routes avec `php artisan route:list`

---

**RoomBook - Lot 4** - Interface Utilisateur & Vues Planning  
Développé avec Laravel 13, Bootstrap 5, et vanilla JavaScript
