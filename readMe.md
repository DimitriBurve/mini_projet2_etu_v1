# création du projet

~~~
php composer.phar create-project symfony/website-skeleton mini_projet2_etu_v1
cp composer.phar mini_projet2_etu_v1
php composer.phar req symfony/asset mailer
php composer.phar req --dev doctrine/doctrine-fixtures-bundle
~~~


dans `.gitignore`

~~~
###> idea ###
# file: ~/.gitignore_global
.DS_Store
.idea
/.idea
###< idea ###
~~~

Dans le fichier .env

~~~
DATABASE_URL=mysql://votreLogin:votreMotDePasse@127.0.0.1:3306/Projet2_S3_etu
~~~


~~~
php bin/console doctrine:database:create

php bin/console doctrine:schema:update --dump-sql
php bin/console doctrine:schema:update --force

php bin/console doctrine:fixtures:load

bin/console server:run
~~~

Mettre à jour la config
de *config/packages/security.yaml* 
voir le fichier

et *config/routes.yaml*

~~~
logout:
    path: /logout
~~~

dans config/services.yaml

~~~
parameters:
    locale: 'fr'
    photos_directory: '%kernel.project_dir%/public/images'
~~~