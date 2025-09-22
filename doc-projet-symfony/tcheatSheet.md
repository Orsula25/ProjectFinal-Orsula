synfony serve

symfony server:start
symfony server:stop

## Après clonage repo
---
composer install
(si dépendances js - npm install)

## git 
---

git add .
git commit -m "message"
git push origin https / repo

git remote remoove origin # supprimer remote


## Symfony  

Après avoir configuré le fichier .env avec la connexion à la base de données
---

# rajoutr les packages por l'orm

symfony c composer req symfony/orm-pack
symfony c composer req symfony/maker-bundle --dev

# lancer la création de la bd
symfony console doctrine: database: created

#creation/update des entités
symfony console make:entity

(valable pour créer une nouvelle entité ou pour mettre à jour une entité existante)
 #créer les migrations
 symfony console make:migration
 #mettre à jour la bd
 symfony console doctrine: database: update

symfony console make:migration
symfony console make:migration
