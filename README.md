# ProjArt_FAKTIV_24

# Procédure d'installation

Etapes : 
1 - git clone git@github.com:OdinWermeille/ProjArt_FAKTIV_24.git
2 - taper "composer install"
3 - taper "cp .env.example .env"
4 - rentrer dans le fichier .env et configurer la database
5 - taper "php artisan key:generate"
6 - dans le terminal :
    npm i
    npm run dev
7 - dans un deuxieme terminal :
    php artisan migrate:install
	php artisan migrate:fresh --seed
	php artisan serve
8 - ouvrir un navigateur et aller localhost:port_indiqué (normalement 8000, si le port n’est pas occupé sur votre machine)
