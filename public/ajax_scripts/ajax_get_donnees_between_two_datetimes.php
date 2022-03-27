<?php
    session_start();
    // require_once "../../show_errors.php";
    if(isset($_SESSION['loggedin'])&&isset($_SESSION['role'])&&$_SESSION['role']>=0&&isset($_POST['datetime1'])&&isset($_POST['datetime2'])){
        require("../commons/dbconfig.php");
        $datetime1 = filter_var($_POST['datetime1'],FILTER_SANITIZE_STRING);
        $datetime2 = filter_var($_POST['datetime2'],FILTER_SANITIZE_STRING);
        $sql = 'SELECT donnees_capteurs.id AS id_donnee, DATE_FORMAT(donnees_capteurs.timestamp, \'%d/%m/%Y %H:%i:%S\') AS timestamp, donnees_capteurs.co2, donnees_capteurs.temp, donnees_capteurs.hum FROM donnees_capteurs WHERE donnees_capteurs.timestamp >= ? AND donnees_capteurs.timestamp <= ? ORDER BY donnees_capteurs.timestamp ASC;';
        $stmt = mysqli_prepare($conn,$sql);
        $timestamp1 = DateTime::createFromFormat('Y-m-d\TG:i', $datetime1);
        $datetime1_formatted = $timestamp1->format('Y-m-d H:i');
        $timestamp2 = DateTime::createFromFormat('Y-m-d\TG:i', $datetime2);
        $datetime2_formatted = $timestamp2->format('Y-m-d H:i');
        mysqli_stmt_bind_param($stmt, 'ss',$datetime1_formatted,$datetime2_formatted);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if (!$result) {
            echo "Problème de requete"."<br/>";
            echo $conn->error;
            return json_encode(array());
            die();
        }
        $list = array();
        if(mysqli_num_rows($result)>0){
            while ($row = mysqli_fetch_assoc($result)){
                $list[] = array(
                    'id_donnee' => $row["id_donnee"],
                    'timestamp' => $row["timestamp"],
                    'co2' => $row["co2"],
                    'temperature' => $row["temp"],
                    'humidite' => $row["hum"]
                );
            }
        }
        $response = json_encode($list);
        header('Content-Length: '.strlen($response));
        header('Content-type: application/json');
        echo $response;
    }
?>