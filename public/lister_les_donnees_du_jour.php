<?php
    $title = "Lister toutes les données du jour"; // Titre de la page
    require_once "commons/header.php"; // Inclusion du header
    if(!isset($_SESSION['loggedin'])){ // Si l'utilisateur n'est pas connecté
        echo '<h1><img src="resources/danger.png" width="5%" height="auto" />Vous n\'etes pas connecté, merci de vous connecter à votre compte !</h1>'; // Message d'erreur
        require_once "commons/footer.php"; // Inclusion du footer
        die(); // Arrêt du script
    }else if($_SESSION['role']<1){ // Si l'utilisateur n'est pas un administrateur
        echo '<h1><img src="resources/danger.png" width="5%" height="auto" />Vous n\'avez pas l\'autorisation d\'accéder à cette page !</h1>'; // Message d'erreur
        require_once "commons/footer.php"; // Inclusion du footer
        die(); // Arrêt du script
    }
?> 
<div class="w3-center"> <!-- Début de la div contenant le formulaire -->
    <h2 id="title">Liste de tous les données des capteurs du jour</h2> <!-- Titre du formulaire -->
</div> <!-- Fin de la div contenant le formulaire -->
<table class="w3-table-all" id="tableau-liste-donnees_capteurs"> <!-- Début de la table contenant les données des capteurs -->
    <thead> <!-- Début du tableau contenant les données des capteurs -->
        <tr class="w3-blue"> <!-- Début de la première ligne du tableau contenant les données des capteurs -->
            <th>N° de la donnée</th> <!-- Colonne contenant le numéro de la donnée -->
            <th>Heure de mesure</th> <!-- Colonne contenant l'heure de la donnée -->
            <th>CO² (Ppm)</th> <!-- Colonne contenant le CO² de la donnée -->
            <th>Température (°C)</th> <!-- Colonne contenant la température de la donnée -->
            <th>Humidité (%HR)</th> <!-- Colonne contenant l'humidité de la donnée -->
        </tr> <!-- Fin de la première ligne du tableau contenant les données des capteurs -->
    </thead> <!-- Fin du tableau contenant les données des capteurs -->
    <?php
        require("commons/dbconfig.php"); // Inclusion de la connexion à la base de données
        $sql = 'SELECT donnees_capteurs.id AS id_mesure, DATE_FORMAT(donnees_capteurs.timestamp, \'%H:%i:%S\') AS heure_mesure, donnees_capteurs.co2, donnees_capteurs.temp, donnees_capteurs.hum FROM donnees_capteurs WHERE DATE(donnees_capteurs.timestamp) = CURRENT_DATE ORDER BY donnees_capteurs.timestamp DESC;'; // Requête SQL permettant de récupérer les données des capteurs du jour
        $stmt = mysqli_prepare($conn,$sql); // Préparation de la requête SQL
        mysqli_stmt_execute($stmt); // Exécution de la requête SQL
        $result = mysqli_stmt_get_result($stmt); // Récupération du résultat de la requête SQL
        if(!$result){ // Si la requête SQL ne s'est pas exécutée correctement
            die("échec de la lecture des données"); // Message d'erreur
        }
        if(mysqli_num_rows($result)>0){ // Si le résultat de la requête SQL contient des données
            while ($row = mysqli_fetch_assoc($result)){ // Tant qu'il y a des données dans le résultat de la requête SQL
                echo '<tr>'."\n"; //Début de la ligne
                echo '<td>'.$row['id_mesure'].'</td>'."\n"; // Colonne contenant le numéro de la donnée
                echo '<td>'.$row['heure_mesure'].'</td>'."\n"; // Colonne contenant l'heure de la donnée
                echo '<td>'.$row['co2'].'</td>'."\n"; // Colonne contenant le CO² de la donnée
                echo '<td>'.$row['temp'].'</td>'."\n"; // Colonne contenant la température de la donnée
                echo '<td>'.$row['hum'].'</td>'."\n"; // Colonne contenant l'humidité de la donnée
                echo '</tr>'."\n"; // Fin de la ligne
            }
        }
    ?>
</table> <!-- Fin de la table contenant les données des capteurs -->
<script> // Début du script
    $nb_donnees = <?=mysqli_num_rows($result)?>; // Récupération du nombre de données du jour
    if($nb_donnees==1){ // Si il y a une seule donnée
        document.getElementById('title').innerHTML = 'Liste de l\'unique donnée du jour'; // Modification du titre de la page
    }else{ // Sinon
        document.getElementById('title').innerHTML = 'Liste des <b>'+$nb_donnees+'</b> données du jour'; // Modification du titre de la page
    } 
    $th = document.getElementsByTagName('th'); // Récupération des th
    for(i=0;i<$th.length;i++){ // Pour chaque th
        $th[i].name=$th[i].innerHTML; // On leur attribue le nom de leur contenu
        $th[i].innerHTML += ' <i class="fa fa-sort"></i> '; // On ajoute un icône de tri
        $th[i].addEventListener('click',function(){ // Au clic sur le th
            sortTable(this.cellIndex); // On trie la table
        });
        $th[i].style.cursor = 'pointer'; // On change le curseur
    } 
    function sortTable(n){ // Fonction de tri
        var table,rows,switching,i,x,y,shouldSwitch,dir,switchcount=0; // Déclaration des variables
        table=document.getElementsByTagName("table")[0]; // Récupération de la table
        switching=true; // On initialise la variable switching à true
        dir="asc"; // On initialise la variable dir à asc
        while(switching){ // Tant que la variable switching est à true
            switching=false; // On met la variable switching à false
            rows=table.rows; // On récupère les lignes de la table
            for(i=1;i<(rows.length-1);i++){ // Pour chaque ligne
                shouldSwitch=false; // On initialise la variable shouldSwitch à false
                x=rows[i].getElementsByTagName("td")[n]; // On récupère la cellule de la ligne
                y=rows[i+1].getElementsByTagName("td")[n]; // On récupère la cellule de la ligne suivante
                typeofdata=0; // On initialise la variable typeofdata à 0
                if(!isNaN(parseFloat(x.innerHTML))){ // Si le contenu de la cellule de la ligne est un nombre
                    typeofdata = 1; // On met la variable typeofdata à 1
                }else{ // Sinon
                    typeofdata = 0; // On met la variable typeofdata à 0
                }
                if(typeofdata==1){ // Si le contenu de la cellule de la ligne est un nombre
                    if(dir=="asc"){ // Si la variable dir est asc
                        if(parseFloat(x.innerHTML)>parseFloat(y.innerHTML)){ // Si le contenu de la cellule de la ligne est supérieur au contenu de la cellule de la ligne suivante
                            shouldSwitch=true; // On met la variable shouldSwitch à true
                            rows[0].getElementsByTagName("th")[n].innerHTML = rows[0].getElementsByTagName("th")[n].name + ' <i class="fa fa-sort-numeric-asc"></i>'; // On change l'icône de tri
                            break; // On quitte la boucle
                        }
                    }else if(dir=="desc"){ // Sinon si la variable dir est desc
                        if(parseFloat(x.innerHTML)<parseFloat(y.innerHTML)){ // Si le contenu de la cellule de la ligne est inférieur au contenu de la cellule de la ligne suivante
                            shouldSwitch=true; // On met la variable shouldSwitch à true
                            rows[0].getElementsByTagName("th")[n].innerHTML = rows[0].getElementsByTagName("th")[n].name + ' <i class="fa fa-sort-numeric-desc"></i>'; // On change l'icône de tri
                            break; // On quitte la boucle
                        }
                    }
                }else if(typeofdata==0){ // Sinon si le contenu de la cellule de la ligne n'est pas un nombre
                    if(dir=="asc"){ // Si la variable dir est asc
                        if(x.innerHTML.toLowerCase()>y.innerHTML.toLowerCase()){ // Si le contenu de la cellule de la ligne est supérieur au contenu de la cellule de la ligne suivante
                            shouldSwitch=true; // On met la variable shouldSwitch à true
                            rows[0].getElementsByTagName("th")[n].innerHTML = rows[0].getElementsByTagName("th")[n].name + ' <i class="fa fa-sort-alpha-asc"></i>'; // On change l'icône de tri
                            break; // On quitte la boucle
                        }
                    }else if(dir=="desc"){ // Sinon si la variable dir est desc
                        if(x.innerHTML.toLowerCase()<y.innerHTML.toLowerCase()){ // Si le contenu de la cellule de la ligne est inférieur au contenu de la cellule de la ligne suivante
                            shouldSwitch=true; // On met la variable shouldSwitch à true
                            rows[0].getElementsByTagName("th")[n].innerHTML = rows[0].getElementsByTagName("th")[n].name + ' <i class="fa fa-sort-alpha-desc"></i>'; // On change l'icône de tri
                            break; // On quitte la boucle
                        }
                    }
                }
            }
            if(shouldSwitch){ // Si la variable shouldSwitch est à true
                rows[i].parentNode.insertBefore(rows[i+1],rows[i]); // On insère la ligne suivante avant la ligne actuelle
                switching=true; // On met la variable switching à true
                switchcount++; // On incrémente la variable switchcount
            }else{ // Sinon
                if(switchcount==0&&dir=="asc"){ // Si la variable switchcount est à 0 et que la variable dir est asc
                    dir="desc"; // On met la variable dir à desc
                    switching=true; // On met la variable switching à true
                }
            }
        }
    }
</script> <!-- Fonction de tri -->
<?php require_once "commons/footer.php";?> <!-- Footer -->