<?php
    $title = "Ajouter un utilisateur";
    require_once "commons/header.php";
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
        if(isset($_POST['nom_utilisateur'])&&isset($_POST['prenom_utilisateur'])&&isset($_POST['type_utilisateur'])&&isset($_POST['role'])&&isset($_POST['description'])&&isset($_POST['login'])&&isset($_POST['email'])&&isset($_POST['telephone'])){
            $sanitized_nom_utilisateur = filter_var($_POST['nom_utilisateur'],FILTER_SANITIZE_STRING);
            $sanitized_prenom_utilisateur = filter_var($_POST['prenom_utilisateur'],FILTER_SANITIZE_STRING);
            $sanitized_type_utilisateur = filter_var($_POST['type_utilisateur'],FILTER_SANITIZE_NUMBER_INT);
            $sanitized_role = filter_var($_POST['role'],FILTER_SANITIZE_NUMBER_INT);
            $sanitized_description = filter_var($_POST['description'],FILTER_SANITIZE_STRING);
            $sanitized_login = filter_var($_POST['login'],FILTER_SANITIZE_STRING);
            $sanitized_email = filter_var($_POST['email'],FILTER_SANITIZE_EMAIL);
            $sanitized_telephone = filter_var($_POST['telephone'],FILTER_SANITIZE_STRING);
            require "commons/dbconfig.php";
            $sql = 'INSERT IGNORE INTO utilisateurs (utilisateurs.nom_utilisateur, utilisateurs.prenom_utilisateur, utilisateurs.type_utilisateur, utilisateurs.role, utilisateurs.description, utilisateurs.login, utilisateurs.email, utilisateurs.telephone) VALUES (?, ?, ?, ?, ?, ?, ?, ?);';
            $stmt = mysqli_prepare($conn,$sql);
            mysqli_stmt_bind_param($stmt, 'ssiissss',$sanitized_nom_utilisateur,$sanitized_prenom_utilisateur,$sanitized_type_utilisateur,$sanitized_role,$sanitized_description,$sanitized_login,$sanitized_email,$sanitized_telephone);
            $status = mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $inserted_id_user = mysqli_insert_id($conn);
            if($conn->affected_rows==0){
                echo '<h1><img src="resources/bad.png" width="5%" height="auto" /> Cet utilisateur existe déjà, merci d\'en choisir un autre !</h1>';
            }else if($conn->affected_rows==1){
                echo '<h1><img src="resources/good.png" width="5%" height="auto"/> Bravo, l\'utilisateur a bien été ajouté !</h1>';
                $sql1 = 'SELECT utilisateurs.email, utilisateurs.login, utilisateurs.nom_utilisateur, utilisateurs.prenom_utilisateur FROM utilisateurs WHERE utilisateurs.id = ? LIMIT 1;';
                $stmt1 = mysqli_prepare($conn,$sql1);
                mysqli_stmt_bind_param($stmt1, 'i',$inserted_id_user);
                $status1 = mysqli_stmt_execute($stmt1);
                $result1 = mysqli_stmt_get_result($stmt1);
                if(mysqli_num_rows($result1)<=0){ //échec de la récupération de l'utilisateur et de ses infos
                    echo '<h1><img src="resources/bad.png" width="5%" height="auto" /> Un problème est survenu, merci de réessayer !</h1>';
                    require_once "commons/footer.php";
                    die();
                }else{ //utilisateur trouvé
                    while ($rowA = mysqli_fetch_assoc($result1)){
                        $output = '<p>Bonjour '.$rowA['prenom_utilisateur'].' '.$rowA['nom_utilisateur'].',</p>';
                        $output.= '<p>Un Administrateur vient de créer votre compte.</p>';
                        $output.= '<p>Pour votre information, vous avez la possibilité de consulter les données de la salle serveur sur <a href="'.$__WEB_ROOT__.'">'.$__WEB_ROOT__.'</a> en vous connectant à votre compte avec l\'identifiant <u><b>'.$rowA['login'].'</b></u></p>';
                        $output.= '<p>Vous n\'avez pas de mot de passe attribué sur le site, nous vous invitons donc à en créer un en utilisant la procédure "mot de passe oublié" sur la page de connexion, merci d\'utiliser cette adresse email <a href="mailto:'.$rowA['email'].'">'.$rowA['email'].'</a> que vous pourrez modifier si vous le souhaitez.</p>';
                        $output.= '<p>Cordialement</p>';
                        $output.= '<p>L\'équipe Informatique du Lycée Sainte Famille Saintonge</p>';
                        $output.= '<img src="'.$__WEB_ROOT__.'resources/logo.jpg"/>';
                        $output.= '<br/>';
                        $output.= '<p>Merci de ne pas répondre, cette adresse e-mail est gérée par un programme informatique.</p>';
                        $output.= '<p>Pour contacter l\'équipe Informatique du Lycée Sainte Famille Saintonge, merci d\'envoyer un email à <a href="mailto:informatique@lyceesaintefamille.com">informatique@lyceesaintefamille.com</a></p>';
			    		$body = $output;
			    		$subject = "Confirmation de création de votre compte";
                        $email_to = $rowA['email'];
                        require "commons/mailconfig.php";
                        if (!$mail->Send()){ //impossible d'envoyer le mail
			    			echo '<h1><img src="resources/bad.png" width="5%" height="auto"/> Une erreur est survenue, impossible d\'envoyer le mail de création de compte, merci de réessayer!</h1>';
			    		}else{ //envoi du mail effectué avec succès
			    			echo '<h1><img src="resources/good.png" width="5%" height="auto"/> Un email de confirmation vient d\'etre envoyé à l\'utilisateur.</h1>';
                        }
                    }
                }
            }else{
                echo 'error='.mysqli_error($conn);
                echo '<h1><img src="resources/bad.png" width="5%" height="auto" /> Un problème est survenu, merci de réessayer !</h1>';
            }
        }
        require_once "commons/footer.php";
        die();
    }else if($_SERVER['REQUEST_METHOD']=='GET'){
        //execute le reste du code de la page
    }else{
        //ne rien faire, méthode invalide
        die();
    }
?>
<br/>
<form action="<?=htmlspecialchars($_SERVER['PHP_SELF'])?>" method="post">
    <table id="ajouter_un_utilisateur" class="w3-table-all full">
        <tbody>
            <tr>
                <th>Nom</th>
                <td>
                    <input id="nom_utilisateur" class="full" type="text" name="nom_utilisateur" required/>
                </td>
            </tr>
            <tr>
                <th>Prénom</th>
                <td>
                    <input id="prenom_utilisateur" class="full" type="text" name="prenom_utilisateur" required/>
                </td>
            </tr>
            <tr>
                <th>Type</th>
                <td>
                    <select id="type_utilisateur" class="w3-select full" name="type_utilisateur" required>
                        <option value="0" selected>Élève</option>
                        <option value="1">Professeur</option>
                        <option value="2">Formateur</option>
                        <option value="3">Personnel</option>
                        <option value="4">Autres</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th>Role</th>
                <td>
                    <select id="role" class="w3-select full" name="role" required>
                        <option value="0" selected>Utilisateur standard</option>
                        <option value="1">Utilisateur Administrateur</option>
                        <option value="2">Utilisateur Super Administrateur</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th>Description</th>
                <td><input id="description" class="full" type="text" name="description" required/></td>
            </tr>
            <tr>
                <th>Identifiant</th>
                <td><input id="login" class="full"type="text" name="login"required/></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><input id="email" class="full" type="email" name="email" required/></td>
            </tr>
            <tr>
                <th>N° de téléphone</th>
                <td><input id="telephone" class="full" type="tel" name="telephone" pattern="[0-9]{10}" required/></td>
            </tr>
        </tbody>
    </table>
    <br/>
    <input type="submit" class="w3-button w3-light-green full" name="submit" value="Enregistrer l'utilisateur dans la base de données"/>
</form>
<?php require_once "commons/footer.php";?>