<?php
    require_once "public/commons/dbconfig.php";
    $data = exec("/usr/bin/python3 /var/www/html/sensor-all.py 2>&1");
    $co2 = explode(",", $data)[0];
    $temp = explode(",", $data)[1];
    $hum = explode(",", $data)[2];
    echo "CO2 : ".$co2." Ppm<br>";
    echo "Temperature : ".$temp." °C<br>";
    echo "Humidity : ".$hum." %<br>";
    $sql = "INSERT INTO donnees_capteurs (co2, temp, hum) VALUES (?,?,?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "idd", $co2, $temp, $hum);
    mysqli_stmt_execute($stmt);
    if(mysqli_stmt_affected_rows($stmt) == 1){
        echo "Insertion réussie";
    }
    else{
        echo "Erreur d'insertion";
    }
?>