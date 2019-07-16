# jimmy_sweat
Projet numéro 6 de ma formation PHP/Symfony chez Openclassrooms qui consiste à créer un site communautaire avec Symfony.

## Description du projet

Voici les principales fonctionnalités disponibles suivant les différents statuts utilisateur:

  * Le visiteur:
      * Visiter la page d'accueil et parcourir les différentes figures.
      * S'inscrire/Se connecter.
      * Envoyer un message à l'administrateur du site.
      * Avoir accès à une figure et parcourir ses médias ainsi que la liste de ses commentaires.
  * L'utilisateur:
      * **Prérequis:** s'être enregistré via le formulaire d'inscription et avoir validé cette inscription.
      * Accès aux mêmes fonctionnaités que le visiteur.
      * Ajout de commentaires.
      * Ajout de figures.
      * Modification des figures créées par ce même utilisateur.
      * Modification du mot de passe en cas d'oubli.
      * Edition de son profil.
  * Administrateur:
      * **Prérequis:** avoir le status administrateur.
      * Accès aux mêmes fonctionnalités que le visiteur/utilisateur.
      * Ajout/suppression/modification de toutes les figures.
      * Validation/suppression de figures/commentaires.
      * Changement status utilisateur.
      * Suppression utilisateur.

## Contrôle du code

La qualité du code a été validé par [Code climate](https://codeclimate.com/). Vous pouvez accéder au rapport de contrôle en cliquant sur le badge ci-dessous.

[![Maintainability](https://api.codeclimate.com/v1/badges/fbf68612b05bc8d9ce58/maintainability)](https://codeclimate.com/github/sebAvenel/jimmy_sweat/maintainability)

## Prérequis

Php ainsi que Composer doivent être installés sur votre ordinateur afin de pouvoir correctement lancé le blog.

## Installation

  * Téléchargez et dézipper l'archive. Installer le contenu dans le répertoire de votre serveur:
      * Wamp : Répertoire 'www'.
      * Mamp : Répertoire 'htdocs'.
      
  * Renommer le fichier '.env-dist' se trouvant à la racine du projet en '.env' puis y configurer les lignes DATABASE_URL et MAILER_URL
      
  * Ensuite placez-vous dans votre répertoire par le biais de votre console de commande (ou terminal) et renseignez la commande suivante:
      * 'composer install' pour windows.
      * 'php composer.phar install' pour Mac OS.
    
  * Création de la base de données:
      * 'php bin/console doctrine:database:create'
      * 'php bin/console make:migration'
      * 'php bin/console doctrine:migrations:migrate'
    
  * Création de données fictives pour tester le site:
      * 'php bin/console doctrine:fixtures:load'
    
  * Démarrage du serveur de symfony:
      * 'php bin/console server:run'
      
  * Renseigner l'URL suivante dans le navigateur:
      * 'http://localhost:8000/'
      * Ou directement via votre serveur local:
          * Windows: http://localhost/jimmy_sweat/public/
          * Mac: http://localhost:8888/jimmy_sweat/public/
      
Le site apparaît sur votre écran.
Vous pouvez directement vous identifier en tant qu'utilisateur ou administrateur:
  * Utilisateur:
      * Identifiant: testuser@user.com
      * Mot de passe: Testuser01
  * Administrateur:
      * Identifiant: testadmin@admin.com
      * Mot de passe: Testadmin01

## Outils utilisés

  * [Symfony](https://symfony.com/)
  * [Composer](https://getcomposer.org/)
  * [Bootstrap](https://getbootstrap.com/)
  * [Twig](https://twig.symfony.com/)
  
## Auteur

  * Avenel Sébastien
  
  
  
  
  
