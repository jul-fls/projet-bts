<?php
    $title = "Ajouter un utilisateur"; // Titre de la page
    require_once "commons/header.php"; // Inclusion du header
    if(!isset($_SESSION['loggedin'])){ // Si l'utilisateur n'est pas connecté
        echo '<h1><img src="resources/danger.png" width="5%" height="auto" />Vous n\'etes pas connecté, merci de vous connecter à votre compte !</h1>'; // Message d'erreur
        require_once "commons/footer.php"; // Inclusion du footer
        die(); // On arrête l'execution du script
    }
    else if($_SESSION['role']<2){ // Si l'utilisateur n'est pas un super administrateur
        echo '<h1><img src="resources/danger.png" width="5%" height="auto" />Vous n\'avez pas l\'autorisation d\'accéder à cette page !</h1>'; // Message d'erreur
        require_once "commons/footer.php"; // Inclusion du footer
        die(); // On arrête l'execution du script
    }
    if($_SERVER['REQUEST_METHOD']=='POST'){ // Si la requête est en POST
        //traitement du formulaire 
        if(isset($_POST['nom_utilisateur'])&&isset($_POST['prenom_utilisateur'])&&isset($_POST['type_utilisateur'])&&isset($_POST['role'])&&isset($_POST['description'])&&isset($_POST['login'])&&isset($_POST['email'])&&isset($_POST['telephone'])){ // Si les champs sont remplis
            $sanitized_nom_utilisateur = filter_var($_POST['nom_utilisateur'],FILTER_SANITIZE_STRING); // On sécurise les données
            $sanitized_prenom_utilisateur = filter_var($_POST['prenom_utilisateur'],FILTER_SANITIZE_STRING); // On sécurise les données
            $sanitized_type_utilisateur = filter_var($_POST['type_utilisateur'],FILTER_SANITIZE_NUMBER_INT); // On sécurise les données
            $sanitized_role = filter_var($_POST['role'],FILTER_SANITIZE_NUMBER_INT); // On sécurise les données
            $sanitized_description = filter_var($_POST['description'],FILTER_SANITIZE_STRING); // On sécurise les données
            $sanitized_login = filter_var($_POST['login'],FILTER_SANITIZE_STRING); // On sécurise les données
            $sanitized_email = filter_var($_POST['email'],FILTER_SANITIZE_EMAIL); // On sécurise les données
            $sanitized_telephone = filter_var($_POST['telephone'],FILTER_SANITIZE_STRING); // On sécurise les données
            require "commons/dbconfig.php"; // Inclusion de la connexion à la base de données
            $sql = 'INSERT IGNORE INTO utilisateurs (utilisateurs.nom_utilisateur, utilisateurs.prenom_utilisateur, utilisateurs.type_utilisateur, utilisateurs.role, utilisateurs.description, utilisateurs.login, utilisateurs.email, utilisateurs.telephone) VALUES (?, ?, ?, ?, ?, ?, ?, ?);'; // Requête SQL
            $stmt = mysqli_prepare($conn,$sql); // On prépare la requête
            mysqli_stmt_bind_param($stmt, 'ssiissss',$sanitized_nom_utilisateur,$sanitized_prenom_utilisateur,$sanitized_type_utilisateur,$sanitized_role,$sanitized_description,$sanitized_login,$sanitized_email,$sanitized_telephone); // On lie les paramètres
            $status = mysqli_stmt_execute($stmt); // On exécute la requête
            $result = mysqli_stmt_get_result($stmt); // On récupère le résultat de la requête
            $inserted_id_user = mysqli_insert_id($conn); // On récupère l'id de l'utilisateur inséré
            if($conn->affected_rows==0){ // Si la requête n'a pas abouti
                echo '<h1><img src="resources/bad.png" width="5%" height="auto" /> Cet utilisateur existe déjà, merci d\'en choisir un autre !</h1>'; // Message d'erreur
            }else if($conn->affected_rows==1){ // Si la requête a abouti
                echo '<h1><img src="resources/good.png" width="5%" height="auto"/> Bravo, l\'utilisateur a bien été ajouté !</h1>'; // Message de confirmation
                $sql1 = 'SELECT utilisateurs.email, utilisateurs.login, utilisateurs.nom_utilisateur, utilisateurs.prenom_utilisateur FROM utilisateurs WHERE utilisateurs.id = ? LIMIT 1;'; // Requête SQL
                $stmt1 = mysqli_prepare($conn,$sql1); // On prépare la requête
                mysqli_stmt_bind_param($stmt1, 'i',$inserted_id_user); // On lie les paramètres
                $status1 = mysqli_stmt_execute($stmt1); // On exécute la requête
                $result1 = mysqli_stmt_get_result($stmt1); // On récupère le résultat de la requête
                if(mysqli_num_rows($result1)<=0){ //échec de la récupération de l'utilisateur et de ses infos
                    echo '<h1><img src="resources/bad.png" width="5%" height="auto" /> Un problème est survenu, merci de réessayer !</h1>'; // Message d'erreur
                    require_once "commons/footer.php"; // Inclusion du footer
                    die(); // On arrête l'execution du script
                }else{ //utilisateur trouvé
                    while ($rowA = mysqli_fetch_assoc($result1)){ // On récupère les informations de l'utilisateur
                        $output = '<p>Bonjour '.$rowA['prenom_utilisateur'].' '.$rowA['nom_utilisateur'].',</p>'; // On prépare le message
                        $output.= '<p>Un Administrateur vient de créer votre compte.</p>'; // On prépare le message
                        $output.= '<p>Pour votre information, vous avez la possibilité de consulter les données de la salle serveur sur <a href="'.$__WEB_ROOT__.'">'.$__WEB_ROOT__.'</a> en vous connectant à votre compte avec l\'identifiant <u><b>'.$rowA['login'].'</b></u></p>'; // On prépare le message
                        $output.= '<p>Vous n\'avez pas de mot de passe attribué sur le site, nous vous invitons donc à en créer un en utilisant la procédure "mot de passe oublié" sur la page de connexion, merci d\'utiliser cette adresse email <a href="mailto:'.$rowA['email'].'">'.$rowA['email'].'</a> que vous pourrez modifier si vous le souhaitez.</p>'; // On prépare le message
                        $output.= '<p>Cordialement</p>'; // On prépare le message
                        $output.= '<p>L\'équipe Informatique du Lycée Sainte Famille Saintonge</p>'; // On prépare le message
                        $output.= '<img src="'.$__WEB_ROOT__.'resources/logo.jpg"/>'; // On prépare le message
                        $output.= '<br/>'; // On prépare le message
                        $output.= '<p>Merci de ne pas répondre, cette adresse e-mail est gérée par un programme informatique.</p>'; // On prépare le message
                        $output.= '<p>Pour contacter l\'équipe Informatique du Lycée Sainte Famille Saintonge, merci d\'envoyer un email à <a href="mailto:informatique@lyceesaintefamille.com">informatique@lyceesaintefamille.com</a></p>'; // On prépare le message
			    		$body = $output; // On prépare le message
			    		$subject = "Confirmation de création de votre compte"; // On prépare le sujet
                        $email_to = $rowA['email']; // On prépare l'adresse e-mail
                        require "commons/mailconfig.php"; // Inclusion du fichier de configuration
                        if (!$mail->Send()){ //impossible d'envoyer le mail
			    			echo '<h1><img src="resources/bad.png" width="5%" height="auto"/> Une erreur est survenue, impossible d\'envoyer le mail de création de compte, merci de réessayer!</h1>'; // Message d'erreur
			    		}else{ //envoi du mail effectué avec succès
			    			echo '<h1><img src="resources/good.png" width="5%" height="auto"/> Un email de confirmation vient d\'etre envoyé à l\'utilisateur.</h1>'; // Message de confirmation
                        }
                    }
                }
            }else{ // Si la requête n'a pas abouti
                echo 'error='.mysqli_error($conn); // Message d'erreur
                echo '<h1><img src="resources/bad.png" width="5%" height="auto" /> Un problème est survenu, merci de réessayer !</h1>'; // Message d'erreur
            }
        }
        require_once "commons/footer.php"; // Inclusion du footer
        die(); // On arrête l'execution du script
    }else if($_SERVER['REQUEST_METHOD']=='GET'){ // Si la requête est en GET
        //execute le reste du code de la page
    }else{
        //ne rien faire, méthode invalide
        die(); // On arrête l'execution du script
    }
?>
<br/> <!-- Saut de ligne -->
<form action="<?=htmlspecialchars($_SERVER['PHP_SELF'])?>" method="post"> <!-- Formulaire de création de compte -->
    <table id="ajouter_un_utilisateur" class="w3-table-all full"> <!-- Tableau de création de compte -->
        <tbody> <!-- Corps du tableau -->
            <tr> <!-- Ligne 1 -->
                <th>Nom</th> <!-- Nom -->
                <td><input id="nom_utilisateur" class="full" type="text" name="nom_utilisateur" required/></td> <!-- Champ de saisie du nom -->
            </tr> <!-- Fin de la ligne 1 -->
            <tr> <!-- Ligne 2 -->
                <th>Prénom</th> <!-- Prénom -->
                <td><input id="prenom_utilisateur" class="full" type="text" name="prenom_utilisateur" required/></td> <!-- Champ de saisie du prénom -->
            </tr> <!-- Fin de la ligne 2 -->
            <tr> <!-- Ligne 3 -->
                <th>Type</th> <!-- Type -->
                <td>
                    <select id="type_utilisateur" class="w3-select full" name="type_utilisateur" required> <!-- Liste déroulante -->
                        <option value="0" selected>Élève</option> <!-- Option élève -->
                        <option value="1">Professeur</option> <!-- Option professeur -->
                        <option value="2">Formateur</option> <!-- Option formateur -->
                        <option value="3">Personnel</option> <!-- Option personnel -->
                        <option value="4">Autres</option> <!-- Option autres -->
                    </select>
                </td> <!-- Fin de la liste déroulante -->
            </tr> <!-- Fin de la ligne 3 -->
            <tr> <!-- Ligne 4 -->
                <th>Role</th> <!-- Role -->
                <td>
                    <select id="role" class="w3-select full" name="role" required> <!-- Liste déroulante -->
                        <option value="0" selected>Utilisateur standard</option> <!-- Option utilisateur standard -->
                        <option value="1">Utilisateur Administrateur</option> <!-- Option utilisateur administrateur -->
                        <option value="2">Utilisateur Super Administrateur</option> <!-- Option utilisateur super administrateur -->
                    </select>
                </td>
            </tr> <!-- Fin de la ligne 4 -->
            <tr> <!-- Ligne 5 -->
                <th>Description</th> <!-- Description -->
                <td><input id="description" class="full" type="text" name="description" required/></td> <!-- Champ de saisie de la description -->
            </tr> <!-- Fin de la ligne 5 -->
            <tr> <!-- Ligne 6 -->
                <th>Identifiant</th> <!-- Identifiant -->
                <td><input id="login" class="full"type="text" name="login"required/></td> <!-- Champ de saisie de l'identifiant -->
            </tr> <!-- Fin de la ligne 6 -->
            <tr> <!-- Ligne 7 -->
                <th>Email</th> <!-- Email -->
                <td><input id="email" class="full" type="email" name="email" required/></td> <!-- Champ de saisie de l'email -->
            </tr> <!-- Fin de la ligne 7 -->
            <tr> <!-- Ligne 8 -->
                <th>N° de téléphone</th> <!-- N° de téléphone -->
                <td><input id="telephone" class="full" type="tel" name="telephone" pattern="[0-9]{10}" required/></td> <!-- Champ de saisie du numéro de téléphone -->
            </tr> <!-- Fin de la ligne 8 -->
        </tbody> <!-- Fin du corps du tableau -->
    </table> <!-- Fin du tableau de création de compte -->
    <br/> <!-- Saut de ligne -->
    <input type="submit" class="w3-button w3-light-green full" name="submit" value="Enregistrer l'utilisateur dans la base de données"/> <!-- Bouton d'enregistrement de l'utilisateur -->
</form> <!-- Fin du formulaire de création de compte -->
<?php require_once "commons/footer.php";?> <!-- Inclusion du footer -->