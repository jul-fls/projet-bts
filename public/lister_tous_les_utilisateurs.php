<?php
    $title = "Lister tout les utilisateurs";
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
?>
<div class="w3-center">
    <h2 id="title">Liste des utilisateurs</h2>
</div>
<table class="w3-table-all" id="tableau-liste-utilisateurs">
    <thead>
        <tr class="w3-blue">
            <th>N° de l'utilisateur</th>
            <th>Type d'utilisateur</th>
            <th>Description de l'utilisateur</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Role de l'utilisateur</th>
            <th>Identifiant</th>
            <th>Adresse email</th>
            <th>N° de téléphone</th>
            <th>Compte activé</th>
            <th>Actions</th>
        </tr>
    </thead>
    <?php
        require("commons/dbconfig.php");
        $sql = "SELECT utilisateurs.id, utilisateurs.nom_utilisateur, utilisateurs.prenom_utilisateur, utilisateurs.type_utilisateur, utilisateurs.description, utilisateurs.role, utilisateurs.login, utilisateurs.email, utilisateurs.telephone, CASE WHEN utilisateurs.password_hash IS NOT NULL THEN 1 ELSE 0 END AS isactive FROM utilisateurs ORDER BY utilisateurs.id ASC;";
        $stmt = mysqli_prepare($conn,$sql);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if(!$result){
            die("échec de la lecture des utilisateurs");    
        }
        if(mysqli_num_rows($result)>0){
            $nb_isactive = 0;
            while ($row = mysqli_fetch_assoc($result)){
                echo '<tr>'."\n";
                echo '<td>'.$row['id'].'</td>'."\n";
                switch($row['type_utilisateur']){
                    case 0:
                        echo '<td>Élève</td>'."\n";
                        break;
                    case 1:
                        echo '<td>Professeur</td>'."\n";
                        break;
                    case 2:
                        echo '<td>Formateur</td>'."\n";
                        break;
                    case 3:
                        echo '<td>Personnel</td>'."\n";
                        break;
                    case 4:
                        echo '<td>Autres</td>'."\n";
                        break;
                    default:
                        echo '<td>Autres</td>'."\n";
                        break;
                }
                echo '<td>'.$row['description'].'</td>'."\n";
                echo '<td>'.mb_strtoupper($row['nom_utilisateur']).'</td>'."\n";
                echo '<td>'.mb_ucfirst($row['prenom_utilisateur'],'UTF-8').'</td>'."\n";
                switch($row['role']){
                    case 0:
                        echo '<td>Utilisateur</td>'."\n";
                        break;
                    case 1:
                        echo '<td>Administrateur</td>'."\n";
                        break;
                    case 2:
                        echo '<td>Super Administrateur</td>'."\n";
                        break;
                    default:
                        echo '<td>Utilisateur</td>'."\n";
                        break;
                }
                echo '<td>'.$row['login'].'</td>'."\n";
                echo '<td>'.$row['email'].'</td>'."\n";
                echo '<td>'.$row['telephone'].'</td>'."\n";
                if($row['isactive']==1){
                    $nb_isactive++;
                    echo '<td><i title="Compte activé" style="color: green" class="fa fa-check-square-o fa-2x"></i></td>'."\n";
                }else{
                    echo '<td><i title="Compte inactivé" style="color: darkred" class="fa fa-window-close-o fa-2x"></i></td>'."\n";
                }
                echo '<td><a href="modifier_un_utilisateur.php?id='.$row['id'].'"><i class="fa fa-pencil fa-2x"></i></a><br/><a href="#" onclick="show_confirmation('.$row['id'].');"><i class="fa fa-trash fa-2x" style="color: darkred"></i></a></td>'."\n";
                echo '</tr>'."\n";
            }
        }
    ?>
</table>
<div id="supprimer_utilisateur_confirmation" class="w3-modal">
    <div class="w3-modal-content w3-animate-top w3-card-4">
        <header class="w3-container w3-red"> 
            <span onclick="document.getElementById('supprimer_utilisateur_confirmation').style.display='none'" class="w3-button w3-display-topright">&times;</span>
            <h2>Etes-vous sûr de vouloir supprimer cet utilisateur ?</h2>
        </header>
        <div class="w3-container">
            <p>Cela entrainera aussi la suppression de tous les donnees_capteurs passés et en cours pour cet utilisateur</p>
            <a id="supprimer_utilisateur_button" class="w3-red w3-center w3-button" style="width: 100%;" href="">Confirmer la suppression</a>
        </div>
    </div>
</div>
<script>
    function show_confirmation(id){
        document.getElementById('supprimer_utilisateur_button').href='supprimer_un_utilisateur.php?id='+id;
        document.getElementById('supprimer_utilisateur_confirmation').style.display='block';
        return false;
    }
    $nb_utilisateurs = <?=mysqli_num_rows($result)?>;
    $nb_isactive = <?=$nb_isactive?>;
    if($nb_utilisateurs==1){
        document.getElementById('title').innerHTML = 'Liste de l\'unique utilisateur';
    }else{
        document.getElementById('title').innerHTML = 'Liste des <b>'+$nb_utilisateurs+'</b> utilisateurs dont <b>'+$nb_isactive+'</b> sont activés';
    }
</script>
<?php require_once "commons/footer.php";?>