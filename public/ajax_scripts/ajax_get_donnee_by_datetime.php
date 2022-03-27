<?php
    session_start();
    if(isset($_SESSION['loggedin'])&&isset($_SESSION['role'])&&$_SESSION['role']>=0&&isset($_POST['datetime'])){
        require("../commons/dbconfig.php");
        $datetime = filter_var($_POST['datetime'],FILTER_SANITIZE_STRING);
        $sql = 'SELECT donnees_capteurs.id AS id_donnee, DATE_FORMAT(donnees_capteurs.timestamp, \'%d/%m/%Y %H:%i:%S\') AS timestamp, donnees_capteurs.co2, donnees_capteurs.temp, donnees_capteurs.hum FROM donnees_capteurs WHERE donnees_capteurs.timestamp LIKE ? ORDER BY donnees_capteurs.timestamp DESC;';
        $stmt = mysqli_prepare($conn,$sql);
        $timestamp = DateTime::createFromFormat('Y-m-d\TG:i', $datetime);
        $datetime2 = $timestamp->format('Y-m-d H:i').'%';
        mysqli_stmt_bind_param($stmt, 's',$datetime2);
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