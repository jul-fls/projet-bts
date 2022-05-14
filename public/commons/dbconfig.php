<?php
    require __DIR__.'/../vendor/autoload.php'; //Chargement des composants
    $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__.'/../../'); //Chargement des variables d'environnement
    $dotenv->load(); //Chargement des variables d'environnement
    $serveur = $_SERVER['DB_HOST']; //Récupération de l'adresse du serveur
    $identifiant_bdd = $_SERVER['DB_USER']; //Récupération de l'identifiant de la base de données
    $motdepasse_bdd = $_SERVER['DB_PASSWORD']; //Récupération du mot de passe de la base de données
    $bdd = $_SERVER['DB_NAME']; //Récupération du nom de la base de données
    $conn = mysqli_connect($serveur,$identifiant_bdd,$motdepasse_bdd,$bdd); //Connexion à la base de données
    if(!$conn){ //Si la connexion à la base de données a échoué
        die("Erreur de connexion à la bdd"); //Affichage d'un message d'erreur
    } 
    date_default_timezone_set('Europe/Paris'); //Définition du fuseau horaire
?>
