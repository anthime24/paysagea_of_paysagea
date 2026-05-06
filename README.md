# Projet

Ajouter ici une description du projet

# Build

Le repo est organisé de la sorte:
* `application` contient toutes les sources applicatives
* `devtools` conserve des outils annexes utiles pour le Dev mais qui ne sont pas livrés en Test/Production
* `docker` comporte les fichiers de build/configuration Docker

## Builder en Dev, Test ou Production

Pour builder l'image docker:
* placez-vous à la racine du projet
* lancez la commande `docker build -t mygardendesigner_paysagea_php:latest -f docker/php7-apache/Dockerfile .`

# Deploy

Pour simplifier le déploiement, un fichier docker-compose.yml très simple a été ajouté.

Pour démarrer le container, en Dev, Test ou Production:
* lancez la commande `cd docker`
* lancez la commande `docker compose up -d`

```
docker exec -it mygardendesigner_paysagea_php php bin/console doctrine:database:create --if-not-exists
docker exec -it mygardendesigner_paysagea_php php bin/console doctrine:schema:update --force
```