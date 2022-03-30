<?php
    $title = "Accueil";
    require_once "commons/header.php";
    if(!isset($_SESSION['loggedin'])){
        echo '<h1><img src="resources/danger.png" width="5%" height="auto" />Vous n\'êtes pas connecté, merci de vous connecter à votre compte !</h1>';
        require_once "commons/footer.php";
        die();
    }//else{
    //     echo '<h1>Bonjour '.$_SESSION['prenom_utilisateur'].' '.$_SESSION['nom_utilisateur'].' !<br/>Veuillez choisir dans la barre de navigation ci-dessus ce que vous souhaitez faire</h1>';
    // }
    //echo img tag
    echo '<img src="resources/index.jpg" style="height:83vh;display:flex;justify-content:center;margin:auto;" />';
    require_once "commons/footer.php";
?>