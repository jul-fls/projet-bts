<?php
    $title = "Supprimer un utilisateur";
    require_once "commons/header.php";
    if(!isset($_SESSION['loggedin'])){
        echo '<h1><img src="resources/danger.png" width="5%" height="auto" />Vous n\'etes pas connecté, merci de vous connecter à votre compte !</h1>';
        require_once "commons/footer.php";
        die();
    }else if($_SESSION['role']<2){
        echo '<h1><img src="resources/danger.png" width="5%" height="auto" />Vous n\'avez pas l\'autorisation d\'accéder à cette page !</h1>';
        require_once "commons/footer.php";
        die();
    }
    if(isset($_GET['id'])){
        require("commons/dbconfig.php");
        $condition = filter_var($_GET['id'],FILTER_SANITIZE_NUMBER_INT);
        $sql = 'DELETE FROM utilisateurs WHERE utilisateurs.id = ? ;';
        $stmt = mysqli_prepare($conn,$sql);
        mysqli_stmt_bind_param($stmt, 'i',$condition);
        $status = mysqli_stmt_execute($stmt);
        if (!$status) {
            echo '<h1><img src="resources/bad.png" width="5%" height="auto"/>L\'utilisateur demandé n\'as pas pu être supprimé !</h1>';
        }else{
            echo '<h1><img src="resources/good.png" width="5%" height="auto"/>L\'utilisateur demandé a bien été supprimé !</h1>';
        }
    }
    require_once "commons/footer.php";
?>