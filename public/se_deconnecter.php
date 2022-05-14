<?php
    require_once "commons/global.php"; //Inclusion du footer
    session_start(); //Démarrage de la session
    if(isset($_COOKIE['rememberme'])){ //Si le cookie "rememberme" existe
        unset($_COOKIE['rememberme']); //On le supprime
        setcookie('rememberme',null,-1,'/'); //On le réinitialise
    }
    session_destroy(); //On détruit la session
    session_regenerate_id(true); //On régénère un ID de session
    header('Location: '.$__WEB_ROOT__); //On redirige vers la page d'accueil
?>