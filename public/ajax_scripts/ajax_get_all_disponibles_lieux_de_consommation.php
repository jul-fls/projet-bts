<?php
    session_start();
    if(isset($_SESSION['loggedin'])&&isset($_SESSION['role'])&&$_SESSION['role']>=0&&isset($_POST['date'])){
        require("../commons/dbconfig.php");
        $sanitized_date = filter_var($_POST['date'],FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $sql = 'SELECT DISTINCT lieux_de_consommation.id, lieux_de_consommation.disponible, lieux_de_consommation.designation, lieux_de_consommation.adresse, lieux_de_consommation.lien_gmaps, lieux_de_consommation.jauge, lieux_de_consommation.difference_prix FROM lieux_de_consommation LEFT JOIN donnees_capteurs ON lieux_de_consommation.id = donnees_capteurs.id_lieu_consommation WHERE lieux_de_consommation.disponible = 1 AND lieux_de_consommation.jauge > (SELECT COUNT(*) FROM donnees_capteurs WHERE donnees_capteurs.valide = 1 AND donnees_capteurs.jour = ?) ORDER BY lieux_de_consommation.id LIMIT 1000;';
        $stmt = mysqli_prepare($conn,$sql);
        $date = Datetime::createFromFormat('d/m/Y', $sanitized_date);
        $condition = $date->format('Y-m-d');
        mysqli_stmt_bind_param($stmt, 's',$condition);
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
                    'id' => $row["id"],
                    'disponible' => $row["disponible"],
                    'designation' => $row["designation"],
                    'adresse' => $row["adresse"],
                    'lien_gmaps' => $row["lien_gmaps"],
                    'jauge' => $row["jauge"],
                    'difference_prix' => $row["difference_prix"]
                );
            }
        }
        $response = json_encode($list);
        header('Content-Length: '.strlen($response));
        header('Content-type: application/json');
        echo $response;
    }
?>