#!/bin/bash

echo "============================================"
echo "  RoomBook - Installation automatique"
echo "  Lot 1 : Gestion des Utilisateurs"
echo "============================================"
echo ""

# Vérification PHP
if ! command -v php &> /dev/null; then
    echo "[ERREUR] PHP n'est pas installé."
    echo "Ubuntu/Debian : sudo apt install php8.2 php8.2-cli php8.2-mbstring php8.2-xml php8.2-curl"
    exit 1
fi
echo "[OK] PHP détecté : $(php --version | head -1)"

# Vérification Composer
if ! command -v composer &> /dev/null; then
    echo "[ERREUR] Composer n'est pas installé."
    echo "Installez via : https://getcomposer.org/download/"
    exit 1
fi
echo "[OK] Composer détecté"

echo ""
echo "[1/5] Installation des dépendances..."
composer install --no-interaction
if [ $? -ne 0 ]; then echo "[ERREUR] composer install échoué."; exit 1; fi

echo ""
echo "[2/5] Copie du fichier .env..."
if [ ! -f .env ]; then
    cp .env.example .env
    echo "[OK] .env créé"
else
    echo "[OK] .env existe déjà"
fi

echo ""
echo "[3/5] Génération de la clé applicative..."
php artisan key:generate
if [ $? -ne 0 ]; then echo "[ERREUR] key:generate échoué."; exit 1; fi

echo ""
echo "============================================"
echo " CONFIGURATION BASE DE DONNÉES"
echo "============================================"
echo ""
echo "Choisissez votre base de données :"
echo "  [1] MySQL / MariaDB"
echo "  [2] SQLite (test rapide, sans installation)"
echo ""
read -p "Votre choix (1 ou 2) : " choix

if [ "$choix" = "2" ]; then
    echo ""
    echo "[SQLite] Configuration..."
    touch database/database.sqlite
    sed -i 's/DB_CONNECTION=mysql/DB_CONNECTION=sqlite/' .env
    DB_PATH="$(pwd)/database/database.sqlite"
    # Commente les lignes MySQL
    sed -i 's/^DB_HOST/#DB_HOST/' .env
    sed -i 's/^DB_PORT/#DB_PORT/' .env
    sed -i 's/^DB_DATABASE=roombook/#DB_DATABASE=roombook/' .env
    sed -i 's/^DB_USERNAME/#DB_USERNAME/' .env
    sed -i 's/^DB_PASSWORD/#DB_PASSWORD/' .env
    echo "DB_DATABASE=$DB_PATH" >> .env
    echo "[OK] SQLite configuré : $DB_PATH"
else
    echo ""
    echo "Editez le fichier .env et configurez :"
    echo "  DB_DATABASE=roombook"
    echo "  DB_USERNAME=votre_user"
    echo "  DB_PASSWORD=votre_mot_de_passe"
    echo ""
    echo "Créez la base MySQL :"
    echo "  mysql -u root -p -e \"CREATE DATABASE roombook CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;\""
    echo ""
    read -p "Appuyez sur Entrée quand la DB est prête..."
fi

echo ""
echo "[4/5] Exécution des migrations..."
php artisan migrate --force
if [ $? -ne 0 ]; then echo "[ERREUR] Migrations échouées. Vérifiez .env"; exit 1; fi

echo ""
echo "[5/5] Insertion des données de test..."
php artisan db:seed --force

echo ""
echo "============================================"
echo "  INSTALLATION TERMINÉE !"
echo "============================================"
echo ""
echo "Démarrez le serveur :"
echo "  php artisan serve"
echo ""
echo "Puis testez sur : http://localhost:8000/api/health"
echo ""
echo "Comptes de test :"
echo "  admin@roombook.fr        / password"
echo "  responsable@roombook.fr  / password"
echo "  enseignant@roombook.fr   / password"
echo ""
