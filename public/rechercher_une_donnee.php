<?php
    $title = "Rechercher une donnée"; // Titre de la page
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
<div class="loading-screen" style="display:none;"> <!--- Div qui affiche le message de chargement --->
    <div class="loading-screen-inner-div"> <!--- Div qui affiche le message de chargement --->
        <i class="fa fa-spinner fa-spin fa-3x fa-fw" aria-hidden="true"></i> <!--- Icone de chargement --->
        <h3 class="">Chargement en cours...<span id="percent"></span></h3> <!--- Message de chargement --->
    </div>
</div>
<div class="w3-center"> <!--- Div qui centre --->
    <h2>Rechercher une donnée</h2> <!--- Titre de la page --->
    <input id="datetime" autofocus type="datetime-local" class="w3-center" onchange="javascript:load_data_donnee(this.value)"/> <!--- Input qui permet de choisir une date --->
    <br/> <!--- Saut de ligne --->
    <h1 id="erreur"></h1> <!--- Div qui affiche le message d'erreur --->
</div>          
<table class="w3-table-all" id="tableau-rechercher-donnees_capteurs"> <!--- Tableau qui affiche les données --->
    <thead> <!--- En-tête du tableau --->
        <tr class="w3-blue"> <!--- Ligne de l'en-tête --->
            <th>N° de donnée</th> <!--- Colonne qui affiche le numéro de la donnée --->
            <th>Date et heure de la mesure</th> <!--- Colonne qui affiche la date et l'heure de la mesure --->
            <th>CO² (Ppm)</th> <!--- Colonne qui affiche le CO² --->
            <th>Température (°C)</th> <!--- Colonne qui affiche la température --->
            <th>Humidité (%HR)</th> <!--- Colonne qui affiche l'humidité --->
        </tr> <!--- Fin de la ligne de l'en-tête --->
    </thead> <!--- Fin de l'en-tête du tableau --->
    <tbody> <!--- Corps du tableau --->
    </tbody> <!--- Fin du corps du tableau --->
</table> <!--- Fin du tableau --->
<script> //Script qui permet de charger les données
    function load_data_donnee(query){ // Fonction qui permet de charger les données
        if(query.length>0){ // Si la date et l'heure sont renseignées
            setLoading(true); // On affiche le message de chargement
            var form_data = new FormData(); // On crée un nouveau formulaire
            form_data.append('datetime',query); // On ajoute la date et l'heure au formulaire
            var ajax_request = new XMLHttpRequest(); // On crée une nouvelle requête AJAX
            ajax_request.open('POST','<?=$__WEB_ROOT__?>ajax_scripts/ajax_get_donnee_by_datetime.php'); // On ouvre la requête AJAX
            ajax_request.addEventListener("progress", function (evt) { // On ajoute un écouteur d'événement
                if(evt.lengthComputable) { // Si la taille est connue
                    var percentComplete = (evt.loaded / evt.total) * 100; // On calcule le pourcentage de chargement
                    document.getElementById("percent").innerHTML = Math.round(percentComplete) + "%"; // On affiche le pourcentage de chargement
                } 
            }, false); 
            ajax_request.send(form_data); // On envoie la requête AJAX
            ajax_request.onreadystatechange = function(){ // On ajoute un écouteur d'événement
                if(ajax_request.readyState==4 && ajax_request.status==200){ // Si la requête AJAX est terminée et qu'elle s'est bien passée
                    setLoading(false); // On cache le message de chargement
                    var response = JSON.parse(ajax_request.responseText); // On récupère la réponse de la requête AJAX
                    if(response.length > 0){ // Si il y a des données
                        tabBody=document.getElementsByTagName("tbody").item(0); // On récupère le corps du tableau
                        tabBody.innerHTML = ""; // On vide le corps du tableau
                        function addRow(id_donnee,timestamp,co2,temperature,humidite){ // Fonction qui permet d'ajouter une ligne
                            if (!document.getElementsByTagName) return; // Si le navigateur ne supporte pas la fonction getElementsByTagName
                            tabBody=document.getElementsByTagName("tbody").item(0); // On récupère le corps du tableau
                            row=document.createElement("tr"); // On crée une nouvelle ligne

                            cell_id_donnee = document.createElement("td"); // On crée une nouvelle cellule
                            cell_timesamp = document.createElement("td"); // On crée une nouvelle cellule
                            cell_co2 = document.createElement("td"); // On crée une nouvelle cellule
                            cell_temperature = document.createElement("td"); // On crée une nouvelle cellule
                            cell_humidite = document.createElement("td"); // On crée une nouvelle cellule

                            text_id_donnee = document.createTextNode(id_donnee); // On crée un nouveau texte
                            text_timesamp = document.createTextNode(timestamp); // On crée un nouveau texte
                            text_co2 = document.createTextNode(co2); // On crée un nouveau texte
                            text_temperature = document.createTextNode(temperature); // On crée un nouveau texte
                            text_humidite = document.createTextNode(humidite);  // On crée un nouveau texte

                            cell_id_donnee.appendChild(text_id_donnee); // On ajoute le texte à la cellule
                            cell_timesamp.appendChild(text_timesamp); // On ajoute le texte à la cellule
                            cell_co2.appendChild(text_co2); // On ajoute le texte à la cellule
                            cell_temperature.appendChild(text_temperature); // On ajoute le texte à la cellule
                            cell_humidite .appendChild(text_humidite); // On ajoute le texte à la cellule
                                
                            row.appendChild(cell_id_donnee); // On ajoute la cellule à la ligne
                            row.appendChild(cell_timesamp); // On ajoute la cellule à la ligne
                            row.appendChild(cell_co2); // On ajoute la cellule à la ligne
                            row.appendChild(cell_temperature); // On ajoute la cellule à la ligne
                            row.appendChild(cell_humidite); // On ajoute la cellule à la ligne
                            tabBody.appendChild(row); // On ajoute la ligne au corps du tableau
                        }
                        for(var i = 0; i <response.length; i++){ // Pour chaque donnée
                            addRow(response[i].id_donnee,response[i].timestamp,response[i].co2,response[i].temperature,response[i].humidite); // On ajoute une ligne
                            document.getElementById('erreur').innerHTML = ''; // On vide le message d'erreur
                        }
                    }else{ // Si il n'y a pas de donnée
                        tabBody=document.getElementsByTagName("tbody").item(0); // On récupère le corps du tableau
                        tabBody.innerHTML = ""; // On vide le corps du tableau
                        document.getElementById('erreur').innerHTML = 'Aucune donnée trouvé'; // On affiche un message d'erreur
                    }
                }
            };
        }
    }
    function setLoading(isLoading){ // Fonction qui permet d'afficher le message de chargement
        document.getElementById("percent").innerHTML = "0%"; // On affiche 0%
        if(isLoading){ // Si on doit afficher le message de chargement
            document.getElementsByClassName('loading-screen')[0].style.display = 'block'; // On affiche le message de chargement
        }else{ // Sinon
            document.getElementsByClassName('loading-screen')[0].style.display = 'none'; // On cache le message de chargement
        }
    }
    date = new Date(); // On crée une nouvelle date
    load_data_donnee(toISOString(date)); // On charge les données de la date actuelle
    document.getElementById('datetime').value=toISOString(date); // On affiche la date actuelle
</script>
<?php require_once "commons/footer.php";?> <!-- On affiche le footer -->