<?php
    $title = "Voir tous les graphiques"; // Titre de la page
    require_once "commons/header.php"; // Inclusion du header
    if(!isset($_SESSION['loggedin'])){ // Si l'utilisateur n'est pas connecté
        echo '<h1><img src="resources/danger.png" width="5%" height="auto" />Vous n\'etes pas connecté, merci de vous connecter à votre compte !</h1>'; // Message d'erreur
        require_once "commons/footer.php"; // Inclusion du footer
        die(); // On arrête l'execution du script
    }
?>
<div class="loading-screen" style="display:none;">  <!-- Début de la div qui affiche le chargement -->
    <div class="loading-screen-inner-div"> <!-- Début de la div qui affiche le chargement -->
        <i class="fa fa-spinner fa-spin fa-3x fa-fw" aria-hidden="true"></i> <!-- Icone de chargement -->
        <h3 class="">Chargement en cours...<span id="percent"></span></h3> <!-- Texte de chargement -->
    </div> <!-- Fin de la div qui affiche le chargement -->
</div> <!-- Fin de la div qui affiche le chargement -->
<div class="w3-center"> <!-- Début de la div qui affiche le titre -->
    <h2 id="title">Voir tous les graphiques</h2> <!-- Titre de la page -->
</div> <!-- Fin de la div qui affiche le titre -->
<script> // Début du script
    var xValues = []; // Création d'un tableau qui contiendra les valeurs de x
    var co2Values = []; // Création d'un tableau qui contiendra les valeurs de CO2
    var tempValues = []; // Création d'un tableau qui contiendra les valeurs de température
    var humValues = []; // Création d'un tableau qui contiendra les valeurs d'humidité
    var thresholdHighArray = []; // Création d'un tableau qui contiendra les valeurs de seuil haut
    function load_data_donnee(datetime1,datetime2){ // Fonction qui charge les données
        var form_data = new FormData(); // Création d'un objet de type FormData
        datetime1 ? datetime1 : document.getElementById("datetime1").value; // Si la variable datetime1 n'est pas définie, on prend la valeur de l'input
        datetime2 ? datetime2 : document.getElementById("datetime2").value; // Si la variable datetime2 n'est pas définie, on prend la valeur de l'input
        form_data.append('datetime1',datetime1); // Ajout de la valeur de datetime1 dans l'objet form_data
        form_data.append('datetime2',datetime2); // Ajout de la valeur de datetime2 dans l'objet form_data
        var ajax_request = new XMLHttpRequest(); // Création d'un objet XMLHttpRequest
        ajax_request.open('POST','<?=$__WEB_ROOT__?>ajax_scripts/ajax_get_donnees_between_two_datetimes.php'); // Ouverture de la requête
        ajax_request.addEventListener("progress", function (evt) { // Ajout d'un évènement progress
            if(evt.lengthComputable) { // Si la taille de la requête est connue
                var percentComplete = (evt.loaded / evt.total) * 100; // Calcul du pourcentage de chargement
                document.getElementById("percent").innerHTML = Math.round(percentComplete) + "%"; // Affichage du pourcentage de chargement
            } 
        }, false);
        ajax_request.send(form_data); // Envoi de l'objet form_data
        ajax_request.onreadystatechange = function(){ // Dès que la requête est terminée
            if(ajax_request.readyState==4 && ajax_request.status==200){ // Si la requête est terminée et que le code est 200
                var response = JSON.parse(ajax_request.responseText); // Récupération de la réponse
                if(response.length > 0){ // Si la réponse contient des données
                    xValues = []; // On vide le tableau xValues
                    co2Values = []; // On vide le tableau co2Values
                    tempValues = []; // On vide le tableau tempValues
                    humValues = []; // On vide le tableau humValues
                    $co2chart.data.datasets.length=1; // On vide le graphique CO2
                    $tempchart.data.datasets.length=1; // On vide le graphique température
                    $humchart.data.datasets.length=1; // On vide le graphique humidité
                    function addRow(id_donnee,timestamp,co2,temperature,humidite){ // Fonction qui ajoute une ligne dans le tableau
                        xValues.push(timestamp); // On ajoute la valeur de timestamp dans le tableau xValues
                        co2Values.push(co2); // On ajoute la valeur de co2 dans le tableau co2Values
                        tempValues.push(temperature); // On ajoute la valeur de temperature dans le tableau tempValues
                        humValues.push(humidite); // On ajoute la valeur de humidité dans le tableau humValues
                    } 
                    for(var i = 0; i <response.length; i++){ // Pour chaque ligne de la réponse
                        addRow(response[i].id_donnee,response[i].timestamp,response[i].co2,response[i].temperature,response[i].humidite); // On ajoute la ligne dans le tableau
                    }
                    $co2_alertes = []; // On vide le tableau $co2_alertes
                    $temp_alertes = []; // On vide le tableau $temp_alertes
                    $hum_alertes = []; // On vide le tableau $hum_alertes
                    <?php
                        require("commons/dbconfig.php"); // Inclusion de la connexion à la base de données
                        $sql = 'SELECT alertes.id, alertes.nom, alertes.type_de_donnees, alertes.type_dalerte, alertes.valeur_de_declenchement, alertes.active FROM alertes WHERE active = 1 LIMIT 1000;'; // Requête SQL
                        $stmt = mysqli_prepare($conn,$sql); // Préparation de la requête
                        mysqli_stmt_execute($stmt); // Exécution de la requête
                        $result = mysqli_stmt_get_result($stmt); // Récupération du résultat de la requête
                        if(!$result){ // Si la requête ne s'est pas exécutée correctement
                            die("échec de la lecture des alertes"); // On arrête le script
                        }
                        if(mysqli_num_rows($result)>0){ // Si la requête a renvoyé des résultats
                            while ($row = mysqli_fetch_assoc($result)){ // Pour chaque ligne de résultat
                                switch($row['type_de_donnees']){ // Selon le type de données
                                    case '0': // Si le type de données est CO2
                                        if($row['type_dalerte'] == '0'){ // Si le type d'alerte est inférieur
                                            $type_dalerte = ">"; // On affecte le type d'alerte
                                            $color = "#FF8F00"; // On affecte la couleur
                                        }else{ // Sinon
                                            $type_dalerte = "<"; // On affecte le type d'alerte
                                            $color = "#00FFFF"; // On affecte la couleur
                                        }
                                        $label = html_entity_decode($row['nom'],ENT_QUOTES)." (".$type_dalerte." ".$row['valeur_de_declenchement'].")"; // On affecte le label
                                        echo 'temparr = [...co2Values];'; // On affecte le tableau temparr
                                        echo 'temparr.fill('.$row['valeur_de_declenchement'].');'; // On remplit le tableau temparr avec la valeur de déclenchement
                                        echo '$co2_alertes.push({label:"'.$label.'",borderColor:"'.$color.'",fill:false,data:temparr});'; // On ajoute l'alerte dans le tableau $co2_alertes
                                        break; // On quitte le switch
                                    case '1': // Si le type de données est température
                                        if($row['type_dalerte'] == '0'){ // Si le type d'alerte est inférieur
                                            $type_dalerte = ">"; // On affecte le type d'alerte
                                            $color = "#FF8F00"; // On affecte la couleur
                                        }else{ // Sinon
                                            $type_dalerte = "<"; // On affecte le type d'alerte
                                            $color = "#00FFFF"; // On affecte la couleur
                                        }
                                        $label = html_entity_decode($row['nom'],ENT_QUOTES)." (".$type_dalerte." ".$row['valeur_de_declenchement'].")"; // On affecte le label
                                        echo 'temparr = [...co2Values];'; // On affecte le tableau temparr
                                        echo 'temparr.fill('.$row['valeur_de_declenchement'].');'; // On remplit le tableau temparr avec la valeur de déclenchement
                                        echo '$temp_alertes.push({label:"'.$label.'",borderColor:"'.$color.'",fill:false,data:temparr});'; // On ajoute l'alerte dans le tableau $temp_alertes
                                        break; // On quitte le switch
                                    case '2': // Si le type de données est humidité
                                        if($row['type_dalerte'] == '0'){ // Si le type d'alerte est inférieur
                                            $type_dalerte = ">"; // On affecte le type d'alerte
                                            $color = "#FF8F00"; // On affecte la couleur
                                        }else{ // Sinon
                                            $type_dalerte = "<"; // On affecte le type d'alerte
                                            $color = "#00FFFF"; // On affecte la couleur
                                        }
                                        $label = html_entity_decode($row['nom'],ENT_QUOTES)." (".$type_dalerte." ".$row['valeur_de_declenchement'].")"; // On affecte le label
                                        echo 'temparr = [...co2Values];'; // On affecte le tableau temparr
                                        echo 'temparr.fill('.$row['valeur_de_declenchement'].');'; // On remplit le tableau temparr avec la valeur de déclenchement
                                        echo '$hum_alertes.push({label:"'.$label.'",borderColor:"'.$color.'",fill:false,data:temparr});'; // On ajoute l'alerte dans le tableau $hum_alertes
                                        break; // On quitte le switch
                                }
                            }
                        }
                    ?>
                    $co2chart.data.datasets = $co2chart.data.datasets.concat($co2_alertes); // On ajoute les alertes dans le tableau $co2chart
                    $tempchart.data.datasets = $tempchart.data.datasets.concat($temp_alertes); // On ajoute les alertes dans le tableau $tempchart
                    $humchart.data.datasets = $humchart.data.datasets.concat($hum_alertes); // On ajoute les alertes dans le tableau $humchart

                    $co2chart.data.labels = xValues; // On affecte les labels
                    $co2chart.data.datasets[0].data = co2Values; // On affecte les données

                    $humchart.data.labels = xValues; // On affecte les labels
                    $humchart.data.datasets[0].data = humValues; // On affecte les données

                    $tempchart.data.labels = xValues; // On affecte les labels
                    $tempchart.data.datasets[0].data = tempValues; // On affecte les données
                    
                    $co2chart.update(); // On met à jour le graphique
                    $humchart.update(); // On met à jour le graphique
                    $tempchart.update(); // On met à jour le graphique
                }else{ // Sinon
                    console.log('Aucune donnée trouvé'); // On affiche un message d'erreur
                }
            }
        };
    }
</script> 
<input id="datetime1" autofocus type="datetime-local" class="w3-center" onchange="javascript:load_data_donnee(document.getElementById('datetime1').value,document.getElementById('datetime2').value)"/> <!-- On crée un input pour la date de début -->
<input id="datetime2" type="datetime-local" class="w3-center" onchange="javascript:load_data_donnee(document.getElementById('datetime1').value,document.getElementById('datetime2').value)"/> <!-- On crée un input pour la date de fin -->
<div class="canvas-container"> <!-- On crée un div pour le graphique -->
    <canvas id="co2" class="canvas"></canvas> <!-- On crée un canvas pour le graphique -->
    <hr style="border-width: 0.1em;border-color: black;"/> <!-- On crée une ligne -->
    <canvas id="hum" class="canvas"></canvas> <!-- On crée un canvas pour le graphique -->
    <hr style="border-width: 0.1em;border-color: black;"/> <!-- On crée une ligne -->
    <canvas id="temp" class="canvas"></canvas> <!-- On crée un canvas pour le graphique -->
</div> <!-- On ferme le div -->
<script> // On crée un script
    var $co2chart = new Chart("co2", { // On crée un nouveau graphique
        type: "line", // On affecte le type de graphique
        data: { // On affecte les données
            labels: xValues, // On affecte les labels
            datasets: [ // On affecte les datasets
                {
                    borderColor: "red", // On affecte la couleur de la bordure
                    fill: false, // On affecte le remplissage
                    label: "CO2 (Ppm)", // On affecte le label
                }
            ]
        },
        options: { // On affecte les options
            legend: {display: true}, // On affiche la légende
            responsive: true, // On active le mode responsive
        }
    });
    var $humchart = new Chart("hum", { // On crée un nouveau graphique
        type: "line", // On affecte le type de graphique
        data: { // On affecte les données
            labels: xValues, // On affecte les labels
            datasets: [ // On affecte les datasets
                {
                    borderColor: "blue", // On affecte la couleur de la bordure
                    fill: false, // On affecte le remplissage
                    label: "Humidité (%HR)", // On affecte le label
                }
            ]
        },
        options: { // On affecte les options
            legend: {display: true}, // On affiche la légende
            responsive: true, // On active le mode responsive
        }
    });
    var $tempchart = new Chart("temp", { // On crée un nouveau graphique
        type: "line", // On affecte le type de graphique
        data: { // On affecte les données
            labels: xValues, // On affecte les labels
            datasets: [ //On affecte les datasets
                {
                    borderColor: "green", // On affecte la couleur de la bordure
                    fill: false, // On affecte le remplissage
                    label: "Température (°C)", // On affecte le label
                }
            ]
        },
        options: { // On affecte les options
            legend: {display: true}, // On affiche la légende
            responsive: true, // On active le mode responsive
        }
    });
    date1 = new Date(); // On crée une nouvelle date
    date2 = new Date(); // On crée une nouvelle date
    date2.setTime(date1.getTime() - (1*60*60*1000)); // On décrémente la date de 1 heure
    load_data_donnee(toISOString(date2),toISOString(date1)); // On charge les données
    document.getElementById('datetime1').value=toISOString(date2); // On affecte la date au input
    document.getElementById('datetime2').value=toISOString(date1); // On affecte la date au input
</script> <!-- On ferme le script -->
<?php require_once "commons/footer.php";?> <!-- On inclut le footer -->