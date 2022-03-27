<?php
    $title = "Rechercher une donnée";
    require_once "commons/header.php";
    if(!isset($_SESSION['loggedin'])){
        echo '<h1><img src="resources/danger.png" width="5%" height="auto" />Vous n\'etes pas connecté, merci de vous connecter à votre compte !</h1>';
        require_once "commons/footer.php";
        die();
    }else if($_SESSION['role']<2){
        echo '<h1><img src="resources/danger.png" width="5%" height="auto" />Vous n\'avez pas l\'autorisation d\'accéder à cette page !</h1>';
        require_once "commons/footer.php";
        die();
    }
?>
<div class="loading-screen" style="display:none;"> 
    <div class="loading-screen-inner-div">
        <i class="fa fa-spinner fa-spin fa-3x fa-fw" aria-hidden="true"></i>
        <h3 class="">Chargement en cours...<span id="percent"></span></h3>
    </div>
</div>
<div class="w3-center">
    <h2>Rechercher une donnée</h2>
    <input id="datetime" autofocus type="datetime-local" class="w3-center" onchange="javascript:load_data_donnee(this.value)"/>
    <br/>
    <h1 id="erreur"></h1>
</div>          
<table class="w3-table-all" id="tableau-rechercher-donnees_capteurs">
    <thead>
        <tr class="w3-blue">
            <th>N° de donnée</th>
            <th>Date et heure de la mesure</th>
            <th>CO²</th>
            <th>Température</th>
            <th>Humidité</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>
<script>
    function load_data_donnee(query){
        if(query.length>0){
            setLoading(true);
            var form_data = new FormData();
            form_data.append('datetime',query);
            var ajax_request = new XMLHttpRequest();
            ajax_request.open('POST','<?=$__WEB_ROOT__?>ajax_scripts/ajax_get_donnee_by_datetime.php');
            ajax_request.addEventListener("progress", function (evt) {
                if(evt.lengthComputable) {
                    var percentComplete = (evt.loaded / evt.total) * 100;
                    document.getElementById("percent").innerHTML = Math.round(percentComplete) + "%";
                }
            }, false);
            ajax_request.send(form_data);
            ajax_request.onreadystatechange = function(){
                if(ajax_request.readyState==4 && ajax_request.status==200){
                    setLoading(false);
                    var response = JSON.parse(ajax_request.responseText);
                    if(response.length > 0){
                        tabBody=document.getElementsByTagName("tbody").item(0);
                        tabBody.innerHTML = "";
                        function addRow(id_donnee,timestamp,co2,temperature,humidite){
                            if (!document.getElementsByTagName) return;
                            tabBody=document.getElementsByTagName("tbody").item(0);
                            row=document.createElement("tr");

                            cell_id_donnee = document.createElement("td");
                            cell_timesamp = document.createElement("td");
                            cell_co2 = document.createElement("td");
                            cell_temperature = document.createElement("td");
                            cell_humidite = document.createElement("td");

                            text_id_donnee = document.createTextNode(id_donnee);
                            text_timesamp = document.createTextNode(timestamp);
                            text_co2 = document.createTextNode(co2);
                            text_temperature = document.createTextNode(temperature);
                            text_humidite = document.createTextNode(humidite);

                            cell_id_donnee.appendChild(text_id_donnee);
                            cell_timesamp.appendChild(text_timesamp);
                            cell_co2.appendChild(text_co2);
                            cell_temperature.appendChild(text_temperature);
                            cell_humidite .appendChild(text_humidite);
                                
                            row.appendChild(cell_id_donnee);
                            row.appendChild(cell_timesamp);
                            row.appendChild(cell_co2);
                            row.appendChild(cell_temperature);
                            row.appendChild(cell_humidite);
                            tabBody.appendChild(row);
                        }
                        for(var i = 0; i <response.length; i++){
                            addRow(response[i].id_donnee,response[i].timestamp,response[i].co2,response[i].temperature,response[i].humidite);
                            document.getElementById('erreur').innerHTML = '';
                        }
                    }else{
                        tabBody=document.getElementsByTagName("tbody").item(0);
                        tabBody.innerHTML = "";
                        document.getElementById('erreur').innerHTML = 'Aucune donnée trouvé';
                    }
                }
            };
        }
    }
    function setLoading(isLoading){
        document.getElementById("percent").innerHTML = "0%";
        if(isLoading){
            document.getElementsByClassName('loading-screen')[0].style.display = 'block';
        }else{
            document.getElementsByClassName('loading-screen')[0].style.display = 'none';
        }
    }
    date = new Date();
    date.setTime(date.getTime() + (1*60*60*1000));
    load_data_donnee(date.toISOString().slice(0, 16));
    document.getElementById('datetime').value=date.toISOString().slice(0, 16);
</script>
<?php require_once "commons/footer.php";?>