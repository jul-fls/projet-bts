<?php
    ini_set('display_errors', 1); //Affichage des erreurs
    ini_set('display_startup_errors', 1); //Affichage des erreurs au démarrage
    error_reporting(E_ALL); //Affichage des erreurs
    mysqli_report(MYSQLI_REPORT_ALL ^ MYSQLI_REPORT_INDEX); //Affichage des erreurs
?>