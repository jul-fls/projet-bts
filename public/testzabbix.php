<?php
    require_once "commons/dbconfig.php";
    $sql0 = "SELECT * FROM donnees_capteurs ORDER BY id DESC LIMIT 1;";
    $stmt0 = mysqli_prepare($conn, $sql0);
    mysqli_stmt_execute($stmt0);
    $result0 = mysqli_stmt_get_result($stmt0);
    if(mysqli_num_rows($result0)>0){
        while ($rowA = mysqli_fetch_assoc($result0)){
            $co2 = $rowA['co2'];
            $temp = $rowA['temp'];
            $hum = $rowA['hum'];
        }
        echo "CO2: ".$co2."<br>";
        echo "Temp: ".$temp."<br>";
        echo "Hum: ".$hum."<br>";
    }
?>