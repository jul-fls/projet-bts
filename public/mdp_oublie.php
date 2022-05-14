<?php
	$title = "Mot de passe oublié"; // Titre de la page
	require_once "commons/header.php"; // Inclusion du fichier d'en-tête
	if($_SERVER['REQUEST_METHOD']=='POST'){ //si le formulaire a été envoyé
        //traitement du formulaire 
		require 'commons/dbconfig.php'; // Inclusion du fichier de configuration de la base de données
		if (isset($_POST["email"]) && (!empty($_POST["email"]))) { //si l'email est renseigné
			$email = filter_var($_POST["email"],FILTER_SANITIZE_EMAIL); //on sécurise l'email
			$email = filter_var($email,FILTER_VALIDATE_EMAIL); //on valide l'email
			if(!$email){ //addresse mail invalide 
				echo '<h1><img src="../resources/bad.png" width="5%" height="auto"/>Adresse email invalide, merci de vérifier que vous avez correctement entré l\'adresse email!</h1>'; //message d'erreur
			}else{ //adresse mail valide 
				$sql = 'SELECT utilisateurs.email, utilisateurs.prenom_utilisateur, utilisateurs.nom_utilisateur, utilisateurs.login FROM utilisateurs WHERE utilisateurs.email = ? LIMIT 1;'; //requête SQL
				$stmt = mysqli_prepare($conn,$sql); //préparation de la requête
				mysqli_stmt_bind_param($stmt, 's',$email); //liaison des paramètres
				mysqli_stmt_execute($stmt); //exécution de la requête
				$result = mysqli_stmt_get_result($stmt); //récupération du résultat de la requête
				if(mysqli_num_rows($result)>0){ //utilisateur trouvé
					while ($row = mysqli_fetch_assoc($result)){ //tant qu'il y a des résultats
						$expFormat = mktime(date("H")+1, date("i"), date("s"), date("m"), date("d"), date("Y")); //date d'expiration du lien
						$expDate = date("Y-m-d H:i:s", $expFormat); //date d'expiration du lien
						$key = md5(time()); //clé de sécurité
						$addKey = substr(md5(uniqid(rand(), 1)), 3, 10); //clé de sécurité
						$key = $key . $addKey; //clé de sécurité
						$sql = 'INSERT INTO password_reset_temp (email, resetkey, expDate) VALUES (?, ?, ?);'; //requête SQL
						$stmt = mysqli_prepare($conn,$sql); //préparation de la requête
						mysqli_stmt_bind_param($stmt, 'sss',$email,$key,$expDate); //liaison des paramètres
						mysqli_stmt_execute($stmt); //exécution de la requête
						$result = mysqli_stmt_get_result($stmt); //récupération du résultat de la requête
						$output = '<p>Bonjour '.$row['prenom_utilisateur'].' '.$row['nom_utilisateur'].',</p>'; //message de bienvenue
						$output.= '<p>Vous venez de demander la réinitialisation du mot de passe de votre compte</p>'; //message de bienvenue
						$output.= '<p>Merci de cliquer sur le lien suivant afin de reinitialiser votre mot de passe.</p>'; //message de bienvenue
						$output.='<p><a href="'.$__WEB_ROOT__.'reset-password.php?key='.$key.'&email='.$email.'&action=reset" target="_blank">'.$__WEB_ROOT__.'reset-password.php?key='.$key.'&email='.$email.'&action=reset</a></p>'; //lien de réinitialisation du mot de passe
						$output.= '<p>Pour votre information, vous avez la possibilité de consulter la totalité de vos donnees_capteurs de repas sur <a href="'.$__WEB_ROOT__.'">'.$__WEB_ROOT__.'</a> en vous connectant à votre compte avec l\'identifiant <u><b>'.$row['login'].'</b></u></p>'; //message de bienvenue
                        $output.= '<p>Cordialement</p>'; //message de bienvenue
                        $output.= '<p>L\'équipe Informatique du Lycée Sainte Famille Saintonge</p>'; //message de bienvenue
                        $output.= '<img src="'.$__WEB_ROOT__.'resources/logo.jpg"/>'; //message de bienvenue
                        $output.= '<br/>'; //message de bienvenue
                        $output.= '<p>Merci de ne pas répondre, cette adresse e-mail est gérée par un programme informatique.</p>'; //message de bienvenue
                        $output.= '<p>Pour contacter l\'équipe Informatique du Lycée Sainte Famille Saintonge, merci d\'envoyer un email à <a href="mailto:informatique@lyceesaintefamille.com">informatique@lyceesaintefamille.com</a></p>'; //message de bienvenue
						$body = $output; //message de bienvenue
						$subject = "Réinitialisation du mot de passe de votre compte"; //sujet du mail
						$email_to = $email; //destinataire du mail
						require_once "commons/mailconfig.php"; //inclusion du fichier de configuration
						if (!$mail->Send()){ //impossible d'envoyer le mail
							echo '<h1><img src="resources/bad.png" width="5%" height="auto"/>Une erreur est survenue, merci de réessayer!</h1>'; //message d'erreur
							echo '<p><a href="/">Revenir à la page d\'accueil</a></p>'; //lien de retour à la page d'accueil
						}else{ //envoi du mail effectué avec succès
							echo '<h1><img src="resources/good.png" width="5%" height="auto"/>Un email vient de vous etre envoyé, veuillez vérifier votre boîte mail, dans une heure le lien sera expiré !</h1>'; //message de confirmation
							echo '<p><a href="se_connecter.php">Revenir à la page de connexion</a></p>'; //lien de retour à la page de connexion
							require_once "commons/footer.php"; //inclusion du fichier de pied de page
						}
					}
				}else{ //utilisateur non trouvé
					echo '<h1><img src="resources/bad.png" width="5%" height="auto"/>Utilisateur non trouvé, merci de vérifier que vous avez correctement entré l\'adresse email.</h1>'; //message d'erreur
					echo '<p><a href="mdp_oublie.php">Revenir à la page Mot de passe oublié</a></p>'; //lien de retour à la page de mot de passe oublié	
				}
			}
		}
		require_once "commons/footer.php"; //inclusion du fichier de pied de page
		die(); //arrêt du script
    }else if($_SERVER['REQUEST_METHOD']=='GET'){ //si la requête est de type GET
        //execute le reste du code de la page
    }else{ //si la requête n'est pas de type GET ou POST
        //ne rien faire, méthode invalide
    }
?>
<div class="mdp_oublie"> <!-- Début de la div mdp_oublie -->
	<h1>Mot de passe oublié</h1> <!-- Titre de la page -->
	<p style="margin-left: 1em;">Par défaut, si vous ne l'avez pas modifié, votre adresse email est votre adresse lycée saintefamille.com (ex: Jean Dupont, j.dupont@lyceesaintefamille.com)</p> <!-- Message d'aide -->
	<form action="<?=htmlspecialchars($_SERVER['PHP_SELF'])?>" method="post"> <!-- Formulaire de connexion -->
		<label for="email"> 
			<i class="fas fa-at"></i> <!-- Icone de l'adresse email -->
		</label>
		<input type="email" name="email" placeholder="Adresse email..." id="email" required> <!-- Champ d'adresse email -->
		<input type="submit" value="Envoyer l'email de réinitialisation"> <!-- Bouton d'envoi de l'email -->
	</form> <!-- Fin du formulaire de connexion -->
</div> <!-- Fin de la div mdp_oublie -->
<?php require_once "commons/footer.php";?> <!-- Inclusion du fichier de pied de page -->