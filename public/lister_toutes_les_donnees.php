<?php
    $title = "Lister toutes les données";
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
    <h2 id="title">Liste de tous les données des capteurs</h2>
</div>
<table class="w3-table-all" id="tableau-liste-donnees_capteurs">
    <thead>
        <tr class="w3-blue">
            <th>N° de la donnée</th>
            <th>Date et heure de mesure</th>
            <th>CO²</th>
            <th>Température</th>
            <th>Humidité</th>
        </tr>
    </thead>
    <?php
        require("commons/dbconfig.php");
        $sql = 'SELECT donnees_capteurs.id AS id_mesure, DATE_FORMAT(donnees_capteurs.timestamp, \'%d/%m/%Y %H:%i:%S\') AS date_mesure, donnees_capteurs.co2, donnees_capteurs.temp, donnees_capteurs.hum FROM donnees_capteurs ORDER BY donnees_capteurs.timestamp DESC;';
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
                echo '<td>'.$row['date_mesure'].'</td>'."\n";
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
        document.getElementById('title').innerHTML = 'Liste de l\'unique donnée';
    }else{
        document.getElementById('title').innerHTML = 'Liste des <b>'+$nb_donnees+'</b> données';
    }
</script>
<?php require_once "commons/footer.php";?>