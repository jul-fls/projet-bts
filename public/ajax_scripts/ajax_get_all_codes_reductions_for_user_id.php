<?php
    session_start();
    if(isset($_SESSION['loggedin'])&&isset($_SESSION['role'])&&$_SESSION['role']>=1){
        require("../commons/dbconfig.php");
        $condition = $_SESSION['id'];
        $sql = 'SELECT codes_reductions.code_reduction FROM codes_reductions LEFT JOIN transactions ON codes_reductions.id = transactions.id_code_reduction WHERE codes_reductions.utilisations_max > (SELECT COUNT(transactions.id) FROM transactions WHERE transactions.status = "succeeded" AND codes_reductions.id = transactions.id_code_reduction) AND codes_reductions.code_reduction LIKE ?;';
        $stmt = mysqli_prepare($conn,$sql);
        $searchstring = 'CRA-'.$condition.'-%';
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
                    'code_reduction' => $row['code_reduction']
                );
            }
        }
        $response = json_encode($list);
        header('Content-Length: '.strlen($response));
        header('Content-type: application/json');
        echo $response;
    }
?>