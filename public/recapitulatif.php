<?php
    $title = "Récapitulatif";
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
<div class="w3-center">
    <h2 id="title">Récapitulatif</h2>
    <a href="export_mesures.php?download=csv" class="w3-btn w3-green"><i class="fa fa-download"> </i>Télécharger le récapitulatif au format CSV</a>
</div>
<table class="w3-table-all" id="tableau-liste-donnees_capteurs">
    <thead>
        <tr class="w3-blue">
            <th>Mois</th>
            <th>Année</th>
            <th>Nombre de mesures</th>
            <th>Moyenne CO² (Ppm)</th>
            <th>Moyenne Température (°C)</th>
            <th>Moyenne Humidité (%HR)</th>
        </tr>
    </thead>
    <?php
        require("commons/dbconfig.php");
        $sql = 'SELECT DATE_FORMAT(donnees_capteurs.timestamp,\'%m\') AS mois, DATE_FORMAT(donnees_capteurs.timestamp,\'%Y\') AS annee, COUNT(*) AS nb_mesures, ROUND(AVG(donnees_capteurs.co2),0) AS co2_moy, ROUND(AVG(donnees_capteurs.temp),2) as temp_moy, ROUND(AVG(donnees_capteurs.hum),2) AS hum_moy FROM donnees_capteurs GROUP BY mois, annee;';
        $stmt = mysqli_prepare($conn,$sql);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if(!$result){
            die("échec de la lecture des données");    
        }
        if(mysqli_num_rows($result)>0){
            $mois = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
            while ($row = mysqli_fetch_assoc($result)){
                echo '<tr>'."\n";
                $moi = DateTime::createFromFormat('m', $row['mois']);
                echo '<td>'.$mois[$moi->format('n')-1].'</td>'."\n";
                echo '<td>'.$row['annee'].'</td>'."\n";
                echo '<td>'.$row['nb_mesures'].'</td>'."\n";
                echo '<td>'.$row['co2_moy'].'</td>'."\n";
                echo '<td>'.$row['temp_moy'].'</td>'."\n";
                echo '<td>'.$row['hum_moy'].'</td>'."\n";
                echo '</tr>'."\n";
            }
        }
    ?>
</table>
<script>
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