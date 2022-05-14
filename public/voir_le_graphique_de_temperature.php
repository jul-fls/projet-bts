<?php
    $title = "Voir le graphique de température"; //Titre de la page
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
    <h2 id="title">Voir le graphique de température</h2> <!-- Titre de la page -->
</div> <!-- Fin de la div de la page -->
<script> //Début du script
    var xValues = []; //Tableau des valeurs de x
    var tempValues = []; //Tableau des valeurs de température
    function load_data_donnee(datetime1,datetime2){ //Fonction de chargement des données
        var form_data = new FormData(); //Création d'un nouveau formulaire
        datetime1 ? datetime1 : document.getElementById("datetime1").value; //Si la date de début est présente dans la requete, on la récupère, sinon on prend la date de début du formulaire
        datetime2 ? datetime2 : document.getElementById("datetime2").value; //Si la date de fin est présente dans la requete, on la récupère, sinon on prend la date de fin du formulaire
        form_data.append('datetime1',datetime1); //Ajout de la date de début dans le formulaire
        form_data.append('datetime2',datetime2); //Ajout de la date de fin dans le formulaire
        var ajax_request = new XMLHttpRequest(); //Création d'une nouvelle requête AJAX
        ajax_request.open('POST','<?=$__WEB_ROOT__?>ajax_scripts/ajax_get_donnees_between_two_datetimes.php'); //Ouverture de la requête AJAX
        ajax_request.addEventListener("progress", function (evt) { //Si la requête AJAX est en cours
            if(evt.lengthComputable) { //Si la taille de la requête est connue
                var percentComplete = (evt.loaded / evt.total) * 100; //Calcul du pourcentage de chargement
                document.getElementById("percent").innerHTML = Math.round(percentComplete) + "%"; //Affichage du pourcentage de chargement
            }
        }, false); //Fin de l'écouteur d'événement
        ajax_request.send(form_data); //Envoi de la requête AJAX
        ajax_request.onreadystatechange = function(){ //Si la requête AJAX est terminée
            if(ajax_request.readyState==4 && ajax_request.status==200){ //Si la requête AJAX est terminée et que le code de retour est 200
                var response = JSON.parse(ajax_request.responseText); //Récupération de la réponse de la requête AJAX
                if(response.length > 0){ //Si la réponse de la requête AJAX est supérieure à 0
                    xValues = []; //On vide le tableau des valeurs de x
                    tempValues = []; //On vide le tableau des valeurs de température
                    $tempchart.data.datasets.length=1; //On vide le tableau des données du graphique
                    function addRow(id_donnee,timestamp,temperature){ //Fonction d'ajout d'une ligne
                        xValues.push(timestamp); //Ajout de la valeur de x
                        tempValues.push(temperature); //Ajout de la valeur de température
                    } 
                    for(var i = 0; i <response.length; i++){ //Pour chaque ligne de la réponse
                        addRow(response[i].id_donnee,response[i].timestamp,response[i].temperature); //Ajout d'une ligne
                    } 
                    $temp_alertes = []; //On vide le tableau des alertes de température
                    <?php 
                        require("commons/dbconfig.php"); //Inclusion de la configuration de la base de données
                        $sql = 'SELECT alertes.id, alertes.nom, alertes.type_de_donnees, alertes.type_dalerte, alertes.valeur_de_declenchement, alertes.active FROM alertes WHERE active = 1 AND type_de_donnees = 1 LIMIT 1000;'; //Requête pour récupérer les alertes de température
                        $stmt = mysqli_prepare($conn,$sql); //Préparation de la requête
                        mysqli_stmt_execute($stmt); //Exécution de la requête
                        $result = mysqli_stmt_get_result($stmt); //Récupération du résultat de la requête
                        if(!$result){ //Si la requête n'a pas abouti
                            die("échec de la lecture des alertes"); //Message d'erreur   
                        }
                        if(mysqli_num_rows($result)>0){ //Si le nombre de lignes de la requête est supérieur à 0
                            while ($row = mysqli_fetch_assoc($result)){ //Pour chaque ligne de la requête
                                if($row['type_dalerte'] == '0'){ //Si le type d'alerte est de type "inférieur"
                                    $type_dalerte = ">"; //On affecte le signe "supérieur" au type d'alerte
                                    $color = "#FF8F00"; //On affecte la couleur orange
                                }else{ //Sinon
                                    $type_dalerte = "<"; //On affecte le signe "inférieur" au type d'alerte
                                    $color = "#00FFFF"; //On affecte la couleur bleue
                                }
                                $label = html_entity_decode($row['nom'],ENT_QUOTES)." (".$type_dalerte." ".$row['valeur_de_declenchement'].")"; //On décode le nom de l'alerte et on le concatène avec le type d'alerte et la valeur de déclenchement
                                echo 'temparr = [...tempValues];'."\n"; //On crée un tableau contenant les valeurs de température
                                echo 'temparr.fill('.$row['valeur_de_declenchement'].');'."\n"; //On remplit le tableau avec la valeur de déclenchement
                                echo '$temp_alertes.push({label:"'.$label.'",borderColor:"'.$color.'",fill:false,data:temparr});'."\n"; //On ajoute l'alerte au tableau des alertes de température
                            }
                        }
                    ?>
                    $tempchart.data.labels = xValues; //On affecte les valeurs de x au graphique
                    $tempchart.data.datasets[0].data = tempValues; //On affecte les valeurs de température au graphique
                    $tempchart.data.datasets = $tempchart.data.datasets.concat($temp_alertes); //On ajoute les alertes de température au graphique
                    
                    $tempchart.update(); //On met à jour le graphique
                }else{ //Sinon
                    console.log('Aucune donnée trouvé'); //On affiche un message d'erreur
                }
            }
        };
    }
</script> <!-- Fin du script de récupération des données -->
<input id="datetime1" autofocus type="datetime-local" class="w3-center" onchange="javascript:load_data_donnee(document.getElementById('datetime1').value,document.getElementById('datetime2').value)"/> <!-- Champ de saisie de la date de début -->
<input id="datetime2" type="datetime-local" class="w3-center" onchange="javascript:load_data_donnee(document.getElementById('datetime1').value,document.getElementById('datetime2').value)"/> <!-- Champ de saisie de la date de fin -->
<div class="canvas-container"> <!-- Début du div contenant le graphique -->
    <canvas id="temp" class="canvas"></canvas> <!-- Début du graphique -->
</div> <!-- Fin du div contenant le graphique -->
<script> //Début du script de création du graphique
    var $tempchart = new Chart("temp", { //Création du graphique
        type: "line", //Type de graphique
        data: { //Données du graphique
            labels: xValues, //Valeurs de x
            datasets: [ // Afgection du dataset
                {
                    data: tempValues, //Valeurs de température
                    borderColor: "green", //Couleur de la bordure
                    fill: false, //Pas de remplissage
                    label: "Température (°C)", //Nom du dataset
                }
            ]
        },
        options: { //Options du graphique
            legend: {display: true}, //Affichage de la légende
            responsive: true, //Graphique adaptatif
        }
    });
    date1 = new Date(); //Création d'une nouvelle date
    date2 = new Date(); //Création d'une nouvelle date
    date2.setTime(date1.getTime() - (1*60*60*1000)); //On soustrait 1 heure à la date2
    load_data_donnee(toISOString(date2),toISOString(date1)); //On charge les données de la date de début à la date de fin
    document.getElementById('datetime1').value=toISOString(date2); //On affecte la date de début au champ de saisie
    document.getElementById('datetime2').value=toISOString(date1); //On affecte la date de fin au champ de saisie
</script> <!-- Fin du script de création du graphique -->
<?php require_once "commons/footer.php";?> <!-- Footer -->