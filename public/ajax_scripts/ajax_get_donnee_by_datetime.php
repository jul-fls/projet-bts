<?php
    session_start(); //Démarrage de la session
    if(isset($_SESSION['loggedin'])&&isset($_SESSION['role'])&&$_SESSION['role']>=0&&isset($_POST['datetime'])){ //Si l'utilisateur est connecté
        require("../commons/dbconfig.php"); //Connexion à la base de données
        $datetime = filter_var($_POST['datetime'],FILTER_SANITIZE_STRING); //Récupération de la date et de l'heure
        $sql = 'SELECT donnees_capteurs.id AS id_donnee, DATE_FORMAT(donnees_capteurs.timestamp, \'%d/%m/%Y %H:%i:%S\') AS timestamp, donnees_capteurs.co2, donnees_capteurs.temp, donnees_capteurs.hum FROM donnees_capteurs WHERE donnees_capteurs.timestamp LIKE ? ORDER BY donnees_capteurs.timestamp DESC;'; //Requête SQL
        $stmt = mysqli_prepare($conn,$sql); //Préparation de la requête
        $timestamp = DateTime::createFromFormat('Y-m-d\TG:i', $datetime); //Création de la date et heure
        $datetime2 = $timestamp->format('Y-m-d H:i').'%'; //Formatage de la date et heure
        mysqli_stmt_bind_param($stmt, 's',$datetime2); //Paramètrage de la requête
        mysqli_stmt_execute($stmt); //Exécution de la requête
        $result = mysqli_stmt_get_result($stmt); //Récupération du résultat de la requête
        if (!$result) { //Si la requête ne s'est pas exécutée correctement
            echo "Problème de requete"."<br/>"; //Affichage d'un message d'erreur
            echo $conn->error; //Affichage de l'erreur
            return json_encode(array()); //Retourne un tableau vide
            die(); //Arrêt du script
        }
        $list = array(); //Création d'un tableau
        if(mysqli_num_rows($result)>0){ //Si il y a des données
            while ($row = mysqli_fetch_assoc($result)){ //Pour chaque ligne
                $list[] = array( //Ajout d'une ligne dans le tableau
                    'id_donnee' => $row["id_donnee"], //Ajout de l'id de la donnée
                    'timestamp' => $row["timestamp"], //Ajout de la date et heure
                    'co2' => $row["co2"], //Ajout de la concentration de CO2
                    'temperature' => $row["temp"], //Ajout de la température
                    'humidite' => $row["hum"] //Ajout de l'humidité
                );
            }
        }
        $response = json_encode($list); //Conversion du tableau en JSON
        header('Content-Length: '.strlen($response)); //Définition de la taille de la réponse
        header('Content-type: application/json'); //Définition du type de la réponse
        echo $response; //Affichage de la réponse
    }
?>