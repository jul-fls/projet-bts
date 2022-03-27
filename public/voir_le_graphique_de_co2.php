<?php
    $title = "Voir le graphique de co2";
    require_once "commons/header.php";
    if(!isset($_SESSION['loggedin'])){
        echo '<h1><img src="resources/danger.png" width="5%" height="auto" />Vous n\'etes pas connecté, merci de vous connecter à votre compte !</h1>';
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
    <h2 id="title">Voir le graphique de co2</h2>
</div>
<script>
    var xValues = [];
    var co2Values = [];
    function load_data_donnee(datetime1,datetime2){
        var form_data = new FormData();
        datetime1 ? datetime1 : document.getElementById("datetime1").value;
        datetime2 ? datetime2 : document.getElementById("datetime2").value;
        form_data.append('datetime1',datetime1);
        form_data.append('datetime2',datetime2);
        var ajax_request = new XMLHttpRequest();
        ajax_request.open('POST','<?=$__WEB_ROOT__?>ajax_scripts/ajax_get_donnees_between_two_datetimes.php');
        ajax_request.addEventListener("progress", function (evt) {
            if(evt.lengthComputable) {
                var percentComplete = (evt.loaded / evt.total) * 100;
                document.getElementById("percent").innerHTML = Math.round(percentComplete) + "%";
            }
        }, false);
        ajax_request.send(form_data);
        ajax_request.onreadystatechange = function(){
            if(ajax_request.readyState==4 && ajax_request.status==200){
                var response = JSON.parse(ajax_request.responseText);
                if(response.length > 0){
                    xValues = [];
                    co2Values = [];
                    $co2chart.data.datasets.length=1;
                    function addRow(id_donnee,timestamp,co2){
                        xValues.push(timestamp);
                        co2Values.push(co2);
                    }
                    for(var i = 0; i <response.length; i++){
                        addRow(response[i].id_donnee,response[i].timestamp,response[i].co2);
                    }
                    $co2_alertes = [];
                    <?php
                        require("commons/dbconfig.php");
                        $sql = 'SELECT alertes.id, alertes.nom, alertes.type_de_donnees, alertes.type_dalerte, alertes.valeur_de_declenchement, alertes.active FROM alertes WHERE active = 1 AND type_de_donnees = 0 LIMIT 1000;';
                        $stmt = mysqli_prepare($conn,$sql);
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);
                        if(!$result){
                            die("échec de la lecture des alertes");    
                        }
                        if(mysqli_num_rows($result)>0){
                            while ($row = mysqli_fetch_assoc($result)){
                                if($row['type_dalerte'] == '0'){
                                    $type_dalerte = ">";
                                    $color = "#FF8F00";
                                }else{
                                    $type_dalerte = "<";
                                    $color = "#00FFFF";
                                }
                                $label = html_entity_decode($row['nom'],ENT_QUOTES)." (".$type_dalerte." ".$row['valeur_de_declenchement'].")";
                                echo 'temparr = [...co2Values];';
                                echo 'temparr.fill('.$row['valeur_de_declenchement'].');';
                                echo '$co2_alertes.push({label:"'.$label.'",borderColor:"'.$color.'",fill:false,data:temparr});';
                            }
                        }
                    ?>
                    $co2chart.data.datasets = $co2chart.data.datasets.concat($co2_alertes);

                    $co2chart.data.labels = xValues;
                    $co2chart.data.datasets[0].data = co2Values;
                    
                    $co2chart.update();
                }else{
                    console.log('Aucune donnée trouvé');
                }
            }
        };
    }
</script>
<input id="datetime1" autofocus type="datetime-local" class="w3-center" onchange="javascript:load_data_donnee(document.getElementById('datetime1').value,document.getElementById('datetime2').value)"/>
<input id="datetime2" type="datetime-local" class="w3-center" onchange="javascript:load_data_donnee(document.getElementById('datetime1').value,document.getElementById('datetime2').value)"/>
<div class="canvas-container">
    <canvas id="co2" class="canvas"></canvas>
</div>
<script>
    var $co2chart = new Chart("co2", {
        type: "line",
        data: {
            labels: xValues,
            datasets: [
                {
                    data: co2Values,
                    borderColor: "red",
                    fill: false,
                    label: "CO2 (Ppm)",
                }
            ]
        },
        options: {
            legend: {display: true}
        }
    });
    date1 = new Date();
    date1.setTime(date1.getTime() + (1*60*60*1000));
    date2 = new Date();
    load_data_donnee(date2.toISOString().slice(0, 16),date1.toISOString().slice(0, 16));
    document.getElementById('datetime1').value=date2.toISOString().slice(0, 16);
    document.getElementById('datetime2').value=date1.toISOString().slice(0, 16);
</script>
<?php require_once "commons/footer.php";?>