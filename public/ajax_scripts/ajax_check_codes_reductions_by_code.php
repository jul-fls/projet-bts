<?php
    session_start();
    if(isset($_SESSION['loggedin'])&&isset($_SESSION['role'])&&$_SESSION['role']>=0&&isset($_POST['code_reduction'])){
        require("../commons/dbconfig.php");
        $sanitized_code = filter_var($_POST['code_reduction'],FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $sql0 = "SELECT codes_reductions.id FROM codes_reductions WHERE codes_reductions.code_reduction = ? LIMIT 1";
        $stmt0 = mysqli_prepare($conn,$sql0);
        mysqli_stmt_bind_param($stmt0,'s',$sanitized_code);
        $status0 = mysqli_stmt_execute($stmt0);
        $result0 = mysqli_stmt_get_result($stmt0);
        if(mysqli_num_rows($result0)>0){
            while($row0 = mysqli_fetch_assoc($result0)){
                $id_code_reduction = $row0['id'];
            }
        }
        $sql = 'SELECT codes_reductions.id, codes_reductions.code_reduction, codes_reductions.montant_reduction, codes_reductions.utilisations_max FROM codes_reductions LEFT JOIN transactions ON codes_reductions.id = transactions.id_code_reduction WHERE codes_reductions.utilisations_max > (SELECT COUNT(transactions.id) FROM transactions WHERE transactions.status = "succeeded" AND codes_reductions.id = transactions.id_code_reduction AND transactions.id_code_reduction = ?) AND codes_reductions.code_reduction = ? ORDER BY codes_reductions.id LIMIT 1;';
        $stmt = mysqli_prepare($conn,$sql);
        mysqli_stmt_bind_param($stmt, 'is',$id_code_reduction,$sanitized_code);
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
                    'code_reduction' => $row["code_reduction"],
                    'montant_reduction' => $row["montant_reduction"],
                    'utilisations_max' => $row["utilisations_max"]
                );
            }
        }
        $response = json_encode($list);
        header('Content-Length: '.strlen($response));
        header('Content-type: application/json');
        echo $response;
    }
?>