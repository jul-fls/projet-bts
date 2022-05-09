<?php
    $title = "Lister toutes les données du jour";
    require_once "commons/header.php";
    if(!isset($_SESSION['loggedin'])){
        echo '<h1><img src="resources/danger.png" width="5%" height="auto" />Vous n\'etes pas connecté, merci de vous connecter à votre compte !</h1>';
        require_once "commons/footer.php";
        die();
    }else if($_SESSION['role']<1){
        echo '<h1><img src="resources/danger.png" width="5%" height="auto" />Vous n\'avez pas l\'autorisation d\'accéder à cette page !</h1>';
        require_once "commons/footer.php";
        die();
    }
?>
<div class="w3-center">
    <h2 id="title">Liste de tous les données des capteurs du jour</h2>
</div>
<table class="w3-table-all" id="tableau-liste-donnees_capteurs">
    <thead>
        <tr class="w3-blue">
            <th>N° de la donnée</th>
            <th>Heure de mesure</th>
            <th>CO² (Ppm)</th>
            <th>Température (°C)</th>
            <th>Humidité (%HR)</th>
        </tr>
    </thead>
    <?php
        require("commons/dbconfig.php");
        $sql = 'SELECT donnees_capteurs.id AS id_mesure, DATE_FORMAT(donnees_capteurs.timestamp, \'%H:%i:%S\') AS heure_mesure, donnees_capteurs.co2, donnees_capteurs.temp, donnees_capteurs.hum FROM donnees_capteurs WHERE DATE(donnees_capteurs.timestamp) = CURRENT_DATE ORDER BY donnees_capteurs.timestamp DESC;';
        $stmt = mysqli_prepare($conn,$sql);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if(!$result){
            die("échec de la lecture des données");    
        }
        if(mysqli_num_rows($result)>0){
            while ($row = mysqli_fetch_assoc($result)){
                echo '<tr>'."\n";
                echo '<td>'.$row['id_mesure'].'</td>'."\n";
                echo '<td>'.$row['heure_mesure'].'</td>'."\n";
                echo '<td>'.$row['co2'].'</td>'."\n";
                echo '<td>'.$row['temp'].'</td>'."\n";
                echo '<td>'.$row['hum'].'</td>'."\n";
                echo '</tr>'."\n";
            }
        }
    ?>
</table>
<script>
    $nb_donnees = <?=mysqli_num_rows($result)?>;
    if($nb_donnees==1){
        document.getElementById('title').innerHTML = 'Liste de l\'unique donnée du jour';
    }else{
        document.getElementById('title').innerHTML = 'Liste des <b>'+$nb_donnees+'</b> données du jour';
    }
    $th = document.getElementsByTagName('th');
    for(i=0;i<$th.length;i++){
        $th[i].name=$th[i].innerHTML;
        $th[i].innerHTML += ' <i class="fa fa-sort"></i> ';
        $th[i].addEventListener('click',function(){
            sortTable(this.cellIndex);
        });
        $th[i].style.cursor = 'pointer';
    }
    function sortTable(n){
        var table,rows,switching,i,x,y,shouldSwitch,dir,switchcount=0;
        table=document.getElementsByTagName("table")[0];
        switching=true;
        dir="asc"; 
        while(switching){
            switching=false;
            rows=table.rows;
            for(i=1;i<(rows.length-1);i++){
                shouldSwitch=false;
                x=rows[i].getElementsByTagName("td")[n];
                y=rows[i+1].getElementsByTagName("td")[n];
                typeofdata=0;
                if(!isNaN(parseFloat(x.innerHTML))){
                    typeofdata = 1;
                }else{
                    typeofdata = 0;
                }
                if(typeofdata==1){
                    if(dir=="asc"){
                        if(parseFloat(x.innerHTML)>parseFloat(y.innerHTML)){
                            shouldSwitch=true;
                            rows[0].getElementsByTagName("th")[n].innerHTML = rows[0].getElementsByTagName("th")[n].name + ' <i class="fa fa-sort-numeric-asc"></i>';
                            break;
                        }
                    }else if(dir=="desc"){
                        if(parseFloat(x.innerHTML)<parseFloat(y.innerHTML)){
                            shouldSwitch=true;
                            rows[0].getElementsByTagName("th")[n].innerHTML = rows[0].getElementsByTagName("th")[n].name + ' <i class="fa fa-sort-numeric-desc"></i>';
                            break;
                        }
                    }
                }else if(typeofdata==0){
                    if(dir=="asc"){
                        if(x.innerHTML.toLowerCase()>y.innerHTML.toLowerCase()){
                            shouldSwitch=true;
                            rows[0].getElementsByTagName("th")[n].innerHTML = rows[0].getElementsByTagName("th")[n].name + ' <i class="fa fa-sort-alpha-asc"></i>';
                            break;
                        }
                    }else if(dir=="desc"){
                        if(x.innerHTML.toLowerCase()<y.innerHTML.toLowerCase()){
                            shouldSwitch=true;
                            rows[0].getElementsByTagName("th")[n].innerHTML = rows[0].getElementsByTagName("th")[n].name + ' <i class="fa fa-sort-alpha-desc"></i>';
                            break;
                        }
                    }
                }
            }
            if(shouldSwitch){
                rows[i].parentNode.insertBefore(rows[i+1],rows[i]);
                switching=true;
                switchcount++;      
            }else{
                if(switchcount==0&&dir=="asc"){
                    dir="desc";
                    switching=true;
                }
            }
        }
    }
</script>
<?php require_once "commons/footer.php";?>