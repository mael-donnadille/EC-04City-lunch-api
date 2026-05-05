# Documentation API

URL de base en local :

```txt
http://127.0.0.1:8000
```

Les réponses sont au format JSON.

## Authentification

### Connexion d'un livreur

```txt
POST /api/login_check
```

Body JSON :

```json
{
  "email": "livreur@example.com",
  "password": "motdepasse"
}
```

Réponse en cas de succès :

```json
{
  "token": "jwt_token"
}
```

Ce token doit être envoyé pour les routes protégées :

```txt
Authorization: Bearer jwt_token
```

## Produits

Les routes produits sont publiques.

### Liste des produits

```txt
GET /api/products
```

Réponse :

```json
[
  {
    "id": 1,
    "name": "Poulet curry",
    "description": "Plat du jour",
    "price": 12.5,
    "type": "plat"
  }
]
```

### Détail d'un produit

```txt
GET /api/products/{id}
```

Réponse :

```json
{
  "id": 1,
  "name": "Poulet curry",
  "description": "Plat du jour",
  "price": 12.5,
  "type": "plat"
}
```

### Créer un produit

```txt
POST /api/products
```

Body JSON :

```json
{
  "name": "Poulet curry",
  "description": "Plat du jour",
  "price": 12.5,
  "type": "plat"
}
```

Règles :

- `name`, `price` et `type` sont obligatoires.
- `type` doit être `plat` ou `dessert`.
- `price` doit être supérieur à 0.

Code HTTP :

- `201` si le produit est créé.
- `400` si le JSON ou les données ne sont pas valides.

### Modifier un produit

```txt
PUT /api/products/{id}
```

Body JSON possible :

```json
{
  "name": "Tiramisu",
  "description": "Dessert du jour",
  "price": 4.5,
  "type": "dessert"
}
```

Code HTTP :

- `200` si le produit est modifié.
- `400` si les données ne sont pas valides.
- `404` si le produit n'existe pas.

### Supprimer un produit

```txt
DELETE /api/products/{id}
```

Code HTTP :

- `200` si le produit est supprimé.
- `404` si le produit n'existe pas.

## Livreurs

Les routes livreurs sont publiques dans ce livrable.

### Liste des livreurs

```txt
GET /api/delivery-persons
```

Réponse :

```json
[
  {
    "id": 1,
    "firstname": "Samir",
    "lastname": "Martin",
    "email": "samir@example.com",
    "isAvailable": true
  }
]
```

### Détail d'un livreur

```txt
GET /api/delivery-persons/{id}
```

### Créer un livreur

```txt
POST /api/delivery-persons
```

Body JSON :

```json
{
  "firstname": "Samir",
  "lastname": "Martin",
  "email": "samir@example.com",
  "isAvailable": true
}
```

Règles :

- `firstname`, `lastname` et `email` sont obligatoires.
- Un mot de passe temporaire est généré.
- Un sac est créé automatiquement pour le livreur.

Réponse :

```json
{
  "message": "Livreur créé avec succès",
  "temporaryPassword": "a1b2c3d4",
  "deliveryPerson": {
    "id": 1,
    "firstname": "Samir",
    "lastname": "Martin",
    "email": "samir@example.com",
    "isAvailable": true
  }
}
```

### Modifier un livreur

```txt
PUT /api/delivery-persons/{id}
```

Body JSON possible :

```json
{
  "firstname": "Samir",
  "lastname": "Durand",
  "email": "samir.durand@example.com",
  "isAvailable": false
}
```

### Supprimer un livreur

```txt
DELETE /api/delivery-persons/{id}
```

## Sac du livreur

Les routes du sac sont protégées par JWT.

Le livreur connecté agit sur son propre sac.

### Voir le contenu du sac

```txt
GET /api/bag
```

Header :

```txt
Authorization: Bearer jwt_token
```

Réponse :

```json
{
  "deliveryPerson": {
    "id": 1,
    "firstname": "Samir",
    "lastname": "Martin",
    "email": "samir@example.com"
  },
  "items": [
    {
      "product": {
        "id": 1,
        "name": "Poulet curry",
        "type": "plat",
        "price": 12.5
      },
      "quantity": 3
    }
  ]
}
```

### Ajouter un produit dans le sac

```txt
POST /api/bag/items
```

Header :

```txt
Authorization: Bearer jwt_token
```

Body JSON :

```json
{
  "productId": 1,
  "quantity": 3
}
```

Règles :

- `productId` et `quantity` sont obligatoires.
- `quantity` doit être supérieur à 0.
- Si le produit est déjà dans le sac, la quantité est ajoutée.

Code HTTP :

- `201` si le produit est ajouté.
- `400` si les données ne sont pas valides.
- `401` si le livreur n'est pas connecté.
- `404` si le produit n'existe pas.

### Retirer un produit du sac

```txt
DELETE /api/bag/items/{productId}
```

Header :

```txt
Authorization: Bearer jwt_token
```

Code HTTP :

- `200` si le produit est retiré du sac.
- `401` si le livreur n'est pas connecté.
- `404` si le produit n'est pas trouvé dans le sac.

## Codes HTTP utilisés

- `200` : succès.
- `201` : ressource créée.
- `400` : mauvaise requête.
- `401` : authentification manquante ou invalide.
- `404` : ressource introuvable.
