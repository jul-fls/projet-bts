<?php
    $title = "Récapitulatif"; // Titre de la page
    require_once "commons/header.php"; // Inclusion du header
    if(!isset($_SESSION['loggedin'])){ // Si l'utilisateur n'est pas connecté
        echo '<h1><img src="resources/danger.png" width="5%" height="auto" />Vous n\'etes pas connecté, merci de vous connecter à votre compte !</h1>'; // Message d'erreur
        require_once "commons/footer.php"; // Inclusion du footer
        die(); // Arrêt du script
    }else if($_SESSION['role']<2){ // Si l'utilisateur n'est pas un administrateur
        echo '<h1><img src="resources/danger.png" width="5%" height="auto" />Vous n\'avez pas l\'autorisation d\'accéder à cette page !</h1>'; // Message d'erreur
        require_once "commons/footer.php"; // Inclusion du footer
        die(); // Arrêt du script
    }
?>
<div class="w3-center"> <!-- Début de la div contenant le formulaire -->
    <h2 id="title">Récapitulatif</h2> <!-- Titre du formulaire -->
    <a href="export_mesures.php?download=csv" class="w3-btn w3-green"><i class="fa fa-download"> </i>Télécharger le récapitulatif au format CSV</a> <!-- Bouton de téléchargement du récapitulatif au format CSV -->
</div> <!-- Fin de la div contenant le formulaire -->
<table class="w3-table-all" id="tableau-liste-donnees_capteurs"> <!-- Début de la table contenant les données capteurs -->
    <thead> <!-- Début du tableau contenant les données capteurs -->
        <tr class="w3-blue"> <!-- Début de la première ligne du tableau contenant les données capteurs -->
            <th>Mois</th> <!-- Colonne contenant le mois -->
            <th>Année</th> <!-- Colonne contenant l'année -->
            <th>Nombre de mesures</th> <!-- Colonne contenant le nombre de mesures -->
            <th>Moyenne CO² (Ppm)</th> <!-- Colonne contenant la moyenne CO² -->
            <th>Moyenne Température (°C)</th> <!-- Colonne contenant la moyenne de la température -->
            <th>Moyenne Humidité (%HR)</th> <!-- Colonne contenant la moyenne de l'humidité -->
        </tr> <!-- Fin de la première ligne du tableau contenant les données capteurs -->
    </thead> <!-- Fin du tableau contenant les données capteurs -->
    <?php 
        require("commons/dbconfig.php"); // Inclusion de la connexion à la base de données
        $sql = 'SELECT DATE_FORMAT(donnees_capteurs.timestamp,\'%m\') AS mois, DATE_FORMAT(donnees_capteurs.timestamp,\'%Y\') AS annee, COUNT(*) AS nb_mesures, ROUND(AVG(donnees_capteurs.co2),0) AS co2_moy, ROUND(AVG(donnees_capteurs.temp),2) as temp_moy, ROUND(AVG(donnees_capteurs.hum),2) AS hum_moy FROM donnees_capteurs GROUP BY mois, annee;'; // Requête SQL pour récupérer les données capteurs
        $stmt = mysqli_prepare($conn,$sql); // Préparation de la requête SQL
        mysqli_stmt_execute($stmt); // Exécution de la requête SQL
        $result = mysqli_stmt_get_result($stmt); // Récupération du résultat de la requête SQL
        if(!$result){ // Si la requête SQL a échoué
            die("échec de la lecture des données"); // Message d'erreur    
        } 
        if(mysqli_num_rows($result)>0){ // Si le nombre de lignes du résultat de la requête SQL est supérieur à 0
            $mois = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre']; // Tableau contenant les mois
            while ($row = mysqli_fetch_assoc($result)){ // Tant qu'il y a des lignes dans le résultat de la requête SQL
                echo '<tr>'."\n"; // Début de la ligne du tableau contenant les données capteurs
                $moi = DateTime::createFromFormat('m', $row['mois']); // Création d'une date à partir du mois
                echo '<td>'.$mois[$moi->format('n')-1].'</td>'."\n"; // Affichage du mois
                echo '<td>'.$row['annee'].'</td>'."\n"; // Affichage de l'année
                echo '<td>'.$row['nb_mesures'].'</td>'."\n"; // Affichage du nombre de mesures
                echo '<td>'.$row['co2_moy'].'</td>'."\n"; // Affichage de la moyenne CO²
                echo '<td>'.$row['temp_moy'].'</td>'."\n"; // Affichage de la moyenne de la température
                echo '<td>'.$row['hum_moy'].'</td>'."\n"; // Affichage de la moyenne de l'humidité
                echo '</tr>'."\n"; // Fin de la ligne du tableau contenant les données capteurs
            } 
        }
    ?>
</table> <!-- Fin de la table contenant les données capteurs -->
<script> // Début du script
    $th = document.getElementsByTagName('th'); // Récupération des th du tableau contenant les données capteurs
    for(i=0;i<$th.length;i++){ // Pour chaque th du tableau contenant les données capteurs
        $th[i].name=$th[i].innerHTML; // Attribution d'un nom à chaque th du tableau contenant les données capteurs
        $th[i].innerHTML += ' <i class="fa fa-sort"></i> '; // Ajout d'un icône de tri à chaque th du tableau contenant les données capteurs
        $th[i].addEventListener('click',function(){ // Ajout d'un évènement au clic sur chaque th du tableau contenant les données capteurs
            sortTable(this.cellIndex); // Appel de la fonction de tri
        }); // Fin de l'évènement au clic sur chaque th du tableau contenant les données capteurs
        $th[i].style.cursor = 'pointer'; // Changement du curseur de la souris sur chaque th du tableau contenant les données capteurs
    } 
    function sortTable(n){ // Début de la fonction de tri
        var table,rows,switching,i,x,y,shouldSwitch,dir,switchcount=0; // Déclaration des variables
        table=document.getElementsByTagName("table")[0]; // Récupération du tableau contenant les données capteurs
        switching=true; // Initialisation de la variable switching
        dir="asc"; // Initialisation de la variable dir
        while(switching){ // Tant que la variable switching est vraie
            switching=false; // Initialisation de la variable switching
            rows=table.rows; // Récupération des lignes du tableau contenant les données capteurs
            for(i=1;i<(rows.length-1);i++){ // Pour chaque ligne du tableau contenant les données capteurs
                shouldSwitch=false; // Initialisation de la variable shouldSwitch
                x=rows[i].getElementsByTagName("td")[n]; // Récupération de la cellule de la ligne du tableau contenant les données capteurs
                y=rows[i+1].getElementsByTagName("td")[n]; // Récupération de la cellule de la ligne suivante du tableau contenant les données capteurs
                typeofdata=0; // Initialisation de la variable typeofdata
                if(!isNaN(parseFloat(x.innerHTML))){ // Si la valeur de la cellule de la ligne du tableau contenant les données capteurs n'est pas un nombre
                    typeofdata = 1; // Attribution de la valeur 1 à la variable typeofdata
                }else{ // Sinon
                    typeofdata = 0; // Attribution de la valeur 0 à la variable typeofdata
                }
                if(typeofdata==1){ // Si la valeur de la variable typeofdata est 1
                    if(dir=="asc"){ // Si la valeur de la variable dir est asc
                        if(parseFloat(x.innerHTML)>parseFloat(y.innerHTML)){ // Si la valeur de la cellule de la ligne du tableau contenant les données capteurs est supérieure à celle de la ligne suivante
                            shouldSwitch=true; // Attribution de la valeur true à la variable shouldSwitch
                            rows[0].getElementsByTagName("th")[n].innerHTML = rows[0].getElementsByTagName("th")[n].name + ' <i class="fa fa-sort-numeric-asc"></i>'; // Changement de l'icône de tri de la cellule de la ligne du tableau contenant les données capteurs
                            break; // Sortie de la boucle
                        }
                    }else if(dir=="desc"){ // Sinon si la valeur de la variable dir est desc
                        if(parseFloat(x.innerHTML)<parseFloat(y.innerHTML)){ // Si la valeur de la cellule de la ligne du tableau contenant les données capteurs est inférieure à celle de la ligne suivante
                            shouldSwitch=true; // Attribution de la valeur true à la variable shouldSwitch
                            rows[0].getElementsByTagName("th")[n].innerHTML = rows[0].getElementsByTagName("th")[n].name + ' <i class="fa fa-sort-numeric-desc"></i>'; // Changement de l'icône de tri de la cellule de la ligne du tableau contenant les données capteurs
                            break; // Sortie de la boucle
                        }
                    }
                }else if(typeofdata==0){ // Sinon si la valeur de la variable typeofdata est 0
                    if(dir=="asc"){ // Si la valeur de la variable dir est asc
                        if(x.innerHTML.toLowerCase()>y.innerHTML.toLowerCase()){ // Si la valeur de la cellule de la ligne du tableau contenant les données capteurs est supérieure à celle de la ligne suivante
                            shouldSwitch=true; // Attribution de la valeur true à la variable shouldSwitch
                            rows[0].getElementsByTagName("th")[n].innerHTML = rows[0].getElementsByTagName("th")[n].name + ' <i class="fa fa-sort-alpha-asc"></i>'; // Changement de l'icône de tri de la cellule de la ligne du tableau contenant les données capteurs
                            break; // Sortie de la boucle
                        } 
                    }else if(dir=="desc"){ // Sinon si la valeur de la variable dir est desc
                        if(x.innerHTML.toLowerCase()<y.innerHTML.toLowerCase()){ // Si la valeur de la cellule de la ligne du tableau contenant les données capteurs est inférieure à celle de la ligne suivante
                            shouldSwitch=true; // Attribution de la valeur true à la variable shouldSwitch
                            rows[0].getElementsByTagName("th")[n].innerHTML = rows[0].getElementsByTagName("th")[n].name + ' <i class="fa fa-sort-alpha-desc"></i>'; // Changement de l'icône de tri de la cellule de la ligne du tableau contenant les données capteurs
                            break; // Sortie de la boucle
                        }
                    }
                }
            }
            if(shouldSwitch){ // Si la valeur de la variable shouldSwitch est vraie
                rows[i].parentNode.insertBefore(rows[i+1],rows[i]); // Inversion de la ligne du tableau contenant les données capteurs
                switching=true; // Attribution de la valeur true à la variable switching
                switchcount++; // Incrémentation de la variable switchcount
            }else{ // Sinon
                if(switchcount==0&&dir=="asc"){ // Si la valeur de la variable switchcount est égale à 0 et que la valeur de la variable dir est asc
                    dir="desc"; // Attribution de la valeur desc à la variable dir
                    switching=true; // Attribution de la valeur true à la variable switching
                } 
            }
        }
    }
</script> <!-- Fin du script -->
<?php require_once "commons/footer.php";?> <!-- Appel du footer -->