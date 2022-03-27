<?php
    session_start();
    if(isset($_SESSION['loggedin'])&&isset($_SESSION['role'])&&$_SESSION['role']>=2&&isset($_POST['code'])){
        require("../commons/dbconfig.php");
        $condition = filter_var($_POST['code'],FILTER_SANITIZE_STRING);
        $sql = 'SELECT donnees_capteurs.id AS id_commande, donnees_capteurs.id_utilisateur, donnees_capteurs.id_menu, donnees_capteurs.id_lieu_consommation, donnees_capteurs.code_commande, transactions.id_code_reduction, donnees_capteurs.montant, donnees_capteurs.valide, DATE_FORMAT(donnees_capteurs.jour, \'%d/%m/%Y\') AS date_commande, utilisateurs.id, utilisateurs.nom_utilisateur, utilisateurs.prenom_utilisateur, utilisateurs.type_utilisateur, utilisateurs.description, utilisateurs.telephone, menus.id, menus.designation, menus.allergenes, menus.photo, lieux_de_consommation.id AS id_lieu, lieux_de_consommation.designation AS designation_lieu, lieux_de_consommation.adresse, lieux_de_consommation.lien_gmaps, codes_reductions.id AS id_code_reduction, codes_reductions.code_reduction FROM donnees_capteurs INNER JOIN transactions INNER JOIN utilisateurs INNER JOIN menus LEFT JOIN codes_reductions ON transactions.id_code_reduction = codes_reductions.id INNER JOIN lieux_de_consommation WHERE donnees_capteurs.id_utilisateur = utilisateurs.id AND donnees_capteurs.id_menu = menus.id AND donnees_capteurs.id_lieu_consommation = lieux_de_consommation.id AND transactions.id = donnees_capteurs.id_transaction AND donnees_capteurs.code_commande LIKE ? ORDER BY donnees_capteurs.id DESC LIMIT 1000;';
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
                    'id_commande' => $row["id_commande"],
                    'id_utilisateur' => $row["id_utilisateur"],
                    'type_utilisateur' => $row["type_utilisateur"],
                    'description' => $row["description"],
                    'nom_utilisateur' => $row["nom_utilisateur"],
                    'prenom_utilisateur' => $row["prenom_utilisateur"],
                    'telephone' => $row["telephone"],
                    'id_menu' => $row["id_menu"],
                    'designation' => $row["designation"],
                    'allergenes' => $row["allergenes"],
                    'photo' => $row["photo"],
                    'id_lieu_consommation' => $row["id_lieu_consommation"],
                    'designation_lieu' => $row["designation_lieu"],
                    'adresse' => $row["adresse"],
                    'lien_gmaps' => $row["lien_gmaps"],
                    'id_code_reduction' => $row["id_code_reduction"],
                    'code_reduction' => $row["code_reduction"],
                    'code_commande' => $row["code_commande"],
                    'montant' => $row["montant"],
                    'valide' => $row["valide"],
                    'date_commande' => $row["date_commande"]
                );
            }
        }
        $response = json_encode($list);
        header('Content-Length: '.strlen($response));
        header('Content-type: application/json');
        echo $response;
    }
?>