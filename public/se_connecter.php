<?php
	$title = "Se connecter";
	require_once "commons/header.php";
	function rememberMe($user_id){
		$encryptedCookieData = base64_encode($_SERVER['COOKIE_SALT'].$user_id);
		setcookie("rememberme", $encryptedCookieData, time()+60*60*24*30, "/");
	}
	if($_SERVER['REQUEST_METHOD']=='POST'){
		require("commons/dbconfig.php");
		isset($_POST['remember']) ? $sanitized_remember = $_POST['remember'] : $sanitized_remember = "";
		if(isset($_POST['login'])&&isset($_POST['password'])){
			$sanitized_login = filter_var($_POST['login'],FILTER_SANITIZE_STRING);
			$sanitized_password = filter_var($_POST['password'],FILTER_SANITIZE_STRING);
			$sanitized_remember = filter_var($_POST['remember'],FILTER_SANITIZE_NUMBER_INT);
			$sql = 'SELECT utilisateurs.id, utilisateurs.nom_utilisateur, utilisateurs.prenom_utilisateur, utilisateurs.type_utilisateur, utilisateurs.role, utilisateurs.description, utilisateurs.login, utilisateurs.password_hash, utilisateurs.email, utilisateurs.telephone FROM utilisateurs WHERE utilisateurs.login = ? LIMIT 1;';
			$stmt = mysqli_prepare($conn,$sql);
			mysqli_stmt_bind_param($stmt, 's',$sanitized_login);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);
			if (!$result) {
				echo "Problème de requete"."<br/>";
				echo $conn->error;
				return json_encode(array());
				die();
			}
			if(mysqli_num_rows($result)>0){
				while ($row = mysqli_fetch_assoc($result)){
					if(password_verify($sanitized_password,$row["password_hash"])){
						if($__MAINTENANCE_MODE__){
							if($row['role']<2){
								header('Location: '.$__WEB_ROOT__.'maintenance.php');
								die();
							}
						}
						if($sanitized_remember === "1"){
							rememberMe($row['id']);
						}
						session_regenerate_id();
						$_SESSION['loggedin'] = TRUE;
						$_SESSION['id'] = $row["id"];
						$_SESSION['nom_utilisateur'] = $row["nom_utilisateur"];
						$_SESSION['prenom_utilisateur'] = $row["prenom_utilisateur"];
						$_SESSION['type_utilisateur'] = $row["type_utilisateur"];
						$_SESSION['role'] = $row["role"];
						$_SESSION['description'] = $row["description"];
						$_SESSION['login'] = $row["login"];
						$_SESSION['email'] = $row["email"];
						$_SESSION['telephone'] = $row["telephone"];
						echo '<script>location.replace("'.$__WEB_ROOT__.'");</script>';
					}else {
						// Mot de passe incorrect
						echo '<h1><img src="resources/bad.png" width="5%" height="auto"/> Identifiant ou mot de passe incorrect, merci de bien vouloir réessayer !</h1>';
					}
				}
			}       
		}else{
			// Impossible de récupérer les données qui aurait du être envoyés
			die('<h1><img src="resources/bad.png" width="5%" height="auto"/> Merci de bien vouloir réessayer en remplissant les 2 champs !</h1>');
		}
	}else if($_SERVER['REQUEST_METHOD']=='GET'){
		if(isset($_SESSION['loggedin'])&&$_SESSION['loggedin']===TRUE){
			echo '<script>location.replace("'.$__WEB_ROOT__.'");</script>';
			die();
		}
		//execute le reste du code de la page
	}else{
		//méthode invalide, ne rien faire
		die();
	}
?>
<div class="login">
	<h1>Connexion</h1>
	<form action="<?=htmlspecialchars($_SERVER['PHP_SELF'])?>" method="post">
		<label for="login">
			<i class="fas fa-user"></i>
		</label>
		<input type="text" name="login" placeholder="Identifiant..." id="login" required>
		<label for="password">
			<i class="fas fa-lock"></i>
		</label>
		<input type="password" name="password" placeholder="Mot de passe..." id="password" required>
		<input type="submit" value="Se connecter">
		<label id="remember">
			<input type="hidden" name="remember" value="0" />
			<input type="checkbox" name="remember" value="1"> Se souvenir de moi
		</label>
      	<a href="mdp_oublie.php">Mot de passe oublié / 1ère connexion</a>
	</form>
</div>
<?php require_once "commons/footer.php";?>