# RoomBook - Lot 4 (Interface Utilisateur)

Application frontend Laravel pour le système de réservation de salles et matériels RoomBook.

## 📋 Description

Lot 4 du projet RoomBook - Interface Utilisateur & Vues Planning.

Cette application fournit l'interface web pour les 3 rôles utilisateurs :
- **Enseignant** : Consultation du planning, demande de réservation, suivi de ses demandes
- **Responsable** : Traitement des demandes en attente, vue du planning global
- **Administrateur** : Gestion complète des utilisateurs, salles et matériels

## 🚀 Installation

### Prérequis

- PHP 8.3+
- Composer
- Node.js & npm
- Backend RoomBook (Lots 1, 2, 3) fonctionnant sur http://localhost:8001

### Étapes d'installation

1. **Cloner/décompresser le projet** dans `roombook4/`

2. **Installer les dépendances PHP**
   ```bash
   composer install
   ```

3. **Configurer l'environnement**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   
   Éditer le fichier `.env` :
   ```env
   ROOMBOOK_API_URL=http://localhost:8001
   USE_MOCK_DATA=false  # Mettre à true pour mode démo sans API
   ```

4. **Installer les dépendances frontend**
   ```bash
   npm install
   ```

5. **Lancer le serveur de développement**
   ```bash
   composer run dev
   # ou
   php artisan serve
   # dans un autre terminal
   npm run dev
   ```

6. **Accéder à l'application**
   - URL : http://localhost:8000
   - Login : comptes de test fournis sur la page de connexion

## 📦 Structure du projet

```
roombook4/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php      # Gestion authentification
│   │   │   ├── DashboardController.php # Tableaux de bord par rôle
│   │   │   ├── BookingController.php   # Réservations
│   │   │   ├── RoomController.php      # Gestion salles
│   │   │   ├── EquipmentController.php # Gestion matériel
│   │   │   └── UserController.php      # Gestion utilisateurs
│   │   └── Middleware/
│   │       └── ApiAuthMiddleware.php   # Authentification API
│   └── Services/
│       └── ApiService.php              # Service d'appel API
├── config/
│   ├── app.php                         # Config application
│   ├── cors.php                        # Configuration CORS
│   └── roombook.php                    # Config RoomBook API
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php           # Layout principal (dark theme)
│       ├── auth/
│       │   ├── login.blade.php         # Page de connexion
│       │   └── unauthorized.blade.php  # Accès refusé
│       ├── dashboard/
│       │   ├── teacher.blade.php       # Dashboard Enseignant
│       │   ├── manager.blade.php       # Dashboard Responsable
│       │   └── admin.blade.php         # Dashboard Admin
│       ├── bookings/
│       │   ├── index.blade.php         # Mes réservations
│       │   ├── create.blade.php        # Nouvelle réservation
│       │   ├── pending.blade.php       # Demandes en attente
│       │   └── show.blade.php          # Détails réservation
│       ├── rooms/
│       │   ├── index.blade.php         # Liste salles
│       │   ├── create.blade.php        # Créer/éditer salle
│       │   └── edit.blade.php
│       ├── equipment/
│       │   ├── index.blade.php         # Liste matériel
│       │   ├── create.blade.php        # Créer/éditer matériel
│       │   └── edit.blade.php
│       └── users/
│           ├── index.blade.php         # Liste utilisateurs
│           ├── create.blade.php        # Créer/éditer utilisateur
│           └── edit.blade.php
├── routes/
│   └── web.php                         # Routes frontend
├── public/
│   ├── css/                            # Styles CSS
│   └── js/                             # Scripts JS
├── .env                                # Variables d'environnement
├── composer.json                       # Dépendances PHP
├── package.json                        # Dépendances Node.js
└── README.md                           # Cette documentation

```

## 🔧 Configuration

### Variables d'environnement

| Variable | Description | Valeur par défaut |
|----------|-------------|------------------|
| `ROOMBOOK_API_URL` | URL de l'API backend | `http://localhost:8001` |
| `USE_MOCK_DATA` | Active le mode démo (sans API) | `false` |
| `APP_URL` | URL de l'application frontend | `http://localhost:8000` |

### Mode Mock/Demo

Pour tester l'interface sans lancer le backend :

1. Dans `.env`, définir :
   ```env
   USE_MOCK_DATA=true
   ```

2. L'application utilisera des données fictives pour tous les appels API.

3. Comptes de test disponibles (tout mot de passe accepté) :
   - Enseignant : `enseignant@test.com`
   - Responsable : `responsable@test.com`
   - Admin : `admin@test.com`

### CORS (Cross-Origin Resource Sharing)

La configuration CORS est définie dans `config/cors.php` pour permettre les requêtes cross-origin.

## 🎨 Interface Utilisateur

### Thème

- Thème sombre (dark mode) inspiré du template HubSpot Dark Free
- Couleurs principales :
  - Fond principal : `#0d1117`
  - Fond secondaire : `#161b22`
  - Accent vert : `#238636` (boutons, validations)
  - Texte principal : `#f0f6fc`

### Navigation

La navigation est adaptée selon le rôle de l'utilisateur :

- **Enseignant** : Tableau de bord, Nouvelle réservation, Mes réservations
- **Responsable** : Tableau de bord, Demandes en attente
- **Admin** : Gestion Utilisateurs, Salles, Matériels

## 📡 API Consommée

L'application consomme les endpoints suivants (backend Lot 1/2/3) :

### Authentification
- `POST /api/login` - Connexion
- `POST /api/logout` - Déconnexion
- `GET /api/me` - Utilisateur connecté

### Utilisateurs (Admin)
- `GET /api/users` - Liste
- `POST /api/users` - Créer
- `PUT /api/users/{id}` - Modifier
- `DELETE /api/users/{id}` - Supprimer

### Salles (Admin)
- `GET /api/rooms` - Liste
- `POST /api/rooms` - Créer
- `PUT /api/rooms/{id}` - Modifier
- `DELETE /api/rooms/{id}` - Supprimer

### Matériel (Admin)
- `GET /api/equipment` - Liste
- `POST /api/equipment` - Créer
- `PUT /api/equipment/{id}` - Modifier
- `DELETE /api/equipment/{id}` - Supprimer

### Réservations
- `GET /api/bookings/my` - Mes réservations (enseignant)
- `POST /api/bookings` - Créer une demande (enseignant)
- `POST /api/bookings/{id}/cancel` - Annuler (enseignant)
- `GET /api/bookings/pending` - Demandes en attente (responsable)
- `POST /api/bookings/{id}/decision` - Accepter/refuser (responsable)

## 📅 Fonctionnalités principales

### Planning hebdomadaire

Le planning affiche les réservations acceptées sur une vue semaine navigable :
- Navigation semaine précédente/suivante
- Retour rapide à "Aujourd'hui"
- Affichage par créneau horaire
- Code couleur par statut

### Formulaire de réservation

Permet aux enseignants de :
- Sélectionner une salle disponible
- Définir les dates/heures de début et fin
- Spécifier l'objet de la réservation
- Ajouter du matériel supplémentaire (optionnel)
- Validation automatique des conflits

### Gestion des demandes (Responsable)

- Liste des demandes en attente
- Détails complets de chaque demande
- Acceptation ou refus avec motif
- Notification email automatique (backend)

### Gestion administrative (Admin)

CRUD complet pour :
- **Utilisateurs** : Création, modification, suppression, attribution des rôles
- **Salles** : Gestion des capacités, bâtiments, disponibilité
- **Matériel** : Stocks, disponibilité

## ⚙️ Développement

### Commandes Artisan utiles

```bash
# Lancer le serveur
php artisan serve

# Vider les caches
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Lister les routes
php artisan route:list

# Générer le cache (production)
php artisan config:cache
php artisan route:cache

# Tests
php artisan test --compact
```

### Convention de code

- PHP : PSR-12, typage strict
- Blade : templates avec sections (`@section`, `@yield`)
- CSS : Bootstrap 5 + styles custom dans le layout
- JS : Vanilla JavaScript (pas de framework frontend)

### Ajouter une nouvelle vue

1. Créer le contrôleur dans `app/Http/Controllers/`
2. Ajouter la route dans `routes/web.php`
3. Créer la vue Blade dans `resources/views/`
4. Étendre le layout : `@extends('layouts.app')`

## 🐛 Dépannage

### Erreur CORS

Vérifier `config/cors.php` et s'assurer que l'URL de l'API backend est autorisée.

### Connexion API échouée

1. Vérifier que le backend (Lot 3) est en cours d'exécution
2. Vérifier `ROOMBOOK_API_URL` dans `.env`
3. Activer `USE_MOCK_DATA=true` pour tester l'interface seule

### Authentification échouée

1. Vérifier que le token Sanctum est bien reçu du backend
2. Vérifier la session PHP (`storage/framework/sessions`)
3. Clear cache: `php artisan config:clear`

### Assets non chargés

```bash
npm run dev  # ou npm run build pour production
```

## 📄 Licence

Projet académique -RoomBook

## 👥 Équipe

- Lot 1 : Gestion Utilisateurs & Authentification (Backend)
- Lot 2 : Gestion Ressources (Salles & Matériels) (Backend)
- Lot 3 : Cœur du Système de Réservation (Backend)
- Lot 4 : Interface Utilisateur & Vues Planning (Frontend - **Ce dossier**)
