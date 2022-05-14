<?php
    $title = "Lister toutes les alertes"; // Titre de la page
    require_once "commons/header.php"; // Inclusion du header
    if(!isset($_SESSION['loggedin'])){ // Si l'utilisateur n'est pas connecté
        echo '<h1><img src="resources/danger.png" width="5%" height="auto" />Vous n\'etes pas connecté, merci de vous connecter à votre compte !</h1>'; // Message d'erreur
        require_once "commons/footer.php"; // Inclusion du footer
        die(); // On arrête l'execution du script
    }else if($_SESSION['role']<2){ // Si l'utilisateur n'est pas un administrateur
        echo '<h1><img src="resources/danger.png" width="5%" height="auto" />Vous n\'avez pas l\'autorisation d\'accéder à cette page !</h1>'; // Message d'erreur
        require_once "commons/footer.php"; // Inclusion du footer
        die(); // On arrête l'execution du script
    }
?>
<div class="w3-center"> <!-- Début de la div contenant le formulaire -->
    <h2 id="title">Liste des alertes</h2> <!-- Titre du formulaire -->
</div> <!-- Fin de la div contenant le formulaire -->
<table class="w3-table-all" id="tableau-liste-menus"> <!-- Début de la table contenant le formulaire -->
    <thead> <!-- Début du tableau contenant les titres des colonnes -->
        <tr class="w3-blue"> <!-- Début de la ligne contenant les titres des colonnes -->
            <th>N° de l'alerte</th> <!-- Titre de la colonne contenant le numéro de l'alerte -->
            <th>Nom</th> <!-- Titre de la colonne contenant le nom de l'alerte -->
            <th>Type de données</th> <!-- Titre de la colonne contenant le type de données de l'alerte -->
            <th>Type d'alerte</th> <!-- Titre de la colonne contenant le type d'alerte de l'alerte -->
            <th>Valeur de déclenchement</th> <!-- Titre de la colonne contenant la valeur de déclenchement de l'alerte -->
            <th>Active</th> <!-- Titre de la colonne contenant l'état de l'alerte -->
            <th>Actions</th> <!-- Titre de la colonne contenant les actions possibles -->
        </tr> <!-- Fin de la ligne contenant les titres des colonnes -->
    </thead> <!-- Fin du tableau contenant les titres des colonnes -->
    <?php // Début de la boucle qui affiche les alertes
        require("commons/dbconfig.php"); // Inclusion de la connexion à la base de données
        $sql = 'SELECT alertes.id, alertes.nom, alertes.type_de_donnees, alertes.type_dalerte, alertes.valeur_de_declenchement, alertes.active FROM alertes LIMIT 1000;'; // Requête SQL permettant de récupérer les alertes
        $stmt = mysqli_prepare($conn,$sql); // Préparation de la requête
        mysqli_stmt_execute($stmt); // Exécution de la requête
        $result = mysqli_stmt_get_result($stmt); // Récupération du résultat de la requête
        if(!$result){ // Si la requête ne s'est pas exécutée correctement
            die("échec de la lecture des alertes"); // Message d'erreur
        } 
        if(mysqli_num_rows($result)>0){ // Si la requête a renvoyé des résultats
            while ($row = mysqli_fetch_assoc($result)){ // Tant qu'il y a des résultats
                echo '<tr>'."\n"; // Début de la ligne
                echo '<td>'.$row['id'].'</td>'."\n"; // Colonne contenant le numéro de l'alerte
                echo '<td>'.$row['nom'].'</td>'."\n"; // Colonne contenant le nom de l'alerte
                if($row['type_de_donnees']==0){ // Si le type de données est CO2
                    echo '<td>Co2 (Ppm)</td>'."\n"; // Colonne contenant le type de données de l'alerte
                }else if($row['type_de_donnees']==1){ // Si le type de données est Température
                    echo '<td>Température (°C)</td>'."\n"; // Colonne contenant le type de données de l'alerte
                }else if($row['type_de_donnees']==2){ // Si le type de données est Humidité
                    echo '<td>Humidité (%HR)</td>'."\n"; // Colonne contenant le type de données de l'alerte
                }
                if($row['type_dalerte']==1){ // Si le type d'alerte est de type "inférieur"
                    echo '<td>En dessous</td>'."\n"; // Colonne contenant le type d'alerte de l'alerte
                }else if($row['type_dalerte']==0){ // Si le type d'alerte est de type "supérieur"
                    echo '<td>Au dessus</td>'."\n"; // Colonne contenant le type d'alerte de l'alerte
                }
                echo '<td>'.$row['valeur_de_declenchement'].'</td>'."\n"; // Colonne contenant la valeur de déclenchement de l'alerte
                if($row['active']==1){ // Si l'alerte est active
                    echo '<td><i title="Activée" style="color: green" class="fa fa-check-square-o fa-2x"></i></td>'."\n"; // Colonne contenant l'état de l'alerte
                }else if($row['active']==0){ // Si l'alerte n'est pas active
                    echo '<td><i title="Désactivée" style="color: darkred" class="fa fa-window-close-o fa-2x"></i></td>'."\n"; // Colonne contenant l'état de l'alerte
                }
                echo '<td><a title="Modifier l\'alerte" href="modifier_une_alerte.php?id='.$row['id'].'"><i class="fa fa-pencil fa-2x"></i></a></td>'."\n"; // Colonne contenant les actions possibles
                echo '</tr>'."\n"; // Fin de la ligne
            }
        }
    ?>
</table> <!-- Fin du tableau contenant les alertes -->
<script> // Début du script javascript
    $nb_alertes = <?=mysqli_num_rows($result)?>; // Récupération du nombre d'alertes
    if($nb_alertes==1){ // Si il y a 1 alerte
        document.getElementById('title').innerHTML = 'Liste de l\'unique alerte'; // Titre de la page
    }else{ // Si il y a plus d'1 alerte
        document.getElementById('title').innerHTML = 'Liste des <b>'+$nb_alertes+'</b> alertes'; // Titre de la page
    }
    $th = document.getElementsByTagName('th'); // Récupération des th
    for(i=0;i<$th.length;i++){ // Pour chaque th
        $th[i].name=$th[i].innerHTML; // On le nomme
        $th[i].innerHTML += ' <i class="fa fa-sort"></i> '; // On ajoute un tri
        $th[i].addEventListener('click',function(){ // Au clic
            sortTable(this.cellIndex); // On trie
        });
        $th[i].style.cursor = 'pointer'; // On change le curseur
    }
    function sortTable(n){ // Fonction de tri
        var table,rows,switching,i,x,y,shouldSwitch,dir,switchcount=0; // Déclaration des variables
        table=document.getElementsByTagName("table")[0]; // Récupération de la table
        switching=true; // On initialise la variable de changement
        dir="asc"; // On initialise la direction du tri
        while(switching){ // Tant que la variable de changement est vraie
            switching=false; // On met la variable de changement à faux
            rows=table.rows; // On récupère les lignes de la table
            for(i=1;i<(rows.length-1);i++){ // Pour chaque ligne
                shouldSwitch=false; // On initialise la variable de changement
                x=rows[i].getElementsByTagName("td")[n]; // On récupère la cellule de la ligne
                y=rows[i+1].getElementsByTagName("td")[n]; // On récupère la cellule de la ligne suivante
                typeofdata=0; // On initialise le type de données
                if(!isNaN(parseFloat(x.innerHTML))){ // Si la valeur de la cellule est un nombre
                    typeofdata = 1; // On met le type de données à 1
                }else{ // Sinon
                    typeofdata = 0; // On met le type de données à 0
                }
                if(typeofdata==1){ // Si le type de données est un nombre
                    if(dir=="asc"){ // Si la direction est ascendant
                        if(parseFloat(x.innerHTML)>parseFloat(y.innerHTML)){ // Si la valeur de la cellule est supérieure à celle de la ligne suivante
                            shouldSwitch=true; // On met la variable de changement à vrai
                            rows[0].getElementsByTagName("th")[n].innerHTML = rows[0].getElementsByTagName("th")[n].name + ' <i class="fa fa-sort-numeric-asc"></i>'; // On change le nom de la colonne
                            break; // On quitte la boucle
                        }
                    }else if(dir=="desc"){ // Si la direction est descendant
                        if(parseFloat(x.innerHTML)<parseFloat(y.innerHTML)){ // Si la valeur de la cellule est inférieure à celle de la ligne suivante
                            shouldSwitch=true; // On met la variable de changement à vrai
                            rows[0].getElementsByTagName("th")[n].innerHTML = rows[0].getElementsByTagName("th")[n].name + ' <i class="fa fa-sort-numeric-desc"></i>'; // On change le nom de la colonne
                            break; // On quitte la boucle
                        }
                    }
                }else if(typeofdata==0){ // Si le type de données est une chaîne de caractères
                    if(dir=="asc"){ // Si la direction est ascendant
                        if(x.innerHTML.toLowerCase()>y.innerHTML.toLowerCase()){ // Si la valeur de la cellule est supérieure à celle de la ligne suivante
                            shouldSwitch=true; // On met la variable de changement à vrai
                            rows[0].getElementsByTagName("th")[n].innerHTML = rows[0].getElementsByTagName("th")[n].name + ' <i class="fa fa-sort-alpha-asc"></i>'; // On change le nom de la colonne
                            break; // On quitte la boucle
                        }
                    }else if(dir=="desc"){ // Si la direction est descendant
                        if(x.innerHTML.toLowerCase()<y.innerHTML.toLowerCase()){ // Si la valeur de la cellule est inférieure à celle de la ligne suivante
                            shouldSwitch=true; // On met la variable de changement à vrai
                            rows[0].getElementsByTagName("th")[n].innerHTML = rows[0].getElementsByTagName("th")[n].name + ' <i class="fa fa-sort-alpha-desc"></i>'; // On change le nom de la colonne
                            break; // On quitte la boucle
                        }
                    }
                }
            }
            if(shouldSwitch){ // Si la variable de changement est vraie
                rows[i].parentNode.insertBefore(rows[i+1],rows[i]); // On insère la ligne suivante avant la ligne actuelle
                switching=true; // On met la variable de changement à vrai
                switchcount++; // On incrémente le compteur de changement
            }else{ // Sinon
                if(switchcount==0&&dir=="asc"){ // Si le compteur de changement est à 0 et que la direction est ascendant
                    dir="desc"; // On change la direction
                    switching=true; // On met la variable de changement à vrai
                }
            }
        }
    }
</script> 
<?php require_once "commons/footer.php";?> <!-- On inclut le footer -->