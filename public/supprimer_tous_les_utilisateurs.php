<?php
    $title = "Supprimer tous les utilisateurs"; //Titre de la page
    require_once "commons/header.php"; //Inclusion du header
    require "commons/dbconfig.php"; //Inclusion de la base de données
    if(!isset($_SESSION['loggedin'])){ //Si l'utilisateur n'est pas connecté
        echo '<h1><img src="resources/danger.png" width="5%" height="auto" />Vous n\'etes pas connecté, merci de vous connecter à votre compte !</h1>'; //On affiche un message d'erreur
        require_once "commons/footer.php"; //Inclusion du footer
        die(); //On arrête le script
    }
    else if($_SESSION['role']<2){ //Si l'utilisateur n'est pas un super administrateur
        echo '<h1><img src="resources/danger.png" width="5%" height="auto" />Vous n\'avez pas l\'autorisation d\'accéder à cette page !</h1>'; //On affiche un message d'erreur
        require_once "commons/footer.php"; //Inclusion du footer
        die(); //On arrête le script
    } 
    if($_SERVER['REQUEST_METHOD']=='POST'){ //Si la requête est en POST
        //traitement du formulaire de suppression
        
        $sql0= 'SET FOREIGN_KEY_CHECKS=1;'; //On active les contraintes d'intégrité
        $stmt0 = mysqli_prepare($conn,$sql0); //On prépare la requête
        $status0 = mysqli_stmt_execute($stmt0); //On exécute la requête
 
        $sql1= 'UPDATE utilisateurs SET utilisateurs.id = 1 WHERE utilisateurs.id = ?;'; //On met à jour les utilisateurs
        $stmt1 = mysqli_prepare($conn,$sql1); //On prépare la requête
        $id = $_SESSION['id']; //On récupère l'id de l'utilisateur
        mysqli_stmt_bind_param($stmt1, 'i',$id); //On lie les paramètres
        $status1 = mysqli_stmt_execute($stmt1); //On exécute la requête
        
        $sql2 = 'DELETE FROM utilisateurs WHERE utilisateurs.id NOT IN (?);'; //On supprime les utilisateurs
        $stmt2 = mysqli_prepare($conn,$sql3); //On prépare la requête
        mysqli_stmt_bind_param($stmt2, 'i',$id); //On lie les paramètres
        $status2 = mysqli_stmt_execute($stmt2); //On exécute la requête
        $deleted_users = mysqli_affected_rows($conn); //On récupère le nombre d'utilisateurs supprimés
 
        $sql3= 'ALTER TABLE utilisateurs AUTO_INCREMENT = 2;'; //On réinitialise l'auto-incrémentation
        $stmt3 = mysqli_prepare($conn,$sql3); //On prépare la requête
        $status3 = mysqli_stmt_execute($stmt3); //On exécute la requête

        if(!$status0||!$status1||!$status2||!$status3){ //Si une erreur est survenue
            echo '<h1><img src="resources/bad.png" width="5%" height="auto" />Un problème est survenu, merci de réessayer !</h1>'; //On affiche un message d'erreur
            echo $conn->error; //On affiche l'erreur
        }else{
            echo '<h1><img src="resources/good.png" width="5%" height="auto"/>Bravo, '.$deleted_users.' utilisateurs ont bien été supprimés !</h1>'; //On affiche un message de confirmation
            $_SESSION['id']=1; //On met à jour l'id de l'utilisateur
        }
    }else if($_SERVER['REQUEST_METHOD']=='GET'){ //Si la requête est en GET
        //execute le reste du code de la page
    }else{ //Si la requête n'est ni en POST ni en GET
        //ne rien faire, méthode invalide
        die(); //On arrête le script
    }
?>
<br/> <!--- Saut de ligne --->
<form action="<?=htmlspecialchars($_SERVER['PHP_SELF'])?>" method="POST"> <!-- Formulaire de suppression -->
    <button type="submit" class="w3-button w3-light-green full">Supprimer tous les utilisateurs</button> <!-- Bouton de confirmation -->
</form> <!--- Fin du formulaire de suppression --->
<p>
    <i class="fa fa-2x fa-info-circle"></i> <!--- Icône d'information --->
    Cela supprime tous les utilisateurs de la base de données excepté : 
    <ul>
        <li>L'utilisateur actuel (vous)</li>
    </ul>
</p>
<?php require_once "commons/footer.php";?> <!-- Inclusion du footer -->
