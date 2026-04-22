@echo off
echo ============================================
echo   RoomBook - Installation automatique
echo   Lot 1 : Gestion des Utilisateurs
echo ============================================
echo.

REM Vérification de PHP
php --version >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERREUR] PHP n'est pas installe ou pas dans le PATH.
    echo Installez PHP 8.2+ depuis https://www.php.net/downloads
    pause
    exit /b 1
)
echo [OK] PHP detecte

REM Vérification de Composer
composer --version >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERREUR] Composer n'est pas installe.
    echo Installez Composer depuis https://getcomposer.org/download/
    pause
    exit /b 1
)
echo [OK] Composer detecte

echo.
echo [1/5] Installation des dependances...
composer install --no-interaction
if %errorlevel% neq 0 (
    echo [ERREUR] composer install a echoue.
    pause
    exit /b 1
)

echo.
echo [2/5] Copie du fichier .env...
if not exist .env (
    copy .env.example .env
    echo [OK] .env cree
) else (
    echo [OK] .env existe deja
)

echo.
echo [3/5] Generation de la cle applicative...
php artisan key:generate
if %errorlevel% neq 0 (
    echo [ERREUR] key:generate a echoue.
    pause
    exit /b 1
)

echo.
echo ============================================
echo  CONFIGURATION BASE DE DONNEES
echo ============================================
echo.
echo Choisissez votre base de donnees :
echo   [1] MySQL / MariaDB
echo   [2] SQLite (test rapide, sans installation)
echo.
set /p choix="Votre choix (1 ou 2) : "

if "%choix%"=="2" (
    echo.
    echo [SQLite] Configuration...
    if not exist database\database.sqlite (
        type nul > database\database.sqlite
    )
    powershell -Command "(gc .env) -replace 'DB_CONNECTION=mysql', 'DB_CONNECTION=sqlite' | Out-File -encoding utf8 .env"
    powershell -Command "(gc .env) -replace '^DB_HOST', '#DB_HOST' | Out-File -encoding utf8 .env"
    powershell -Command "(gc .env) -replace '^DB_PORT', '#DB_PORT' | Out-File -encoding utf8 .env"
    powershell -Command "(gc .env) -replace '^DB_DATABASE=roombook', '#DB_DATABASE=roombook' | Out-File -encoding utf8 .env"
    powershell -Command "(gc .env) -replace '^DB_USERNAME', '#DB_USERNAME' | Out-File -encoding utf8 .env"
    powershell -Command "(gc .env) -replace '^DB_PASSWORD', '#DB_PASSWORD' | Out-File -encoding utf8 .env"
    echo [OK] SQLite configure
) else (
    echo.
    echo Ouvrez le fichier .env et configurez :
    echo   DB_DATABASE=roombook
    echo   DB_USERNAME=votre_user
    echo   DB_PASSWORD=votre_mot_de_passe
    echo.
    echo Creez aussi la base de donnees dans MySQL :
    echo   CREATE DATABASE roombook CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    echo.
    pause
)

echo.
echo [4/5] Execution des migrations...
php artisan migrate --force
if %errorlevel% neq 0 (
    echo [ERREUR] Les migrations ont echoue. Verifiez la configuration DB dans .env
    pause
    exit /b 1
)

echo.
echo [5/5] Insertion des donnees de test...
php artisan db:seed --force

echo.
echo ============================================
echo   INSTALLATION TERMINEE !
echo ============================================
echo.
echo Demarrez le serveur avec :
echo   php artisan serve
echo.
echo Puis testez sur : http://localhost:8000/api/health
echo.
echo Comptes de test :
echo   admin@roombook.fr        / password
echo   responsable@roombook.fr  / password
echo   enseignant@roombook.fr   / password
echo.
pause
