<?php
    $title = "Ajouter une alerte"; // Titre de la page
    require_once "commons/header.php"; // Inclusion du header
    if(!isset($_SESSION['loggedin'])){ // Si l'utilisateur n'est pas connecté
        echo '<h1><img src="resources/danger.png" width="5%" height="auto" />Vous n\'etes pas connecté, merci de vous connecter à votre compte !</h1>'; // Message d'erreur
        require_once "commons/footer.php"; // Inclusion du footer
        die(); // On arrête l'execution du script
    }else if($_SESSION['role']<2){ // Si l'utilisateur n'est pas un super administrateur
        echo '<h1><img src="resources/danger.png" width="5%" height="auto" />Vous n\'avez pas l\'autorisation d\'accéder à cette page !</h1>'; // Message d'erreur
        require_once "commons/footer.php"; // Inclusion du footer
        die(); // On arrête l'execution du script
    }
    if($_SERVER['REQUEST_METHOD']=='POST'){ // Si la requête est en POST
        //traitement du formulaire 
        if(isset($_POST['nom'])&&isset($_POST['type_de_donnees'])&&isset($_POST['type_dalerte'])&&isset($_POST['valeur_de_declenchement'])){ // Si les champs sont remplis
            $sanitized_nom = filter_var($_POST['nom'], FILTER_SANITIZE_STRING); // On sécurise les données
            $sanitized_type_de_donnees = filter_var($_POST['type_de_donnees'], FILTER_SANITIZE_NUMBER_INT); // On sécurise les données
            $sanitized_type_dalerte = filter_var($_POST['type_dalerte'], FILTER_SANITIZE_NUMBER_INT); // On sécurise les données
            $sanitized_valeur_de_declenchement = filter_var($_POST['valeur_de_declenchement'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION); // On sécurise les données
            require "commons/dbconfig.php"; // Inclusion du fichier de configuration de la base de données
            $sql = "INSERT IGNORE INTO alertes (nom, type_de_donnees, type_dalerte, valeur_de_declenchement) VALUES (?, ?, ?, ?)"; // Requête SQL
            $stmt = mysqli_prepare($conn,$sql); // On prépare la requête
            mysqli_stmt_bind_param($stmt, 'siid',$sanitized_nom,$sanitized_type_de_donnees,$sanitized_type_dalerte,$sanitized_valeur_de_declenchement); // On lie les paramètres
            $status = mysqli_stmt_execute($stmt); // On exécute la requête
            $result = mysqli_stmt_get_result($stmt); // On récupère le résultat de la requête
                if($conn->affected_rows==0){ // Si la requête n'a pas abouti
                    echo '<h1><img src="resources/bad.png" width="5%" height="auto" />Cette alerte existe déjà merci d\'en choisir une autre !</h1>'; // Message d'erreur
                }else if($conn->affected_rows==1){ // Si la requête a abouti
                    echo '<h1><img src="resources/good.png" width="5%" height="auto"/>Bravo, l\'alerte a bien été ajoutée !</h1>'; // Message de confirmation
                }else{ // Si la requête n'a pas abouti
                    echo '<h1><img src="resources/bad.png" width="5%" height="auto" />Un problème est survenu, merci de réessayer !</h1>'; // Message d'erreur
                }
        }
    }else if($_SERVER['REQUEST_METHOD']=='GET'){ // Si la requête est en GET
        //execute le reste du code de la page
    }else{ // Si la requête n'est ni en POST ni en GET
        //méthode invalide
        die(); // On arrête l'execution du script
    }
?>
<form class="w3-container full" method="POST" action="<?=htmlspecialchars($_SERVER['PHP_SELF'])?>" enctype="multipart/form-data"> <!-- Formulaire -->
    <p>
        <input class="w3-input full" type="text" id="nom" name="nom" required> <!-- Champ de texte -->
        <label for="nom">Nom</label> <!-- Label -->
    </p>
    <p>
        <select id="type_de_donnees" class="w3-select full" name="type_de_donnees" required> <!-- Liste déroulante -->
            <option value="0" selected>Co2 (Ppm)</option> <!-- Option CO2 -->
            <option value="1">Température (°C)</option> <!-- Option Température -->
            <option value="2">Humidité (%HR)</option> <!-- Option Humidité -->
        </select> <!-- Fin de la liste déroulante -->
        <label for="type_de_donnees">Type de données que cette alerte va devoir surveiller</label> <!-- Label -->
    </p>
    <p>
        <select id="type_dalerte" class="w3-select full" name="type_dalerte" required> <!-- Liste déroulante -->
            <option value="0" selected>Au dessus</option> <!-- Option Supérieur -->
            <option value="1">En dessous</option> <!-- Option Inférieur -->
        </select>
        <label for="type_dalerte">Type d'évènement que cette alerte va devoir surveiller</label> <!-- Label -->
    </p>
    <p>
        <input class="w3-input full" type="number" id="valeur_de_declenchement" name="valeur_de_declenchement" min="-100" max="99999" step="0.01" required> <!-- Champ de choix de la valeur de déclenchement -->
        <label for="valeur_de_declenchement">Valeur de déclenchement</label> <!-- Label -->
    </p>
    <br/><br/> <!-- Saut de ligne -->

  <input type="submit" name="enregistrer" value="Enregistrer l'alerte dans la base de données et l'activer immédiatement" class="w3-button w3-light-green full"/> <!-- Bouton d'enregistrement -->
</form> <!-- Fin du formulaire -->
<?php require_once "commons/footer.php";?> <!-- Inclusion du footer -->