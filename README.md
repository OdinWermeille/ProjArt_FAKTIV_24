# ProjArt_FAKTIV_24

# Procédure d'installation

Etapes : 
1 - git clone git@github.com:OdinWermeille/ProjArt_FAKTIV_24.git  
2 - cd ./ProjArt_FAKTIV_24  
3 - composer install  
4 - cp .env.example .env  
5 - configurer database dans le .env  (ligne ~22)  
    DB_CONNECTION=mysql  
    DB_HOST=127.0.0.1  
    DB_PORT=3306  
    DB_DATABASE=db_faktiv  
    DB_USERNAME=root  
    DB_PASSWORD=root  
6 - php artisan key:generate (créer la cle d’application)  
7 - Ouvrez ce dossier dans un éditeur de code muni d’un terminal.  
8 - Dans un terminal, rentrer et valider les instructions suivantes :  
    8.1 - npm i  
    8.2 - npm run dev  
9 - Dans votre database MySQL, créér une base de donnée avec le nom indiqué dans le .env et assignez-lui le mot de passe correct (la base de donnée doit être accessible pour les prochaines étapes).
10 - Dans un deuxième terminal : 
    10.1 - php artisan migrate:fresh --seed 
    10.2 - php artisan serve 
11 - Ouvrir un navigateur
12 - Aller sous localhost:port_indiqué (normalement 8000, si le port n’est pas occupé sur votre machine) 