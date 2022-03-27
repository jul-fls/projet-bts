<?php
    require __DIR__.'/../vendor/autoload.php';
    $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__.'/../../');
    $dotenv->load();
    $serveur = $_SERVER['DB_HOST'];
    $identifiant_bdd = $_SERVER['DB_USER'];
    $motdepasse_bdd = $_SERVER['DB_PASSWORD'];
    $bdd = $_SERVER['DB_NAME'];
    $conn = mysqli_connect($serveur,$identifiant_bdd,$motdepasse_bdd,$bdd);
    if(!$conn){
        die("Erreur de connexion à la bdd");
    }
    date_default_timezone_set('Europe/Paris');
?>
