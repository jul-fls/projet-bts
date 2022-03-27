<?php
    $title = "Gérer le mode de maintenance";
    require_once "commons/header.php";
    $file = 'commons/maintenance.status';
    if(isset($_SESSION)&&$_SESSION['role']>=2){ //si super administrateur
        if(isset($_GET['action'])){ //si une action est spécifiée
            $sanitized_action = filter_var($_GET['action'],FILTER_SANITIZE_NUMBER_INT);
            if($sanitized_action==1){ //si activation du mode maintenance
                if(file_put_contents($file,1)){ //si le mode de maintenance a bien été activé
                    echo '<h1><img src="resources/good.png" width="5%" height="auto"/> Bravo, le mode de maintenance du site a bien été activé, à partir de maintenant tous les utilisateurs du site ne pourront plus y accéder !</h1>';
                    echo '<br/><h2>Pour se connecter depuis la page de maintenance, merci d\'utiliser la combinaison de touches "Ctrl+Alt+Espace" qui fera apparaitre le bouton "Se connecter"</h2>';
                    echo '<br/><h2>Seul les utilisateurs super administrateurs peuvent se connecter au site pendant qu\'il est en maintenance</h2>';
                }
            }else{ //si désactivation du mode maintenance
                if(file_put_contents($file,0)){ //si le mode maintenance a bien été désactivé
                    echo '<h1><img src="resources/good.png" width="5%" height="auto"/> Bravo, le mode de maintenance du site a bien été désactivé, à partir de maintenant tous les utilisateurs du site peuvent de nouveau y accéder !</h1>';
                    echo '<br/><h2>Les utilisateurs sur la page de maintenance seront redirigés vers la page d\'accueil lors du prochain rafraichissement de la page</h2>';
                }
            }
        }else{
            echo '<br/>';
            if($__MAINTENANCE_MODE__){
                echo '<a class="w3-button w3-green" href="#" onclick="javascript:changer_mode_maintenance(0);"><i class="fa fa-power-off"></i> Désactiver la maintenance du site</a>';
            }else{
                echo '<a class="w3-button w3-red" href="#" onclick="javascript:changer_mode_maintenance(1);"><i class="fa fa-power-off"></i> Activer la maintenance du site</a>';
            }
            echo '<script>';
                echo 'function changer_mode_maintenance(mode){';
                echo 'if(mode == 0){';
                echo 'confirmation = window.confirm(\'Etes vous sûr de vouloir désactiver le mode maintenance ?\');';
                echo 'if(confirmation){';
                echo 'document.location.href = "'.htmlspecialchars($_SERVER['PHP_SELF']).'?action=0"';
                echo '}';
                echo '}else if(mode == 1){';
                echo 'confirmation = window.confirm(\'Etes vous sûr de vouloir activer le mode maintenance ?\');';
                echo 'if(confirmation){';
                echo 'document.location.href = "'.htmlspecialchars($_SERVER['PHP_SELF']).'?action=1"';
                echo '}';
                echo '}else{';
                    //invalide ne rien faire
                echo '}';
                echo '}';
                echo '</script>';
        }
        require_once "commons/footer.php";
    }else{
        echo '<h1><img src="resources/danger.png" width="5%" height="auto" />Vous n\'avez pas l\'autorisation d\'accéder à cette page !</h1>';
        require_once "commons/footer.php";
        die();
    }
?>