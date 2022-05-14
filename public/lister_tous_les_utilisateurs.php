<?php
    $title = "Lister tout les utilisateurs"; // Titre de la page
    require_once "commons/header.php"; // Inclusion du header
    if(!isset($_SESSION['loggedin'])){ // Si l'utilisateur n'est pas connecté
        echo '<h1><img src="resources/danger.png" width="5%" height="auto" />Vous n\'etes pas connecté, merci de vous connecter à votre compte !</h1>'; // Message d'erreur
        require_once "commons/footer.php"; // Inclusion du footer
        die(); // Arrêt du script
    }else if($_SESSION['role']<2){ // Si l'utilisateur n'est pas administrateur
        echo '<h1><img src="resources/danger.png" width="5%" height="auto" />Vous n\'avez pas l\'autorisation d\'accéder à cette page !</h1>'; // Message d'erreur
        require_once "commons/footer.php"; // Inclusion du footer
        die(); // Arrêt du script
    }
?>
<div class="w3-center"> <!-- Début de la div contenant le titre -->
    <h2 id="title">Liste des utilisateurs</h2> <!-- Titre -->
</div> <!-- Fin de la div contenant le titre -->
<table class="w3-table-all" id="tableau-liste-utilisateurs"> <!-- Début de la table -->
    <thead> <!-- Début du header -->
        <tr class="w3-blue"> <!-- Début de la ligne du header -->
            <th>N° de l'utilisateur</th> <!-- Colonne N° de l'utilisateur -->
            <th>Type d'utilisateur</th> <!-- Colonne Type d'utilisateur -->
            <th>Description de l'utilisateur</th> <!-- Colonne Description de l'utilisateur -->
            <th>Nom</th> <!-- Colonne Nom -->
            <th>Prénom</th> <!-- Colonne Prénom -->
            <th>Role de l'utilisateur</th> <!-- Colonne Role de l'utilisateur -->
            <th>Identifiant</th> <!-- Colonne Identifiant -->
            <th>Adresse email</th> <!-- Colonne Adresse email -->
            <th>N° de téléphone</th> <!-- Colonne N° de téléphone -->
            <th>Compte activé</th> <!-- Colonne Compte activé -->
            <th>Actions</th> <!-- Colonne Actions -->
        </tr> <!-- Fin de la ligne du header -->
    </thead> <!-- Fin du header -->
    <?php 
        require("commons/dbconfig.php"); // Inclusion de la connexion à la base de données
        $sql = "SELECT utilisateurs.id, utilisateurs.nom_utilisateur, utilisateurs.prenom_utilisateur, utilisateurs.type_utilisateur, utilisateurs.description, utilisateurs.role, utilisateurs.login, utilisateurs.email, utilisateurs.telephone, CASE WHEN utilisateurs.password_hash IS NOT NULL THEN 1 ELSE 0 END AS isactive FROM utilisateurs ORDER BY utilisateurs.id ASC;"; // Requête SQL
        $stmt = mysqli_prepare($conn,$sql); // Préparation de la requête
        mysqli_stmt_execute($stmt); // Exécution de la requête
        $result = mysqli_stmt_get_result($stmt); // Récupération du résultat de la requête
        if(!$result){ // Si la requête ne s'est pas exécutée correctement
            die("échec de la lecture des utilisateurs"); // Message d'erreur
        } 
        if(mysqli_num_rows($result)>0){ // Si la requête a renvoyé des résultats
            $nb_isactive = 0; // Initialisation du nombre d'utilisateurs actifs
            while ($row = mysqli_fetch_assoc($result)){ // Pour chaque utilisateur
                echo '<tr>'."\n"; // Début de la ligne
                echo '<td>'.$row['id'].'</td>'."\n"; // Colonne N° de l'utilisateur
                switch($row['type_utilisateur']){ // Switch sur le type d'utilisateur
                    case 0: // Si l'utilisateur est un eleve
                        echo '<td>Élève</td>'."\n"; // Colonne Type d'utilisateur
                        break; // Fin du switch
                    case 1: // Si l'utilisateur est un professeur
                        echo '<td>Professeur</td>'."\n"; // Colonne Type d'utilisateur
                        break; // Fin du switch
                    case 2: // Si l'utilisateur est un formateur
                        echo '<td>Formateur</td>'."\n"; // Colonne Type d'utilisateur
                        break; // Fin du switch
                    case 3: // Si l'utilisateur est un personnel
                        echo '<td>Personnel</td>'."\n"; // Colonne Type d'utilisateur
                        break; // Fin du switch
                    case 4: // Si l'utilisateur est autre
                        echo '<td>Autres</td>'."\n"; // Colonne Type d'utilisateur
                        break; // Fin du switch
                    default: // Si aucun type d'utilisateur n'est renseigné
                        echo '<td>Autres</td>'."\n"; // Colonne Type d'utilisateur
                        break; // Fin du switch
                }
                echo '<td>'.$row['description'].'</td>'."\n"; // Colonne Description de l'utilisateur
                echo '<td>'.mb_strtoupper($row['nom_utilisateur']).'</td>'."\n"; // Colonne Nom
                echo '<td>'.mb_ucfirst($row['prenom_utilisateur'],'UTF-8').'</td>'."\n"; // Colonne Prénom
                switch($row['role']){ // Switch sur le role de l'utilisateur
                    case 0: // Si l'utilisateur est un eleve
                        echo '<td>Utilisateur</td>'."\n"; // Colonne Role de l'utilisateur
                        break; // Fin du switch
                    case 1: // Si l'utilisateur est un professeur
                        echo '<td>Administrateur</td>'."\n"; // Colonne Role de l'utilisateur
                        break; // Fin du switch
                    case 2: // Si l'utilisateur est un formateur
                        echo '<td>Super Administrateur</td>'."\n"; // Colonne Role de l'utilisateur
                        break; // Fin du switch
                    default: // Si aucun role n'est renseigné
                        echo '<td>Utilisateur</td>'."\n"; // Colonne Role de l'utilisateur
                        break; // Fin du switch
                }
                echo '<td>'.$row['login'].'</td>'."\n"; // Colonne Identifiant
                echo '<td>'.$row['email'].'</td>'."\n"; // Colonne Adresse email
                echo '<td>'.$row['telephone'].'</td>'."\n"; // Colonne N° de téléphone
                if($row['isactive']==1){ // Si l'utilisateur est actif
                    $nb_isactive++; // Incrémentation du nombre d'utilisateurs actifs
                    echo '<td><i title="Compte activé" style="color: green" class="fa fa-check-square-o fa-2x"></i></td>'."\n"; // Colonne Compte activé
                }else{ // Sinon
                    echo '<td><i title="Compte inactivé" style="color: darkred" class="fa fa-window-close-o fa-2x"></i></td>'."\n"; // Colonne Compte activé
                } 
                echo '<td><a href="modifier_un_utilisateur.php?id='.$row['id'].'"><i class="fa fa-pencil fa-2x"></i></a><br/><a href="#" onclick="show_confirmation('.$row['id'].');"><i class="fa fa-trash fa-2x" style="color: darkred"></i></a></td>'."\n"; // Colonne Actions
                echo '</tr>'."\n"; // Fin de la ligne
            }
        }
    ?>
</table> <!-- Fin de la table -->
<div id="supprimer_utilisateur_confirmation" class="w3-modal"> <!-- Début de la fenêtre de confirmation de suppression -->
    <div class="w3-modal-content w3-animate-top w3-card-4"> <!-- Début du contenu de la fenêtre de confirmation -->
        <header class="w3-container w3-red"> <!-- Début du header de la fenêtre de confirmation -->
            <span onclick="document.getElementById('supprimer_utilisateur_confirmation').style.display='none'" class="w3-button w3-display-topright">&times;</span> <!-- Fermeture de la fenêtre de confirmation -->
            <h2>Etes-vous sûr de vouloir supprimer cet utilisateur ?</h2> <!-- Titre de la fenêtre de confirmation -->
        </header> <!-- Fin du header de la fenêtre de confirmation -->
        <div class="w3-container"> <!-- Début du contenu de la fenêtre de confirmation -->
            <p>Cela entrainera aussi la suppression de tous les donnees_capteurs passés et en cours pour cet utilisateur</p> <!-- Message de confirmation -->
            <a id="supprimer_utilisateur_button" class="w3-red w3-center w3-button" style="width: 100%;" href="">Confirmer la suppression</a> <!-- Bouton de confirmation -->
        </div> <!-- Fin du contenu de la fenêtre de confirmation -->
    </div> <!-- Fin du contenu de la fenêtre de confirmation -->
</div> <!-- Fin de la fenêtre de confirmation de suppression -->
<script> // Début du script
    function show_confirmation(id){ // Début de la fonction show_confirmation
        document.getElementById('supprimer_utilisateur_button').href='supprimer_un_utilisateur.php?id='+id; // Modification de l'attribut href du bouton de confirmation
        document.getElementById('supprimer_utilisateur_confirmation').style.display='block'; // Affichage de la fenêtre de confirmation
        return false; // Annulation de l'action par défaut du lien
    }
    $nb_utilisateurs = <?=mysqli_num_rows($result)?>; // Définition de la variable $nb_utilisateurs
    $nb_isactive = <?=$nb_isactive?>; // Définition de la variable $nb_isactive
    if($nb_utilisateurs==1){ // Si il y a un seul utilisateur
        document.getElementById('title').innerHTML = 'Liste de l\'unique utilisateur'; // Modification du titre de la page
    }else{ // Sinon
        document.getElementById('title').innerHTML = 'Liste des <b>'+$nb_utilisateurs+'</b> utilisateurs dont <b>'+$nb_isactive+'</b> sont activés'; // Modification du titre de la page
    }
    $th = document.getElementsByTagName('th'); // Définition de la variable $th
    for(i=0;i<$th.length;i++){ // Début de la boucle for
        $th[i].name=$th[i].innerHTML; // Modification de l'attribut name de chaque th
        $th[i].innerHTML += ' <i class="fa fa-sort"></i> '; // Ajout d'un icône de tri à chaque th
        $th[i].addEventListener('click',function(){ // Début de l'écouteur d'évènement
            sortTable(this.cellIndex); // Appel de la fonction sortTable
        }); // Fin de l'écouteur d'évènement
        $th[i].style.cursor = 'pointer'; // Modification du curseur
    }
    function sortTable(n){ // Début de la fonction sortTable
        var table,rows,switching,i,x,y,shouldSwitch,dir,switchcount=0; // Définition des variables
        table=document.getElementsByTagName("table")[0]; // Définition de la variable table
        switching=true; // Définition de la variable switching
        dir="asc"; // Définition de la variable dir
        while(switching){ // Début de la boucle while
            switching=false; // Définition de la variable switching
            rows=table.rows; // Définition de la variable rows
            for(i=1;i<(rows.length-1);i++){ // Début de la boucle for
                shouldSwitch=false; // Définition de la variable shouldSwitch
                x=rows[i].getElementsByTagName("td")[n]; // Définition de la variable x
                y=rows[i+1].getElementsByTagName("td")[n]; // Définition de la variable y
                typeofdata=0; // Définition de la variable typeofdata
                if(!isNaN(parseFloat(x.innerHTML))){ // Si x est un nombre
                    typeofdata = 1; // Modification de la variable typeofdata
                }else{ // Sinon
                    typeofdata = 0; // Modification de la variable typeofdata
                } 
                if(typeofdata==1){ // Si x est un nombre
                    if(dir=="asc"){ // Si dir est asc
                        if(parseFloat(x.innerHTML)>parseFloat(y.innerHTML)){ // Si x est supérieur à y
                            shouldSwitch=true; // Modification de la variable shouldSwitch
                            rows[0].getElementsByTagName("th")[n].innerHTML = rows[0].getElementsByTagName("th")[n].name + ' <i class="fa fa-sort-numeric-asc"></i>'; // Modification de l'icône de tri
                            break; // Sortie de la boucle for
                        }
                    }else if(dir=="desc"){ // Sinon si dir est desc
                        if(parseFloat(x.innerHTML)<parseFloat(y.innerHTML)){ // Si x est inférieur à y
                            shouldSwitch=true; // Modification de la variable shouldSwitch
                            rows[0].getElementsByTagName("th")[n].innerHTML = rows[0].getElementsByTagName("th")[n].name + ' <i class="fa fa-sort-numeric-desc"></i>'; // Modification de l'icône de tri
                            break; // Sortie de la boucle for
                        }
                    }
                }else if(typeofdata==0){ // Sinon si x est une chaîne de caractères
                    if(dir=="asc"){ // Si dir est asc
                        if(x.innerHTML.toLowerCase()>y.innerHTML.toLowerCase()){ // Si x est supérieur à y
                            shouldSwitch=true; // Modification de la variable shouldSwitch
                            rows[0].getElementsByTagName("th")[n].innerHTML = rows[0].getElementsByTagName("th")[n].name + ' <i class="fa fa-sort-alpha-asc"></i>'; // Modification de l'icône de tri
                            break; // Sortie de la boucle for
                        }
                    }else if(dir=="desc"){ // Sinon si dir est desc
                        if(x.innerHTML.toLowerCase()<y.innerHTML.toLowerCase()){ // Si x est inférieur à y
                            shouldSwitch=true; // Modification de la variable shouldSwitch
                            rows[0].getElementsByTagName("th")[n].innerHTML = rows[0].getElementsByTagName("th")[n].name + ' <i class="fa fa-sort-alpha-desc"></i>'; // Modification de l'icône de tri
                            break; // Sortie de la boucle for
                        }
                    }
                }
            }
            if(shouldSwitch){ // Début de la condition if
                rows[i].parentNode.insertBefore(rows[i+1],rows[i]); // Insertion de rows[i+1] avant rows[i]
                switching=true; // Modification de la variable switching
                switchcount++; // Incrémentation de switchcount
            }else{ // Sinon
                if(switchcount==0&&dir=="asc"){ // Début de la condition if
                    dir="desc"; // Modification de la variable dir
                    switching=true; // Modification de la variable switching
                }
            }
        }
    }
</script> <!-- Fin du script -->
<?php require_once "commons/footer.php";?> <!-- Appel du footer -->