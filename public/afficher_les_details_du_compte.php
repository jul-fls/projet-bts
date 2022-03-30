<?php 
    $title = "Afficher les détails du compte";
    require_once "commons/header.php";
?>
<?php
    if($_SERVER['REQUEST_METHOD']=='POST'){
        //traitement du formulaire
        if(isset($_SESSION['loggedin'])&&isset($_POST['nom_utilisateur'])&&isset($_POST['prenom_utilisateur'])&&isset($_POST['login'])&&isset($_POST['email'])&&isset($_POST['telephone'])){
            $sanitized_nom_utilisateur = filter_var($_POST['nom_utilisateur'],FILTER_SANITIZE_STRING);
            $sanitized_prenom_utilisateur = filter_var($_POST['prenom_utilisateur'],FILTER_SANITIZE_STRING);
            $sanitized_login = filter_var($_POST['login'],FILTER_SANITIZE_STRING);
            $sanitized_email = filter_var($_POST['email'],FILTER_SANITIZE_EMAIL);
            $sanitized_telephone = filter_var($_POST['telephone'],FILTER_SANITIZE_STRING);
            require "commons/dbconfig.php";
            $sql = 'UPDATE utilisateurs SET nom_utilisateur = ?, prenom_utilisateur = ?, login = ?, email = ?, telephone = ? WHERE id = ?;';
            $stmt = mysqli_prepare($conn,$sql);
            mysqli_stmt_bind_param($stmt, 'sssssi',$sanitized_nom_utilisateur,$sanitized_prenom_utilisateur,$sanitized_login,$sanitized_email,$sanitized_telephone,$_SESSION['id']);
            $status = mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if($conn->affected_rows==0){
                echo '<h1><img src="resources/bad.png" width="5%" height="auto" />Cet identifiant ou cette adresse email est déjà utilisé(e) merci d\'en choisir un(e) autre !</h1>';
            }else if($conn->affected_rows==1){
                echo '<h1><img src="resources/good.png" width="5%" height="auto"/>Bravo, votre compte à bien été mis à jour !</h1>';
            }else{
                echo "échec de la mise à jour de votre compte, veuillez réessayer !";    
            }
        }
    }else if($_SERVER['REQUEST_METHOD']=='GET'){
        if(!isset($_SESSION['loggedin'])){
            echo '<h1><img src="resources/danger.png" width="5%" height="auto" />Vous n\'etes pas connecté, merci de vous connecter à votre compte !</h1>';
            require_once "commons/footer.php";
            die();
        }
        //execute le reste du code de la page
    }else{
        //méthode invalide
        die();
    }
?>
<h1>Bonjour <?=$_SESSION['prenom_utilisateur']." ".$_SESSION['nom_utilisateur']?></h1>
<form action="<?=htmlspecialchars($_SERVER['PHP_SELF'])?>" method="post">
    <table id="afficher_details_compte" class="w3-table-all full">
        <tbody>
            <tr>
                <th>Nom</th>
                <td><input id="nom_utilisateur" class="full" type="text" name="nom_utilisateur" readonly required value="<?=$_SESSION['nom_utilisateur']?>"/></td>
                <td><i id="edit_nom_utilisateur" class="fa fa-pencil pointer"></i></td>
            </tr>
            <tr>
                <th>Prénom</th>
                <td><input id="prenom_utilisateur" class="full" type="text" name="prenom_utilisateur" readonly required value="<?=$_SESSION['prenom_utilisateur']?>"/></td>
                <td><i id="edit_prenom_utilisateur" class="fa fa-pencil pointer"></i></td>
            </tr>
            <tr>
                <th>Identifiant</th>
                <td><input id="login" class="full" type="text" name="login" readonly required value="<?=$_SESSION['login']?>"/></td>
                <td><i id="edit_login" class="fa fa-pencil pointer"></i></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><input id="email" class="full" type="email" name="email" readonly required value="<?=$_SESSION['email']?>"/></td>
                <td><i id="edit_email" class="fa fa-pencil pointer"></i></td>
            </tr>
            <tr>
                <th>N° de téléphone</th>
                <td><input id="telephone" class="full" type="tel" name="telephone" pattern="[0-9]{10}" readonly required value="<?=$_SESSION['telephone']?>"/></td>
                <td><i id="edit_telephone" class="fa fa-pencil pointer"></i></td>
            </tr>
            <tr>
                <th>Mot de passe</th>
                <td>******</td>
                <td><a href="mdp_oublie.php"><i class="fa fa-pencil pointer"></i></a></td>
            </tr>
        </tbody>
    </table>
    <br/>
    <input type="submit" name="submit" value="Enregistrer les modifications"/>
</form>
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
<p>
    <i class="fa fa-2x fa-info-circle"></i>
    Pour modifier votre mot de passe cliquez sur le crayon à coté de votre mot de passe et complétez la procédure de mot de passe oublié afin de réinitialier votre mot de passe.
</p>
<?php require_once "commons/footer.php";?>