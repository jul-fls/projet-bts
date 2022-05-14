<?php 
    $title = "Afficher les détails du compte"; // Titre de la page
    require_once "commons/header.php"; // Inclusion du header
    if($_SERVER['REQUEST_METHOD']=='POST'){ // Si le formulaire a été envoyé
        //traitement du formulaire
        if(isset($_SESSION['loggedin'])&&isset($_POST['nom_utilisateur'])&&isset($_POST['prenom_utilisateur'])&&isset($_POST['login'])&&isset($_POST['email'])&&isset($_POST['telephone'])){ // Si les champs sont remplis
            $sanitized_nom_utilisateur = filter_var($_POST['nom_utilisateur'],FILTER_SANITIZE_STRING); // On sécurise les données
            $sanitized_prenom_utilisateur = filter_var($_POST['prenom_utilisateur'],FILTER_SANITIZE_STRING); // On sécurise les données
            $sanitized_login = filter_var($_POST['login'],FILTER_SANITIZE_STRING); // On sécurise les données
            $sanitized_email = filter_var($_POST['email'],FILTER_SANITIZE_EMAIL); // On sécurise les données
            $sanitized_telephone = filter_var($_POST['telephone'],FILTER_SANITIZE_STRING); // On sécurise les données
            require "commons/dbconfig.php"; // Inclusion de la connexion à la base de données
            $sql = 'UPDATE utilisateurs SET nom_utilisateur = ?, prenom_utilisateur = ?, login = ?, email = ?, telephone = ? WHERE id = ?;'; // Requête SQL
            $stmt = mysqli_prepare($conn,$sql); // Préparation de la requête
            mysqli_stmt_bind_param($stmt, 'sssssi',$sanitized_nom_utilisateur,$sanitized_prenom_utilisateur,$sanitized_login,$sanitized_email,$sanitized_telephone,$_SESSION['id']); // On lie les paramètres
            $status = mysqli_stmt_execute($stmt); // On exécute la requête
            $result = mysqli_stmt_get_result($stmt); // On récupère le résultat de la requête
            if($conn->affected_rows==0){ // Si la requête n'a pas abouti
                echo '<h1><img src="resources/bad.png" width="5%" height="auto" />Cet identifiant ou cette adresse email est déjà utilisé(e) merci d\'en choisir un(e) autre !</h1>'; // Message d'erreur
            }else if($conn->affected_rows==1){ // Si la requête a abouti
                echo '<h1><img src="resources/good.png" width="5%" height="auto"/>Bravo, votre compte à bien été mis à jour !</h1>'; // Message de confirmation
            }else{ // Si la requête n'a pas abouti
                echo "échec de la mise à jour de votre compte, veuillez réessayer !"; // Message d'erreur
            }
        }
    }else if($_SERVER['REQUEST_METHOD']=='GET'){ // Si le formulaire n'a pas été envoyé
        if(!isset($_SESSION['loggedin'])){ // Si l'utilisateur n'est pas connecté
            echo '<h1><img src="resources/danger.png" width="5%" height="auto" />Vous n\'etes pas connecté, merci de vous connecter à votre compte !</h1>'; // Message d'erreur
            require_once "commons/footer.php"; // Inclusion du footer
            die(); // On arrête le script
        }
        //execute le reste du code de la page
    }else{ 
        //méthode invalide
        die(); // On arrête le script
    }
?>
<h1>Bonjour <?=$_SESSION['prenom_utilisateur']." ".$_SESSION['nom_utilisateur']?></h1> <!-- Affichage du prénom et du nom de l'utilisateur -->
<form action="<?=htmlspecialchars($_SERVER['PHP_SELF'])?>" method="post"> <!-- Formulaire de modification du compte -->
    <table id="afficher_details_compte" class="w3-table-all full"> <!-- Tableau de modification du compte -->
        <tbody> <!-- Corps du tableau -->
            <tr> <!-- Ligne 1 -->
                <th>Nom</th> <!-- Nom -->
                <td><input id="nom_utilisateur" class="full" type="text" name="nom_utilisateur" readonly required value="<?=$_SESSION['nom_utilisateur']?>"/></td> <!-- Champ de saisie du nom -->
                <td><i id="edit_nom_utilisateur" class="fa fa-pencil pointer"></i></td> <!-- Icone d'édition du nom -->
            </tr>
            <tr> <!-- Ligne 2 -->
                <th>Prénom</th> <!-- Prénom -->
                <td><input id="prenom_utilisateur" class="full" type="text" name="prenom_utilisateur" readonly required value="<?=$_SESSION['prenom_utilisateur']?>"/></td> <!-- Champ de saisie du prénom -->
                <td><i id="edit_prenom_utilisateur" class="fa fa-pencil pointer"></i></td> <!-- Icone d'édition du prénom -->
            </tr>
            <tr> <!-- Ligne 3 -->
                <th>Identifiant</th> <!-- Identifiant -->
                <td><input id="login" class="full" type="text" name="login" readonly required value="<?=$_SESSION['login']?>"/></td> <!-- Champ de saisie de l'identifiant -->
                <td><i id="edit_login" class="fa fa-pencil pointer"></i></td> <!-- Icone d'édition de l'identifiant -->
            </tr>
            <tr> <!-- Ligne 4 -->
                <th>Email</th> <!-- Email -->
                <td><input id="email" class="full" type="email" name="email" readonly required value="<?=$_SESSION['email']?>"/></td> <!-- Champ de saisie de l'email -->
                <td><i id="edit_email" class="fa fa-pencil pointer"></i></td> <!-- Icone d'édition de l'email -->
            </tr>
            <tr> <!-- Ligne 5 -->
                <th>N° de téléphone</th> <!-- N° de téléphone -->
                <td><input id="telephone" class="full" type="tel" name="telephone" pattern="[0-9]{10}" readonly required value="<?=$_SESSION['telephone']?>"/></td> <!-- Champ de saisie du numéro de téléphone -->
                <td><i id="edit_telephone" class="fa fa-pencil pointer"></i></td> <!-- Icone d'édition du numéro de téléphone -->
            </tr>
            <tr> <!-- Ligne 6 -->
                <th>Mot de passe</th> <!-- Mot de passe -->
                <td>******</td> <!-- Mot de passe caché -->
                <td><a href="mdp_oublie.php"><i class="fa fa-pencil pointer"></i></a></td> <!-- Icone d'édition du mot de passe -->
            </tr>
        </tbody> <!-- Fin du corps du tableau -->
    </table> <!-- Fin du tableau de modification du compte -->
    <br/> <!-- Saut de ligne -->
    <input type="submit" name="submit" value="Enregistrer les modifications"/> <!-- Bouton d'enregistrement des modifications -->
</form> <!-- Fin du formulaire de modification du compte -->
<script> // Script de modification du compte
    document.getElementById('edit_nom_utilisateur').onclick = function(){ // Si l'utilisateur clique sur l'icone d'édition du nom
        if(document.getElementById('edit_nom_utilisateur').className.includes('fa-pencil')){ // Si l'icone est de type "éditer"
            document.getElementById('nom_utilisateur').readOnly = false; // On rend le champ de saisie du nom non-readonly
            document.getElementById('edit_nom_utilisateur').classList.remove('fa-pencil'); // On retire l'icone "éditer"
            document.getElementById('edit_nom_utilisateur').classList.add('fa-check'); // On ajoute l'icone "valider"
        }else{ // Si l'icone est de type "valider"
            document.getElementById('nom_utilisateur').readOnly = true; // On rend le champ de saisie du nom readonly
            document.getElementById('edit_nom_utilisateur').classList.remove('fa-check'); // On retire l'icone "valider"
            document.getElementById('edit_nom_utilisateur').classList.add('fa-pencil'); // On ajoute l'icone "éditer"
        }
    }
    document.getElementById('edit_prenom_utilisateur').onclick = function(){ // Si l'utilisateur clique sur l'icone d'édition du prénom
        if(document.getElementById('edit_prenom_utilisateur').className.includes('fa-pencil')){ // Si l'icone est de type "éditer"
            document.getElementById('prenom_utilisateur').readOnly = false; // On rend le champ de saisie du prénom non-readonly
            document.getElementById('edit_prenom_utilisateur').classList.remove('fa-pencil'); // On retire l'icone "éditer"
            document.getElementById('edit_prenom_utilisateur').classList.add('fa-check'); // On ajoute l'icone "valider"
        }else{ // Si l'icone est de type "valider"
            document.getElementById('prenom_utilisateur').readOnly = true; // On rend le champ de saisie du prénom readonly
            document.getElementById('edit_prenom_utilisateur').classList.remove('fa-check'); // On retire l'icone "valider"
            document.getElementById('edit_prenom_utilisateur').classList.add('fa-pencil'); // On ajoute l'icone "éditer"
        }
    }
    document.getElementById('edit_login').onclick = function(){ // Si l'utilisateur clique sur l'icone d'édition de l'identifiant
        if(document.getElementById('edit_login').className.includes('fa-pencil')){ // Si l'icone est de type "éditer"
            document.getElementById('login').readOnly = false; // On rend le champ de saisie de l'identifiant non-readonly
            document.getElementById('edit_login').classList.remove('fa-pencil'); // On retire l'icone "éditer"
            document.getElementById('edit_login').classList.add('fa-check'); // On ajoute l'icone "valider"
        }else{ // Si l'icone est de type "valider"
            document.getElementById('login').readOnly = true; // On rend le champ de saisie de l'identifiant readonly
            document.getElementById('edit_login').classList.remove('fa-check'); // On retire l'icone "valider"
            document.getElementById('edit_login').classList.add('fa-pencil'); // On ajoute l'icone "éditer"
        }
    }
    document.getElementById('edit_email').onclick = function(){ // Si l'utilisateur clique sur l'icone d'édition de l'email
        if(document.getElementById('edit_email').className.includes('fa-pencil')){ // Si l'icone est de type "éditer"
            document.getElementById('email').readOnly = false; // On rend le champ de saisie de l'email non-readonly
            document.getElementById('edit_email').classList.remove('fa-pencil'); // On retire l'icone "éditer"
            document.getElementById('edit_email').classList.add('fa-check'); // On ajoute l'icone "valider"
        }else{ // Si l'icone est de type "valider"
            document.getElementById('email').readOnly = true; // On rend le champ de saisie de l'email readonly
            document.getElementById('edit_email').classList.remove('fa-check'); // On retire l'icone "valider"
            document.getElementById('edit_email').classList.add('fa-pencil'); // On ajoute l'icone "éditer"
        } 
    }
    document.getElementById('edit_telephone').onclick = function(){ // Si l'utilisateur clique sur l'icone d'édition du numéro de téléphone
        if(document.getElementById('edit_telephone').className.includes('fa-pencil')){ // Si l'icone est de type "éditer"
            document.getElementById('telephone').readOnly = false; // On rend le champ de saisie du numéro de téléphone non-readonly
            document.getElementById('edit_telephone').classList.remove('fa-pencil'); // On retire l'icone "éditer"
            document.getElementById('edit_telephone').classList.add('fa-check'); // On ajoute l'icone "valider"
        }else{ // Si l'icone est de type "valider"
            document.getElementById('telephone').readOnly = true; // On rend le champ de saisie du numéro de téléphone readonly
            document.getElementById('edit_telephone').classList.remove('fa-check'); // On retire l'icone "valider"
            document.getElementById('edit_telephone').classList.add('fa-pencil'); // On ajoute l'icone "éditer"
        }
    }
</script> 
<p>
    <i class="fa fa-2x fa-info-circle"></i>
    Pour modifier votre mot de passe cliquez sur le crayon à coté de votre mot de passe et complétez la procédure de mot de passe oublié afin de réinitialier votre mot de passe.
</p> <!-- Message d'information -->
<?php require_once "commons/footer.php";?> <!-- On inclut le footer -->