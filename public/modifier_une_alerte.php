<?php
    $title = "Modifier une alerte"; // titre de la page
    require_once "commons/header.php"; // inclusion du header
    require "commons/dbconfig.php"; // inclusion de la base de données
    if(!isset($_SESSION['loggedin'])){ // si l'utilisateur n'est pas connecté
        echo '<h1><img src="resources/danger.png" width="5%" height="auto" />Vous n\'etes pas connecté, merci de vous connecter à votre compte !</h1>'; // message d'erreur
        require_once "commons/footer.php"; // inclusion du footer
        die(); // on arrête le script
    }
    else if($_SESSION['role']<2){ // si l'utilisateur n'est pas un administrateur
        echo '<h1><img src="resources/danger.png" width="5%" height="auto" />Vous n\'avez pas l\'autorisation d\'accéder à cette page !</h1>'; // message d'erreur
        require_once "commons/footer.php"; // inclusion du footer
        die(); // on arrête le script
    }
    if($_SERVER['REQUEST_METHOD']=='POST'){ // si la requête est de type POST
        //traitement du formulaire 
        if(isset($_POST['nom'])&&isset($_POST['type_de_donnees'])&&isset($_POST['type_dalerte'])&&isset($_POST['valeur_de_declenchement'])&&isset($_POST['active'])&&isset($_POST['id'])){ // si les champs sont remplis
            $sanitized_nom = filter_var($_POST['nom'],FILTER_SANITIZE_STRING); // on sécurise le nom
            $sanitized_type_de_donnees = filter_var($_POST['type_de_donnees'], FILTER_SANITIZE_NUMBER_INT); // on sécurise le type de données
            $sanitized_type_dalerte = filter_var($_POST['type_dalerte'], FILTER_SANITIZE_NUMBER_INT); // on sécurise le type d'alerte
            $sanitized_valeur_de_declenchement = filter_var($_POST['valeur_de_declenchement'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION); // on sécurise la valeur de déclenchement
            $sanitized_active = filter_var($_POST['active'], FILTER_SANITIZE_NUMBER_INT); // on sécurise l'activation
            $sanitized_id = filter_var($_POST['id'],FILTER_SANITIZE_NUMBER_INT); // on sécurise l'id
            $sql2 = 'UPDATE alertes SET nom = ?, type_de_donnees = ?, type_dalerte = ?, valeur_de_declenchement = ?, active = ? WHERE id = ?;'; // requête SQL
            $stmt2 = mysqli_prepare($conn,$sql2); // on prépare la requête
            mysqli_stmt_bind_param($stmt2, 'siidii',$sanitized_nom,$sanitized_type_de_donnees, $sanitized_type_dalerte, $sanitized_valeur_de_declenchement, $sanitized_active, $sanitized_id); // on lie les paramètres
            $status2 = mysqli_stmt_execute($stmt2); // on exécute la requête
            $result2 = mysqli_stmt_get_result($stmt2); // on récupère le résultat de la requête
            if($conn->affected_rows==0){ // si la requête n'a pas abouti
                echo '<h1><img src="resources/bad.png" width="5%" height="auto" />Cette alerte existe déjà merci d\'en choisir une autre !</h1>'; // message d'erreur
            }else if($conn->affected_rows==1){ // si la requête a abouti
                echo '<h1><img src="resources/good.png" width="5%" height="auto"/>Bravo, l\'alerte a bien été modifiée !</h1>'; // message de confirmation
            }else{ // si la requête n'a pas abouti
                echo '<h1><img src="resources/bad.png" width="5%" height="auto" />Un problème est survenu, merci de réessayer !</h1>'; // message d'erreur
            }
        }else{ // si les champs ne sont pas remplis
            echo '<h1><img src="resources/bad.png" width="5%" height="auto" />Un problème est survenu, merci de réessayer !</h1>'; // message d'erreur
        } 
    }else if($_SERVER['REQUEST_METHOD']=='GET'){ // si la requête est de type GET
        //execute le reste du code de la page
        if(isset($_GET['id'])&&$_GET['id']>0){ // si l'id est bien défini
            $sanitized_id = filter_var($_GET['id'],FILTER_SANITIZE_NUMBER_INT); // on sécurise l'id
            $sql = 'SELECT alertes.id, alertes.nom, alertes.type_de_donnees, alertes.type_dalerte, alertes.valeur_de_declenchement, alertes.active FROM alertes WHERE alertes.id = ? LIMIT 1;'; // requête SQL
            $stmt = mysqli_prepare($conn,$sql); // on prépare la requête
            mysqli_stmt_bind_param($stmt, 'i',$sanitized_id); // on lie les paramètres
            mysqli_stmt_execute($stmt); // on exécute la requête
            $result = mysqli_stmt_get_result($stmt); // on récupère le résultat de la requête
            if(mysqli_num_rows($result)<1){ // si la requête n'a pas abouti
                echo '<h1><img src="../resources/bad.png" width="5%" height="auto" />Aucune alerte ne porte ce numéro !</h1>'; // message d'erreur
                require_once "commons/footer.php"; // inclusion du footer
                die(); // on arrête le script
            }else{ // si la requête a abouti
                while ($row = mysqli_fetch_assoc($result)){ // on récupère les données de la requête
                    echo '<br/>'; // séparation
                    echo '<form action="'.htmlspecialchars($_SERVER['PHP_SELF']).'" method="post" enctype="multipart/form-data">'; // formulaire
                    echo '<input type="hidden" name="id" value="'.$row['id'].'" />'; // champ caché
                    echo '<table id="modifier_une_alerte" class="w3-table-all full">'; // tableau
                    echo '<tbody>'; // corps du tableau
                    echo '<tr>'; // ligne
                    echo '<th>Nom</th>'; // colonne
                    echo '<td><input id="nom" class="full" tabindex="-1" aria-disabled="true" type="text" name="nom" readonly required value="'.$row['nom'].'"/></td>'; // champ
                    echo '<td><i id="edit_nom" class="fa fa-pencil pointer"></i></td>'; // icône
                    echo '</tr>'; // fin de ligne
                    echo '<tr>'; // ligne
                    echo '<th>Type de données que cette alerte va devoir surveiller</th>'; // colonne
                    echo '<td><select id="type_de_donnees" class="full readonly" tabindex="-1" aria-disabled="true" name="type_de_donnees" required>'; // champ
                    switch($row['type_de_donnees']){ // on sélectionne le bon type de données
                        case 0: // si le type de données est 0 (CO2)
                            echo '<option value="0" selected>Co2</option>'; // option CO2
                            echo '<option value="1">Température</option>'; // option Température
                            echo '<option value="2">Humidité</option>'; // option Humidité
                            break; // fin de la sélection
                        case 1: // si le type de données est 1 (Température)
                            echo '<option value="0">Co2</option>'; // option CO2
                            echo '<option value="1" selected>Température</option>'; // option Température
                            echo '<option value="2">Humidité</option>'; // option Humidité
                            break; // fin de la sélection
                        case 2: // si le type de données est 2 (Humidité)
                            echo '<option value="0">Co2</option>'; // option CO2
                            echo '<option value="1">Température</option>'; // option Température
                            echo '<option value="2" selected>Humidité</option>'; // option Humidité
                            break; // fin de la sélection
                    }
                    echo '</select>'; // fin du champ
                    echo '<td><i id="edit_type_de_donnees" class="fa fa-pencil pointer"></i></td>'; // icône
                    echo '</tr>'; // fin de ligne
                    echo '<th>Type d\'évènement que cette alerte va devoir surveiller</th>'; // colonne
                    echo '<td><select id="type_dalerte" class="full readonly" tabindex="-1" aria-disabled="true" name="type_dalerte" required>'; // champ
                    switch($row['type_dalerte']){ // on sélectionne le bon type d'évènement
                        case 0: // si le type d'évènement est 0 (augmentation)
                            echo '<option value="0" selected>Au dessus</option>'; // option Augmentation
                            echo '<option value="1">En dessous</option>'; // option Décrémentation
                            break; // fin de la sélection
                        case 1: // si le type d'évènement est 1 (décrémentation)
                            echo '<option value="0">Au dessus</option>'; // option Augmentation
                            echo '<option value="1" selected>En dessous</option>'; // option Décrémentation
                            break; // fin de la sélection
                    } 
                    echo '</select>'; // fin du champ
                    echo '<td><i id="edit_type_dalerte" class="fa fa-pencil pointer"></i></td>'; // icône
                    echo '</tr>'; // fin de ligne
                    echo '<tr>'; // ligne
                    echo '<th>Valeur de déclenchement</th>'; // colonne
                    echo '<td><input id="valeur_de_declenchement" class="full" tabindex="-1" aria-disabled="true" type="number" step="0.01" min="-100" max="99999" name="valeur_de_declenchement" readonly required value="'.$row['valeur_de_declenchement'].'"/></td>'; // champ
                    echo '<td><i id="edit_valeur_de_declenchement" class="fa fa-pencil pointer"></i></td>'; // icône
                    echo '</tr>'; // fin de ligne
                    echo '<th>Active</th>'; // colonne
                    echo '<td><select id="active" class="full readonly" tabindex="-1" aria-disabled="true" name="active" required>'; // champ
                    switch($row['active']){ // on sélectionne le bon type d'évènement
                        case 0: // si l'alerte est désactivée
                            echo '<option value="0" selected>Désactivée</option>'; // option Désactivée
                            echo '<option value="1">Activée</option>'; // option Activée
                            break; // fin de la sélection
                        case 1: // si l'alerte est activée
                            echo '<option value="0">Désactivée</option>'; // option Désactivée
                            echo '<option value="1" selected>Activée</option>'; // option Activée
                            break; // fin de la sélection
                    }
                    echo '</select>'; // fin du champ
                    echo '<td><i id="edit_active" class="fa fa-pencil pointer"></i></td>'; // icône
                    echo '</tr>'; // fin de ligne
                    echo '</tbody>'; // fin du corps du tableau
                    echo '</table>'; // fin du tableau
                    echo '<br/>'; // saut de ligne
                    echo '<input type="submit" name="submit" value="Enregistrer les modifications"  class="w3-button w3-light-green full"/>'; // bouton d'enregistrement
                    echo '</form>'; // fin du formulaire
                }
            }
        }else{ // si l'utilisateur n'est pas connecté
            echo '<h1><img src="resources/bad.png" width="5%" height="auto" />Une erreur est survenue, merci de réessayer !</h1>'; // message d'erreur
            require_once "commons/footer.php"; // inclusion du footer
            die(); // on arrête l'exécution du script
        }
    }else{
        //ne rien faire, méthode invalide
        die(); // on arrête l'exécution du script
    }
?>
<script> // script de gestion de la page
    document.getElementById('edit_nom').onclick = function(){ // lorsque l'on clique sur l'icône
        if(document.getElementById('edit_nom').className.includes('fa-pencil')){ // si l'icône est en mode édition
            document.getElementById('nom').readOnly = false; // on rend le champ éditable
            document.getElementById('edit_nom').classList.remove('fa-pencil'); // on retire l'icône
            document.getElementById('edit_nom').classList.add('fa-check'); // on ajoute l'icône de validation
        }else{ // sinon
            document.getElementById('nom').readOnly = true; // on rend le champ non éditable
            document.getElementById('edit_nom').classList.remove('fa-check'); // on retire l'icône
            document.getElementById('edit_nom').classList.add('fa-pencil'); // on ajoute l'icône d'édition
        } 
    }
    document.getElementById('edit_type_de_donnees').onclick = function(){ // lorsque l'on clique sur l'icône
        if(document.getElementById('edit_type_de_donnees').className.includes('fa-pencil')){ // si l'icône est en mode édition
            document.getElementById('type_de_donnees').classList.remove('readonly'); // on rend le champ éditable
            document.getElementById('edit_type_de_donnees').classList.remove('fa-pencil'); // on retire l'icône
            document.getElementById('edit_type_de_donnees').classList.add('fa-check'); // on ajoute l'icône de validation
        }else{ // sinon
            document.getElementById('type_de_donnees').classList.add('readonly'); // on rend le champ non éditable
            document.getElementById('edit_type_de_donnees').classList.remove('fa-check'); // on retire l'icône
            document.getElementById('edit_type_de_donnees').classList.add('fa-pencil'); // on ajoute l'icône d'édition
        } 
    }
    document.getElementById('edit_type_dalerte').onclick = function(){ // lorsque l'on clique sur l'icône
        if(document.getElementById('edit_type_dalerte').className.includes('fa-pencil')){ // si l'icône est en mode édition
            document.getElementById('type_dalerte').classList.remove('readonly'); // on rend le champ éditable
            document.getElementById('edit_type_dalerte').classList.remove('fa-pencil'); // on retire l'icône
            document.getElementById('edit_type_dalerte').classList.add('fa-check'); // on ajoute l'icône de validation
        }else{ // sinon
            document.getElementById('type_dalerte').classList.add('readonly'); // on rend le champ non éditable
            document.getElementById('edit_type_dalerte').classList.remove('fa-check'); // on retire l'icône
            document.getElementById('edit_type_dalerte').classList.add('fa-pencil'); // on ajoute l'icône d'édition
        } 
    }
    document.getElementById('edit_valeur_de_declenchement').onclick = function(){ // lorsque l'on clique sur l'icône
        if(document.getElementById('edit_valeur_de_declenchement').className.includes('fa-pencil')){ // si l'icône est en mode édition
            document.getElementById('valeur_de_declenchement').readOnly = false; // on rend le champ éditable
            document.getElementById('edit_valeur_de_declenchement').classList.remove('fa-pencil'); // on retire l'icône
            document.getElementById('edit_valeur_de_declenchement').classList.add('fa-check'); // on ajoute l'icône de validation
        }else{ // sinon
            document.getElementById('valeur_de_declenchement').readOnly = true; // on rend le champ non éditable
            document.getElementById('edit_valeur_de_declenchement').classList.remove('fa-check'); // on retire l'icône
            document.getElementById('edit_valeur_de_declenchement').classList.add('fa-pencil'); // on ajoute l'icône d'édition
        } 
    }
    document.getElementById('edit_active').onclick = function(){ // lorsque l'on clique sur l'icône
        if(document.getElementById('edit_active').className.includes('fa-pencil')){ // si l'icône est en mode édition
            document.getElementById('active').classList.remove('readonly'); // on rend le champ éditable
            document.getElementById('edit_active').classList.remove('fa-pencil'); // on retire l'icône
            document.getElementById('edit_active').classList.add('fa-check'); // on ajoute l'icône de validation
        }else{ // sinon 
            document.getElementById('active').classList.add('readonly'); // on rend le champ non éditable
            document.getElementById('edit_active').classList.remove('fa-check'); // on retire l'icône
            document.getElementById('edit_active').classList.add('fa-pencil'); // on ajoute l'icône d'édition
        }
    }
</script> <!-- fin du script -->
<?php require_once "commons/footer.php";?> <!-- on inclut le footer -->