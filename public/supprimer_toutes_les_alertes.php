<?php
    // require_once "../show_errors.php";
    $title = "Supprimer toutes les alertes";
    require_once "commons/header.php";
    require "commons/dbconfig.php";
    if(!isset($_SESSION['loggedin'])){
        echo '<h1><img src="resources/danger.png" width="5%" height="auto" />Vous n\'etes pas connecté, merci de vous connecter à votre compte !</h1>';
        require_once "commons/footer.php";
        die();
    }
    else if($_SESSION['role']<2){
        echo '<h1><img src="resources/danger.png" width="5%" height="auto" />Vous n\'avez pas l\'autorisation d\'accéder à cette page !</h1>';
        require_once "commons/footer.php";
        die();
    }
    if($_SERVER['REQUEST_METHOD']=='POST'){
        //traitement du formulaire

        $sql = 'TRUNCATE TABLE alertes;';
        $stmt = mysqli_prepare($conn,$sql);
        $status = mysqli_stmt_execute($stmt);

        if(!$status){
            echo '<h1><img src="resources/bad.png" width="5%" height="auto" />Un problème est survenu, merci de réessayer !</h1>';
            echo $conn->error;
        }else{
            echo '<h1><img src="resources/good.png" width="5%" height="auto"/>Bravo, toutes les alertes ont bien été supprimées !</h1>';
        }
    }else if($_SERVER['REQUEST_METHOD']=='GET'){
        //execute le reste du code de la page
    }else{
        //ne rien faire, méthode invalide
        die();
    }
?>
<br/>
<form action="<?=htmlspecialchars($_SERVER['PHP_SELF'])?>" method="POST">
    <button type="submit" class="w3-button w3-light-green full">Supprimer toutes les alertes</button>
</form>
<p>
    <i class="fa fa-2x fa-info-circle"></i>
    Cela supprime toutes les alertes de la base de données
</p>
<?php require_once "commons/footer.php";?>
