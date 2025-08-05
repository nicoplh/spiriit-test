## Lancer de docker

- Se rendre dans le dossier du projet
- Exécuter la commande ```docker compose up --build --force-recreate -d``` 
- Éditer le fichier ```entrypoints.json``` dans ```public/build``` pour enlever les :8080 dans les quatre urls générées. (Je n'ai pas pris le temps de corriger mon webpack pour ne pas avoir ce problème.)
- Lancer le script de migration ```docker compose exec php bin/console doctrine:migration:migrate```
- Charger les fixtures avec la commande ```docker compose exec php bin/console doctrine:fixtures:load```

### Commande export CSV 
- Pour générer le fichier CSV, il faut exécuter la commande : ```docker compose exec php bin/console app:product:export```, le fichier se trouve dans ```var```, sous le nom ```products.csv```

### En cas de problème d'assets 
- Vérifier dans le fichier ```entrypoints.json``` dans ```public/build``` que le port n'est pas revenu.

## Urls
- http://localhost/
- http://localhost/admin
- http://localhost/api
