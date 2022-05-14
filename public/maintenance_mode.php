<?php
    $title = "Gérer le mode de maintenance"; // Titre de la page
    require_once "commons/header.php"; // Inclusion du header
    $file = 'commons/maintenance.status'; // Chemin du fichier de status
    if(isset($_SESSION)&&$_SESSION['role']>=2){ //si super administrateur
        if(isset($_GET['action'])){ //si une action est spécifiée
            $sanitized_action = filter_var($_GET['action'],FILTER_SANITIZE_NUMBER_INT); //on sécurise l'action
            if($sanitized_action==1){ //si activation du mode maintenance
                if(file_put_contents($file,1)){ //si le mode de maintenance a bien été activé
                    echo '<h1><img src="resources/good.png" width="5%" height="auto"/> Bravo, le mode de maintenance du site a bien été activé, à partir de maintenant tous les utilisateurs du site ne pourront plus y accéder !</h1>'; //message de confirmation
                    echo '<br/><h2>Pour se connecter depuis la page de maintenance, merci d\'utiliser la combinaison de touches "Ctrl+Alt+Espace" qui fera apparaitre le bouton "Se connecter"</h2>'; //message de confirmation
                    echo '<br/><h2>Seul les utilisateurs super administrateurs peuvent se connecter au site pendant qu\'il est en maintenance</h2>'; //message de confirmation
                }
            }else{ //si désactivation du mode maintenance
                if(file_put_contents($file,0)){ //si le mode maintenance a bien été désactivé
                    echo '<h1><img src="resources/good.png" width="5%" height="auto"/> Bravo, le mode de maintenance du site a bien été désactivé, à partir de maintenant tous les utilisateurs du site peuvent de nouveau y accéder !</h1>'; //message de confirmation
                    echo '<br/><h2>Les utilisateurs sur la page de maintenance seront redirigés vers la page d\'accueil lors du prochain rafraichissement de la page</h2>'; //message de confirmation
                }
            }
        }else{ //si aucune action spécifiée
            echo '<br/>'; //saut de ligne
            if($__MAINTENANCE_MODE__){ //si le mode maintenance est activé
                echo '<a class="w3-button w3-green" href="#" onclick="javascript:changer_mode_maintenance(0);"><i class="fa fa-power-off"></i> Désactiver la maintenance du site</a>'; //bouton d'activation du mode maintenance
            }else{ //si le mode maintenance est désactivé
                echo '<a class="w3-button w3-red" href="#" onclick="javascript:changer_mode_maintenance(1);"><i class="fa fa-power-off"></i> Activer la maintenance du site</a>'; //bouton de désactivation du mode maintenance
            }
            echo '<script>'; //début du script
                echo 'function changer_mode_maintenance(mode){'; //fonction de changement de mode
                echo 'if(mode == 0){'; //si on veut désactiver le mode maintenance
                echo 'confirmation = window.confirm(\'Etes vous sûr de vouloir désactiver le mode maintenance ?\');'; //on demande confirmation
                echo 'if(confirmation){'; //si on confirme
                echo 'document.location.href = "'.htmlspecialchars($_SERVER['PHP_SELF']).'?action=0"'; //on redirige vers la page courante avec l'action spécifiée
                echo '}';
                echo '}else if(mode == 1){'; //si on veut activer le mode maintenance
                echo 'confirmation = window.confirm(\'Etes vous sûr de vouloir activer le mode maintenance ?\');'; //on demande confirmation
                echo 'if(confirmation){'; //si on confirme
                echo 'document.location.href = "'.htmlspecialchars($_SERVER['PHP_SELF']).'?action=1"'; //on redirige vers la page courante avec l'action spécifiée
                echo '}';
                echo '}else{'; //si on ne spécifie pas d'action
                    //invalide ne rien faire
                echo '}';
                echo '}';
                echo '</script>'; //fin du script
        }
        require_once "commons/footer.php"; // Inclusion du footer
    }else{ //si l'utilisateur n'est pas connecté ou n'est pas super administrateur
        echo '<h1><img src="resources/danger.png" width="5%" height="auto" />Vous n\'avez pas l\'autorisation d\'accéder à cette page !</h1>'; //message d'erreur
        require_once "commons/footer.php"; // Inclusion du footer
        die(); //arret du script
    }
?>