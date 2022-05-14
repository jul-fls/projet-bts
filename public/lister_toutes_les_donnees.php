<?php
    $title = "Lister toutes les données"; // Modification du titre de la page
    require_once "commons/header.php"; // Inclusion du header
    if(!isset($_SESSION['loggedin'])){ // Si l'utilisateur n'est pas connecté
        echo '<h1><img src="resources/danger.png" width="5%" height="auto" />Vous n\'etes pas connecté, merci de vous connecter à votre compte !</h1>'; // Message d'erreur
        require_once "commons/footer.php"; // Inclusion du footer
        die(); // Fin du script
    }else if($_SESSION['role']<1){ // Si l'utilisateur n'est pas un administrateur
        echo '<h1><img src="resources/danger.png" width="5%" height="auto" />Vous n\'avez pas l\'autorisation d\'accéder à cette page !</h1>'; // Message d'erreur
        require_once "commons/footer.php"; // Inclusion du footer
        die(); // Fin du script
    }
?> 
<div class="w3-center"> <!-- Début de la div contenant le titre -->
    <h2 id="title">Liste de tous les données des capteurs</h2> <!-- Titre -->
</div> <!-- Fin de la div contenant le titre -->
<table class="w3-table-all" id="tableau-liste-donnees_capteurs"> <!-- Début de la table -->
    <thead> <!-- Début du header -->
        <tr class="w3-blue"> <!-- Début du header -->
            <th>N° de la donnée</th> <!-- Colonne N° de la donnée -->
            <th>Date et heure de mesure</th> <!-- Colonne Date et heure de mesure -->
            <th>CO² (Ppm)</th> <!-- Colonne CO² (Ppm) -->
            <th>Température (°C)</th> <!-- Colonne Température (°C) -->
            <th>Humidité (%HR)</th> <!-- Colonne Humidité (%HR) -->
        </tr> <!-- Fin du header -->
    </thead> <!-- Fin du header -->
    <?php 
        require("commons/dbconfig.php"); // Inclusion de la connexion à la base de données
        $sql = 'SELECT donnees_capteurs.id AS id_mesure, DATE_FORMAT(donnees_capteurs.timestamp, \'%d/%m/%Y %H:%i:%S\') AS date_mesure, donnees_capteurs.co2, donnees_capteurs.temp, donnees_capteurs.hum FROM donnees_capteurs ORDER BY donnees_capteurs.timestamp DESC;'; // Requête SQL
        $stmt = mysqli_prepare($conn,$sql); // Préparation de la requête
        mysqli_stmt_execute($stmt); // Exécution de la requête
        $result = mysqli_stmt_get_result($stmt); // Récupération du résultat de la requête
        if(!$result){ // Si la requête ne s'est pas exécutée correctement
            die("échec de la lecture des données"); // Message d'erreur
        } 
        if(mysqli_num_rows($result)>0){ // Si le nombre de lignes de résultat est supérieur à 0
            while ($row = mysqli_fetch_assoc($result)){ // Tant qu'il y a des lignes de résultat
                echo '<tr>'."\n"; // Début de la ligne
                echo '<td>'.$row['id_mesure'].'</td>'."\n"; // Colonne N° de la donnée
                echo '<td>'.$row['date_mesure'].'</td>'."\n"; // Colonne Date et heure de mesure
                echo '<td>'.$row['co2'].'</td>'."\n"; // Colonne CO² (Ppm)
                echo '<td>'.$row['temp'].'</td>'."\n"; // Colonne Température (°C)
                echo '<td>'.$row['hum'].'</td>'."\n"; // Colonne Humidité (%HR)
                echo '</tr>'."\n"; // Fin de la ligne
            }
        }
    ?>
</table> <!-- Fin de la table -->
<script> // Début du script
    $nb_donnees = <?=mysqli_num_rows($result)?>; // Récupération du nombre de lignes de résultat
    if($nb_donnees==1){ // Si le nombre de lignes de résultat est égal à 1
        document.getElementById('title').innerHTML = 'Liste de l\'unique donnée'; // Modification du titre
    }else{ // Sinon
        document.getElementById('title').innerHTML = 'Liste des <b>'+$nb_donnees+'</b> données'; // Modification du titre
    }
    $th = document.getElementsByTagName('th'); // Récupération des th
    for(i=0;i<$th.length;i++){ // Pour chaque th
         $th[i].name=$th[i].innerHTML; // On le nomme
        $th[i].innerHTML += ' <i class="fa fa-sort"></i> '; // On ajoute un icône de tri
        $th[i].addEventListener('click',function(){ // Au clic sur le th
            sortTable(this.cellIndex); // On trie la table
        }); // Fin de l'évènement
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
            for(i=1;i<(rows.length-1);i++){ // Pour chaque ligne de la table
                shouldSwitch=false; // On initialise la variable shouldSwitch à false
                x=rows[i].getElementsByTagName("td")[n]; // On récupère la cellule du th
                y=rows[i+1].getElementsByTagName("td")[n]; // On récupère la cellule du th
                typeofdata=0; // On initialise la variable typeofdata à 0
                if(!isNaN(parseFloat(x.innerHTML))){ // Si la valeur de x est un nombre
                    typeofdata = 1; // On met la variable typeofdata à 1
                }else{ // Sinon
                    typeofdata = 0; // On met la variable typeofdata à 0
                }
                if(typeofdata==1){ // Si la valeur de typeofdata est égale à 1
                    if(dir=="asc"){ // Si la valeur de dir est égale à asc
                        if(parseFloat(x.innerHTML)>parseFloat(y.innerHTML)){ // Si la valeur de x est supérieure à la valeur de y
                            shouldSwitch=true; // On met la variable shouldSwitch à true
                            rows[0].getElementsByTagName("th")[n].innerHTML = rows[0].getElementsByTagName("th")[n].name + ' <i class="fa fa-sort-numeric-asc"></i>'; // On change l'icône du th
                            break; // On quitte la boucle
                        }
                    }else if(dir=="desc"){ // Sinon si la valeur de dir est égale à desc
                        if(parseFloat(x.innerHTML)<parseFloat(y.innerHTML)){ // Si la valeur de x est inférieure à la valeur de y
                            shouldSwitch=true; // On met la variable shouldSwitch à true
                            rows[0].getElementsByTagName("th")[n].innerHTML = rows[0].getElementsByTagName("th")[n].name + ' <i class="fa fa-sort-numeric-desc"></i>'; // On change l'icône du th
                            break; // On quitte la boucle
                        }
                    }
                }else if(typeofdata==0){ // Sinon si la valeur de typeofdata est égale à 0
                    if(dir=="asc"){ // Si la valeur de dir est égale à asc
                        if(x.innerHTML.toLowerCase()>y.innerHTML.toLowerCase()){ // Si la valeur de x est supérieure à la valeur de y
                            shouldSwitch=true; // On met la variable shouldSwitch à true
                            rows[0].getElementsByTagName("th")[n].innerHTML = rows[0].getElementsByTagName("th")[n].name + ' <i class="fa fa-sort-alpha-asc"></i>'; // On change l'icône du th
                            break; // On quitte la boucle
                        } 
                    }else if(dir=="desc"){ // Sinon si la valeur de dir est égale à desc
                        if(x.innerHTML.toLowerCase()<y.innerHTML.toLowerCase()){ // Si la valeur de x est inférieure à la valeur de y
                            shouldSwitch=true; // On met la variable shouldSwitch à true
                            rows[0].getElementsByTagName("th")[n].innerHTML = rows[0].getElementsByTagName("th")[n].name + ' <i class="fa fa-sort-alpha-desc"></i>'; // On change l'icône du th
                            break; // On quitte la boucle
                        }
                    }
                }
            }
            if(shouldSwitch){ // Si la variable shouldSwitch est à true
                rows[i].parentNode.insertBefore(rows[i+1],rows[i]); // On insère la ligne i+1 avant la ligne i
                switching=true; // On met la variable switching à true
                switchcount++; // On incrémente la variable switchcount
            }else{ // Sinon
                if(switchcount==0&&dir=="asc"){ // Si la variable switchcount est égale à 0 et que la valeur de dir est égale à asc
                    dir="desc"; // On met la variable dir à desc
                    switching=true; // On met la variable switching à true
                }
            }
        }
    }
</script> <!-- Fin du script -->
<?php require_once "commons/footer.php";?> <!-- Require du footer -->