<?php
    $title = "Supprimer tous les utilisateurs";
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
        
        $sql0= 'SET FOREIGN_KEY_CHECKS=1;';
        $stmt0 = mysqli_prepare($conn,$sql0);
        $status0 = mysqli_stmt_execute($stmt0);

        $sql1= 'UPDATE utilisateurs SET utilisateurs.id = 1 WHERE utilisateurs.id = ?;';
        $stmt1 = mysqli_prepare($conn,$sql1);
        $id = $_SESSION['id'];
        mysqli_stmt_bind_param($stmt1, 'i',$id);
        $status1 = mysqli_stmt_execute($stmt1);

        $sql2= 'SET FOREIGN_KEY_CHECKS=1;';
        $stmt2 = mysqli_prepare($conn,$sql2);
        $status2 = mysqli_stmt_execute($stmt2);
        
        $sql3 = 'DELETE FROM utilisateurs WHERE utilisateurs.id NOT IN (?);';
        $stmt3 = mysqli_prepare($conn,$sql3);
        mysqli_stmt_bind_param($stmt3, 'i',$id);
        $status3 = mysqli_stmt_execute($stmt3);
        $deleted_users = mysqli_affected_rows($conn);

        $sql4= 'ALTER TABLE utilisateurs AUTO_INCREMENT = 2;';
        $stmt4 = mysqli_prepare($conn,$sql4);
        $status4 = mysqli_stmt_execute($stmt4);

        if(!$status0||!$status1||!$status2||!$status3||!$status4){
            echo '<h1><img src="resources/bad.png" width="5%" height="auto" />Un problème est survenu, merci de réessayer !</h1>';
            echo $conn->error;
        }else{
            echo '<h1><img src="resources/good.png" width="5%" height="auto"/>Bravo, '.$deleted_users.' utilisateurs ont bien été supprimés !</h1>';
            $_SESSION['id']=1;
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
    <button type="submit" class="w3-button w3-light-green full">Supprimer tous les utilisateurs</button>
</form>
<p>
    <i class="fa fa-2x fa-info-circle"></i>
    Cela supprime tous les utilisateurs de la base de données excepté :
    <ul>
        <li>L'utilisateur actuel (vous)</li>
    </ul>
</p>
<?php require_once "commons/footer.php";?>
