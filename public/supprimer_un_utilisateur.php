<?php
    $title = "Supprimer un utilisateur"; //Titre de la page
    require_once "commons/header.php"; //Inclusion du header
    if(!isset($_SESSION['loggedin'])){ //Si l'utilisateur n'est pas connecté
        echo '<h1><img src="resources/danger.png" width="5%" height="auto" />Vous n\'etes pas connecté, merci de vous connecter à votre compte !</h1>'; //Message d'erreur
        require_once "commons/footer.php"; //Inclusion du footer
        die(); //Arrêt du script
    }else if($_SESSION['role']<2){ //Si l'utilisateur n'est pas un super administrateur
        echo '<h1><img src="resources/danger.png" width="5%" height="auto" />Vous n\'avez pas l\'autorisation d\'accéder à cette page !</h1>'; //Message d'erreur
        require_once "commons/footer.php"; //Inclusion du footer
        die(); //Arrêt du script
    }
    if(isset($_GET['id'])){ //Si l'id de l'utilisateur est présent dans l'URL
        require("commons/dbconfig.php"); //Inclusion de la base de données
        $condition = filter_var($_GET['id'],FILTER_SANITIZE_NUMBER_INT); //Récupération de l'id de l'utilisateur
        $sql = 'DELETE FROM utilisateurs WHERE utilisateurs.id = ? ;'; //Requête SQL
        $stmt = mysqli_prepare($conn,$sql); //Préparation de la requête
        mysqli_stmt_bind_param($stmt, 'i',$condition); //Paramètres de la requête
        $status = mysqli_stmt_execute($stmt); //Exécution de la requête
        if (!$status) { //Si la requête ne s'est pas exécutée correctement
            echo '<h1><img src="resources/bad.png" width="5%" height="auto"/>L\'utilisateur demandé n\'as pas pu être supprimé !</h1>'; //Message d'erreur
        }else{ //Si la requête s'est bien exécutée
            echo '<h1><img src="resources/good.png" width="5%" height="auto"/>L\'utilisateur demandé a bien été supprimé !</h1>'; //Message de confirmation
        }
    }
    require_once "commons/footer.php"; //Inclusion du footer
?>