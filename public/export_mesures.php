<?php
    session_start();
    if(!isset($_SESSION['loggedin'])){
        echo '<h1><img src="resources/danger.png" width="5%" height="auto" />Vous n\'etes pas connecté, merci de vous connecter à votre compte !</h1>';
        require_once "commons/footer.php";
        die();
    }else if($_SESSION['role']<2){
        echo '<h1><img src="resources/danger.png" width="5%" height="auto" />Vous n\'avez pas l\'autorisation d\'accéder à cette page !</h1>';
        require_once "commons/footer.php";
        die();
    }
    require("commons/dbconfig.php");
    if(isset($_GET['download'])&&$_GET['download']=='csv'){
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=export_mesures.csv');
        header("Content-Transfer-Encoding: UTF-8");
        $f = fopen('php://output', 'a');
        $csv = "Mois,Année,Nombre de mesures,CO² (Ppm),Température (°C),Humidité (%HR)\n";
        fputs($f, $csv);
        $sql = 'SELECT DATE_FORMAT(donnees_capteurs.timestamp,\'%m\') AS mois, DATE_FORMAT(donnees_capteurs.timestamp,\'%Y\') AS annee, COUNT(*) AS nb_mesures, ROUND(AVG(donnees_capteurs.co2),0) AS co2_moy, ROUND(AVG(donnees_capteurs.temp),2) as temp_moy, ROUND(AVG(donnees_capteurs.hum),2) AS hum_moy FROM donnees_capteurs GROUP BY mois, annee;';
        $stmt = mysqli_prepare($conn,$sql);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if(mysqli_num_rows($result)>0){
            $mois = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
            while ($row = mysqli_fetch_assoc($result)){
                $moi = $mois[DateTime::createFromFormat('m', $row['mois'])->format('n')-1];
                $annee = $row['annee'];
                $nb_mesures = $row['nb_mesures'];
                $co2_moy = $row['co2_moy'];
                $temp_moy = $row['temp_moy'];
                $hum_moy = $row['hum_moy'];
                fputcsv($f, [$moi, $annee, $nb_mesures, $co2_moy, $temp_moy, $hum_moy]);
            }
        }
        fclose($f);
        die();
    }
?>