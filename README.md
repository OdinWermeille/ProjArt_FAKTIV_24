# ProjArt_FAKTIV_24

# Procédure d'installation

Etapes :  
  
1 - git clone git@github.com:OdinWermeille/ProjArt_FAKTIV_24.git  
2 - cd ./ProjArt_FAKTIV_24  
3 - composer install  
4 - cp .env.example .env   
5 - php artisan key:generate (créer la cle d’application)  
6 - Ouvrez ce dossier dans un éditeur de code muni d’un terminal.  
7 - Dans un terminal, rentrer et valider les instructions suivantes :  
    7.1 - npm i  
    7.2 - npm run dev  
8 - Dans votre database MySQL, créér une base de donnée avec le nom indiqué dans le .env et assignez-lui le mot de passe correct (la base de donnée doit être accessible pour les prochaines étapes).
9 - Dans un deuxième terminal : 
    10.1 - php artisan migrate:fresh --seed 
    10.2 - php artisan serve 
10 - Ouvrir un navigateur
11 - Aller sous localhost:port_indiqué (normalement 8000, si le port n’est pas occupé sur votre machine) 