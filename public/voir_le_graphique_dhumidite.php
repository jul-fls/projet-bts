<?php
    $title = "Voir le graphique d'humidité"; //Titre de la page
    require_once "commons/header.php"; //Inclusion du header
    if(!isset($_SESSION['loggedin'])){ //Si l'utilisateur n'est pas connecté
        echo '<h1><img src="resources/danger.png" width="5%" height="auto" />Vous n\'etes pas connecté, merci de vous connecter à votre compte !</h1>'; //Message d'erreur
        require_once "commons/footer.php"; //Inclusion du footer
        die(); //Arrêt du script
    }
?>
<div class="loading-screen" style="display:none;"> <!-- On cache la div de chargement -->
    <div class="loading-screen-inner-div"> <!-- On cache la div de chargement -->
        <i class="fa fa-spinner fa-spin fa-3x fa-fw" aria-hidden="true"></i> <!-- On affiche une icône de chargement -->
        <h3 class="">Chargement en cours...<span id="percent"></span></h3> <!-- On affiche un message de chargement -->
    </div> <!-- On cache la div de chargement -->
</div> <!-- On cache la div de chargement -->
<div class="w3-center"> <!-- On centre le contenu -->
    <h2 id="title">Voir le graphique d'humidité</h2> <!-- Titre de la page -->
</div> <!-- On centre le contenu -->
<script> //On crée un script
    var xValues = []; //On crée un tableau vide contenant les valeurs de l'axe X (horodatage)
    var humValues = []; //On crée un tableau vide contenant les valeurs de l'axe Y (humidité)
    function load_data_donnee(datetime1,datetime2){ //Fonction qui charge les données
        var form_data = new FormData(); //On crée un nouveau formulaire
        datetime1 ? datetime1 : document.getElementById("datetime1").value; //On récupère la valeur de la première date
        datetime2 ? datetime2 : document.getElementById("datetime2").value; //On récupère la valeur de la deuxième date
        form_data.append('datetime1',datetime1); //On ajoute la première date au formulaire
        form_data.append('datetime2',datetime2); //On ajoute la deuxième date au formulaire
        var ajax_request = new XMLHttpRequest(); //On crée une nouvelle requête AJAX
        ajax_request.open('POST','<?=$__WEB_ROOT__?>ajax_scripts/ajax_get_donnees_between_two_datetimes.php'); //On ouvre la requête AJAX
        ajax_request.addEventListener("progress", function (evt) { //On ajoute un écouteur d'événement sur la requête AJAX
            if(evt.lengthComputable) { //Si la taille de la requête est connue
                var percentComplete = (evt.loaded / evt.total) * 100; //On calcule le pourcentage de chargement
                document.getElementById("percent").innerHTML = Math.round(percentComplete) + "%"; //On affiche le pourcentage de chargement
            } 
        }, false);
        ajax_request.send(form_data); //On envoie le formulaire
        ajax_request.onreadystatechange = function(){ //On crée un écouteur d'événement sur la requête AJAX
            if(ajax_request.readyState==4 && ajax_request.status==200){ //Si la requête AJAX est terminée et que le code de retour est 200
                var response = JSON.parse(ajax_request.responseText); //On récupère la réponse de la requête AJAX
                if(response.length > 0){ //Si la réponse de la requête AJAX contient des données
                    xValues = []; //On vide le tableau des valeurs de l'axe X
                    humValues = []; //On vide le tableau des valeurs de l'axe Y
                    $humchart.data.datasets.length=1; //On vide le graphique
                    function addRow(id_donnee,timestamp,humidite){ //Fonction qui ajoute une ligne dans le tableau
                        xValues.push(timestamp); //On ajoute la valeur de l'axe X
                        humValues.push(humidite); //On ajoute la valeur de l'axe Y
                    } 
                    for(var i = 0; i <response.length; i++){ //On parcours la réponse de la requête AJAX
                        addRow(response[i].id_donnee,response[i].timestamp,response[i].humidite); //On ajoute une ligne dans le tableau
                    } 
                    $hum_alertes = []; //On vide le tableau des alertes
                    <?php
                        require("commons/dbconfig.php"); //On inclut la configuration de la base de données
                        $sql = 'SELECT alertes.id, alertes.nom, alertes.type_de_donnees, alertes.type_dalerte, alertes.valeur_de_declenchement, alertes.active FROM alertes WHERE active = 1 AND type_de_donnees = 2 LIMIT 1000;'; //On crée la requête SQL
                        $stmt = mysqli_prepare($conn,$sql); //On prépare la requête SQL
                        mysqli_stmt_execute($stmt); //On exécute la requête SQL
                        $result = mysqli_stmt_get_result($stmt); //On récupère le résultat de la requête SQL
                        if(!$result){ //Si la requête SQL a échoué
                            die("échec de la lecture des alertes"); //On affiche un message d'erreur
                        }
                        if(mysqli_num_rows($result)>0){ //Si la requête SQL a réussi et qu'il y a des alertes
                            while ($row = mysqli_fetch_assoc($result)){ //On parcours les alertes
                                if($row['type_dalerte'] == '0'){ //Si l'alerte est de type "inférieur"
                                    $type_dalerte = ">"; //On affiche ">"
                                    $color = "#FF8F00"; //On définit la couleur de l'alerte
                                }else{ //Sinon
                                    $type_dalerte = "<"; //On affiche "<"
                                    $color = "#00FFFF"; //On définit la couleur de l'alerte
                                }
                                $label = html_entity_decode($row['nom'],ENT_QUOTES)." (".$type_dalerte." ".$row['valeur_de_declenchement'].")"; //On définit le label de l'alerte
                                echo 'temparr = [...humValues];'."\n"; //On crée un tableau temporaire contenant les valeurs de l'axe Y (humidité)
                                echo 'temparr.fill('.$row['valeur_de_declenchement'].');'."\n"; //On remplit le tableau temporaire avec la valeur de déclenchement de l'alerte
                                echo '$hum_alertes.push({label:"'.$label.'",borderColor:"'.$color.'",fill:false,data:temparr});'."\n"; //On ajoute l'alerte au tableau des alertes
                            }
                        }
                    ?>
                    $humchart.data.labels = xValues; //On définit les valeurs de l'axe X
                    $humchart.data.datasets[0].data = humValues; //On définit les valeurs de l'axe Y
                    $humchart.data.datasets = $humchart.data.datasets.concat($hum_alertes); //On ajoute les alertes au graphique
                    
                    $humchart.update(); //On met à jour le graphique
                }else{ //Sinon
                    console.log('Aucune donnée trouvé'); //On affiche un message d'erreur
                }
            }
        };
    }
</script> <!-- Fin du script -->
<input id="datetime1" autofocus type="datetime-local" class="w3-center" onchange="javascript:load_data_donnee(document.getElementById('datetime1').value,document.getElementById('datetime2').value)"/> <!-- Champ de saisie de la première date -->
<input id="datetime2" type="datetime-local" class="w3-center" onchange="javascript:load_data_donnee(document.getElementById('datetime1').value,document.getElementById('datetime2').value)"/> <!-- Champ de saisie de la seconde date -->
<div class="canvas-container"> <!-- Début du div contenant le graphique -->
    <canvas id="hum" class="canvas"></canvas> <!-- Début du graphique -->
</div> <!-- Fin du div contenant le graphique -->
<script> //Début du script
    var $humchart = new Chart("hum", { //On crée un nouveau graphique
        type: "line", //On définit le type de graphique
        data: { //On définit les données du graphique
            labels: xValues, //On définit les valeurs de l'axe X
            datasets: [ // Affectation du dataset
                { 
                    data: humValues, //On définit les valeurs de l'axe Y
                    borderColor: "blue", //On définit la couleur de la bordure
                    fill: false, //On définit si le graphique est rempli ou non
                    label: "Humidité (%HR)", //On définit le label du graphique
                }
            ]
        },
        options: { //On définit les options du graphique
            legend: {display: true}, //On affiche la légende
            responsive: true //On rend le graphique responsive
        }
    });
    date1 = new Date(); //On crée une nouvelle date
    date2 = new Date(); //On crée une nouvelle date
    date2.setTime(date1.getTime() - (1*60*60*1000)); //On définit la date de début du graphique
    load_data_donnee(toISOString(date2),toISOString(date1)); //On charge les données du graphique
    document.getElementById('datetime1').value=toISOString(date2); //On définit la première date du graphique
    document.getElementById('datetime2').value=toISOString(date1); //On définit la seconde date du graphique
</script> <!-- Fin du script -->
<?php require_once "commons/footer.php";?> <!-- On inclut le footer -->