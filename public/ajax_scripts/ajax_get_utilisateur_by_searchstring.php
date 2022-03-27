<?php
    session_start();
    if(isset($_SESSION['loggedin'])&&isset($_POST['query'])&&isset($_SESSION['role'])&&$_SESSION['role']>=1){
        require("../commons/dbconfig.php");
        $condition = filter_var($_POST['query'],FILTER_SANITIZE_STRING);
        $sql = 'SELECT utilisateurs.id, utilisateurs.nom_utilisateur, utilisateurs.prenom_utilisateur, utilisateurs.type_utilisateur, utilisateurs.role, utilisateurs.description, utilisateurs.login, utilisateurs.email, utilisateurs.telephone, CASE WHEN utilisateurs.password_hash IS NOT NULL THEN 1 ELSE 0 END AS isactive FROM utilisateurs WHERE utilisateurs.nom_utilisateur LIKE ? OR utilisateurs.prenom_utilisateur LIKE ? OR utilisateurs.description LIKE ? ORDER BY utilisateurs.nom_utilisateur LIMIT 20;';
        $stmt = mysqli_prepare($conn,$sql);
        $searchstring = '%'.$condition.'%';
        mysqli_stmt_bind_param($stmt, 'sss',$searchstring,$searchstring,$searchstring);
        $status = mysqli_stmt_execute($stmt);
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
                $row["type_utilisateur_str"];
                switch($row["type_utilisateur"]){
                    case 0:
                        $row["type_utilisateur_str"] = "Élève";
                        break;
                    case 1:
                        $row["type_utilisateur_str"] = "Professeur";
                        break;
                    case 2:
                        $row["type_utilisateur_str"] = "Formateur";
                        break;
                    case 3:
                        $row["type_utilisateur_str" ]= "Personnel";
                        break;
                    case 4:
                        $row["type_utilisateur_str" ]= "Autres";
                        break;
                    default:
                        $row["type_utilisateur_str"] = "Autres";
                        break;
                }
                $list[] = array(
                    'id' => $row["id"],
                    'type_utilisateur_str' => $row["type_utilisateur_str"],
                    'description' => $row["description"],
                    'nom_utilisateur' => $row["nom_utilisateur"],
                    'prenom_utilisateur' => $row["prenom_utilisateur"],
                    'prenom_utilisateur' => $row["prenom_utilisateur"],
                    'login' => $row["login"],
                    'role' => $row["role"],
                    'email' => $row["email"],
                    'telephone' => $row["telephone"],
                    'isactive' => $row["isactive"]
                );
            }
        }
        $response = json_encode($list);
        header('Content-Length: '.strlen($response));
        header('Content-type: application/json');
        echo $response;
    }
?>