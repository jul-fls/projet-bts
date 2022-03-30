<?php
    $title = "Ajouter une alerte";
    require_once "commons/header.php";
    if(!isset($_SESSION['loggedin'])){
        echo '<h1><img src="resources/danger.png" width="5%" height="auto" />Vous n\'etes pas connecté, merci de vous connecter à votre compte !</h1>';
        require_once "commons/footer.php";
        die();
    }else if($_SESSION['role']<2){
        echo '<h1><img src="resources/danger.png" width="5%" height="auto" />Vous n\'avez pas l\'autorisation d\'accéder à cette page !</h1>';
        require_once "commons/footer.php";
    die();
    }
    if($_SERVER['REQUEST_METHOD']=='POST'){
        //traitement du formulaire
        if(isset($_POST['nom'])&&isset($_POST['type_de_donnees'])&&isset($_POST['type_dalerte'])&&isset($_POST['valeur_de_declenchement'])){
            //on vérifie que les champs sont remplis
            //Sanitize inputs
            $sanitized_nom = filter_var($_POST['nom'], FILTER_SANITIZE_STRING);
            $sanitized_type_de_donnees = filter_var($_POST['type_de_donnees'], FILTER_SANITIZE_NUMBER_INT);
            $sanitized_type_dalerte = filter_var($_POST['type_dalerte'], FILTER_SANITIZE_NUMBER_INT);
            $sanitized_valeur_de_declenchement = filter_var($_POST['valeur_de_declenchement'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            require "commons/dbconfig.php";
            //insertion dans la base de données
            $sql = "INSERT IGNORE INTO alertes (nom, type_de_donnees, type_dalerte, valeur_de_declenchement) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn,$sql);
            mysqli_stmt_bind_param($stmt, 'siid',$sanitized_nom,$sanitized_type_de_donnees,$sanitized_type_dalerte,$sanitized_valeur_de_declenchement);
            $status = mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
                if($conn->affected_rows==0){
                    echo '<h1><img src="resources/bad.png" width="5%" height="auto" />Cette alerte existe déjà merci d\'en choisir une autre !</h1>';
                }else if($conn->affected_rows==1){
                    echo '<h1><img src="resources/good.png" width="5%" height="auto"/>Bravo, l\'alerte a bien été ajoutée !</h1>';
                    
                }else{
                    echo '<h1><img src="resources/bad.png" width="5%" height="auto" />Un problème est survenu, merci de réessayer !</h1>';
                }
        }
    }else if($_SERVER['REQUEST_METHOD']=='GET'){
        //execute le reste du code de la page
    }else{
        //méthode invalide
        die();
    }
?>
<form class="w3-container full" method="POST" action="<?=htmlspecialchars($_SERVER['PHP_SELF'])?>" enctype="multipart/form-data">
    <p>
        <input class="w3-input full" type="text" id="nom" name="nom" required>
        <label for="nom">Nom</label>
    </p>
    <p>
        <select id="type_de_donnees" class="w3-select full" name="type_de_donnees" required>
            <option value="0" selected>Co2</option>
            <option value="1">Température</option>
            <option value="2">Humidité</option>
        </select>
        <label for="type_de_donnees">Type de données que cette alerte va devoir surveiller</label>
    </p>
    <p>
        <select id="type_dalerte" class="w3-select full" name="type_dalerte" required>
            <option value="0" selected>Au dessus</option>
            <option value="1">En dessous</option>
        </select>
        <label for="type_dalerte">Type d'évènement que cette alerte va devoir surveiller</label>
    </p>
    <p>
        <input class="w3-input full" type="number" id="valeur_de_declenchement" name="valeur_de_declenchement" min="-100" max="99999" step="0.01" required>
        <label for="valeur_de_declenchement">Valeur de déclenchement</label>
    </p>
    <br/><br/>

  <input type="submit" name="enregistrer" value="Enregistrer l'alerte dans la base de données et l'activer immédiatement" class="w3-button w3-light-green full"/>
</form>
<?php require_once "commons/footer.php";?>