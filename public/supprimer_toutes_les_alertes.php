<?php
    $title = "Supprimer toutes les alertes"; //Titre de la page
    require_once "commons/header.php"; //Inclusion du header
    require "commons/dbconfig.php"; //Inclusion de la base de données
    if(!isset($_SESSION['loggedin'])){ //Si l'utilisateur n'est pas connecté
        echo '<h1><img src="resources/danger.png" width="5%" height="auto" />Vous n\'etes pas connecté, merci de vous connecter à votre compte !</h1>'; //Message d'erreur
        require_once "commons/footer.php"; //Inclusion du footer
        die(); //Arrêt du script
    }
    else if($_SESSION['role']<2){ //Si l'utilisateur n'est pas un super administrateur
        echo '<h1><img src="resources/danger.png" width="5%" height="auto" />Vous n\'avez pas l\'autorisation d\'accéder à cette page !</h1>'; //Message d'erreur
        require_once "commons/footer.php"; //Inclusion du footer
        die(); //Arrêt du script
    } 
    if($_SERVER['REQUEST_METHOD']=='POST'){ //Si la requête est de type POST
        //traitement du formulaire

        $sql = 'TRUNCATE TABLE alertes;'; //Requête SQL
        $stmt = mysqli_prepare($conn,$sql); //Préparation de la requête
        $status = mysqli_stmt_execute($stmt); //Exécution de la requête

        if(!$status){ //Si la requête ne s'est pas exécutée correctement
            echo '<h1><img src="resources/bad.png" width="5%" height="auto" />Un problème est survenu, merci de réessayer !</h1>'; //Message d'erreur
            echo $conn->error; //Affichage de l'erreur
        }else{ //Si la requête s'est exécutée correctement
            echo '<h1><img src="resources/good.png" width="5%" height="auto"/>Bravo, toutes les alertes ont bien été supprimées !</h1>'; //Message de confirmation
        }
    }else if($_SERVER['REQUEST_METHOD']=='GET'){ //Si la requête est de type GET
        //execute le reste du code de la page
    }else{ //Si la requête n'est pas de type POST ou GET
        //ne rien faire, méthode invalide
        die(); //Arrêt du script
    } 
?>
<br/> <!-- Saut de ligne -->
<form action="<?=htmlspecialchars($_SERVER['PHP_SELF'])?>" method="POST"> <!-- Formulaire de confirmation -->
    <button type="submit" class="w3-button w3-light-green full">Supprimer toutes les alertes</button> <!-- Bouton de confirmation -->
</form> <!-- Fin du formulaire de confirmation -->
<p>
    <i class="fa fa-2x fa-info-circle"></i> <!--- Icône d'information -->
    Cela supprime toutes les alertes de la base de données 
</p>
<?php require_once "commons/footer.php";?> <!-- Inclusion du footer -->