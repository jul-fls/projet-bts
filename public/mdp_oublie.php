<?php
	$title = "Mot de passe oublié";
	require_once "commons/header.php";
	if($_SERVER['REQUEST_METHOD']=='POST'){
        //traitement du formulaire
		require 'commons/dbconfig.php';
		if (isset($_POST["email"]) && (!empty($_POST["email"]))) {
			$email = filter_var($_POST["email"],FILTER_SANITIZE_EMAIL);
			$email = filter_var($email,FILTER_VALIDATE_EMAIL);
			if(!$email){ //addresse mail invalide
				echo '<h1><img src="../resources/bad.png" width="5%" height="auto"/>Adresse email invalide, merci de vérifier que vous avez correctement entré l\'adresse email!</h1>';
			}else{ //adresse mail valide
				$sql = 'SELECT utilisateurs.email, utilisateurs.prenom_utilisateur, utilisateurs.nom_utilisateur, utilisateurs.login FROM utilisateurs WHERE utilisateurs.email = ? LIMIT 1;';
				$stmt = mysqli_prepare($conn,$sql);
				mysqli_stmt_bind_param($stmt, 's',$email);
				mysqli_stmt_execute($stmt);
				$result = mysqli_stmt_get_result($stmt);
				if(mysqli_num_rows($result)>0){ //utilisateur trouvé
					while ($row = mysqli_fetch_assoc($result)){
						$expFormat = mktime(date("H")+1, date("i"), date("s"), date("m"), date("d"), date("Y"));
						$expDate = date("Y-m-d H:i:s", $expFormat);
						$key = md5(time());
						$addKey = substr(md5(uniqid(rand(), 1)), 3, 10);
						$key = $key . $addKey;
						$sql = 'INSERT INTO password_reset_temp (email, resetkey, expDate) VALUES (?, ?, ?);';
						$stmt = mysqli_prepare($conn,$sql);
						mysqli_stmt_bind_param($stmt, 'sss',$email,$key,$expDate);
						mysqli_stmt_execute($stmt);
						$result = mysqli_stmt_get_result($stmt);
						$output = '<p>Bonjour '.$row['prenom_utilisateur'].' '.$row['nom_utilisateur'].',</p>';
						$output.= '<p>Vous venez de demander la réinitialisation du mot de passe de votre compte</p>';
						$output.= '<p>Merci de cliquer sur le lien suivant afin de reinitialiser votre mot de passe.</p>';
						$output.='<p><a href="'.$__WEB_ROOT__.'reset-password.php?key='.$key.'&email='.$email.'&action=reset" target="_blank">'.$__WEB_ROOT__.'reset-password.php?key='.$key.'&email='.$email.'&action=reset</a></p>';
						$output.= '<p>Pour votre information, vous avez la possibilité de consulter la totalité de vos donnees_capteurs de repas sur <a href="'.$__WEB_ROOT__.'">'.$__WEB_ROOT__.'</a> en vous connectant à votre compte avec l\'identifiant <u><b>'.$row['login'].'</b></u></p>';
                        $output.= '<p>Cordialement</p>';
                        $output.= '<p>L\'équipe Informatique du Lycée Sainte Famille Saintonge</p>';
                        $output.= '<img src="'.$__WEB_ROOT__.'resources/logo.jpg"/>';
                        $output.= '<br/>';
                        $output.= '<p>Merci de ne pas répondre, cette adresse e-mail est gérée par un programme informatique.</p>';
                        $output.= '<p>Pour contacter l\'équipe Informatique du Lycée Sainte Famille Saintonge, merci d\'envoyer un email à <a href="mailto:informatique@lyceesaintefamille.com">informatique@lyceesaintefamille.com</a></p>';
						$body = $output;
						$subject = "Réinitialisation du mot de passe de votre compte";
						$email_to = $email;
						require_once "commons/mailconfig.php";
						if (!$mail->Send()){ //impossible d'envoyer le mail
							echo '<h1><img src="resources/bad.png" width="5%" height="auto"/>Une erreur est survenue, merci de réessayer!</h1>';
							echo '<p><a href="/">Revenir à la page d\'accueil</a></p>';
						}else{ //envoi du mail effectué avec succès
							echo '<h1><img src="resources/good.png" width="5%" height="auto"/>Un email vient de vous etre envoyé, veuillez vérifier votre boîte mail, dans une heure le lien sera expiré !</h1>';
							echo '<p><a href="se_connecter.php">Revenir à la page de connexion</a></p>';
							require_once "commons/footer.php";
						}
					}
				}else{ //utilisateur non trouvé
					echo '<h1><img src="resources/bad.png" width="5%" height="auto"/>Utilisateur non trouvé, merci de vérifier que vous avez correctement entré l\'adresse email.</h1>';
					echo '<p><a href="mdp_oublie.php">Revenir à la page Mot de passe oublié</a></p>';
					
				}
			}
		}
		require_once "commons/footer.php";
		die();
    }else if($_SERVER['REQUEST_METHOD']=='GET'){
        //execute le reste du code de la page
    }else{
        //ne rien faire, méthode invalide
    }
?>
<div class="mdp_oublie">
	<h1>Mot de passe oublié</h1>
	<p style="margin-left: 1em;">Par défaut, si vous ne l'avez pas modifié, votre adresse email est votre adresse lycée saintefamille.com (ex: Jean Dupont, j.dupont@lyceesaintefamille.com)</p>
	<form action="<?=htmlspecialchars($_SERVER['PHP_SELF'])?>" method="post">
		<label for="email">
			<i class="fas fa-at"></i>
		</label>
		<input type="email" name="email" placeholder="Adresse email..." id="email" required>
		<input type="submit" value="Envoyer l'email de réinitialisation">
	</form>
</div>
<?php require_once "commons/footer.php";?>