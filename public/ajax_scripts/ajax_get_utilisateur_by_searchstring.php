<?php
    session_start();
    if(isset($_SESSION['loggedin'])&&isset($_POST['query'])&&isset($_SESSION['role'])&&$_SESSION['role']>=1){ //Si l'utilisateur est connecté
        require("../commons/dbconfig.php"); //Connexion à la base de données
        $condition = filter_var($_POST['query'],FILTER_SANITIZE_STRING); //Récupération de la condition
        $sql = 'SELECT utilisateurs.id, utilisateurs.nom_utilisateur, utilisateurs.prenom_utilisateur, utilisateurs.type_utilisateur, utilisateurs.role, utilisateurs.description, utilisateurs.login, utilisateurs.email, utilisateurs.telephone, CASE WHEN utilisateurs.password_hash IS NOT NULL THEN 1 ELSE 0 END AS isactive FROM utilisateurs WHERE utilisateurs.nom_utilisateur LIKE ? OR utilisateurs.prenom_utilisateur LIKE ? OR utilisateurs.description LIKE ? ORDER BY utilisateurs.nom_utilisateur LIMIT 20;'; //Requête SQL
        $stmt = mysqli_prepare($conn,$sql); //Préparation de la requête
        $searchstring = '%'.$condition.'%'; //Création de la chaîne de caractères à rechercher
        mysqli_stmt_bind_param($stmt, 'sss',$searchstring,$searchstring,$searchstring); //Paramètrage de la requête
        $status = mysqli_stmt_execute($stmt); //Exécution de la requête
        $result = mysqli_stmt_get_result($stmt); //Récupération du résultat de la requête
        if (!$result) { //Si la requête ne s'est pas exécutée correctement
            echo "Problème de requete"."<br/>"; //Affichage d'un message d'erreur
            echo $conn->error; //Affichage de l'erreur
            return json_encode(array()); //Retourne un tableau vide
            die(); //Arrêt du script
        }
        $list = array(); //Création d'un tableau
        if(mysqli_num_rows($result)>0){ //Si il y a des données
            while ($row = mysqli_fetch_assoc($result)){ //Tant qu'il y a des données
                $row["type_utilisateur_str"]; //Définition du type de l'utilisateur
                switch($row["type_utilisateur"]){ //Switch sur le type de l'utilisateur
                    case 0: //Si c'est un eleve
                        $row["type_utilisateur_str"] = "Élève"; //Affectation du type de l'utilisateur
                        break; //Sortie du switch
                    case 1: //Si c'est un professeur
                        $row["type_utilisateur_str"] = "Professeur"; //Affectation du type de l'utilisateur
                        break; //Sortie du switch
                    case 2: //Si c'est un formateur
                        $row["type_utilisateur_str"] = "Formateur"; //Affectation du type de l'utilisateur
                        break; //Sortie du switch
                    case 3: //Si c'est un personnel
                        $row["type_utilisateur_str" ]= "Personnel"; //Affectation du type de l'utilisateur
                        break; //Sortie du switch
                    case 4: //Si c'est un autre
                        $row["type_utilisateur_str" ]= "Autres"; //Affectation du type de l'utilisateur
                        break; //Sortie du switch
                    default: //Si c'est un utilisateur inconnu
                        $row["type_utilisateur_str"] = "Autres"; //Affectation du type de l'utilisateur
                        break; //Sortie du switch
                }
                $list[] = array( //Ajout d'un élément dans le tableau
                    'id' => $row["id"], //Ajout de l'id de l'utilisateur
                    'type_utilisateur_str' => $row["type_utilisateur_str"], //Ajout du type de l'utilisateur
                    'description' => $row["description"], //Ajout de la description de l'utilisateur
                    'nom_utilisateur' => $row["nom_utilisateur"], //Ajout du nom de l'utilisateur
                    'prenom_utilisateur' => $row["prenom_utilisateur"], //Ajout du prénom de l'utilisateur
                    'prenom_utilisateur' => $row["prenom_utilisateur"], //Ajout du prénom de l'utilisateur
                    'login' => $row["login"], //Ajout du login de l'utilisateur
                    'role' => $row["role"], //Ajout du role de l'utilisateur
                    'email' => $row["email"], //Ajout de l'email de l'utilisateur
                    'telephone' => $row["telephone"], //Ajout du téléphone de l'utilisateur
                    'isactive' => $row["isactive"] //Ajout de l'état de l'utilisateur
                );
            }
        }
        $response = json_encode($list); //Conversion du tableau en chaîne de caractères
        header('Content-Length: '.strlen($response)); //Définition de la longueur de la réponse
        header('Content-type: application/json'); //Définition du type de la réponse
        echo $response; //Affichage de la réponse
    }
?>