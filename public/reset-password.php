<?php
    $title = "Réinitialiser le mot de passe"; // Titre de la page
    require_once "commons/header.php"; // Inclusion du header
    require 'commons/dbconfig.php'; // Inclusion de la connexion à la base de données
    if (isset($_GET["key"]) && isset($_GET["email"]) && isset($_GET["action"]) && ($_GET["action"] == "reset") && !isset($_POST["action"])){ // Si l'utilisateur a cliqué sur le lien de réinitialisation du mot de passe
        $sanitized_key = filter_var($_GET["key"],FILTER_SANITIZE_STRING); // On sécurise la clé
        $sanitized_email = filter_var($_GET["email"],FILTER_SANITIZE_EMAIL); // On sécurise l'email
        $curDate = date("Y-m-d H:i:s"); // On récupère la date courante
        $sql = 'SELECT * FROM password_reset_temp WHERE resetkey = ? AND email = ? LIMIT 1;'; // On récupère les informations de la clé
        $stmt = mysqli_prepare($conn,$sql); // On prépare la requête
        mysqli_stmt_bind_param($stmt, 'ss',$sanitized_key,$sanitized_email); // On lie les paramètres
        mysqli_stmt_execute($stmt); // On exécute la requête
        $result = mysqli_stmt_get_result($stmt); // On récupère le résultat de la requête
        if (mysqli_num_rows($result)>0){ // Si le résultat de la requête est supérieur à 0
            $row = mysqli_fetch_assoc($result); // On récupère les informations de la clé
            $expDate = $row['expDate']; // On récupère la date d'expiration de la clé
            if ($expDate >= $curDate){ // Si la date d'expiration de la clé est supérieur à la date courante
                echo '<div class="mdp_oublie">'; // On affiche le formulaire de réinitialisation du mot de passe
                echo '<h1>Réinitialiser son mot de passe</h1>'; // Titre du formulaire
                echo '<form method="post" name="update">'; // Début du formulaire
                echo '<input type="hidden" name="email" value="'.$sanitized_email.'"/>'; // On récupère l'email
                echo '<input type="hidden" name="action" value="update" class="form-control"/>'; // On récupère l'action
                echo '<label for="pass1">'; // Début du label
                echo '<i class="fas fa-lock"></i>'; // Icône du mot de passe
                echo '</label>'; // Fin du label
                echo '<input type="password" name="pass1" placeholder="Nouveau mot de passe.." id="pass1" required>'; // On affiche le champ du mot de passe
                echo '<label for="password">'; // Début du label
                echo '<i class="fas fa-lock"></i>'; // Icône du mot de passe
                echo '</label>'; // Fin du label
                echo '<input type="password" name="pass2" placeholder="Répéter le nouveau mot de passe.." id="pass2" required>'; // On affiche le champ du mot de passe
                echo '<input type="submit" id="reset" value="Réinitialiser le mot de passe">'; // On affiche le bouton de validation
                echo '</form>'; // Fin du formulaire
                echo '</div>'; // Fin de la div
            }else{ // Sinon
                echo '<h1><img src="resources/bad.png" width="5%" height="auto"/>Lien expiré !</h1>'; // On affiche un message d'erreur
            }
        }else{ // Sinon
            echo '<h1><img src="resources/bad.png" width="5%" height="auto"/>Lien invalide !</h1>'; // On affiche un message d'erreur
        }
    }
    if(isset($_POST["email"])&&isset($_POST["action"])&&($_POST["action"]=="update")){ // Si l'utilisateur a cliqué sur le bouton de validation
        $pass1 = filter_var($_POST["pass1"],FILTER_SANITIZE_STRING); // On sécurise le mot de passe
        $pass2 = filter_var($_POST["pass2"],FILTER_SANITIZE_STRING); // On sécurise le mot de passe
        $email = filter_var($_POST["email"],FILTER_SANITIZE_EMAIL); // On sécurise l'email
        if($pass1 != $pass2){ // Si les deux mots de passe ne sont pas identiques
            echo '<h1><img src="resources/bad.png" width="5%" height="auto"/>Les mots de passe ne correspondent pas, ils doivent être identiques</h1>'; // On affiche un message d'erreur
        }else{ // Sinon
            $password_hash = password_hash($pass1,PASSWORD_BCRYPT); // On hash le mot de passe
            $sql = 'UPDATE utilisateurs SET password_hash = ? WHERE email = ? ;'; // On récupère les informations de la clé
            $stmt = mysqli_prepare($conn,$sql); // On prépare la requête
            mysqli_stmt_bind_param($stmt, 'ss', $password_hash, $email); // On lie les paramètres
            mysqli_stmt_execute($stmt); // On exécute la requête
            $result = mysqli_stmt_get_result($stmt); // On récupère le résultat de la requête
            $sql = 'DELETE FROM password_reset_temp WHERE email = ? ;'; // On récupère les informations de la clé
            $stmt = mysqli_prepare($conn,$sql); // On prépare la requête
            mysqli_stmt_bind_param($stmt, 's', $email); // On lie les paramètres
            mysqli_stmt_execute($stmt); // On exécute la requête
            $result = mysqli_stmt_get_result($stmt); // On récupère le résultat de la requête
            echo '<h1><img src="resources/good.png" width="5%" height="auto"/>Bravo, votre mot de passe a bien été reinitialisé !</h1>'; // On affiche un message de confirmation
            echo '<h1>N\'oubliez pas de rajouter votre numéro de téléphone mobile dans <a href="'.$__WEB_ROOT__.'afficher_les_details_du_compte.php">mon compte</a> si ce n\'est pas déjà fait</h1>'; // On affiche un message de confirmation
            echo '<p><a href="se_connecter.php">Revenir à la page de connexion</a></p>'; // On affiche un message de confirmation
        }
    }
    require_once "commons/footer.php"; // On affiche le footer
?>