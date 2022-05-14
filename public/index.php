<?php
    $title = "Accueil"; // Titre de la page
    require_once "commons/header.php"; // Inclusion du header
    if(!isset($_SESSION['loggedin'])){ // Si l'utilisateur n'est pas connecté
        echo '<h1><img src="resources/danger.png" width="5%" height="auto" />Vous n\'êtes pas connecté, merci de vous connecter à votre compte !</h1>'; // Message d'erreur
        require_once "commons/footer.php"; // Inclusion du footer
        die(); // Arrêt du script
    }
    echo '<img src="resources/index.jpg" style="height:83vh;display:flex;justify-content:center;margin:auto;" />'; // Image de l'accueil
    require_once "commons/footer.php"; // Inclusion du footer
?>