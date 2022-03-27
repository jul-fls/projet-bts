<?php
    $title = "Réinitialiser le mot de passe";
    require_once "commons/header.php";
    require 'commons/dbconfig.php';
    if (isset($_GET["key"]) && isset($_GET["email"]) && isset($_GET["action"]) && ($_GET["action"] == "reset") && !isset($_POST["action"])){
        $sanitized_key = filter_var($_GET["key"],FILTER_SANITIZE_STRING);
        $sanitized_email = filter_var($_GET["email"],FILTER_SANITIZE_EMAIL);
        $curDate = date("Y-m-d H:i:s");
        $sql = 'SELECT * FROM password_reset_temp WHERE resetkey = ? AND email = ? LIMIT 1;';
        $stmt = mysqli_prepare($conn,$sql);
        mysqli_stmt_bind_param($stmt, 'ss',$sanitized_key,$sanitized_email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($result)>0){
            $row = mysqli_fetch_assoc($result);
            $expDate = $row['expDate'];
            if ($expDate >= $curDate){
                echo '<div class="mdp_oublie">';
                echo '<h1>Réinitialiser son mot de passe</h1>';
                echo '<form method="post" name="update">';
                echo '<input type="hidden" name="email" value="'.$sanitized_email.'"/>';
                echo '<input type="hidden" name="action" value="update" class="form-control"/>';
                echo '<label for="pass1">';
                echo '<i class="fas fa-lock"></i>';
                echo '</label>';
                echo '<input type="password" name="pass1" placeholder="Nouveau mot de passe.." id="pass1" required>';
                echo '<label for="password">';
                echo '<i class="fas fa-lock"></i>';
                echo '</label>';
                echo '<input type="password" name="pass2" placeholder="Répéter le nouveau mot de passe.." id="pass2" required>';
                echo '<input type="submit" id="reset" value="Réinitialiser le mot de passe">';
                echo '</form>';
                echo '</div>';
            }else{
                echo '<h1><img src="resources/bad.png" width="5%" height="auto"/>Lien expiré !</h1>';
            }
        }else{
            echo '<h1><img src="resources/bad.png" width="5%" height="auto"/>Lien invalide !</h1>';
        }
    }
    if(isset($_POST["email"])&&isset($_POST["action"])&&($_POST["action"]=="update")){
        $pass1 = filter_var($_POST["pass1"],FILTER_SANITIZE_STRING);
        $pass2 = filter_var($_POST["pass2"],FILTER_SANITIZE_STRING);
        $email = filter_var($_POST["email"],FILTER_SANITIZE_EMAIL);
        if($pass1 != $pass2){
            echo '<h1><img src="resources/bad.png" width="5%" height="auto"/>Les mots de passe ne correspondent pas, ils doivent être identiques</h1>';
        }else{
            $password_hash = password_hash($pass1,PASSWORD_BCRYPT);
            $sql = 'UPDATE utilisateurs SET password_hash = ? WHERE email = ? ;';
            $stmt = mysqli_prepare($conn,$sql);
            mysqli_stmt_bind_param($stmt, 'ss', $password_hash, $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $sql = 'DELETE FROM password_reset_temp WHERE email = ? ;';
            $stmt = mysqli_prepare($conn,$sql);
            mysqli_stmt_bind_param($stmt, 's', $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            echo '<h1><img src="resources/good.png" width="5%" height="auto"/>Bravo, votre mot de passe a bien été reinitialisé !</h1>';
            echo '<h1>N\'oubliez pas de rajouter votre numéro de téléphone mobile dans <a href="'.$__WEB_ROOT__.'afficher_les_details_du_compte.php">mon compte</a> si ce n\'est pas déjà fait</h1>';
            echo '<p><a href="se_connecter.php">Revenir à la page de connexion</a></p>';
        }
    }
    require_once "commons/footer.php";
?>