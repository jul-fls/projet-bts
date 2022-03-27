<?php
    require_once "global.php";
    session_start();
    if($__MAINTENANCE_MODE__){
        if($_SESSION['role']<2){
            if(basename($_SERVER['REQUEST_URI'], '?' . $_SERVER['QUERY_STRING'])!='se_connecter.php'){
                header('Location: '.$__WEB_ROOT__.'maintenance.php');
            }
        }
    }else {
        // do nothing
    }
    function isCookieValid(){
        require_once("dbconfig.php");
        $isValid = false;
        if(isset($_COOKIE["rememberme"])){
            $decryptedCookieData = base64_decode($_COOKIE["rememberme"]);
            $user_id = explode($_SERVER['COOKIE_SALT'],$decryptedCookieData);
            $user_id = $user_id[1];
            $sql = 'SELECT utilisateurs.id, utilisateurs.nom_utilisateur, utilisateurs.prenom_utilisateur, utilisateurs.type_utilisateur, utilisateurs.role, utilisateurs.description, utilisateurs.login, utilisateurs.password_hash, utilisateurs.email, utilisateurs.telephone FROM utilisateurs WHERE utilisateurs.id = ? LIMIT 1;';
            $stmt = mysqli_prepare($conn,$sql);
            mysqli_stmt_bind_param($stmt, 's',$user_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if (!$result) {
            }
            if(mysqli_num_rows($result)>0){
                while ($row = mysqli_fetch_assoc($result)){
                    session_regenerate_id();
                    $_SESSION['loggedin'] = TRUE;
                    $_SESSION['id'] = $row["id"];
                    $_SESSION['nom_utilisateur'] = $row["nom_utilisateur"];
                    $_SESSION['prenom_utilisateur'] = $row["prenom_utilisateur"];
                    $_SESSION['type_utilisateur'] = $row["type_utilisateur"];
                    $_SESSION['role'] = $row["role"];
                    $_SESSION['description'] = $row["description"];
                    $_SESSION['login'] = $row["login"];
                    $_SESSION['email'] = $row["email"];
                    $_SESSION['telephone'] = $row["telephone"];
                    $isValid = true;
                }
            }else{
                $isValid = false;
                header('Location: '.$__WEB_ROOT__.'se_deconnecter.php');
            }
            return $isValid;
        }
    }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Salle Serveurs Saintonge - <?=$title?></title>
    <meta name="description" content="Site intranet pour la gestion de la salle serveur du lycée Sainte Famille Saintonge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#FFFFFF"/>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.5.0/css/all.min.css" integrity="sha512-QfDd74mlg8afgSqm3Vq2Q65e9b3xMhJB4GZ9OcHDVy1hZ6pqBJPWWnMsKDXM7NINoKqJANNGBuVRIpIJ5dogfA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fork-awesome@1.2.0/css/fork-awesome.min.css" integrity="sha256-XoaMnoYC5TH6/+ihMEnospgm0J1PM/nioxbOUdnM8HY=" crossorigin="anonymous">
    <link rel="stylesheet" href="<?=$__WEB_ROOT__?>commons/style.css">
    <link rel="icon" href="<?=$__WEB_ROOT__?>resources/favicon.ico" />
    <link rel="apple-touch-icon" href="<?=$__WEB_ROOT__?>resources/logo_saintonge_512x512px.png">
    <link rel="manifest" href="<?=$__WEB_ROOT__?>resources/manifest.webmanifest" />
</head>

<body>
    <script>
        document.addEventListener("DOMContentLoaded", function(){
            var loc = window.location.href;
            var str = loc.split("/")[3];
            str = str.split("?")[0];
            str = str.split("#")[0];
            if(!str){
                document.querySelectorAll("a[href='<?=$__WEB_ROOT__?>index.php']")[0].classList.add('w3-light-green');
            }
            document.querySelectorAll("a[href='<?=$__WEB_ROOT__?>"+str+"']")[0].classList.add('w3-light-green');
        });
    </script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript" src="https://www.termsfeed.com/public/cookie-consent/4.0.0/cookie-consent.js" charset="UTF-8"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <script type="text/javascript" charset="UTF-8">
    document.addEventListener('DOMContentLoaded', function () {
    cookieconsent.run({"notice_banner_type":"interstitial","consent_type":"express","palette":"light","language":"fr","page_load_consent_levels":["strictly-necessary"],"notice_banner_reject_button_hide":false,"preferences_center_close_button_hide":false,"page_refresh_confirmation_buttons":false,"open_preferences_center_selector":"#open_cookies_preferences","website_privacy_policy_url":"<?=$__WEB_ROOT__?>rgpd.php"});
    });
    </script>
    <div class="w3-container">
        <div class="w3-bar w3-light-grey">
            <?php
                isCookieValid();
                echo '<img class="w3-bar-item" src="'.$__WEB_ROOT__.'resources/logo.jpg" style="height: 38.5px;padding: unset;"/>'."\n";
                echo '<a href="'.$__WEB_ROOT__.'index.php" class="w3-bar-item w3-border w3-button" ><i class="fa fa-home"></i> Accueil</a>';
                if($_SESSION['loggedin']==1){
                    if($_SESSION['role']>=1){
                        echo '<div class="w3-dropdown-hover">'."\n";
                        echo '<button class="w3-button"><i class="fa fa-list"></i> Gérer les données des capteurs <i class="fa fa-caret-down"></i></button>'."\n";
                        echo '<div class="w3-dropdown-content w3-bar-block w3-card-4">'."\n";
                        if($_SESSION['role']==2){
                            echo '<a href="'.$__WEB_ROOT__.'rechercher_une_donnee.php" class="w3-bar-item w3-button"><i class="fa fa-search"></i> Rechercher une donnée</a>'."\n";
                            echo '<a href="'.$__WEB_ROOT__.'recapitulatif.php" class="w3-bar-item w3-button"><i class="fa fa-calendar"></i> Récapitulatif</a>'."\n";
                        }
                        echo '<a href="'.$__WEB_ROOT__.'lister_toutes_les_donnees.php" class="w3-bar-item w3-button"><i class="fa fa-list"></i> Lister toutes les donnees</a>';
                        echo '<a href="'.$__WEB_ROOT__.'lister_les_donnees_du_jour.php" class="w3-bar-item w3-button"><i class="fa fa-list"></i> Données du jour</a>'."\n";
                        echo '</div>'."\n";
                        echo '</div>'."\n";
                        echo '<div class="w3-dropdown-hover">'."\n";
                        echo '<button class="w3-button"><i class="fa fa-users"></i> Gérer les utilisateurs <i class="fa fa-caret-down"></i></button>'."\n";
                        echo '<div class="w3-dropdown-content w3-bar-block w3-card-4">'."\n";
                        echo '<a href="'.$__WEB_ROOT__.'rechercher_un_utilisateur.php" class="w3-bar-item w3-button"><i class="fa fa-search"></i> Rechercher un utilisateur</a>'."\n";
                        if($_SESSION['role']==2){
                            echo '<a href="'.$__WEB_ROOT__.'lister_tous_les_utilisateurs.php" class="w3-bar-item w3-button"><i class="fa fa-list"></i> Lister tous les utilisateurs</a>'."\n";
                            echo '<a href="'.$__WEB_ROOT__.'ajouter_un_utilisateur.php" class="w3-bar-item w3-button"><i class="fa fa-plus-circle"></i> Ajouter un utilisateur</a>'."\n";
                            echo '<a href="'.$__WEB_ROOT__.'supprimer_tous_les_utilisateurs.php" class="w3-bar-item w3-button"><i class="fa fa-trash"></i> Supprimer tous les utilisateurs</a>'."\n";
                            echo '<a href="'.$__WEB_ROOT__.'importer_des_utilisateurs.php" class="w3-bar-item w3-button"><i class="fa fa-upload"></i> Importer des utilisateurs (Export Office365)</a>'."\n";
                        }
                        echo '</div>'."\n";
                        echo '</div>'."\n";
                    }
                    if($_SESSION['role']==2){
                        echo '<div class="w3-dropdown-hover">'."\n";
                        echo '<button class="w3-button"><i class="fa fa-line-chart"></i> Gérer les graphiques <i class="fa fa-caret-down"></i></button>'."\n";
                        echo '<div class="w3-dropdown-content w3-bar-block w3-card-4">'."\n";
                        echo '<a href="'.$__WEB_ROOT__.'voir_tous_les_graphiques.php" class="w3-bar-item w3-button"><i class="fa fa-eye"></i> Voir tous les graphiques</a>'."\n";
                        echo '<a href="'.$__WEB_ROOT__.'voir_le_graphique_de_co2.php" class="w3-bar-item w3-button"><i class="fas fa-smog"></i> Voir le graphique de co2</a>'."\n";
                        echo '<a href="'.$__WEB_ROOT__.'voir_le_graphique_de_temperature.php" class="w3-bar-item w3-button"><i class="fas fa-thermometer-full"></i> Voir le graphique de température</a>'."\n";
                        echo '<a href="'.$__WEB_ROOT__.'voir_le_graphique_dhumidite.php" class="w3-bar-item w3-button"><i class="fas fa-tint"></i> Voir le graphique d\'humidité</a>'."\n";
                        echo '</div>'."\n";
                        echo '</div>'."\n";
                        echo '<div class="w3-dropdown-hover">'."\n";
                        echo '<button class="w3-button"><i class="fa fa-bell"></i> Gérer les alertes <i class="fa fa-caret-down"></i></button>'."\n";
                        echo '<div class="w3-dropdown-content w3-bar-block w3-card-4">'."\n";
                        echo '<a href="'.$__WEB_ROOT__.'lister_toutes_les_alertes.php" class="w3-bar-item w3-button"><i class="fa fa-list"></i> Lister toutes les alertes</a>'."\n";
                        echo '<a href="'.$__WEB_ROOT__.'ajouter_une_alerte.php" class="w3-bar-item w3-button"><i class="fa fa-plus-circle"></i> Ajouter une alerte</a>'."\n";
                        echo '<a href="'.$__WEB_ROOT__.'supprimer_toutes_les_alertes.php" class="w3-bar-item w3-button"><i class="fa fa-trash"></i> Supprimer toutes les alertes</a>'."\n";
                        echo '</div>'."\n";
                        echo '</div>'."\n";
                        echo '<a href="http://'.$_SERVER['DOMAIN'].':8765" class="w3-bar-item w3-button"><i class="fa fa-video-camera"></i> Voir la caméra</a>'."\n";
                    }
                    echo '<div class="w3-dropdown-hover">'."\n";
                    echo '<button class="w3-button"><i class="fa fa-user"></i> '.$_SESSION['prenom_utilisateur'].' '.$_SESSION['nom_utilisateur'].' <i class="fa fa-caret-down"></i></button>'."\n";
                    echo '<div class="w3-dropdown-content w3-bar-block w3-card-4">'."\n";
                    echo '<a href="'.$__WEB_ROOT__.'se_deconnecter.php" class="w3-bar-item w3-button"><i class="fa fa-sign-out"></i> Se déconnecter</a>'."\n";
                    echo '<a href="'.$__WEB_ROOT__.'afficher_les_details_du_compte.php" class="w3-bar-item w3-button"><i class="fa fa-eye"></i> Mon compte</a>'."\n";
                    echo '</div>'."\n";
                    echo '</div>'."\n";
                    if($_SESSION['role']==2){
                        echo '<a class="w3-button" href="'.$__WEB_ROOT__.'maintenance_mode.php"><i class="fa fa-cog"></i> Paramètres super administrateur</a>';
                        echo '</div>'."\n";
                        echo '</div>'."\n";
                    }
                }else{
                    echo '<a href="'.$__WEB_ROOT__.'se_connecter.php" class="w3-bar-item w3-button"><i class="fa fa-sign-in"></i> Se connecter</a>'."\n";
                }
            ?>
        </div>