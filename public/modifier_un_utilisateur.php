<?php
    $title = "Modifier un utilisateur"; // Titre de la page
    require_once "commons/header.php"; // Inclusion du header
    require "commons/dbconfig.php"; // Inclusion du fichier de configuration de la base de données
    if(!isset($_SESSION['loggedin'])){ //si l'utilisateur n'est pas connecté
        echo '<h1><img src="resources/danger.png" width="5%" height="auto" />Vous n\'etes pas connecté, merci de vous connecter à votre compte !</h1>'; //message d'erreur
        require_once "commons/footer.php"; // Inclusion du footer
        die(); // Arrêt du script
    }
    else if($_SESSION['role']<2){ //si l'utilisateur n'est pas un super administrateur
        echo '<h1><img src="resources/danger.png" width="5%" height="auto" />Vous n\'avez pas l\'autorisation d\'accéder à cette page !</h1>'; //message d'erreur
        require_once "commons/footer.php"; // Inclusion du footer
        die(); // Arrêt du script
    }
    if($_SERVER['REQUEST_METHOD']=='POST'){ // Si la requête est en POST
        //traitement du formulaire 
        if(isset($_SESSION['loggedin'])&&isset($_POST['nom_utilisateur'])&&isset($_POST['prenom_utilisateur'])&&isset($_POST['type_utilisateur'])&&isset($_POST['role'])&&isset($_POST['description'])&&isset($_POST['login'])&&isset($_POST['email'])&&isset($_POST['telephone'])&&isset($_POST['id'])){ // Si les variables existent
            $sanitized_nom_utilisateur = filter_var($_POST['nom_utilisateur'],FILTER_SANITIZE_STRING); // Sanitize le nom de l'utilisateur
            $sanitized_prenom_utilisateur = filter_var($_POST['prenom_utilisateur'],FILTER_SANITIZE_STRING); // Sanitize le prénom de l'utilisateur
            $sanitized_type_utilisateur = filter_var($_POST['type_utilisateur'],FILTER_SANITIZE_NUMBER_INT); // Sanitize le type de l'utilisateur
            $sanitized_role = filter_var($_POST['role'],FILTER_SANITIZE_NUMBER_INT); // Sanitize le rôle de l'utilisateur
            $sanitized_description = filter_var($_POST['description'],FILTER_SANITIZE_STRING); // Sanitize la description de l'utilisateur
            $sanitized_login = filter_var($_POST['login'],FILTER_SANITIZE_STRING); // Sanitize le login de l'utilisateur
            $sanitized_email = filter_var($_POST['email'],FILTER_SANITIZE_EMAIL); // Sanitize l'email de l'utilisateur
            $sanitized_telephone = filter_var($_POST['telephone'],FILTER_SANITIZE_STRING); // Sanitize le téléphone de l'utilisateur
            $sanitized_id = filter_var($_POST['id'],FILTER_SANITIZE_NUMBER_INT); // Sanitize l'id de l'utilisateur
            $sql = 'UPDATE utilisateurs SET utilisateurs.nom_utilisateur = ?, utilisateurs.prenom_utilisateur = ?, utilisateurs.type_utilisateur = ?, utilisateurs.role = ?, utilisateurs.description = ?, utilisateurs.login = ?, utilisateurs.email = ?, utilisateurs.telephone = ? WHERE utilisateurs.id = ? ;'; // Requête SQL
            $stmt = mysqli_prepare($conn,$sql); // Préparation de la requête
            mysqli_stmt_bind_param($stmt, 'ssiissssi',$sanitized_nom_utilisateur,$sanitized_prenom_utilisateur,$sanitized_type_utilisateur,$sanitized_role,$sanitized_description,$sanitized_login,$sanitized_email,$sanitized_telephone,$sanitized_id); // Bind des paramètres
            $status = mysqli_stmt_execute($stmt); // Execution de la requête
            $result = mysqli_stmt_get_result($stmt); // Récupération du résultat de la requête
            if($conn->affected_rows==0){ // Si aucune ligne n'a été affectée
                echo '<h1><img src="resources/bad.png" width="5%" height="auto" />Cet identifiant ou cette adresse email est déjà utilisé(e) merci d\'en choisir un(e) autre !</h1>'; // Message d'erreur
            }else if($conn->affected_rows==1){ // Si une ligne a été affectée
                echo '<h1><img src="resources/good.png" width="5%" height="auo"/>Bravo, l\'utilisateur a bien été mis à jour !</h1>'; // Message de confirmation
            }else{ // Si aucune ligne n'a été affectée
                echo '<h1><img src="resources/bad.png" width="5%" height="auto" />Un problème est survenu, merci de réessayer !</h1>'; // Message d'erreur
            } 
        }
    }else if($_SERVER['REQUEST_METHOD']=='GET'){ // Si la requête est en GET
        //execute le reste du code de la page 
        if(isset($_GET['id'])&&$_GET['id']>0){ // Si l'id est bien défini
            $sanitized_id = filter_var($_GET['id'],FILTER_SANITIZE_NUMBER_INT); // Sanitize l'id
            $sql = 'SELECT utilisateurs.id, utilisateurs.nom_utilisateur, utilisateurs.prenom_utilisateur, utilisateurs.type_utilisateur, utilisateurs.role, utilisateurs.description, utilisateurs.login, utilisateurs.email FROM utilisateurs WHERE utilisateurs.id = ? LIMIT 1;'; // Requête SQL
            $stmt = mysqli_prepare($conn,$sql); // Préparation de la requête
            mysqli_stmt_bind_param($stmt, 'i',$sanitized_id); // Bind des paramètres
            mysqli_stmt_execute($stmt); // Execution de la requête
            $result = mysqli_stmt_get_result($stmt); // Récupération du résultat de la requête
            if(mysqli_num_rows($result)<1){ // Si aucune ligne n'a été retournée
                echo '<h1><img src="resources/bad.png" width="5%" height="auto" />Aucun utilisateur ne porte ce numéro !</h1>'; // Message d'erreur
                require_once "commons/footer.php"; // Inclure le footer
                die(); // Arrête le script
            }else{ // Si une ligne a été retournée
                while ($row = mysqli_fetch_assoc($result)){ // Récupération des données de la requête
                    echo '<br/>'; // Saut de ligne
                    echo '<form action="'.htmlspecialchars($_SERVER['PHP_SELF']).'" method="post">'; // Début du formulaire
                    echo '<input type="hidden" name="id" value="'.$row['id'].'" />'; // Champ caché pour l'id
                    echo '<table id="modifier_un_utilisateur" class="w3-table-all full">'; // Début de la table
                    echo '<tbody>'; // Début du corps de la table
                    echo '<tr>'; // Début de la ligne
                    echo '<th>Nom</th>'; // Colonne Nom
                    echo '<td><input id="nom_utilisateur" class="full" tabindex="-1" aria-disabled="true" type="text" name="nom_utilisateur" readonly required value="'.$row['nom_utilisateur'].'"/></td>'; // Champ Nom
                    echo '<td><i id="edit_nom_utilisateur" class="fa fa-pencil pointer"></i></td>'; // Icône éditer
                    echo '</tr>'; // Fin de la ligne
                    echo '<tr>'; // Début de la ligne
                    echo '<th>Prénom</th>'; // Colonne Prénom
                    echo '<td><input id="prenom_utilisateur" class="full" tabindex="-1" aria-disabled="true" type="text" name="prenom_utilisateur" readonly required value="'.$row['prenom_utilisateur'].'"/></td>'; // Champ Prénom
                    echo '<td><i id="edit_prenom_utilisateur" class="fa fa-pencil pointer"></i></td>'; // Icône éditer
                    echo '</tr>'; // Fin de la ligne
                    echo '<tr>'; // Début de la ligne
                    echo '<th>Type</th>'; // Colonne Type
                    echo '<td><select id="type_utilisateur" class="full readonly" tabindex="-1" aria-disabled="true" name="type_utilisateur" required>'; // Début du select
                    switch($row['type_utilisateur']){ // Switch sur le type d'utilisateur
                        case 0: // Si l'utilisateur est un eleve
                            echo '<option value="0" selected>Élève</option>'; // Option élève
                            echo '<option value="1">Professeur</option>'; // Option professeur
                            echo '<option value="2">Formateur</option>'; // Option formateur
                            echo '<option value="3">Personnel</option>'; // Option personnel
                            echo '<option value="4">Autres</option>'; // Option autres
                            break; // Arrête le switch
                        case 1: // Si l'utilisateur est un professeur
                            echo '<option value="0"Élève</option>'; // Option élève
                            echo '<option value="1" selected>Professeur</option>'; // Option professeur
                            echo '<option value="2">Formateur</option>'; // Option formateur
                            echo '<option value="3">Personnel</option>'; // Option personnel
                            echo '<option value="4">Autres</option>'; // Option autres
                            break; // Arrête le switch
                        case 2: // Si l'utilisateur est un formateur
                            echo '<option value="0">Élève</option>'; // Option élève
                            echo '<option value="1">Professeur</option>'; // Option professeur
                            echo '<option value="2" selected>Formateur</option>'; // Option formateur
                            echo '<option value="3">Personnel</option>'; // Option personnel
                            echo '<option value="4">Autres</option>'; // Option autres
                            break; // Arrête le switch
                        case 3: // Si l'utilisateur est un personnel
                            echo '<option value="0">Élève</option>'; // Option élève
                            echo '<option value="1">Professeur</option>'; // Option professeur
                            echo '<option value="2">Formateur</option>'; // Option formateur
                            echo '<option value="3" selected>Personnel</option>'; // Option personnel
                            echo '<option value="4">Autres</option>'; // Option autres
                            break; // Arrête le switch
                        case 4: // Si l'utilisateur est un autre
                            echo '<option value="0">Élève</option>'; // Option élève
                            echo '<option value="1">Professeur</option>'; // Option professeur
                            echo '<option value="2">Formateur</option>'; // Option formateur
                            echo '<option value="3">Personnel</option>'; // Option personnel
                            echo '<option value="4" selected>Autres</option>'; // Option autres
                            break; // Arrête le switch
                    } 
                    echo '</select>'; // Fin du select
                    echo '</td>'; // Fin de la colonne
                    echo '<td><i id="edit_type_utilisateur" class="fa fa-pencil pointer"></i></td>'; // Icône éditer
                    echo '</tr>'; // Fin de la ligne
                    echo '<tr>'; // Début de la ligne
                    echo '<th>Role</th>'; // Colonne Role
                    echo '<td><select id="role" class="full readonly" tabindex="-1" aria-disabled="true" name="role" required>'; // Début du select
                    if($row['role']==0){ // Si le role est 0
                        echo '<option value="0" selected>Utilisateur standard</option>'; // Option utilisateur standard
                        echo '<option value="1">Utilisateur Administrateur</option>'; // Option utilisateur administrateur
                        echo '<option value="2">Utilisateur Super Administrateur</option>'; // Option utilisateur super administrateur
                    }else if($row['role']==1){ // Si le role est 1
                        echo '<option value="0">Utilisateur standard</option>'; // Option utilisateur standard
                        echo '<option value="1" selected>Utilisateur Administrateur</option>'; // Option utilisateur administrateur
                        echo '<option value="2">Utilisateur Super Administrateur</option>'; // Option utilisateur super administrateur
                    }else{ // Si le role est 2
                        echo '<option value="0">Utilisateur standard</option>'; // Option utilisateur standard
                        echo '<option value="1">Utilisateur Administrateur</option>'; // Option utilisateur administrateur
                        echo '<option value="2" selected>Utilisateur Super Administrateur</option>'; // Option utilisateur super administrateur
                    } 
                    echo '</select>'; // Fin du select
                    echo '</td>'; // Fin de la colonne
                    echo '<td><i id="edit_role" class="fa fa-pencil pointer"></i></td>'; // Icône éditer
                    echo '</tr>'; // Fin de la ligne
                    echo '<tr>'; // Début de la ligne
                    echo '<th>Description</th>'; // Colonne Description
                    echo '<td><input id="description" class="full" tabindex="-1" aria-disabled="true" type="text" name="description" readonly required value="'.$row['description'].'"/></td>'; // Input description
                    echo '<td><i id="edit_description" class="fa fa-pencil pointer"></i></td>'; // Icône éditer
                    echo '</tr>'; // Fin de la ligne
                    echo '<tr>'; // Début de la ligne
                    echo '<th>Identifiant</th>'; // Colonne Identifiant
                    echo '<td><input id="login" class="full" tabindex="-1" aria-disabled="true" type="text" name="login" readonly required value="'.$row['login'].'"/></td>'; // Input login
                    echo '<td><i id="edit_login" class="fa fa-pencil pointer"></i></td>'; // Icône éditer
                    echo '</tr>'; // Fin de la ligne
                    echo '<tr>'; // Début de la ligne
                    echo '<th>Email</th>'; // Colonne Email
                    echo '<td><input id="email" class="full" tabindex="-1" aria-disabled="true" type="email" name="email" readonly required value="'.$row['email'].'"/></td>'; // Input email
                    echo '<td><i id="edit_email" class="fa fa-pencil pointer"></i></td>'; // Icône éditer
                    echo '</tr>'; // Fin de la ligne
                    echo '<tr>'; // Début de la ligne
                    echo '<th>Téléphone</th>'; // Colonne Téléphone
                    echo '<td><input id="telephone" class="full" tabindex="-1" aria-disabled="true" type="tel" name="telephone" pattern="[0-9]{10}" readonly required value="'.$row['telephone'].'"/></td>'; // Input telephone
                    echo '<td><i id="edit_telephone" class="fa fa-pencil pointer"></i></td>'; // Icône éditer
                    echo '</tr>'; // Fin de la ligne
                    echo '</tbody>'; // Fin du tableau
                    echo '</table>'; // Fin du tableau
                    echo '<br/>'; // Saut de ligne
                    echo '<input type="submit" name="submit" value="Enregistrer les modifications"/>'; // Bouton Enregistrer les modifications
                    echo '</form>'; // Fin du formulaire
                }
            }
        }else{ // Si l'utilisateur n'est pas connecté
            echo '<h1><img src="resources/bad.png" width="5%" height="auto" />Une erreur est survenue, merci de réessayer !</h1>'; // Message d'erreur
            require_once "commons/footer.php"; // Inclusion du footer
            die(); // Arrête le script
        } 
    }else{ // Si l'utilisateur n'est pas connecté
        //ne rien faire, méthode invalide
    }
?>
<script> // Script JS
    document.getElementById('edit_nom_utilisateur').onclick = function(){ // Au clic sur l'icône éditer
        if(document.getElementById('edit_nom_utilisateur').className.includes('fa-pencil')){ // Si l'icône est éditer
            document.getElementById('nom_utilisateur').readOnly = false; // Rend le champ non-lecture seule
            document.getElementById('edit_nom_utilisateur').classList.remove('fa-pencil'); // Supprime l'icône éditer
            document.getElementById('edit_nom_utilisateur').classList.add('fa-check'); // Ajoute l'icône valider
        }else{ // Si l'icône est valider
            document.getElementById('nom_utilisateur').readOnly = true; // Rend le champ lecture seule
            document.getElementById('edit_nom_utilisateur').classList.remove('fa-check'); // Supprime l'icône valider
            document.getElementById('edit_nom_utilisateur').classList.add('fa-pencil'); // Ajoute l'icône éditer
        }
    }
    document.getElementById('edit_prenom_utilisateur').onclick = function(){ // Au clic sur l'icône éditer
        if(document.getElementById('edit_prenom_utilisateur').className.includes('fa-pencil')){ // Si l'icône est éditer
            document.getElementById('prenom_utilisateur').readOnly = false; // Rend le champ non-lecture seule
            document.getElementById('edit_prenom_utilisateur').classList.remove('fa-pencil'); // Supprime l'icône éditer
            document.getElementById('edit_prenom_utilisateur').classList.add('fa-check'); // Ajoute l'icône valider
        }else{ // Si l'icône est valider
            document.getElementById('prenom_utilisateur').readOnly = true; // Rend le champ lecture seule
            document.getElementById('edit_prenom_utilisateur').classList.remove('fa-check'); // Supprime l'icône valider
            document.getElementById('edit_prenom_utilisateur').classList.add('fa-pencil'); // Ajoute l'icône éditer
        }
    }
    document.getElementById('edit_type_utilisateur').onclick = function(){ // Au clic sur l'icône éditer
        if(document.getElementById('edit_type_utilisateur').className.includes('fa-pencil')){ // Si l'icône est éditer
            document.getElementById('type_utilisateur').classList.remove('readonly'); // Rend le champ non-lecture seule
            document.getElementById('edit_type_utilisateur').classList.remove('fa-pencil'); // Supprime l'icône éditer
            document.getElementById('edit_type_utilisateur').classList.add('fa-check'); // Ajoute l'icône valider
        }else{ // Si l'icône est valider
            document.getElementById('type_utilisateur').classList.add('readonly'); // Rend le champ lecture seule
            document.getElementById('edit_type_utilisateur').classList.remove('fa-check'); // Supprime l'icône valider
            document.getElementById('edit_type_utilisateur').classList.add('fa-pencil'); // Ajoute l'icône éditer
        } 
    }
    document.getElementById('edit_role').onclick = function(){ // Au clic sur l'icône éditer
        if(document.getElementById('edit_role').className.includes('fa-pencil')){ // Si l'icône est éditer
            document.getElementById('role').classList.remove('readonly'); // Rend le champ non-lecture seule
            document.getElementById('edit_role').classList.remove('fa-pencil'); // Supprime l'icône éditer
            document.getElementById('edit_role').classList.add('fa-check'); // Ajoute l'icône valider
        }else{ // Si l'icône est valider
            document.getElementById('role').classList.add('readonly'); // Rend le champ lecture seule
            document.getElementById('edit_role').classList.remove('fa-check'); // Supprime l'icône valider
            document.getElementById('edit_role').classList.add('fa-pencil'); // Ajoute l'icône éditer
        } 
    }
    document.getElementById('edit_description').onclick = function(){ // Au clic sur l'icône éditer
        if(document.getElementById('edit_description').className.includes('fa-pencil')){ // Si l'icône est éditer
            document.getElementById('description').readOnly = false; // Rend le champ non-lecture seule
            document.getElementById('edit_description').classList.remove('fa-pencil'); // Supprime l'icône éditer
            document.getElementById('edit_description').classList.add('fa-check'); // Ajoute l'icône valider
        }else{ // Si l'icône est valider
            document.getElementById('description').readOnly = true; // Rend le champ lecture seule
            document.getElementById('edit_description').classList.remove('fa-check'); // Supprime l'icône valider
            document.getElementById('edit_description').classList.add('fa-pencil'); // Ajoute l'icône éditer
        }
    }
    document.getElementById('edit_login').onclick = function(){ // Au clic sur l'icône éditer
        if(document.getElementById('edit_login').className.includes('fa-pencil')){ // Si l'icône est éditer
            document.getElementById('login').readOnly = false; // Rend le champ non-lecture seule
            document.getElementById('edit_login').classList.remove('fa-pencil'); // Supprime l'icône éditer
            document.getElementById('edit_login').classList.add('fa-check'); // Ajoute l'icône valider
        }else{ // Si l'icône est valider
            document.getElementById('login').readOnly = true; // Rend le champ lecture seule
            document.getElementById('edit_login').classList.remove('fa-check'); // Supprime l'icône valider
            document.getElementById('edit_login').classList.add('fa-pencil'); // Ajoute l'icône éditer
        } 
    }
    document.getElementById('edit_email').onclick = function(){ // Au clic sur l'icône éditer
        if(document.getElementById('edit_email').className.includes('fa-pencil')){ // Si l'icône est éditer
            document.getElementById('email').readOnly = false; // Rend le champ non-lecture seule
            document.getElementById('edit_email').classList.remove('fa-pencil'); // Supprime l'icône éditer
            document.getElementById('edit_email').classList.add('fa-check'); // Ajoute l'icône valider
        }else{ // Si l'icône est valider
            document.getElementById('email').readOnly = true; // Rend le champ lecture seule
            document.getElementById('edit_email').classList.remove('fa-check'); // Supprime l'icône valider
            document.getElementById('edit_email').classList.add('fa-pencil'); // Ajoute l'icône éditer
        }
    }
    document.getElementById('edit_telephone').onclick = function(){ // Au clic sur l'icône éditer
        if(document.getElementById('edit_telephone').className.includes('fa-pencil')){ // Si l'icône est éditer
            document.getElementById('telephone').readOnly = false; // Rend le champ non-lecture seule
            document.getElementById('edit_telephone').classList.remove('fa-pencil'); // Supprime l'icône éditer
            document.getElementById('edit_telephone').classList.add('fa-check'); // Ajoute l'icône valider
        }else{ // Si l'icône est valider
            document.getElementById('telephone').readOnly = true; // Rend le champ lecture seule
            document.getElementById('edit_telephone').classList.remove('fa-check'); // Supprime l'icône valider
            document.getElementById('edit_telephone').classList.add('fa-pencil'); // Ajoute l'icône éditer
        } 
    }
</script> <!-- Script pour la modification des champs -->
<?php require_once "commons/footer.php";?> <!-- Appel du footer -->