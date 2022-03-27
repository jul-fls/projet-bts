<?php
    $title = "Modifier un utilisateur";
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
        if(isset($_SESSION['loggedin'])&&isset($_POST['nom_utilisateur'])&&isset($_POST['prenom_utilisateur'])&&isset($_POST['type_utilisateur'])&&isset($_POST['role'])&&isset($_POST['description'])&&isset($_POST['login'])&&isset($_POST['email'])&&isset($_POST['telephone'])&&isset($_POST['id'])){
            $sanitized_nom_utilisateur = filter_var($_POST['nom_utilisateur'],FILTER_SANITIZE_STRING);
            $sanitized_prenom_utilisateur = filter_var($_POST['prenom_utilisateur'],FILTER_SANITIZE_STRING);
            $sanitized_type_utilisateur = filter_var($_POST['type_utilisateur'],FILTER_SANITIZE_NUMBER_INT);
            $sanitized_role = filter_var($_POST['role'],FILTER_SANITIZE_NUMBER_INT);
            $sanitized_description = filter_var($_POST['description'],FILTER_SANITIZE_STRING);
            $sanitized_login = filter_var($_POST['login'],FILTER_SANITIZE_STRING);
            $sanitized_email = filter_var($_POST['email'],FILTER_SANITIZE_EMAIL);
            $sanitized_telephone = filter_var($_POST['telephone'],FILTER_SANITIZE_STRING);
            $sanitized_id = filter_var($_POST['id'],FILTER_SANITIZE_NUMBER_INT);
            $sql = 'UPDATE utilisateurs SET utilisateurs.nom_utilisateur = ?, utilisateurs.prenom_utilisateur = ?, utilisateurs.type_utilisateur = ?, utilisateurs.role = ?, utilisateurs.description = ?, utilisateurs.login = ?, utilisateurs.email = ?, utilisateurs.telephone = ? WHERE utilisateurs.id = ? ;';
            $stmt = mysqli_prepare($conn,$sql);
            mysqli_stmt_bind_param($stmt, 'ssiissssi',$sanitized_nom_utilisateur,$sanitized_prenom_utilisateur,$sanitized_type_utilisateur,$sanitized_role,$sanitized_description,$sanitized_login,$sanitized_email,$sanitized_telephone,$sanitized_id);
            $status = mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if($conn->affected_rows==0){
                echo '<h1><img src="resources/bad.png" width="5%" height="auto" />Cet identifiant ou cette adresse email est déjà utilisé(e) merci d\'en choisir un(e) autre !</h1>';
            }else if($conn->affected_rows==1){
                echo '<h1><img src="resources/good.png" width="5%" height="auto"/>Bravo, l\'utilisateur a bien été mis à jour !</h1>';
            }else{
                echo '<h1><img src="resources/bad.png" width="5%" height="auto" />Un problème est survenu, merci de réessayer !</h1>';
            }
        }
    }else if($_SERVER['REQUEST_METHOD']=='GET'){
        //execute le reste du code de la page
        if(isset($_GET['id'])&&$_GET['id']>0){
            $sanitized_id = filter_var($_GET['id'],FILTER_SANITIZE_NUMBER_INT);
            $sql = 'SELECT utilisateurs.id, utilisateurs.nom_utilisateur, utilisateurs.prenom_utilisateur, utilisateurs.type_utilisateur, utilisateurs.role, utilisateurs.description, utilisateurs.login, utilisateurs.email FROM utilisateurs WHERE utilisateurs.id = ? LIMIT 1;';
            $stmt = mysqli_prepare($conn,$sql);
            mysqli_stmt_bind_param($stmt, 'i',$sanitized_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if(mysqli_num_rows($result)<1){
                echo '<h1><img src="resources/bad.png" width="5%" height="auto" />Aucun utilisateur ne porte ce numéro !</h1>';
                require_once "commons/footer.php";
                die();    
            }else{
                while ($row = mysqli_fetch_assoc($result)){
                    echo '<br/>';
                    echo '<form action="'.htmlspecialchars($_SERVER['PHP_SELF']).'" method="post">';
                    echo '<input type="hidden" name="id" value="'.$row['id'].'" />';
                    echo '<table id="modifier_un_utilisateur" class="w3-table-all full">';
                    echo '<tbody>';
                    echo '<tr>';
                    echo '<th>Nom</th>';
                    echo '<td><input id="nom_utilisateur" class="full" tabindex="-1" aria-disabled="true" type="text" name="nom_utilisateur" readonly required value="'.$row['nom_utilisateur'].'"/></td>';
                    echo '<td><i id="edit_nom_utilisateur" class="fa fa-pencil pointer"></i></td>';
                    echo '</tr>';
                    echo '<tr>';
                    echo '<th>Prénom</th>';
                    echo '<td><input id="prenom_utilisateur" class="full" tabindex="-1" aria-disabled="true" type="text" name="prenom_utilisateur" readonly required value="'.$row['prenom_utilisateur'].'"/></td>';
                    echo '<td><i id="edit_prenom_utilisateur" class="fa fa-pencil pointer"></i></td>';
                    echo '</tr>';
                    echo '<tr>';
                    echo '<th>Type</th>';
                    echo '<td><select id="type_utilisateur" class="full readonly" tabindex="-1" aria-disabled="true" name="type_utilisateur" required>';
                    switch($row['type_utilisateur']){
                        case 0:
                            echo '<option value="0" selected>Élève</option>';
                            echo '<option value="1">Professeur</option>';
                            echo '<option value="2">Formateur</option>';
                            echo '<option value="3">Personnel</option>';
                            echo '<option value="4">Autres</option>';
                            break;
                        case 1:
                            echo '<option value="0"Élève</option>';
                            echo '<option value="1" selected>Professeur</option>';
                            echo '<option value="2">Formateur</option>';
                            echo '<option value="3">Personnel</option>';
                            echo '<option value="4">Autres</option>';
                            break;
                        case 2:
                            echo '<option value="0">Élève</option>';
                            echo '<option value="1">Professeur</option>';
                            echo '<option value="2" selected>Formateur</option>';
                            echo '<option value="3">Personnel</option>';
                            echo '<option value="4">Autres</option>';
                            break;
                        case 3:
                            echo '<option value="0">Élève</option>';
                            echo '<option value="1">Professeur</option>';
                            echo '<option value="2">Formateur</option>';
                            echo '<option value="3" selected>Personnel</option>';
                            echo '<option value="4">Autres</option>';
                            break;
                        case 4:
                            echo '<option value="0">Élève</option>';
                            echo '<option value="1">Professeur</option>';
                            echo '<option value="2">Formateur</option>';
                            echo '<option value="3">Personnel</option>';
                            echo '<option value="4" selected>Autres</option>';
                            break;
                    }
                    echo '</select>';
                    echo '</td>';
                    echo '<td><i id="edit_type_utilisateur" class="fa fa-pencil pointer"></i></td>';
                    echo '</tr>';
                    echo '<tr>';
                    echo '<th>Role</th>';
                    echo '<td><select id="role" class="full readonly" tabindex="-1" aria-disabled="true" name="role" required>';
                    if($row['role']==0){
                        echo '<option value="0" selected>Utilisateur standard</option>';
                        echo '<option value="1">Utilisateur Administrateur</option>';
                        echo '<option value="2">Utilisateur Super Administrateur</option>';
                    }else if($row['role']==1){
                        echo '<option value="0">Utilisateur standard</option>';
                        echo '<option value="1" selected>Utilisateur Administrateur</option>';
                        echo '<option value="2">Utilisateur Super Administrateur</option>';
                    }else{
                        echo '<option value="0">Utilisateur standard</option>';
                        echo '<option value="1">Utilisateur Administrateur</option>';
                        echo '<option value="2" selected>Utilisateur Super Administrateur</option>';
                    }
                    echo '</select>';
                    echo '</td>';
                    echo '<td><i id="edit_role" class="fa fa-pencil pointer"></i></td>';
                    echo '</tr>';
                    echo '<tr>';
                    echo '<th>Description</th>';
                    echo '<td><input id="description" class="full" tabindex="-1" aria-disabled="true" type="text" name="description" readonly required value="'.$row['description'].'"/></td>';
                    echo '<td><i id="edit_description" class="fa fa-pencil pointer"></i></td>';
                    echo '</tr>';
                    echo '<tr>';
                    echo '<th>Identifiant</th>';
                    echo '<td><input id="login" class="full" tabindex="-1" aria-disabled="true" type="text" name="login" readonly required value="'.$row['login'].'"/></td>';
                    echo '<td><i id="edit_login" class="fa fa-pencil pointer"></i></td>';
                    echo '</tr>';
                    echo '<tr>';
                    echo '<th>Email</th>';
                    echo '<td><input id="email" class="full" tabindex="-1" aria-disabled="true" type="email" name="email" readonly required value="'.$row['email'].'"/></td>';
                    echo '<td><i id="edit_email" class="fa fa-pencil pointer"></i></td>';
                    echo '</tr>';
                    echo '<tr>';
                    echo '<th>Téléphone</th>';
                    echo '<td><input id="telephone" class="full" tabindex="-1" aria-disabled="true" type="tel" name="telephone" pattern="[0-9]{10}" readonly required value="'.$row['telephone'].'"/></td>';
                    echo '<td><i id="edit_telephone" class="fa fa-pencil pointer"></i></td>';
                    echo '</tr>';
                    echo '</tbody>';
                    echo '</table>';
                    echo '<br/>';
                    echo '<input type="submit" name="submit" value="Enregistrer les modifications"/>';
                    echo '</form>';
                }
            }
        }else{
            echo '<h1><img src="resources/bad.png" width="5%" height="auto" />Une erreur est survenue, merci de réessayer !</h1>';
            require_once "commons/footer.php";
            die();
        }
    }else{
        //ne rien faire, méthode invalide
    }
?>
<script>
    document.getElementById('edit_nom_utilisateur').onclick = function(){
        if(document.getElementById('edit_nom_utilisateur').className.includes('fa-pencil')){
            document.getElementById('nom_utilisateur').readOnly = false;
            document.getElementById('edit_nom_utilisateur').classList.remove('fa-pencil');
            document.getElementById('edit_nom_utilisateur').classList.add('fa-check');
        }else{
            document.getElementById('nom_utilisateur').readOnly = true;
            document.getElementById('edit_nom_utilisateur').classList.remove('fa-check');
            document.getElementById('edit_nom_utilisateur').classList.add('fa-pencil');
        }
    }
    document.getElementById('edit_prenom_utilisateur').onclick = function(){
        if(document.getElementById('edit_prenom_utilisateur').className.includes('fa-pencil')){
            document.getElementById('prenom_utilisateur').readOnly = false;
            document.getElementById('edit_prenom_utilisateur').classList.remove('fa-pencil');
            document.getElementById('edit_prenom_utilisateur').classList.add('fa-check');
        }else{
            document.getElementById('prenom_utilisateur').readOnly = true;
            document.getElementById('edit_prenom_utilisateur').classList.remove('fa-check');
            document.getElementById('edit_prenom_utilisateur').classList.add('fa-pencil');
        }
    }
    document.getElementById('edit_type_utilisateur').onclick = function(){
        if(document.getElementById('edit_type_utilisateur').className.includes('fa-pencil')){
            document.getElementById('type_utilisateur').classList.remove('readonly');
            document.getElementById('edit_type_utilisateur').classList.remove('fa-pencil');
            document.getElementById('edit_type_utilisateur').classList.add('fa-check');
        }else{
            document.getElementById('type_utilisateur').classList.add('readonly');
            document.getElementById('edit_type_utilisateur').classList.remove('fa-check');
            document.getElementById('edit_type_utilisateur').classList.add('fa-pencil');
        }
    }
    document.getElementById('edit_role').onclick = function(){
        if(document.getElementById('edit_role').className.includes('fa-pencil')){
            document.getElementById('role').classList.remove('readonly');
            document.getElementById('edit_role').classList.remove('fa-pencil');
            document.getElementById('edit_role').classList.add('fa-check');
        }else{
            document.getElementById('role').classList.add('readonly');
            document.getElementById('edit_role').classList.remove('fa-check');
            document.getElementById('edit_role').classList.add('fa-pencil');
        }
    }
    document.getElementById('edit_description').onclick = function(){
        if(document.getElementById('edit_description').className.includes('fa-pencil')){
            document.getElementById('description').readOnly = false;
            document.getElementById('edit_description').classList.remove('fa-pencil');
            document.getElementById('edit_description').classList.add('fa-check');
        }else{
            document.getElementById('description').readOnly = true;
            document.getElementById('edit_description').classList.remove('fa-check');
            document.getElementById('edit_description').classList.add('fa-pencil');
        }
    }
    document.getElementById('edit_login').onclick = function(){
        if(document.getElementById('edit_login').className.includes('fa-pencil')){
            document.getElementById('login').readOnly = false;
            document.getElementById('edit_login').classList.remove('fa-pencil');
            document.getElementById('edit_login').classList.add('fa-check');
        }else{
            document.getElementById('login').readOnly = true;
            document.getElementById('edit_login').classList.remove('fa-check');
            document.getElementById('edit_login').classList.add('fa-pencil');
        }
    }
    document.getElementById('edit_email').onclick = function(){
        if(document.getElementById('edit_email').className.includes('fa-pencil')){
            document.getElementById('email').readOnly = false;
            document.getElementById('edit_email').classList.remove('fa-pencil');
            document.getElementById('edit_email').classList.add('fa-check');
        }else{
            document.getElementById('email').readOnly = true;
            document.getElementById('edit_email').classList.remove('fa-check');
            document.getElementById('edit_email').classList.add('fa-pencil');
        }
    }
    document.getElementById('edit_telephone').onclick = function(){
        if(document.getElementById('edit_telephone').className.includes('fa-pencil')){
            document.getElementById('telephone').readOnly = false;
            document.getElementById('edit_telephone').classList.remove('fa-pencil');
            document.getElementById('edit_telephone').classList.add('fa-check');
        }else{
            document.getElementById('telephone').readOnly = true;
            document.getElementById('edit_telephone').classList.remove('fa-check');
            document.getElementById('edit_telephone').classList.add('fa-pencil');
        }
    }
</script>
<?php require_once "commons/footer.php";?>