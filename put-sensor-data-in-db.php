<?php
    require_once "public/commons/dbconfig.php"; //Inclusion de la configuration de la base de données
    $data = exec("/usr/bin/python3 /var/www/html/sensor-all.py 2>&1"); //Exécution du script python
    $co2 = explode(",", $data)[0]; //Extraction de la valeur de CO2
    $temp = explode(",", $data)[1]; //Extraction de la valeur de température
    $hum = explode(",", $data)[2]; //Extraction de la valeur d'humidité
    echo "CO2 : ".$co2." Ppm<br>"; //Affichage de la valeur de CO2
    echo "Temperature : ".$temp." °C<br>"; //Affichage de la valeur de température
    echo "Humidity : ".$hum." %<br>"; //Affichage de la valeur d'humidité
    $sql = "INSERT INTO donnees_capteurs (co2, temp, hum) VALUES (?,?,?)"; //Requête SQL
    $stmt = mysqli_prepare($conn, $sql); //Préparation de la requête
    mysqli_stmt_bind_param($stmt, "idd", $co2, $temp, $hum); //Définition des paramètres de la requête
    mysqli_stmt_execute($stmt); //Exécution de la requête
    if(mysqli_stmt_affected_rows($stmt) == 1){ //Si la requête a été exécutée
        echo "Insertion réussie"; //Affichage d'un message de succès
    } 
    else{ //Sinon
        echo "Erreur d'insertion"; //Affichage d'un message d'erreur
    }
?>