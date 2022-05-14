<?php
    $__PATH__ = '/var/www/html/public/'; //Définition du chemin du projet
    require_once $__PATH__.'vendor/autoload.php'; //Chargement des composants
    $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__.'/../../'); //Chargement des variables d'environnement
    $dotenv->load(); //Chargement des variables d'environnement
    $__MAINTENANCE_MODE__ = intval(file_get_contents($__PATH__.'commons/maintenance.status')); //Récupération du status de la maintenance
    $__MAIL_DOMAIN__ = $_SERVER['MAIL_DOMAIN']; //Récupération du domaine de l'adresse mail
    $__DOMAIN__ = $_SERVER['DOMAIN']; //Récupération du domaine
    $__WEB_ROOT__ = 'http://'.$__DOMAIN__.'/'; //Récupération du chemin du site web
    
    function mb_ucfirst($string, $encoding){ //Fonction qui met la première lettre en majuscule
        $firstChar = mb_substr($string, 0, 1, $encoding); //Récupération de la première lettre
        $then = mb_substr($string, 1, null, $encoding); //Récupération de la suite de la chaine
        return mb_strtoupper($firstChar, $encoding) . $then; //Retour de la chaine
    }
?>