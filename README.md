# GearStore

GearStore est un site de vente en ligne de matériel gaming (souris, claviers, casques, etc.).

## Ce que fait le site

- **Page principale** : affiche tous les produits avec la possibilité de filtrer par catégorie et de trier par prix ou par nom.
- **Page produit** : montre les détails d'un produit (photos, description, prix).
- **Panier** : l'utilisateur peut ajouter des produits, changer les quantités et voir le total.
- **Connexion** : une page pour se connecter avec un email et un mot de passe.

## Technologies utilisées

- **PHP** : pour afficher les produits depuis la base de données.
- **MySQL** : pour stocker les produits.
- **HTML / CSS** : pour la mise en page et le design.
- **JavaScript** : pour gérer le panier sans recharger la page.

## Structure des fichiers

```
index.php       → page d'accueil avec la liste des produits
product.php     → page d'un seul produit
cart.html       → page du panier
cart.js         → logique du panier (JavaScript)
login.html      → page de connexion
db.php          → connexion à la base de données
css/            → fichiers de style
```
