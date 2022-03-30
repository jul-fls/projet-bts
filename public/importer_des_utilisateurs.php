<?php
    $title = "Importer des utilisateurs";
    require_once "commons/header.php";
    require "commons/dbconfig.php";
    if(!isset($_SESSION['loggedin'])){
        echo '<h1><img src="resources/danger.png" width="5%" height="auto" />Vous n\'etes pas connecté, merci de vous connecter à votre compte !</h1>';
        require_once "commons/footer.php";
        die();
    }
    else if($_SESSION['role']<2){
        echo '<h1><img src="resources/danger.png" width="5%" height="auto" />Vous n\'avez pas l\'autorisation d\'accéder à cette page !</h1>';
        require_once "commons/footer.php";
        die();
    }
    if($_SERVER['REQUEST_METHOD']=='POST'){
        //traitement du formulaire
        if(isset($_FILES['file'])&&$_POST['randcheck']==$_SESSION['rand']){ //<========= version définitive, permet d'éviter les multiples envois du même formulaire (refresh par exemple) par session
            $uploadfile = $__PATH__.'office_export.csv';
            $csvMimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $_FILES['file']['tmp_name']);
            if(in_array($mime, $csvMimes) === true){
                //c'est un vrai csv
                if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) { //si le fichier peut bien être déplacé
                    $inputCSV = 'office_export.csv';
                    $inputHandle = fopen($inputCSV, 'r');
                    $service_column;
                    $prenom_column;
                    $nom_column;
                    $titre_column;
                    $email_column;
                    $telephone_column;
                    $row=0;
                    $column=0;
                    $fails = 0;
                    $success = 0;
                    while (false !== ($data = fgetcsv($inputHandle))){
                        if(!$row==0){
                            if(!strpos($data[$email_column],'onmicrosoft.com')&&!empty($data[$service_column])&&($data[$titre_column]=="ELEVE"||$data[$titre_column]=="PROFESSEUR"||$data[$titre_column]=="FORMATEUR"||$data[$titre_column]=="PERSONNEL")){
                                $sql = 'INSERT IGNORE INTO utilisateurs (utilisateurs.nom_utilisateur, utilisateurs.prenom_utilisateur, utilisateurs.type_utilisateur, utilisateurs.role, utilisateurs.description, utilisateurs.login, utilisateurs.email, utilisateurs.telephone) VALUES (?, ?, ?, ?, ?, ?, ?, ?);';
                                $stmt = mysqli_prepare($conn,$sql);
                                $nom = mb_strtoupper(str_replace('_',' ',str_replace('�','é',$data[$nom_column])),'UTF-8');
                                $prenom = ucfirst(str_replace('_',' ',str_replace('�','é',$data[$prenom_column])));
                                switch($data[$titre_column]){
                                    case "ELEVE":
                                        $titre = 0;
                                        break;
                                    case "PROFESSEUR":
                                        $titre = 1;
                                        break;
                                    case "FORMATEUR":
                                        $titre = 2;
                                        break;
                                    case "PERSONNEL":
                                        $titre = 3;
                                        break;
                                }
                                $role = 0;
                                $service = $data[$service_column];
                                $login = strtolower(str_replace('@'.$__MAIL_DOMAIN__,'',$data[$email_column]));
                                $email = strtolower($data[$email_column]);
                                $telephone = $data[$telephone_column];
                                $telephone = preg_replace("/[^0-9]/", "",$telephone);
                                $telephone = substr($telephone, -9);
                                $telephone = '0'.$telephone;
                                mysqli_stmt_bind_param($stmt, 'ssiissss',$nom,$prenom,$titre,$role,$service,$login,$email,$telephone);
                                $status = mysqli_stmt_execute($stmt);
                                $result = mysqli_stmt_get_result($stmt);
                                if($conn->affected_rows==0){
                                    $fails++;
                                }else{
                                    $success++;
                                }
                            }
                        }else{
                            foreach($data as $header){
                                if($header=="Service"){
                                    $service_column = $column;
                                }else if($header=="Prénom"){
                                    $prenom_column = $column;
                                }else if($header=="Nom"){
                                    $nom_column = $column;
                                }else if($header=="Titre"){
                                    $titre_column = $column;
                                }else if($header=="Nom d’utilisateur principal"){
                                    $email_column = $column;
                                }else if($header=="Téléphone mobile"){
                                    $telephone_column = $column;
                                }else{}
                                $column++;
                            }
                        }
                        $row++;
                    }
                    fclose($inputHandle);
                    if($success>0){
                        echo '<h1><img src="resources/good.png" width="5%" height="auto"/> Bravo, les '.$success.' utilisateurs ont bien été ajoutés !</h1>';
                        if($fails>=2){
                            echo '<h2>Cependant '.$fails.' utilisateurs n\'ont pas pu être ajoutés car ils existaient déjà</h2>';
                        }else if($fails==1){
                            echo '<h2>Cependant 1 utilisateur n\'as pas pu être ajouté car il existait déjà</h2>';
                        }else{} //sinon c'est qu'aucun utilisateur n'as pas pu être ajouté
                    }else{
                        echo '<h1><img src="resources/bad.png" width="5%" height="auto"/> Aucun utilisateur n\'as pu être ajouté !</h1>';
                        echo mysqli_error($conn);
                    }
                }else{
                    echo '<h1><img src="resources/bad.png" width="5%" height="auto"/>Le fichier est valide mais n\'as pas été téléchargé avec succès, code d\'erreur: '.$_FILES["file"]["error"].'</h1>';
                }
            }else{
                //c'est un faux csv
                echo '<h1><img src="resources/bad.png" width="5%" height="auto"/>Le fichier est invalide et n\'as donc pas été téléchargé avec succès.</h1>';
            }
            require_once "commons/footer.php";
            unlink($uploadfile);
            die();
        }else{
            echo 'aucun fichier uploadé';
        }
    }else if($_SERVER['REQUEST_METHOD']=='GET'){
        //execute le reste du code de la page
    }else{
        //ne rien faire, méthode invalide
        die();
    }
?>
<br/>
<h2>Merci de sélectionner dans le formulaire suivant le fichier téléchargé à la suite de l'export des utilisateurs depuis le centre d'administration Office365</h2>
<form action="<?=htmlspecialchars($_SERVER['PHP_SELF'])?>" enctype="multipart/form-data" method="POST">
    <?php
        $rand=rand();
        $_SESSION['rand']=$rand;
    ?>
    <input type="hidden" value="<?=$rand?>" name="randcheck" />
    <input type="file" id="file-input" name="file" accept=".csv, text/x-comma-separated-values, text/comma-separated-values, application/octet-stream, application/vnd.ms-excel, application/x-csv, text/x-csv, text/csv, application/csv, application/excel, application/vnd.msexcel, text/plain">
    <br/><br/>
    <button type="submit" class="w3-button w3-light-green full" id="btnsubmit">Ajouter les utilisateurs dans la base de données</button>
</form>
<p>
    <i class="fa fa-2x fa-info-circle"></i>
    Pour importer des utilisateurs, il faut tous d'abord être certains que tous les utilisateurs possèdent tous :
</p>
<ul>
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
</ul>
<p>Ensuite, il faut exporter les utilisateurs depuis le centre d'administration Office 365, voici la procédure en 5 étapes :</p>
<p><img src="resources/export_office_users/etape_1.png" style="height: auto; width: 600px;"></img></p>
<p><img src="resources/export_office_users/etape_2.png" style="height: auto; width: 600px;"></img></p>
<p><img src="resources/export_office_users/etape_3.png" style="height: auto; width: 600px;"></img></p>
<p><img src="resources/export_office_users/etape_4.png" style="height: auto; width: 600px;"></img></p>
<p><img src="resources/export_office_users/etape_5.png" style="height: auto; width: 600px;"></img></p>
<script >
/**SYSTEME DE VERIFICATIION DU FICHIER POUR IMPORT UTILISATEURS*/
    let input = document.getElementById('file-input');
    let file;
    input.addEventListener("change", function(){
        file = this.files[0];
        checkFile(file);
    });
    function checkFile(file){
        let fileType = file.type;
        let validExtensions = ["application/vnd.ms-excel", "text/csv", "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"];
        if(validExtensions.includes(fileType)){
        }else{
            alert('Fichier invalide, merci de choisir un autre fichier !');
            input.value = '';
        }
    }
/**SYSTEME DE VERIFICATIION DU FICHIER POUR IMPORT UTILISATEURS*/
</script>
<?php require_once "commons/footer.php";?>
