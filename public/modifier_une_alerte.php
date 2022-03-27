<?php
    $title = "Modifier une alerte";
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
        if(isset($_POST['nom'])&&isset($_POST['type_de_donnees'])&&isset($_POST['type_dalerte'])&&isset($_POST['valeur_de_declenchement'])&&isset($_POST['active'])&&isset($_POST['id'])){
            //on vérifie que les champs sont remplis
            $sanitized_nom = filter_var($_POST['nom'],FILTER_SANITIZE_STRING);
            $sanitized_type_de_donnees = filter_var($_POST['type_de_donnees'], FILTER_SANITIZE_NUMBER_INT);
            $sanitized_type_dalerte = filter_var($_POST['type_dalerte'], FILTER_SANITIZE_NUMBER_INT);
            $sanitized_valeur_de_declenchement = filter_var($_POST['valeur_de_declenchement'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $sanitized_active = filter_var($_POST['active'], FILTER_SANITIZE_NUMBER_INT);
            $sanitized_id = filter_var($_POST['id'],FILTER_SANITIZE_NUMBER_INT);
            $sql2 = 'UPDATE alertes SET nom = ?, type_de_donnees = ?, type_dalerte = ?, valeur_de_declenchement = ?, active = ? WHERE id = ?;';
            $stmt2 = mysqli_prepare($conn,$sql2);
            mysqli_stmt_bind_param($stmt2, 'siidii',$sanitized_nom,$sanitized_type_de_donnees, $sanitized_type_dalerte, $sanitized_valeur_de_declenchement, $sanitized_active, $sanitized_id);
            $status2 = mysqli_stmt_execute($stmt2);
            $result2 = mysqli_stmt_get_result($stmt2);
            if($conn->affected_rows==0){
                echo '<h1><img src="resources/bad.png" width="5%" height="auto" />Cette alerte existe déjà merci d\'en choisir une autre !</h1>';
            }else if($conn->affected_rows==1){
                echo '<h1><img src="resources/good.png" width="5%" height="auto"/>Bravo, l\'alerte a bien été modifiée !</h1>';
            }else{
                echo '<h1><img src="resources/bad.png" width="5%" height="auto" />Un problème est survenu, merci de réessayer !</h1>';
            }
        }else{
            echo '<h1><img src="resources/bad.png" width="5%" height="auto" />Un problème est survenu, merci de réessayer !</h1>';
        }
    }else if($_SERVER['REQUEST_METHOD']=='GET'){
        //execute le reste du code de la page
        if(isset($_GET['id'])&&$_GET['id']>0){
            $sanitized_id = filter_var($_GET['id'],FILTER_SANITIZE_NUMBER_INT);
            $sql = 'SELECT alertes.id, alertes.nom, alertes.type_de_donnees, alertes.type_dalerte, alertes.valeur_de_declenchement, alertes.active FROM alertes WHERE alertes.id = ? LIMIT 1;';
            $stmt = mysqli_prepare($conn,$sql);
            mysqli_stmt_bind_param($stmt, 'i',$sanitized_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if(mysqli_num_rows($result)<1){
                echo '<h1><img src="../resources/bad.png" width="5%" height="auto" />Aucune alerte ne porte ce numéro !</h1>';
                require_once "commons/footer.php";
                die();
            }else{
                while ($row = mysqli_fetch_assoc($result)){
                    echo '<br/>';
                    echo '<form action="'.htmlspecialchars($_SERVER['PHP_SELF']).'" method="post" enctype="multipart/form-data">';
                    echo '<input type="hidden" name="id" value="'.$row['id'].'" />';
                    echo '<table id="modifier_une_alerte" class="w3-table-all full">';
                    echo '<tbody>';
                    echo '<tr>';
                    echo '<th>Nom</th>';
                    echo '<td><input id="nom" class="full" tabindex="-1" aria-disabled="true" type="text" name="nom" readonly required value="'.$row['nom'].'"/></td>';
                    echo '<td><i id="edit_nom" class="fa fa-pencil pointer"></i></td>';
                    echo '</tr>';
                    echo '<tr>';
                    echo '<th>Type de données que cette alerte va devoir surveiller</th>';
                    echo '<td><select id="type_de_donnees" class="full readonly" tabindex="-1" aria-disabled="true" name="type_de_donnees" required>';
                    switch($row['type_de_donnees']){
                        case 0:
                            echo '<option value="0" selected>Co2</option>';
                            echo '<option value="1">Température</option>';
                            echo '<option value="2">Humidité</option>';
                            break;
                        case 1:
                            echo '<option value="0">Co2</option>';
                            echo '<option value="1" selected>Température</option>';
                            echo '<option value="2">Humidité</option>';
                            break;
                        case 2:
                            echo '<option value="0">Co2</option>';
                            echo '<option value="1">Température</option>';
                            echo '<option value="2" selected>Humidité</option>';
                            break;
                    }
                    echo '</select>';
                    echo '<td><i id="edit_type_de_donnees" class="fa fa-pencil pointer"></i></td>';
                    echo '</tr>';
                    echo '<th>Type d\'évènement que cette alerte va devoir surveiller</th>';
                    echo '<td><select id="type_dalerte" class="full readonly" tabindex="-1" aria-disabled="true" name="type_dalerte" required>';
                    switch($row['type_dalerte']){
                        case 0:
                            echo '<option value="0" selected>Au dessus</option>';
                            echo '<option value="1">En dessous</option>';
                            break;
                        case 1:
                            echo '<option value="0">Au dessus</option>';
                            echo '<option value="1" selected>En dessous</option>';
                            break;
                    }
                    echo '</select>';
                    echo '<td><i id="edit_type_dalerte" class="fa fa-pencil pointer"></i></td>';
                    echo '</tr>';
                    echo '<tr>';
                    echo '<th>Valeur de déclenchement</th>';
                    echo '<td><input id="valeur_de_declenchement" class="full" tabindex="-1" aria-disabled="true" type="number" step="0.01" min="-100" max="99999" name="valeur_de_declenchement" readonly required value="'.$row['valeur_de_declenchement'].'"/></td>';
                    echo '<td><i id="edit_valeur_de_declenchement" class="fa fa-pencil pointer"></i></td>';
                    echo '</tr>';
                    echo '<th>Active</th>';
                    echo '<td><select id="active" class="full readonly" tabindex="-1" aria-disabled="true" name="active" required>';
                    switch($row['active']){
                        case 0:
                            echo '<option value="0" selected>Désactivée</option>';
                            echo '<option value="1">Activée</option>';
                            break;
                        case 1:
                            echo '<option value="0">Désactivée</option>';
                            echo '<option value="1" selected>Activée</option>';
                            break;
                    }
                    echo '</select>';
                    echo '<td><i id="edit_active" class="fa fa-pencil pointer"></i></td>';
                    echo '</tr>';
                    echo '</tbody>';
                    echo '</table>';
                    echo '<br/>';
                    echo '<input type="submit" name="submit" value="Enregistrer les modifications"  class="w3-button w3-light-green full"/>';
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
        die();
    }
?>
<script>
    document.getElementById('edit_nom').onclick = function(){
        if(document.getElementById('edit_nom').className.includes('fa-pencil')){
            document.getElementById('nom').readOnly = false;
            document.getElementById('edit_nom').classList.remove('fa-pencil');
            document.getElementById('edit_nom').classList.add('fa-check');
        }else{
            document.getElementById('nom').readOnly = true;
            document.getElementById('edit_nom').classList.remove('fa-check');
            document.getElementById('edit_nom').classList.add('fa-pencil');
        }
    }
    document.getElementById('edit_type_de_donnees').onclick = function(){
        if(document.getElementById('edit_type_de_donnees').className.includes('fa-pencil')){
            document.getElementById('type_de_donnees').classList.remove('readonly');
            document.getElementById('edit_type_de_donnees').classList.remove('fa-pencil');
            document.getElementById('edit_type_de_donnees').classList.add('fa-check');
        }else{
            document.getElementById('type_de_donnees').classList.add('readonly');
            document.getElementById('edit_type_de_donnees').classList.remove('fa-check');
            document.getElementById('edit_type_de_donnees').classList.add('fa-pencil');
        }
    }
    document.getElementById('edit_type_dalerte').onclick = function(){
        if(document.getElementById('edit_type_dalerte').className.includes('fa-pencil')){
            document.getElementById('type_dalerte').classList.remove('readonly');
            document.getElementById('edit_type_dalerte').classList.remove('fa-pencil');
            document.getElementById('edit_type_dalerte').classList.add('fa-check');
        }else{
            document.getElementById('type_dalerte').classList.add('readonly');
            document.getElementById('edit_type_dalerte').classList.remove('fa-check');
            document.getElementById('edit_type_dalerte').classList.add('fa-pencil');
        }
    }
    document.getElementById('edit_valeur_de_declenchement').onclick = function(){
        if(document.getElementById('edit_valeur_de_declenchement').className.includes('fa-pencil')){
            document.getElementById('valeur_de_declenchement').readOnly = false;
            document.getElementById('edit_valeur_de_declenchement').classList.remove('fa-pencil');
            document.getElementById('edit_valeur_de_declenchement').classList.add('fa-check');
        }else{
            document.getElementById('valeur_de_declenchement').readOnly = true;
            document.getElementById('edit_valeur_de_declenchement').classList.remove('fa-check');
            document.getElementById('edit_valeur_de_declenchement').classList.add('fa-pencil');
        }
    }
    document.getElementById('edit_active').onclick = function(){
        if(document.getElementById('edit_active').className.includes('fa-pencil')){
            document.getElementById('active').classList.remove('readonly');
            document.getElementById('edit_active').classList.remove('fa-pencil');
            document.getElementById('edit_active').classList.add('fa-check');
        }else{
            document.getElementById('active').classList.add('readonly');
            document.getElementById('edit_active').classList.remove('fa-check');
            document.getElementById('edit_active').classList.add('fa-pencil');
        }
    }
</script>
<?php require_once "commons/footer.php";?>