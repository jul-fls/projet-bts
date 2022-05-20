<?php
	$title = "Se connecter"; //definition du titre de la page
	require_once "commons/header.php"; //inclusion du header
	function rememberMe($user_id){ //fonction qui permet de mémoriser l'utilisateur
		$encryptedCookieData = base64_encode($_SERVER['COOKIE_SALT'].$user_id); //cryptage du cookie
		setcookie("rememberme", $encryptedCookieData, time()+60*60*24*30, "/"); //création du cookie
	}
	if($_SERVER['REQUEST_METHOD']=='POST'){ //si le formulaire est posté
		require("commons/dbconfig.php"); //inclusion de la connexion à la base de données
		isset($_POST['remember']) ? $sanitized_remember = $_POST['remember'] : $sanitized_remember = ""; //récupération du checkbox
		if(isset($_POST['login'])&&isset($_POST['password'])){ //si les champs login et password sont remplis
			$sanitized_login = filter_var($_POST['login'],FILTER_SANITIZE_STRING); //sanitisation du login
			$sanitized_password = filter_var($_POST['password'],FILTER_SANITIZE_STRING); //sanitisation du password
			$sanitized_remember = filter_var($_POST['remember'],FILTER_SANITIZE_NUMBER_INT); //sanitisation du checkbox
			$sql = 'SELECT utilisateurs.id, utilisateurs.nom_utilisateur, utilisateurs.prenom_utilisateur, utilisateurs.type_utilisateur, utilisateurs.role, utilisateurs.description, utilisateurs.login, utilisateurs.password_hash, utilisateurs.email, utilisateurs.telephone FROM utilisateurs WHERE utilisateurs.login = ? LIMIT 1;'; //requête de recherche de l'utilisateur
			$stmt = mysqli_prepare($conn,$sql); //préparation de la requête
			mysqli_stmt_bind_param($stmt, 's',$sanitized_login); //liaison des paramètres de la requête
			mysqli_stmt_execute($stmt); //exécution de la requête
			$result = mysqli_stmt_get_result($stmt); //récupération du résultat de la requête
			if (!$result) { //si la requête ne retourne aucun résultat
				echo "Problème de requete"."<br/>"; //affichage d'un message d'erreur
				echo $conn->error; //affichage de l'erreur
				return json_encode(array()); //retour de la requête
				die(); //arrêt du script
			}
			if(mysqli_num_rows($result)>0){ //si la requête retourne un résultat
				while ($row = mysqli_fetch_assoc($result)){ //tant qu'il y a des résultats
					if(password_verify($sanitized_password,$row["password_hash"])){ //si le mot de passe est correct
						if($__MAINTENANCE_MODE__){ //si le site est en maintenance
							if($row['role']<2){ //si l'utilisateur n'est pas un administrateur
								header('Location: '.$__WEB_ROOT__.'maintenance.php'); //redirection vers la page de maintenance
								die(); //arrêt du script
							}
						}
						if($sanitized_remember === "1"){ //si le checkbox est coché
							rememberMe($row['id']); //mémorisation de l'utilisateur
						}
						session_regenerate_id(); //regénération de la session
						$_SESSION['loggedin'] = TRUE; //mise à jour de la variable de session
						$_SESSION['id'] = $row["id"]; //mise à jour de la variable de session
						$_SESSION['nom_utilisateur'] = $row["nom_utilisateur"]; //mise à jour de la variable de session
						$_SESSION['prenom_utilisateur'] = $row["prenom_utilisateur"]; //mise à jour de la variable de session
						$_SESSION['type_utilisateur'] = $row["type_utilisateur"]; //mise à jour de la variable de session
						$_SESSION['role'] = $row["role"]; //mise à jour de la variable de session
						$_SESSION['description'] = $row["description"]; //mise à jour de la variable de session
						$_SESSION['login'] = $row["login"]; //mise à jour de la variable de session
						$_SESSION['email'] = $row["email"]; //mise à jour de la variable de session
						$_SESSION['telephone'] = $row["telephone"]; //mise à jour de la variable de session
						echo '<script>location.replace("'.$__WEB_ROOT__.'");</script>'; //redirection vers la page d'accueil
					}else { //si le mot de passe est incorrect
						echo '<h1><img src="resources/bad.png" width="5%" height="auto"/> Identifiant ou mot de passe incorrect, merci de bien vouloir réessayer !</h1>'; //affichage d'un message d'erreur
					}
				}
			}       
		}else{ // Impossible de récupérer les données qui aurait du être envoyés par le formulaire
			die('<h1><img src="resources/bad.png" width="5%" height="auto"/> Merci de bien vouloir réessayer en remplissant les 2 champs !</h1>'); //affichage d'un message d'erreur
		}
	}else if($_SERVER['REQUEST_METHOD']=='GET'){ //si le formulaire est get
		if(isset($_SESSION['loggedin'])&&$_SESSION['loggedin']===TRUE){ //si l'utilisateur est connecté
			echo '<script>location.replace("'.$__WEB_ROOT__.'");</script>'; //redirection vers la page d'accueil
			die(); //arrêt du script
		}
		//execute le reste du code de la page
	}else{ //si le formulaire n'est pas post ou get
		die();
	}
?>
<div class="login"> <!-- Début de la div login -->
	<h1>Connexion</h1> <!-- Titre de la page -->
	<form action="<?=htmlspecialchars($_SERVER['PHP_SELF'])?>" method="post"> <!-- Début du formulaire -->
		<label for="login"> <!-- Label du champ login -->
			<i class="fas fa-user"></i> <!-- icone du champ login -->
		</label> <!-- Fin du label du champ login -->
		<input type="text" name="login" placeholder="Identifiant..." id="login" required> <!-- Champ login -->
		<label for="password"> <!-- Label du champ mot de passe -->
			<i class="fas fa-lock"></i> <!-- icone du champ mot de passe -->
		</label> <!-- Champ mot de passe -->
		<input type="password" name="password" placeholder="Mot de passe..." id="password" required> <!-- Champ mot de passe -->
		<input type="submit" value="Se connecter"> <!-- Bouton de connexion -->
		<label id="remember"> <!-- Label du champ mémoriser -->
			<input type="hidden" name="remember" value="0" /> <!-- Champ mémoriser -->
			<input type="checkbox" name="remember" value="1"> Se souvenir de moi <!-- Champ mémoriser -->
		</label> <!-- Fin du label du champ mémoriser -->
      	<a href="mdp_oublie.php">Mot de passe oublié / 1ère connexion</a> <!-- Lien vers la page de mot de passe oublié -->
	</form> <!-- Fin du formulaire -->
</div> <!-- Fin de la div login -->
<?php require_once "commons/footer.php";?> <!-- inclusion du footer -->
