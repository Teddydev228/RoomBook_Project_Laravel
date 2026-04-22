# Exemple de commandes pour tester l'application

# 1. Lancer le backend (Lot 3) sur le port 8001
cd ../roombook-lot3
php artisan serve --port=8001

# 2. Dans un autre terminal, lancer le frontend (Lot 4) sur le port 8000
cd roombook4
php artisan serve --port=8000

# 3. Accéder à l'application
# Ouvrir http://localhost:8000 dans le navigateur

# 4. Mode mock (sans backend)
# Éditer .env et définir USE_MOCK_DATA=true
# Relancer le serveur

# 5. Installer les dépendances (première utilisation)
composer install
npm install
npm run dev

# 6. Vider les caches en cas de problème
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 7. Voir les routes disponibles
php artisan route:list

# 8. Exécuter les tests
php artisan test --compact
