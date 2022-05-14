<?php
    session_start(); //Démarrage de la session
    if(isset($_SESSION['loggedin'])&&isset($_SESSION['role'])&&$_SESSION['role']>=0&&isset($_POST['datetime1'])&&isset($_POST['datetime2'])){ //Si l'utilisateur est connecté
        require("../commons/dbconfig.php"); //Connexion à la base de données
        $datetime1 = filter_var($_POST['datetime1'],FILTER_SANITIZE_STRING); //Récupération de la date et de l'heure
        $datetime2 = filter_var($_POST['datetime2'],FILTER_SANITIZE_STRING); //Récupération de la date et de l'heure
        $sql = 'SELECT donnees_capteurs.id AS id_donnee, DATE_FORMAT(donnees_capteurs.timestamp, \'%d/%m/%Y %H:%i:%S\') AS timestamp, donnees_capteurs.co2, donnees_capteurs.temp, donnees_capteurs.hum FROM donnees_capteurs WHERE donnees_capteurs.timestamp >= ? AND donnees_capteurs.timestamp <= ? ORDER BY donnees_capteurs.timestamp ASC;'; //Requête SQL
        $stmt = mysqli_prepare($conn,$sql); //Préparation de la requête
        $timestamp1 = DateTime::createFromFormat('Y-m-d\TG:i', $datetime1); //Création de la date et heure
        $datetime1_formatted = $timestamp1->format('Y-m-d H:i'); //Formatage de la date et heure
        $timestamp2 = DateTime::createFromFormat('Y-m-d\TG:i', $datetime2); //Création de la date et heure
        $datetime2_formatted = $timestamp2->format('Y-m-d H:i'); //Formatage de la date et heure
        mysqli_stmt_bind_param($stmt, 'ss',$datetime1_formatted,$datetime2_formatted); //Paramètrage de la requête
        mysqli_stmt_execute($stmt); //Exécution de la requête
        $result = mysqli_stmt_get_result($stmt); //Récupération du résultat de la requête
        if (!$result) { //Si la requête ne s'est pas exécutée correctement
            echo "Problème de requete"."<br/>"; //Affichage d'un message d'erreur
            echo $conn->error; //Affichage de l'erreur
            return json_encode(array()); //Retourne un tableau vide
            die(); //Arrêt du script
        } 
        $list = array(); //Création d'un tableau
        if(mysqli_num_rows($result)>0){ //Si le nombre de lignes renvoyées par la requête est supérieur à 0
            while ($row = mysqli_fetch_assoc($result)){ //Tant qu'il y a des lignes dans le résultat de la requête
                $list[] = array( //Ajout d'un élément dans le tableau
                    'id_donnee' => $row["id_donnee"], //Ajout de l'id de la donnée
                    'timestamp' => $row["timestamp"], //Ajout de la date et heure
                    'co2' => $row["co2"], //Ajout de la concentration de CO2
                    'temperature' => $row["temp"], //Ajout de la température
                    'humidite' => $row["hum"] //Ajout de l'humidité
                );
            }
        }
        $response = json_encode($list); //Conversion du tableau en JSON
        header('Content-Length: '.strlen($response)); //Définition de la longueur de la réponse
        header('Content-type: application/json'); //Définition du type de la réponse
        echo $response; //Affichage de la réponse
    }
?>