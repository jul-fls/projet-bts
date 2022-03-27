<?php
    session_start();
    if(isset($_SESSION['loggedin'])&&isset($_SESSION['role'])&&$_SESSION['role']>=0){
        require("../commons/dbconfig.php");
        $sql = 'SELECT DISTINCT menus.id, menus.disponible, menus.quantite_max, menus.jours_dispo, menus.designation, menus.prix, menus.allergenes, menus.photo FROM menus LEFT JOIN donnees_capteurs ON menus.id = donnees_capteurs.id_menu WHERE menus.disponible = 1 ORDER BY menus.id LIMIT 9999;';
        $stmt = mysqli_prepare($conn,$sql);
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
                    'designation' => $row["designation"],
                    'allergenes' => $row["allergenes"],
                    'photo' => $row["photo"],
                    'prix' => $row["prix"]
                );
            }
        }
        $response = json_encode($list);
        header('Content-Length: '.strlen($response));
        header('Content-type: application/json');
        echo $response;
    }
?>