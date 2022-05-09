<?php
    $title = "Lister toutes les alertes";
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
    <h2 id="title">Liste des alertes</h2>
</div>
<table class="w3-table-all" id="tableau-liste-menus">
    <thead>
        <tr class="w3-blue">
            <th>N° de l'alerte</th>
            <th>Nom</th>
            <th>Type de données</th>
            <th>Type d'alerte</th>
            <th>Valeur de déclenchement</th>
            <th>Active</th>
            <th>Actions</th>
        </tr>
    </thead>
    <?php
        require("commons/dbconfig.php");
        $sql = 'SELECT alertes.id, alertes.nom, alertes.type_de_donnees, alertes.type_dalerte, alertes.valeur_de_declenchement, alertes.active FROM alertes LIMIT 1000;';
        $stmt = mysqli_prepare($conn,$sql);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if(!$result){
            die("échec de la lecture des alertes");    
        }
        if(mysqli_num_rows($result)>0){
            while ($row = mysqli_fetch_assoc($result)){
                echo '<tr>'."\n";
                echo '<td>'.$row['id'].'</td>'."\n";
                echo '<td>'.$row['nom'].'</td>'."\n";
                if($row['type_de_donnees']==0){
                    echo '<td>Co2 (Ppm)</td>'."\n";
                }else if($row['type_de_donnees']==1){
                    echo '<td>Température (°C)</td>'."\n";
                }else if($row['type_de_donnees']==2){
                    echo '<td>Humidité (%HR)</td>'."\n";
                }
                if($row['type_dalerte']==1){
                    echo '<td>En dessous</td>'."\n";
                }else if($row['type_dalerte']==0){
                    echo '<td>Au dessus</td>'."\n";
                }
                echo '<td>'.$row['valeur_de_declenchement'].'</td>'."\n";
                if($row['active']==1){
                    echo '<td><i title="Activée" style="color: green" class="fa fa-check-square-o fa-2x"></i></td>'."\n";
                }else if($row['active']==0){
                    echo '<td><i title="Désactivée" style="color: darkred" class="fa fa-window-close-o fa-2x"></i></td>'."\n";
                }
                echo '<td><a title="Modifier l\'alerte" href="modifier_une_alerte.php?id='.$row['id'].'"><i class="fa fa-pencil fa-2x"></i></a></td>'."\n";
                echo '</tr>'."\n";
            }
        }
    ?>
</table>
<script>
    $nb_alertes = <?=mysqli_num_rows($result)?>;
    if($nb_alertes==1){
        document.getElementById('title').innerHTML = 'Liste de l\'unique alerte';
    }else{
        document.getElementById('title').innerHTML = 'Liste des <b>'+$nb_alertes+'</b> alertes';
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