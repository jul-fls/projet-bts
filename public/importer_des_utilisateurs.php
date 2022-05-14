<?php
    $title = "Importer des utilisateurs"; // On définit le titre de la page
    require_once "commons/header.php"; // On inclut le header
    require "commons/dbconfig.php"; // On inclut le fichier de configuration de la base de données
    if(!isset($_SESSION['loggedin'])){ // Si la variable "loggedin" n'existe pas dans la session
        echo '<h1><img src="resources/danger.png" width="5%" height="auto" />Vous n\'etes pas connecté, merci de vous connecter à votre compte !</h1>'; // On affiche un message d'erreur
        require_once "commons/footer.php"; // On inclut le footer
        die(); // On arrete tout
    }
    else if($_SESSION['role']<2){ // Si le rôle de la personne n'est pas super administrateur
        echo '<h1><img src="resources/danger.png" width="5%" height="auto" />Vous n\'avez pas l\'autorisation d\'accéder à cette page !</h1>'; // On affiche un message d'erreur
        require_once "commons/footer.php"; // On inclut le footer
        die(); // On arrete tout
    }
    if($_SERVER['REQUEST_METHOD']=='POST'){ // Si la requête est de type POST
        //traitement du formulaire
        if(isset($_FILES['file'])&&$_POST['randcheck']==$_SESSION['rand']){ //<========= version définitive, permet d'éviter les multiples envois du même formulaire (refresh par exemple) par session
            $uploadfile = $__PATH__.'office_export.csv'; // On définit le chemin du fichier à uploader
            $csvMimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain'); // On définit les types de fichiers autorisés
            $finfo = finfo_open(FILEINFO_MIME_TYPE); // On ouvre le fichier
            $mime = finfo_file($finfo, $_FILES['file']['tmp_name']); // On récupère le type du fichier
            if(in_array($mime, $csvMimes) === true){ // Si le type du fichier est autorisé
                //c'est un vrai csv
                if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) { //si le fichier peut bien être déplacé
                    $inputCSV = 'office_export.csv'; // On définit le nom du fichier à importer
                    $inputHandle = fopen($inputCSV, 'r'); // On ouvre le fichier
                    $service_column; // On définit la colonne de la table qui contient le service
                    $prenom_column; // On définit la colonne de la table qui contient le prénom
                    $nom_column; // On définit la colonne de la table qui contient le nom
                    $titre_column; // On définit la colonne de la table qui contient le titre
                    $email_column; // On définit la colonne de la table qui contient l'email
                    $telephone_column; // On définit la colonne de la table qui contient le téléphone
                    $row=0; // On définit la ligne du fichier
                    $column=0; // On définit la colonne du fichier
                    $fails = 0; // On définit le nombre d'erreurs
                    $success = 0; // On définit le nombre d'utilisateurs insérés
                    while (false !== ($data = fgetcsv($inputHandle))){ // On boucle tant que le fichier n'est pas fini
                        if(!$row==0){ // Si la ligne n'est pas la première
                            if(!strpos($data[$email_column],'onmicrosoft.com')&&!empty($data[$service_column])&&($data[$titre_column]=="ELEVE"||$data[$titre_column]=="PROFESSEUR"||$data[$titre_column]=="FORMATEUR"||$data[$titre_column]=="PERSONNEL")){ // Si le service est renseigné et que le titre est correct
                                $sql = 'INSERT IGNORE INTO utilisateurs (utilisateurs.nom_utilisateur, utilisateurs.prenom_utilisateur, utilisateurs.type_utilisateur, utilisateurs.role, utilisateurs.description, utilisateurs.login, utilisateurs.email, utilisateurs.telephone) VALUES (?, ?, ?, ?, ?, ?, ?, ?);'; // On définit la requête SQL
                                $stmt = mysqli_prepare($conn,$sql); // On prépare la requête
                                $nom = mb_strtoupper(str_replace('_',' ',str_replace('�','é',$data[$nom_column])),'UTF-8'); // On définit le nom
                                $prenom = ucfirst(str_replace('_',' ',str_replace('�','é',$data[$prenom_column]))); // On définit le prénom
                                switch($data[$titre_column]){ // On définit le type
                                    case "ELEVE": // Si c'est un élève
                                        $titre = 0; // On définit le titre
                                        break; // On sort de la boucle
                                    case "PROFESSEUR": // Si c'est un professeur
                                        $titre = 1; // On définit le titre
                                        break; // On sort de la boucle
                                    case "FORMATEUR": // Si c'est un formateur
                                        $titre = 2; // On définit le titre
                                        break; // On sort de la boucle
                                    case "PERSONNEL": // Si c'est un personnel
                                        $titre = 3; // On définit le titre
                                        break; // On sort de la boucle
                                } // On sort de la boucle
                                $role = 0; // On définit le rôle
                                $service = $data[$service_column]; // On définit le service
                                $login = strtolower(str_replace('@'.$__MAIL_DOMAIN__,'',$data[$email_column])); // On définit le login
                                $email = strtolower($data[$email_column]); // On définit l'email
                                $telephone = $data[$telephone_column]; // On définit le téléphone
                                $telephone = preg_replace("/[^0-9]/", "",$telephone); // On supprime tout ce qui n'est pas un chiffre
                                $telephone = substr($telephone, -9); // On récupère les 9 derniers chiffres
                                $telephone = '0'.$telephone; // On ajoute le 0 devant
                                mysqli_stmt_bind_param($stmt, 'ssiissss',$nom,$prenom,$titre,$role,$service,$login,$email,$telephone); // On lie les paramètres
                                $status = mysqli_stmt_execute($stmt); // On exécute la requête
                                $result = mysqli_stmt_get_result($stmt); // On récupère le résultat
                                if($conn->affected_rows==0){ // Si la requête n'a pas abouti
                                    $fails++; // On incrémente le nombre d'erreurs
                                }else{ // Sinon
                                    $success++; // On incrémente le nombre d'utilisateurs insérés
                                }
                            }
                        }else{ // Sinon
                            foreach($data as $header){ // On boucle sur les données
                                if($header=="Service"){ // Si c'est la colonne du service
                                    $service_column = $column; // On définit la colonne du service
                                }else if($header=="Prénom"){ // Si c'est la colonne du prénom
                                    $prenom_column = $column; // On définit la colonne du prénom
                                }else if($header=="Nom"){ // Si c'est la colonne du nom
                                    $nom_column = $column; // On définit la colonne du nom
                                }else if($header=="Titre"){ // Si c'est la colonne du titre
                                    $titre_column = $column; // On définit la colonne du titre
                                }else if($header=="Nom d’utilisateur principal"){ // Si c'est la colonne du nom d'utilisateur principal
                                    $email_column = $column; // On définit la colonne du nom d'utilisateur principal
                                }else if($header=="Téléphone mobile"){ // Si c'est la colonne du téléphone mobile
                                    $telephone_column = $column; // On définit la colonne du téléphone mobile
                                }else{} // Sinon
                                $column++; // On incrémente la colonne
                            }
                        }
                        $row++; // On incrémente la ligne
                    }
                    fclose($inputHandle); // On ferme le fichier
                    if($success>0){ // Si au moins un utilisateur a été inséré
                        echo '<h1><img src="resources/good.png" width="5%" height="auto"/> Bravo, les '.$success.' utilisateurs ont bien été ajoutés !</h1>'; // On affiche un message de succès
                        if($fails>=2){ // Si au moins deux utilisateurs n'ont pas pu être insérés
                            echo '<h2>Cependant '.$fails.' utilisateurs n\'ont pas pu être ajoutés car ils existaient déjà</h2>'; // On affiche un message d'erreur
                        }else if($fails==1){ // Si un utilisateur n'a pas pu être inséré
                            echo '<h2>Cependant 1 utilisateur n\'as pas pu être ajouté car il existait déjà</h2>'; // On affiche un message d'erreur
                        }else{} //sinon c'est qu'aucun utilisateur n'as pas pu être ajouté
                    }else{ // Sinon
                        echo '<h1><img src="resources/bad.png" width="5%" height="auto"/> Aucun utilisateur n\'as pu être ajouté !</h1>'; // On affiche un message d'erreur
                        echo mysqli_error($conn); // On affiche l'erreur
                    }
                }else{ // Sinon
                    echo '<h1><img src="resources/bad.png" width="5%" height="auto"/>Le fichier est valide mais n\'as pas été téléchargé avec succès, code d\'erreur: '.$_FILES["file"]["error"].'</h1>'; // On affiche un message d'erreur
                }
            }else{ // Sinon
                //c'est un faux csv
                echo '<h1><img src="resources/bad.png" width="5%" height="auto"/>Le fichier est invalide et n\'as donc pas été téléchargé avec succès.</h1>'; // On affiche un message d'erreur
            }
            require_once "commons/footer.php"; // On inclut le footer
            unlink($uploadfile); // On supprime le fichier
            die(); // On arrête le script
        }else{ // Sinon
            echo 'aucun fichier uploadé'; // On affiche un message d'erreur
        }
    }else if($_SERVER['REQUEST_METHOD']=='GET'){ // Si la requête est de type GET
        //execute le reste du code de la page
    }else{ // Sinon
        //ne rien faire, méthode invalide
        die(); // On arrête le script
    }
?>
<br/> <!-- On saute une ligne -->
<h2>Merci de sélectionner dans le formulaire suivant le fichier téléchargé à la suite de l'export des utilisateurs depuis le centre d'administration Office365</h2> <!-- On affiche un message d'information -->
<form action="<?=htmlspecialchars($_SERVER['PHP_SELF'])?>" enctype="multipart/form-data" method="POST"> <!-- On crée un formulaire -->
    <?php 
        $rand=rand(); // On génère un nombre aléatoire
        $_SESSION['rand']=$rand; // On enregistre le nombre aléatoire dans la session
    ?>
    <input type="hidden" value="<?=$rand?>" name="randcheck" /> <!-- On crée un champ caché pour vérifier que le formulaire a été envoyé -->
    <input type="file" id="file-input" name="file" accept=".csv, text/x-comma-separated-values, text/comma-separated-values, application/octet-stream, application/vnd.ms-excel, application/x-csv, text/x-csv, text/csv, application/csv, application/excel, application/vnd.msexcel, text/plain"> <!-- On crée un champ de type fichier pour sélectionner le fichier -->
    <br/><br/> <!-- On saute 2 lignes -->
    <button type="submit" class="w3-button w3-light-green full" id="btnsubmit">Ajouter les utilisateurs dans la base de données</button> <!-- On crée un bouton pour envoyer le formulaire -->
</form> <!-- On ferme le formulaire -->
<p>
    <i class="fa fa-2x fa-info-circle"></i> <!-- On affiche une icône d'information -->
    Pour importer des utilisateurs, il faut tous d'abord être certains que tous les utilisateurs possèdent tous : 
</p> 
<ul> <!-- On crée une liste -->
    <li>Un Service (correspond à la description) (les utilisateurs sans service seront ignorés) 
        <ul> 
            <li>Pour un élève, le service doit comporter le code classe (ex: BTSSN2UFA, TS1, etc...)</li>
            <li>Pour un professeur, le service doit comporter le code matière (ex: L0100 - PHILOSOPHIE, etc...) ou le nom de la matière si celle ci n'as pas de code</li>
            <li>Pour un formateur, le service doit comporter le code matière (ex: L0422 - ANGLAIS, etc...) ou le nom de la matière si celle ci n'as pas de code</li>
            <li>Pour un personnel, le service doit comporter la fonction (ex: Equipe Informatique, Administratif, Entretien, etc...)</li>
        </ul>
    </li>
    <li>Un Poste (correspond au type d'utilisateur) (les utilisateurs avec un poste autre que ces 4 seront ignorés)
        <ul>
            <li>Pour un élève, le poste doit etre égal à "ELEVE")</li>
            <li>Pour un professeur, le poste doit etre égal à "PROFESSEUR"</li>
            <li>Pour un formateur, le poste doit etre égal à "FORMATEUR"</li>
            <li>Pour un personnel, le poste doit etre égal à "PERSONNEL"</li>
        </ul>
    </li>
    <li>Un Nom</li>
    <li>Un Prénom</li>
</ul> <!-- On ferme la liste -->
<p>Ensuite, il faut exporter les utilisateurs depuis le centre d'administration Office 365, voici la procédure en 5 étapes :</p> <!-- On affiche un message d'information -->
<p><img src="resources/export_office_users/etape_1.png" style="height: auto; width: 600px;"></img></p>
<p><img src="resources/export_office_users/etape_2.png" style="height: auto; width: 600px;"></img></p>
<p><img src="resources/export_office_users/etape_3.png" style="height: auto; width: 600px;"></img></p>
<p><img src="resources/export_office_users/etape_4.png" style="height: auto; width: 600px;"></img></p>
<p><img src="resources/export_office_users/etape_5.png" style="height: auto; width: 600px;"></img></p>
<script >
/**SYSTEME DE VERIFICATIION DU FICHIER POUR IMPORT UTILISATEURS*/
    let input = document.getElementById('file-input'); // On récupère le champ de type fichier
    let file; // On déclare une variable qui contiendra le fichier
    input.addEventListener("change", function(){ // On ajoute un écouteur d'évènement sur le champ de type fichier
        file = this.files[0]; // On récupère le fichier
        checkFile(file); // On appelle la fonction qui vérifie le fichier
    });
    function checkFile(file){ // Fonction qui vérifie le fichier
        let fileType = file.type; // On récupère le type du fichier
        let validExtensions = ["application/vnd.ms-excel", "text/csv", "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"]; // On déclare une variable qui contiendra les extensions valides
        if(validExtensions.includes(fileType)){ // Si le type de fichier est valide
        }else{ // Sinon
            alert('Fichier invalide, merci de choisir un autre fichier !'); // On affiche un message d'erreur
            input.value = ''; // On vide le champ de type fichier
        }
    }
/**SYSTEME DE VERIFICATIION DU FICHIER POUR IMPORT UTILISATEURS*/
</script> <!-- On ferme le script -->
<?php require_once "commons/footer.php";?> <!-- On inclut le footer -->
