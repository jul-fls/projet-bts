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
            <th>Moyenne CO²</th>
            <th>Moyenne Température</th>
            <th>Moyenne Humidité</th>
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
<?php require_once "commons/footer.php";?>