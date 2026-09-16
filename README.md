# DarFix

DarFix est une plateforme Laravel de mise en relation entre clients et prestataires de services a domicile.

## Fonctionnalites

- Authentification, verification email et reinitialisation du mot de passe avec Laravel Breeze.
- Roles `client`, `prestataire` et `admin`.
- Profils prestataires avec competences, experience, disponibilite et photo.
- Missions avec categorie, budget, date souhaitee et photos.
- Offres avec prix, message et delai d execution.
- Acceptation d une offre et creation automatique d une prestation.
- Messagerie privee et notifications en base de donnees.
- Evaluations apres terminaison d une mission.
- Dashboards client, prestataire et administrateur.
- Recherche, filtres et pagination des missions disponibles.
- Export PDF des prestations terminees.

## Prerequis

- PHP 8.3 ou superieur
- Composer 2
- Node.js 22 et npm
- SQLite pour le developpement ou MySQL 8 pour Docker/production

## Installation locale

```bash
composer install
copy .env.example .env
php artisan key:generate
if not exist database\database.sqlite type nul > database\database.sqlite
php artisan migrate --seed
npm install
npm run build
php artisan storage:link
php artisan serve
```

Sous Linux/macOS, remplacer `copy` et `type nul` par les commandes equivalentes.

## Comptes de demonstration

Les seeders creent les comptes suivants avec le mot de passe `password123`:

- `client@bricolink.com`
- `prestataire@bricolink.com`
- `admin@bricolink.com`

## Tests

```bash
php artisan test
```

## Docker

Configurer `.env` avec MySQL:

```dotenv
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=brico_link
DB_USERNAME=brico
DB_PASSWORD=brico_password
```

Puis lancer:

```bash
docker compose up --build
docker compose exec app php artisan migrate --seed
docker compose exec app php artisan storage:link
```

L application est disponible sur `http://localhost:8000`.

## CI

GitHub Actions execute les migrations, les tests PHPUnit et le build Vite sur chaque push vers `main` et chaque pull request.

## Choix d authentification

L application est une application Blade web et utilise l authentification par session de Laravel Breeze. Sanctum n est pas necessaire tant qu aucune API mobile ou authentification par token n est exposee.
