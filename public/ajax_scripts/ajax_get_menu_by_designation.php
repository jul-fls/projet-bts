<?php
    session_start();
    if(isset($_SESSION['loggedin'])&&isset($_SESSION['role'])&&$_SESSION['role']>=1&&isset($_POST['designation'])){
        require("../commons/dbconfig.php");
        $condition = filter_var($_POST['designation'],FILTER_SANITIZE_STRING);
        $sql = 'SELECT menus.id, menus.disponible, menus.quantite_max, menus.jours_dispo, menus.designation, menus.prix, menus.allergenes, menus.photo FROM menus WHERE menus.designation LIKE ? ORDER BY menus.id LIMIT 20;';
        $stmt = mysqli_prepare($conn,$sql);
        $searchstring = '%'.$condition.'%';
        mysqli_stmt_bind_param($stmt, 's',$searchstring);
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
                    'disponible' => $row['disponible'],
                    'quantite_max' => $row['quantite_max'],
                    'jours_dispo' => $row['jours_dispo'],
                    'designation' => $row["designation"],
                    'prix' => $row['prix'],
                    'allergenes' => $row["allergenes"],
                    'photo' => $row["photo"]
                );
            }
        }
        $response = json_encode($list);
        header('Content-Length: '.strlen($response));
        header('Content-type: application/json');
        echo $response;
    }
?>