<?php
    require_once "global.php"; //Chargement des variables globales
    session_start(); //Démarrage de la session
    if($__MAINTENANCE_MODE__){ //Si le mode de maintenance est activé
        if($_SESSION['role']<2){ //Si le rôle de l'utilisateur n'est pas super administrateur
            if(basename($_SERVER['REQUEST_URI'], '?' . $_SERVER['QUERY_STRING'])!='se_connecter.php'){ //Si l'utilisateur n'est pas sur la page de connexion
                header('Location: '.$__WEB_ROOT__.'maintenance.php'); //Redirection vers la page de maintenance
            }
        }
    }else { //Si le mode de maintenance n'est pas activé
        // do nothing
    }
    function isCookieValid(){ //Fonction qui vérifie si le cookie est valide
        require_once("dbconfig.php"); //Chargement de la configuration de la base de données
        $isValid = false; //Initialisation de la variable de retour
        if(isset($_COOKIE["rememberme"])){ //Si le cookie existe
            $decryptedCookieData = base64_decode($_COOKIE["rememberme"]); //Décodage du cookie
            $user_id = explode($_SERVER['COOKIE_SALT'],$decryptedCookieData); //Récupération de l'id de l'utilisateur
            $user_id = $user_id[1]; //Récupération de l'id de l'utilisateur
            $sql = 'SELECT utilisateurs.id, utilisateurs.nom_utilisateur, utilisateurs.prenom_utilisateur, utilisateurs.type_utilisateur, utilisateurs.role, utilisateurs.description, utilisateurs.login, utilisateurs.password_hash, utilisateurs.email, utilisateurs.telephone FROM utilisateurs WHERE utilisateurs.id = ? LIMIT 1;'; //Requête SQL
            $stmt = mysqli_prepare($conn,$sql); //Préparation de la requête
            mysqli_stmt_bind_param($stmt, 's',$user_id); //Paramètrage de la requête
            mysqli_stmt_execute($stmt); //Exécution de la requête
            $result = mysqli_stmt_get_result($stmt); //Récupération du résultat de la requête
            if (!$result) { //Si la requête a échoué
                echo '<p>Erreur dans la requête</p>'; //Affichage d'un message d'erreur
            }
            if(mysqli_num_rows($result)>0){ //Si le résultat de la requête est supérieur à 0
                while ($row = mysqli_fetch_assoc($result)){ //Pour chaque ligne du résultat de la requête
                    session_regenerate_id(); //Regénération de l'id de session
                    $_SESSION['loggedin'] = TRUE; //Définition de la variable de session loggedin
                    $_SESSION['id'] = $row["id"]; //Définition de la variable de session id
                    $_SESSION['nom_utilisateur'] = $row["nom_utilisateur"]; //Définition de la variable de session nom_utilisateur
                    $_SESSION['prenom_utilisateur'] = $row["prenom_utilisateur"]; //Définition de la variable de session prenom_utilisateur
                    $_SESSION['type_utilisateur'] = $row["type_utilisateur"]; //Définition de la variable de session type_utilisateur
                    $_SESSION['role'] = $row["role"]; //Définition de la variable de session role
                    $_SESSION['description'] = $row["description"]; //Définition de la variable de session description
                    $_SESSION['login'] = $row["login"]; //Définition de la variable de session login
                    $_SESSION['email'] = $row["email"]; //Définition de la variable de session email
                    $_SESSION['telephone'] = $row["telephone"]; //Définition de la variable de session telephone
                    $isValid = true; //Définition de la variable de retour
                }
            }else{ //Si le résultat de la requête est égal à 0
                $isValid = false; //Définition de la variable de retour
                header('Location: '.$__WEB_ROOT__.'se_deconnecter.php'); //Redirection vers la page de déconnexion
            }
            return $isValid; //Retour de la variable de retour
        }
    }
?>
<!DOCTYPE html> <!-- Déclaration du doctype -->
<html lang="fr"> <!-- Déclaration de la langue du document -->
<head> <!-- Déclaration de l'en-tête du document -->
    <meta charset="utf-8"> <!-- Déclaration du charset -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge"> <!-- Déclaration de la compatibilité avec les navigateurs -->
    <title>Salle Serveurs Saintonge - <?=$title?></title> <!-- Déclaration du titre du document -->
    <meta name="description" content="Site intranet pour la gestion de la salle serveur du lycée Sainte Famille Saintonge"> <!-- Déclaration de la description du document -->
    <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- Déclaration de la taille de la fenêtre -->
    <meta name="theme-color" content="#FFFFFF"/> <!-- Déclaration de la couleur de fond du navigateur -->
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css"> <!-- Déclaration de la feuille de style -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.5.0/css/all.min.css" integrity="sha512-QfDd74mlg8afgSqm3Vq2Q65e9b3xMhJB4GZ9OcHDVy1hZ6pqBJPWWnMsKDXM7NINoKqJANNGBuVRIpIJ5dogfA==" crossorigin="anonymous" referrerpolicy="no-referrer" /> <!-- Déclaration de la feuille de style de fontawesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fork-awesome@1.2.0/css/fork-awesome.min.css" integrity="sha256-XoaMnoYC5TH6/+ihMEnospgm0J1PM/nioxbOUdnM8HY=" crossorigin="anonymous"> <!-- Déclaration de la feuille de style de forkawesome -->
    <link rel="stylesheet" href="<?=$__WEB_ROOT__?>commons/style.css"> <!-- Déclaration de la feuille de style commune -->
    <link rel="icon" href="<?=$__WEB_ROOT__?>resources/favicon.ico" /> <!-- Déclaration de l'icône du site -->
    <link rel="apple-touch-icon" href="<?=$__WEB_ROOT__?>resources/logo_saintonge_512x512px.png"> <!-- Déclaration de l'icône du site pour les appareils mobiles -->
    <link rel="manifest" href="<?=$__WEB_ROOT__?>resources/manifest.webmanifest" /> <!-- Déclaration du manifest du site -->
    <script src="<?=$__WEB_ROOT__?>commons/script.js"></script> <!-- Déclaration du script commun -->
</head> <!-- Fin de l'en-tête -->

<body> <!-- Déclaration du corps du document -->
    <script> //Début du script
        document.addEventListener("DOMContentLoaded", function(){ //Début de la fonction
            var loc = window.location.href; //Récupération de l'url de la page
            var str = loc.split("/")[3]; //Récupération du nom de la page
            str = str.split("?")[0]; //Récupération du nom de la page sans paramètre
            str = str.split("#")[0]; //Récupération du nom de la page sans fragment
            if(!str){ //Si le nom de la page est vide
                document.querySelectorAll("a[href='<?=$__WEB_ROOT__?>index.php']")[0].classList.add('w3-light-green'); //Ajout de la classe w3-light-green au lien index
            }
            document.querySelectorAll("a[href='<?=$__WEB_ROOT__?>"+str+"']")[0].classList.add('w3-light-green'); //Ajout de la classe w3-light-green au lien correspondant
        });
    </script> <!-- Fin du script -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- Déclaration du script sweetalert2 -->
    <script type="text/javascript" src="https://www.termsfeed.com/public/cookie-consent/4.0.0/cookie-consent.js" charset="UTF-8"></script> <!-- Déclaration du script cookieconsent -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script> <!-- Déclaration du script Chart.js -->
    <script type="text/javascript" charset="UTF-8"> //Début du script
    document.addEventListener('DOMContentLoaded', function () { 
        cookieconsent.run({"notice_banner_type":"interstitial","consent_type":"express","palette":"light","language":"fr","page_load_consent_levels":["strictly-necessary"],"notice_banner_reject_button_hide":false,"preferences_center_close_button_hide":false,"page_refresh_confirmation_buttons":false,"open_preferences_center_selector":"#open_cookies_preferences","website_privacy_policy_url":"<?=$__WEB_ROOT__?>rgpd.php"}); //Définition de la configuration du script cookieconsent
    });
    </script> <!-- Fin du script -->
    <div class="w3-container"> <!-- Début du bloc principal -->
        <div class="w3-bar w3-light-grey"> <!-- Début de la barre de navigation -->
            <?php 
                isCookieValid(); //Appel de la fonction isCookieValid
                echo '<img class="w3-bar-item" src="'.$__WEB_ROOT__.'resources/logo.jpg" style="height: 38.5px;padding: unset;"/>'."\n"; //Affichage du logo
                echo '<a href="'.$__WEB_ROOT__.'index.php" class="w3-bar-item w3-border w3-button" ><i class="fa fa-home"></i> Accueil</a>'; //Affichage du lien vers la page d'accueil
                if($_SESSION['loggedin']==1){ //Si l'utilisateur est connecté
                    if($_SESSION['role']>=1){ //Si l'utilisateur est un administrateur
                        echo '<div class="w3-dropdown-hover">'."\n"; //Début de la sous-barre
                        echo '<button class="w3-button"><i class="fa fa-list"></i> Gérer les données des capteurs <i class="fa fa-caret-down"></i></button>'."\n"; //Affichage du lien vers la page de gestion des capteurs
                        echo '<div class="w3-dropdown-content w3-bar-block w3-card-4">'."\n"; //Début du menu
                        if($_SESSION['role']==2){ //Si l'utilisateur est un super administrateur
                            echo '<a href="'.$__WEB_ROOT__.'rechercher_une_donnee.php" class="w3-bar-item w3-button"><i class="fa fa-search"></i> Rechercher une donnée</a>'."\n"; //Affichage du lien vers la page de recherche d'une donnée
                            echo '<a href="'.$__WEB_ROOT__.'recapitulatif.php" class="w3-bar-item w3-button"><i class="fa fa-calendar"></i> Récapitulatif</a>'."\n"; //Affichage du lien vers la page de récapitulatif
                        }
                        echo '<a href="'.$__WEB_ROOT__.'lister_toutes_les_donnees.php" class="w3-bar-item w3-button"><i class="fa fa-list"></i> Lister toutes les donnees</a>'; //Affichage du lien vers la page de lister toutes les données
                        echo '<a href="'.$__WEB_ROOT__.'lister_les_donnees_du_jour.php" class="w3-bar-item w3-button"><i class="fa fa-list"></i> Données du jour</a>'."\n"; //Affichage du lien vers la page de lister les données du jour
                        echo '</div>'."\n"; //Fin du menu
                        echo '</div>'."\n"; //Fin de la sous-barre
                        echo '<div class="w3-dropdown-hover">'."\n"; //Début de la sous-barre
                        echo '<button class="w3-button"><i class="fa fa-users"></i> Gérer les utilisateurs <i class="fa fa-caret-down"></i></button>'."\n"; //Affichage du lien vers la page de gestion des utilisateurs
                        echo '<div class="w3-dropdown-content w3-bar-block w3-card-4">'."\n"; //Début du menu
                        echo '<a href="'.$__WEB_ROOT__.'rechercher_un_utilisateur.php" class="w3-bar-item w3-button"><i class="fa fa-search"></i> Rechercher un utilisateur</a>'."\n"; //Affichage du lien vers la page de recherche d'un utilisateur
                        if($_SESSION['role']==2){ //Si l'utilisateur est un super administrateur
                            echo '<a href="'.$__WEB_ROOT__.'lister_tous_les_utilisateurs.php" class="w3-bar-item w3-button"><i class="fa fa-list"></i> Lister tous les utilisateurs</a>'."\n"; //Affichage du lien vers la page de lister tous les utilisateurs
                            echo '<a href="'.$__WEB_ROOT__.'ajouter_un_utilisateur.php" class="w3-bar-item w3-button"><i class="fa fa-plus-circle"></i> Ajouter un utilisateur</a>'."\n"; //Affichage du lien vers la page d'ajout d'un utilisateur
                            echo '<a href="'.$__WEB_ROOT__.'supprimer_tous_les_utilisateurs.php" class="w3-bar-item w3-button"><i class="fa fa-trash"></i> Supprimer tous les utilisateurs</a>'."\n"; //Affichage du lien vers la page de suppression de tous les utilisateurs
                            echo '<a href="'.$__WEB_ROOT__.'importer_des_utilisateurs.php" class="w3-bar-item w3-button"><i class="fa fa-upload"></i> Importer des utilisateurs (Export Office365)</a>'."\n"; //Affichage du lien vers la page d'importation d'utilisateurs
                        }
                        echo '</div>'."\n"; //Fin du menu
                        echo '</div>'."\n"; //Fin de la sous-barre
                    }
                    if($_SESSION['role']==2){ //Si l'utilisateur est un super administrateur
                        echo '<div class="w3-dropdown-hover">'."\n"; //Début de la sous-barre
                        echo '<button class="w3-button"><i class="fa fa-line-chart"></i> Gérer les graphiques <i class="fa fa-caret-down"></i></button>'."\n"; //Affichage du lien vers la page de gestion des graphiques
                        echo '<div class="w3-dropdown-content w3-bar-block w3-card-4">'."\n"; //Début du menu
                        echo '<a href="'.$__WEB_ROOT__.'voir_tous_les_graphiques.php" class="w3-bar-item w3-button"><i class="fa fa-eye"></i> Voir tous les graphiques</a>'."\n"; //Affichage du lien vers la page de visualisation de tous les graphiques
                        echo '<a href="'.$__WEB_ROOT__.'voir_le_graphique_de_co2.php" class="w3-bar-item w3-button"><i class="fas fa-smog"></i> Voir le graphique de co2</a>'."\n"; //Affichage du lien vers la page de visualisation du graphique de co2
                        echo '<a href="'.$__WEB_ROOT__.'voir_le_graphique_de_temperature.php" class="w3-bar-item w3-button"><i class="fas fa-thermometer-full"></i> Voir le graphique de température</a>'."\n"; //Affichage du lien vers la page de visualisation du graphique de température
                        echo '<a href="'.$__WEB_ROOT__.'voir_le_graphique_dhumidite.php" class="w3-bar-item w3-button"><i class="fas fa-tint"></i> Voir le graphique d\'humidité</a>'."\n";
                        echo '</div>'."\n"; //Fin du menu
                        echo '</div>'."\n"; //Fin de la sous-barre
                        echo '<div class="w3-dropdown-hover">'."\n"; //Début de la sous-barre
                        echo '<button class="w3-button"><i class="fa fa-bell"></i> Gérer les alertes <i class="fa fa-caret-down"></i></button>'."\n"; //Affichage du lien vers la page de gestion des alertes
                        echo '<div class="w3-dropdown-content w3-bar-block w3-card-4">'."\n"; //Début du menu
                        echo '<a href="'.$__WEB_ROOT__.'lister_toutes_les_alertes.php" class="w3-bar-item w3-button"><i class="fa fa-list"></i> Lister toutes les alertes</a>'."\n"; //Affichage du lien vers la page de lister toutes les alertes
                        echo '<a href="'.$__WEB_ROOT__.'ajouter_une_alerte.php" class="w3-bar-item w3-button"><i class="fa fa-plus-circle"></i> Ajouter une alerte</a>'."\n"; //Affichage du lien vers la page d'ajout d'une alerte
                        echo '<a href="'.$__WEB_ROOT__.'supprimer_toutes_les_alertes.php" class="w3-bar-item w3-button"><i class="fa fa-trash"></i> Supprimer toutes les alertes</a>'."\n"; //Affichage du lien vers la page de suppression de toutes les alertes
                        echo '</div>'."\n"; //Fin du menu
                        echo '</div>'."\n"; //Fin de la sous-barre
                        echo '<a href="http://'.$_SERVER['DOMAIN'].':8765" class="w3-bar-item w3-button"><i class="fa fa-video-camera"></i> Voir la caméra</a>'."\n"; //Affichage du lien vers la page de visualisation de la caméra
                    }
                    echo '<div class="w3-dropdown-hover">'."\n"; //Début de la sous-barre
                    echo '<button class="w3-button"><i class="fa fa-user"></i> '.$_SESSION['prenom_utilisateur'].' '.$_SESSION['nom_utilisateur'].' <i class="fa fa-caret-down"></i></button>'."\n"; //Affichage du lien vers la page de gestion des alertes
                    echo '<div class="w3-dropdown-content w3-bar-block w3-card-4">'."\n"; //Début du menu
                    echo '<a href="'.$__WEB_ROOT__.'se_deconnecter.php" class="w3-bar-item w3-button"><i class="fa fa-sign-out"></i> Se déconnecter</a>'."\n"; //Affichage du lien vers la page de déconnexion
                    echo '<a href="'.$__WEB_ROOT__.'afficher_les_details_du_compte.php" class="w3-bar-item w3-button"><i class="fa fa-eye"></i> Mon compte</a>'."\n"; //Affichage du lien vers la page de visualisation de ses informations
                    echo '</div>'."\n"; //Fin du menu
                    echo '</div>'."\n"; //Fin de la sous-barre
                    if($_SESSION['role']==2){ //Si l'utilisateur est un super administrateur
                        echo '<a class="w3-button" href="'.$__WEB_ROOT__.'maintenance_mode.php"><i class="fa fa-cog"></i> Paramètres super administrateur</a>'; //Affichage du lien vers la page de paramètres du super administrateur
                        echo '</div>'."\n"; //Fin de la barre
                        echo '</div>'."\n"; //Fin de la barre
                    }
                }else{ //Si l'utilisateur n'est pas connecté
                    echo '<a href="'.$__WEB_ROOT__.'se_connecter.php" class="w3-bar-item w3-button"><i class="fa fa-sign-in"></i> Se connecter</a>'."\n"; //Affichage du lien vers la page de connexion
                }
            ?>
        </div>