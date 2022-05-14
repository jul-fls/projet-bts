<?php
    $title = "Voir le graphique de co2"; //Titre de la page
    require_once "commons/header.php"; //Inclusion du header
    if(!isset($_SESSION['loggedin'])){ //Si l'utilisateur n'est pas connecté
        echo '<h1><img src="resources/danger.png" width="5%" height="auto" />Vous n\'etes pas connecté, merci de vous connecter à votre compte !</h1>'; //Message d'erreur
        require_once "commons/footer.php"; //Inclusion du footer
        die(); //Arrêt du script
    }
?>
<div class="loading-screen" style="display:none;"> <!-- Début de la div de chargement -->
    <div class="loading-screen-inner-div"> <!-- Début de la div de chargement -->
        <i class="fa fa-spinner fa-spin fa-3x fa-fw" aria-hidden="true"></i> <!-- Icone de chargement -->
        <h3 class="">Chargement en cours...<span id="percent"></span></h3> <!-- Texte de chargement -->
    </div> <!-- Fin de la div de chargement -->
</div> <!-- Fin de la div de chargement -->
<div class="w3-center"> <!-- Début de la div de la page -->
    <h2 id="title">Voir le graphique de co2</h2> <!-- Titre de la page -->
</div> <!-- Fin de la div de la page -->
<script> //Début du script
    var xValues = []; //Création d'un tableau vide pour les valeurs de x
    var co2Values = []; //Création d'un tableau vide pour les valeurs de co2
    function load_data_donnee(datetime1,datetime2){ //Fonction de chargement des données
        var form_data = new FormData(); //Création d'un objet FormData
        datetime1 ? datetime1 : document.getElementById("datetime1").value; //Si la valeur de datetime1 est présente dans la requete, on la récupère, sinon on récupère la valeur de la date de début
        datetime2 ? datetime2 : document.getElementById("datetime2").value; //Si la valeur de datetime2 est présente dans la requete, on la récupère, sinon on récupère la valeur de la date de fin
        form_data.append('datetime1',datetime1); //Ajout de la valeur de datetime1 dans l'objet FormData
        form_data.append('datetime2',datetime2); //Ajout de la valeur de datetime2 dans l'objet FormData
        var ajax_request = new XMLHttpRequest(); //Création d'un objet XMLHttpRequest
        ajax_request.open('POST','<?=$__WEB_ROOT__?>ajax_scripts/ajax_get_donnees_between_two_datetimes.php'); //Ouverture de la requête
        ajax_request.addEventListener("progress", function (evt) { //Ajout d'un écouteur d'événement pour la progression de la requête
            if(evt.lengthComputable) { //Si la taille de la requête est connue
                var percentComplete = (evt.loaded / evt.total) * 100; //Calcul du pourcentage de progression
                document.getElementById("percent").innerHTML = Math.round(percentComplete) + "%"; //Affichage du pourcentage de progression
            }
        }, false); //Fin de l'écouteur d'événement
        ajax_request.send(form_data); //Envoi de l'objet FormData
        ajax_request.onreadystatechange = function(){ //Début de la fonction d'écoute de l'état de la requête
            if(ajax_request.readyState==4 && ajax_request.status==200){ //Si la requête est terminée et que le code de retour est 200
                var response = JSON.parse(ajax_request.responseText); //Récupération de la réponse de la requête
                if(response.length > 0){ //Si la taille de la réponse est supérieure à 0
                    xValues = []; //On vide le tableau des valeurs de x
                    co2Values = []; //On vide le tableau des valeurs de co2
                    $co2chart.data.datasets.length=1; //On vide le tableau des données du graphique
                    function addRow(id_donnee,timestamp,co2){ //Fonction d'ajout d'une ligne
                        xValues.push(timestamp); //Ajout de la valeur de timestamp dans le tableau des valeurs de x
                        co2Values.push(co2); //Ajout de la valeur de co2 dans le tableau des valeurs de co2
                    } 
                    for(var i = 0; i <response.length; i++){ //Pour chaque ligne de la réponse
                        addRow(response[i].id_donnee,response[i].timestamp,response[i].co2); //Ajout d'une ligne
                    }
                    $co2_alertes = []; //On vide le tableau des alertes
                    <?php 
                        require("commons/dbconfig.php"); //Inclusion de la connexion à la base de données
                        $sql = 'SELECT alertes.id, alertes.nom, alertes.type_de_donnees, alertes.type_dalerte, alertes.valeur_de_declenchement, alertes.active FROM alertes WHERE active = 1 AND type_de_donnees = 0 LIMIT 1000;'; //Requête pour récupérer les alertes
                        $stmt = mysqli_prepare($conn,$sql); //Préparation de la requête
                        mysqli_stmt_execute($stmt); //Exécution de la requête
                        $result = mysqli_stmt_get_result($stmt); //Récupération du résultat de la requête
                        if(!$result){ //Si la requête ne s'est pas exécutée correctement
                            die("échec de la lecture des alertes"); //On affiche un message d'erreur
                        }
                        if(mysqli_num_rows($result)>0){ //Si le nombre de lignes du résultat de la requête est supérieur à 0
                            while ($row = mysqli_fetch_assoc($result)){ //Pour chaque ligne du résultat de la requête
                                if($row['type_dalerte'] == '0'){ //Si le type d'alerte est égal à 0
                                    $type_dalerte = ">"; //On affecte la valeur ">" à la variable $type_dalerte
                                    $color = "#FF8F00"; //On affecte la couleur orange à la variable $color
                                }else{ //Sinon
                                    $type_dalerte = "<"; //On affecte la valeur "<" à la variable $type_dalerte
                                    $color = "#00FFFF"; //On affecte la couleur bleu à la variable $color
                                }
                                $label = html_entity_decode($row['nom'],ENT_QUOTES)." (".$type_dalerte." ".$row['valeur_de_declenchement'].")"; //On décode le nom de l'alerte et on le met dans la variable $label
                                echo 'temparr = [...co2Values];'; //On crée un tableau temporaire avec les valeurs de co2
                                echo 'temparr.fill('.$row['valeur_de_declenchement'].');'; //On remplit le tableau temporaire avec la valeur de déclenchement de l'alerte
                                echo '$co2_alertes.push({label:"'.$label.'",borderColor:"'.$color.'",fill:false,data:temparr});'; //On ajoute l'alerte dans le tableau des alertes
                            }
                        }
                    ?>
                    $co2chart.data.datasets = $co2chart.data.datasets.concat($co2_alertes); //On ajoute les alertes dans le tableau des données du graphique

                    $co2chart.data.labels = xValues; //On affecte les valeurs de x dans le tableau des labels du graphique
                    $co2chart.data.datasets[0].data = co2Values; //On affecte les valeurs de co2 dans le tableau des données du graphique
                    
                    $co2chart.update(); //On met à jour le graphique
                }else{ //Sinon
                    console.log('Aucune donnée trouvé'); //On affiche un message d'erreur
                }
            }
        };
    }
</script> 
<input id="datetime1" autofocus type="datetime-local" class="w3-center" onchange="javascript:load_data_donnee(document.getElementById('datetime1').value,document.getElementById('datetime2').value)"/> <!-- Input de la date de début -->
<input id="datetime2" type="datetime-local" class="w3-center" onchange="javascript:load_data_donnee(document.getElementById('datetime1').value,document.getElementById('datetime2').value)"/> <!-- Input de la date de fin -->
<div class="canvas-container"> <!-- Début du div contenant le graphique -->
    <canvas id="co2" class="canvas"></canvas> <!-- Canvas du graphique -->
</div> <!-- Fin du div contenant le graphique -->
<script> //Début du script
    var $co2chart = new Chart("co2", { //Création du graphique
        type: "line", //Type de graphique
        data: { //Données du graphique
            labels: xValues, //Labels des valeurs de x
            datasets: [ // Affectation du dataset
                {
                    data: co2Values, //Valeurs de co2
                    borderColor: "red", //Couleur de la bordure
                    fill: false, //Pas de remplissage
                    label: "CO2 (Ppm)", //Nom du dataset
                }
            ]
        },
        options: { //Options du graphique
            legend: {display: true}, //Affichage de la légende
            responsive: true, //Responsive
        }
    });
    date1 = new Date(); //Création d'une nouvelle date
    date2 = new Date(); //Création d'une nouvelle date
    date2.setTime(date1.getTime() - (1*60*60*1000)); //On soustrait 1 heure à la date2
    load_data_donnee(toISOString(date2),toISOString(date1)); //On charge les données de la date de début à la date de fin
    document.getElementById('datetime1').value=toISOString(date2); //On affecte la date de début dans le input
    document.getElementById('datetime2').value=toISOString(date1); //On affecte la date de fin dans le input
</script> <!-- Fin du script -->
<?php require_once "commons/footer.php";?> <!-- Inclusion du footer -->