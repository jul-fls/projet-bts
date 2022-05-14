<?php
    session_start(); // On démarre la session AVANT toute chose
    if(!isset($_SESSION['loggedin'])){ // Si la variable "loggedin" n'existe pas dans la session
        echo '<h1><img src="resources/danger.png" width="5%" height="auto" />Vous n\'etes pas connecté, merci de vous connecter à votre compte !</h1>'; // On affiche un message d'erreur
        require_once "commons/footer.php"; // On inclut le footer
        die(); // On arrete tout
    }else if($_SESSION['role']<2){ // Si le rôle de la personne n'est pas super administrateur
        echo '<h1><img src="resources/danger.png" width="5%" height="auto" />Vous n\'avez pas l\'autorisation d\'accéder à cette page !</h1>'; // On affiche un message d'erreur
        require_once "commons/footer.php"; // On inclut le footer
        die(); // On arrete tout
    }
    require("commons/dbconfig.php"); // On inclut le fichier de configuration de la base de données
    if(isset($_GET['download'])&&$_GET['download']=='csv'){ // Si on demande le téléchargement du fichier CSV
        header('Content-type: application/csv'); // On définit le type de contenu
        header('Content-Disposition: attachment; filename=export_mesures.csv'); // On définit le nom du fichier
        header("Content-Transfer-Encoding: UTF-8"); // On définit l'encodage
        $f = fopen('php://output', 'a'); // On ouvre le fichier
        $csv = "Mois,Année,Nombre de mesures,CO² (Ppm),Température (°C),Humidité (%HR)\n"; // On définit le contenu du fichier
        fputs($f, $csv); // On écrit le contenu dans le fichier
        $sql = 'SELECT DATE_FORMAT(donnees_capteurs.timestamp,\'%m\') AS mois, DATE_FORMAT(donnees_capteurs.timestamp,\'%Y\') AS annee, COUNT(*) AS nb_mesures, ROUND(AVG(donnees_capteurs.co2),0) AS co2_moy, ROUND(AVG(donnees_capteurs.temp),2) as temp_moy, ROUND(AVG(donnees_capteurs.hum),2) AS hum_moy FROM donnees_capteurs GROUP BY mois, annee;'; // On définit la requête SQL
        $stmt = mysqli_prepare($conn,$sql); // On prépare la requête
        mysqli_stmt_execute($stmt); // On exécute la requête
        $result = mysqli_stmt_get_result($stmt); // On récupère le résultat de la requête
        if(mysqli_num_rows($result)>0){ // Si il y a des résultats
            $mois = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre']; // On définit les mois
            while ($row = mysqli_fetch_assoc($result)){ // On parcourt les résultats
                $moi = $mois[DateTime::createFromFormat('m', $row['mois'])->format('n')-1]; // On récupère le mois
                $annee = $row['annee']; // On récupère l'année
                $nb_mesures = $row['nb_mesures']; // On récupère le nombre de mesures
                $co2_moy = $row['co2_moy']; // On récupère la moyenne de CO²
                $temp_moy = $row['temp_moy']; // On récupère la moyenne de température
                $hum_moy = $row['hum_moy']; // On récupère la moyenne d'humidité
                fputcsv($f, [$moi, $annee, $nb_mesures, $co2_moy, $temp_moy, $hum_moy]); // On écrit les données dans le fichier
            }
        }
        fclose($f); // On ferme le fichier
        die(); // On arrete tout
    }
?>