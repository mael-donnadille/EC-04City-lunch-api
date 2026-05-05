# City Lunch API

API REST pour le projet City Lunch.

Le projet permet de gérer :

- les produits : plats et desserts ;
- les livreurs ;
- le sac d'un livreur ;
- la connexion d'un livreur avec un token JWT.

## Technologies utilisées

- PHP 8.2 ou plus
- Symfony 7.4
- Composer
- Doctrine ORM
- MySQL ou MariaDB
- LexikJWTAuthenticationBundle
- PHPUnit

## Dépôt Git

Adresse du dépôt :

```txt
https://github.com/mael-donnadille/EC-04City-lunch-api.git
```

## Installation du projet

Cloner le projet :

```bash
git clone https://github.com/mael-donnadille/EC-04City-lunch-api.git
cd EC-04City-lunch-api
```

Installer les dépendances :

```bash
composer install
```

Créer le fichier `.env.local` si besoin, puis configurer la base de données :

```dotenv
DATABASE_URL="mysql://root:@127.0.0.1:3306/city_lunch_api?serverVersion=8.0.32&charset=utf8mb4"
```

Créer la base de données :

```bash
php bin/console doctrine:database:create
```

Lancer les migrations :

```bash
php bin/console doctrine:migrations:migrate
```

Si les clés JWT ne sont pas présentes, les générer :

```bash
php bin/console lexik:jwt:generate-keypair
```

Lancer le serveur :

```bash
symfony server:start
```

Si Symfony CLI n'est pas installé :

```bash
php -S 127.0.0.1:8000 -t public
```

## Utilisation rapide

URL de base en local :

```txt
http://127.0.0.1:8000
```

Les routes produits et livreurs sont publiques.

Les routes du sac sont protégées par JWT. Il faut d'abord se connecter avec :

```txt
POST /api/login_check
```

Puis il faut envoyer le token dans les requêtes protégées :

```txt
Authorization: Bearer VOTRE_TOKEN
```

## Documentation

La documentation des routes est ici :

```txt
docs/API.md
```

Le modèle conceptuel de données est ici :

```txt
docs/MCD.md
```

## Test

Le projet contient un test métier sur la création d'un produit.

Il vérifie qu'un produit ne peut pas être créé avec un prix négatif.

Commande pour lancer les tests :

```bash
php bin/phpunit
```
