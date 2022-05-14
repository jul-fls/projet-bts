<?php
    require_once "public/commons/global.php"; //Chargement des variables d'environnement
    require_once "public/commons/dbconfig.php"; //Chargement de la configuration de la base de données
    $buzzer_pin = 0; //Définition du buzzer
    $co2; //Définition de la variable co2
    $temp; //Définition de la variable temp
    $hum; //Définition de la variable hum
    $email_to = []; //Définition de la variable email_to
    $email_to[] = "j.flusin@lyceesaintefamille.com"; //Ajout de l'adresse mail de l'administrateur
    $sql0 = "SELECT * FROM donnees_capteurs ORDER BY id DESC LIMIT 1;"; //Requête SQL
    $stmt0 = mysqli_prepare($conn, $sql0); //Préparation de la requête SQL
    mysqli_stmt_execute($stmt0); //Exécution de la requête SQL
    $result0 = mysqli_stmt_get_result($stmt0); //Récupération du résultat de la requête SQL
    if(mysqli_num_rows($result0)>0){ //Si le nombre de lignes est supérieur à 0
        while ($rowA = mysqli_fetch_assoc($result0)){ //Tant qu'il y a des lignes
            $co2 = $rowA['co2']; //Récupération de la valeur de co2
            $temp = $rowA['temp']; //Récupération de la valeur de temp
            $hum = $rowA['hum']; //Récupération de la valeur de hum
        } 
    }
    function writebuzzerstate($pin, $state){ //Fonction qui écrit le statut du buzzer
        $pin = $pin; //Définition de la variable pin
        $state = $state; //Définition de la variable state
        exec("gpio mode $pin out"); //Définition du mode de la pin
        if($state == 1){ //Si le statut est 1
            exec("gpio write $pin 1"); //Ecriture de la valeur 1
            sleep(3); //Attente de 3 secondes
            exec("gpio write $pin 0"); //Ecriture de la valeur 0
        } 
        else{ //Sinon
            exec("gpio write $pin 0"); //Ecriture de la valeur 0
        }
    }
    $sql = "SELECT * FROM alertes WHERE alertes.active = 1"; //Requête SQL
    $stmt = mysqli_prepare($conn, $sql); //Préparation de la requête SQL
    mysqli_stmt_execute($stmt); //Exécution de la requête SQL
    $result = mysqli_stmt_get_result($stmt); //Récupération du résultat de la requête SQL
    if(mysqli_num_rows($result)>0){ //Si le nombre de lignes est supérieur à 0
        //desactiver l'alerte 
        writebuzzerstate($buzzer_pin, 0); //Désactivation du buzzer
        while ($row = mysqli_fetch_assoc($result)){ //Tant qu'il y a des lignes
            //if co2 is higher than co2 alerte
            if($row['type_de_donnees']==0){ //if co2
                if($row['type_dalerte']==1){ //if alerte is for less than value
                    if($row['valeur_de_declenchement']>$co2){ //if co2 is lower than co2 alerte
                        //then send alert that co2 is lower than co2 alerte
                        $output = '<p>Bonjour,</p>'; //Définition de la variable output
                        $output.= '<p>Le taux de Co2 actuel ('.$co2.') est inférieur à '.$row['valeur_de_declenchement'].' Ppm, l\'alerte '.html_entity_decode($row['nom'],ENT_QUOTES).' a donc été déclenchéee.</p>'; //Définition de la variable output
                        $output.= '<p>Pour votre information, vous avez la possibilité de consulter les données de la salle serveur sur <a href="'.$__WEB_ROOT__.'">'.$__WEB_ROOT__.'</a> en vous connectant à votre compte</p>'; //Définition de la variable output
                        $output.= '<p>Cordialement</p>'; //Définition de la variable output
                        $output.= '<p>L\'équipe Informatique du Lycée Sainte Famille Saintonge</p>'; //Définition de la variable output
                        $output.= '<img src="'.$__WEB_ROOT__.'resources/logo.jpg"/>'; //Définition de la variable output
                        $output.= '<br/>'; //Définition de la variable output
                        $output.= '<p>Merci de ne pas répondre, cette adresse e-mail est gérée par un programme informatique.</p>'; //Définition de la variable output
                        $output.= '<p>Pour contacter l\'équipe Informatique du Lycée Sainte Famille Saintonge, merci d\'envoyer un email à <a href="mailto:informatique@lyceesaintefamille.com">informatique@lyceesaintefamille.com</a></p>'; //Définition de la variable output
			    		$body = $output; //Définition de la variable body
			    		$subject = 'Alerte Co2 ('.html_entity_decode($row['nom'],ENT_QUOTES).') a été déclenchée'; //Définition de la variable subject
                        require "public/commons/mailconfig.php"; //Chargement de la configuration de l'envoi de mail
                        if (!$mail->Send()){ //impossible d'envoyer le mail 
			    			echo "Mailer Error: " . $mail->ErrorInfo."\n"; //Affichage de l'erreur
			    		}else{ //envoi du mail effectué avec succès 
			    			echo "Le message a bien été envoyé.\n"; //Affichage de la confirmation
                        }
                        writebuzzerstate($buzzer_pin, 1); //Activation du buzzer
                    }
                }else if($row['type_dalerte']==0){ //if alerte is for more than value
                    if($row['valeur_de_declenchement']<$co2){ //if co2 is higher than co2 alerte
                        //send that co2 is higher than co2 alerte
                        $output = '<p>Bonjour,</p>'; //Définition de la variable output
                        $output.= '<p>Le taux de Co2 actuel ('.$co2.') est supéieur à '.$row['valeur_de_declenchement'].' Ppm, l\'alerte '.html_entity_decode($row['nom'],ENT_QUOTES).' a donc été déclenchée.</p>'; //Définition de la variable output
                        $output.= '<p>Pour votre information, vous avez la possibilité de consulter les données de la salle serveur sur <a href="'.$__WEB_ROOT__.'">'.$__WEB_ROOT__.'</a> en vous connectant à votre compte</p>'; //Définition de la variable output
                        $output.= '<p>Cordialement</p>'; //Définition de la variable output
                        $output.= '<p>L\'équipe Informatique du Lycée Sainte Famille Saintonge</p>'; //Définition de la variable output
                        $output.= '<img src="'.$__WEB_ROOT__.'resources/logo.jpg"/>'; //Définition de la variable output
                        $output.= '<br/>'; //Définition de la variable output
                        $output.= '<p>Merci de ne pas répondre, cette adresse e-mail est gérée par un programme informatique.</p>'; //Définition de la variable output
                        $output.= '<p>Pour contacter l\'équipe Informatique du Lycée Sainte Famille Saintonge, merci d\'envoyer un email à <a href="mailto:informatique@lyceesaintefamille.com">informatique@lyceesaintefamille.com</a></p>'; //Définition de la variable output
			    		$body = $output; //Définition de la variable body
			    		$subject = 'L\'alerte Co2 ('.html_entity_decode($row['nom'],ENT_QUOTES).') a été déclenchée'; //Définition de la variable subject
                        require "public/commons/mailconfig.php"; //Chargement de la configuration de l'envoi de mail
                        if (!$mail->Send()){ //impossible d'envoyer le mail
			    			echo "Mailer Error: " . $mail->ErrorInfo."\n"; //Affichage de l'erreur
			    		}else{ //envoi du mail effectué avec succès
			    			echo "Le message a bien été envoyé.\n"; //Affichage de la confirmation
                        }
                        writebuzzerstate($buzzer_pin, 1); //Activation du buzzer
                    }
                }
            }else if($row['type_de_donnees']==1){ //if temperature
                if($row['type_dalerte']==1){ //if alerte is for less than value
                    if($row['valeur_de_declenchement']>$temp){ //if temperature is lower than temperature alerte
                        //send that temperature is lower than temperature alerte
                        $output = '<p>Bonjour,</p>'; //Définition de la variable output
                        $output.= '<p>La température actuelle ('.$temp.') est inférieure à '.$row['valeur_de_declenchement'].' °C, l\'alerte '.html_entity_decode($row['nom'],ENT_QUOTES).' a donc été déclenchée.</p>'; //Définition de la variable output
                        $output.= '<p>Pour votre information, vous avez la possibilité de consulter les données de la salle serveur sur <a href="'.$__WEB_ROOT__.'">'.$__WEB_ROOT__.'</a> en vous connectant à votre compte</p>'; //Définition de la variable output
                        $output.= '<p>Cordialement</p>'; //Définition de la variable output
                        $output.= '<p>L\'équipe Informatique du Lycée Sainte Famille Saintonge</p>'; //Définition de la variable output
                        $output.= '<img src="'.$__WEB_ROOT__.'resources/logo.jpg"/>'; //Définition de la variable output
                        $output.= '<br/>'; //Définition de la variable output
                        $output.= '<p>Merci de ne pas répondre, cette adresse e-mail est gérée par un programme informatique.</p>'; //Définition de la variable output
                        $output.= '<p>Pour contacter l\'équipe Informatique du Lycée Sainte Famille Saintonge, merci d\'envoyer un email à <a href="mailto:informatique@lyceesaintefamille.com">informatique@lyceesaintefamille.com</a></p>'; //Définition de la variable output
			    		$body = $output; //Définition de la variable body
			    		$subject = 'L\'alerte Température ('.html_entity_decode($row['nom'],ENT_QUOTES).') a été déclenchée'; //Définition de la variable subject
                        require "public/commons/mailconfig.php"; //Chargement de la configuration de l'envoi de mail
                        if (!$mail->Send()){ //impossible d'envoyer le mail 
			    			echo "Mailer Error: " . $mail->ErrorInfo."\n"; //Affichage de l'erreur
			    		}else{ //envoi du mail effectué avec succès 
			    			echo "Le message a bien été envoyé.\n"; //Affichage de la confirmation
                        }
                        writebuzzerstate($buzzer_pin, 1); //Activation du buzzer
                    }
                }else if($row['type_dalerte']==0){ //if alerte is for more than value
                    if($row['valeur_de_declenchement']<$temp){ //if temperature is higher than temperature alerte
                        //send that temperature is higher than temperature alerte
                        $output = '<p>Bonjour,</p>'; //Définition de la variable output
                        $output.= '<p>La température actuelle ('.$temp.') est supérieure à '.$row['valeur_de_declenchement'].'°C, l\'alerte '.html_entity_decode($row['nom'],ENT_QUOTES).' a donc été déclenchée.</p>'; //Définition de la variable output
                        $output.= '<p>Pour votre information, vous avez la possibilité de consulter les données de la salle serveur sur <a href="'.$__WEB_ROOT__.'">'.$__WEB_ROOT__.'</a> en vous connectant à votre compte</p>'; //Définition de la variable output
                        $output.= '<p>Cordialement</p>'; //Définition de la variable output
                        $output.= '<p>L\'équipe Informatique du Lycée Sainte Famille Saintonge</p>'; //Définition de la variable output
                        $output.= '<img src="'.$__WEB_ROOT__.'resources/logo.jpg"/>'; //Définition de la variable output
                        $output.= '<br/>'; //Définition de la variable output
                        $output.= '<p>Merci de ne pas répondre, cette adresse e-mail est gérée par un programme informatique.</p>'; //Définition de la variable output
                        $output.= '<p>Pour contacter l\'équipe Informatique du Lycée Sainte Famille Saintonge, merci d\'envoyer un email à <a href="mailto:informatique@lyceesaintefamille.com">informatique@lyceesaintefamille.com</a></p>'; //Définition de la variable output
			    		$body = $output; //Définition de la variable body
			    		$subject = 'L\'alerte Température ('.html_entity_decode($row['nom'],ENT_QUOTES).') a été déclenchée'; //Définition de la variable subject
                        require "public/commons/mailconfig.php"; //Chargement de la configuration de l'envoi de mail
                        if (!$mail->Send()){ //impossible d'envoyer le mail
			    			echo "Mailer Error: " . $mail->ErrorInfo."\n"; //Affichage de l'erreur
			    		}else{ //envoi du mail effectué avec succès 
			    			echo "Le message a bien été envoyé.\n"; //Affichage de la confirmation
                        }
                        writebuzzerstate($buzzer_pin, 1); //Activation du buzzer
                    }
                }
            }else if($row['type_de_donnees']==2){ //if humidity
                if($row['type_dalerte']==1){ //if alerte is for less than value
                    if($row['valeur_de_declenchement']>$hum){ //if humidity is lower than humidity alerte
                        //send that humidity is lower than humidity alerte
                        $output = '<p>Bonjour,</p>'; //Définition de la variable output
                        $output.= '<p>Le taux d\'humidité actuel ('.$hum.') est inférieur à '.$row['valeur_de_declenchement'].' %HR, l\'alerte '.html_entity_decode($row['nom'],ENT_QUOTES).' a donc été déclenchée.</p>'; //Définition de la variable output
                        $output.= '<p>Pour votre information, vous avez la possibilité de consulter les données de la salle serveur sur <a href="'.$__WEB_ROOT__.'">'.$__WEB_ROOT__.'</a> en vous connectant à votre compte</p>'; //Définition de la variable output
                        $output.= '<p>Cordialement</p>'; //Définition de la variable output
                        $output.= '<p>L\'équipe Informatique du Lycée Sainte Famille Saintonge</p>'; //Définition de la variable output
                        $output.= '<img src="'.$__WEB_ROOT__.'resources/logo.jpg"/>'; //Définition de la variable output
                        $output.= '<br/>'; //Définition de la variable output
                        $output.= '<p>Merci de ne pas répondre, cette adresse e-mail est gérée par un programme informatique.</p>'; //Définition de la variable output
                        $output.= '<p>Pour contacter l\'équipe Informatique du Lycée Sainte Famille Saintonge, merci d\'envoyer un email à <a href="mailto:informatique@lyceesaintefamille.com">informatique@lyceesaintefamille.com</a></p>'; //Définition de la variable output
			    		$body = $output; //Définition de la variable body
			    		$subject = 'L\'alerte Humidité ('.html_entity_decode($row['nom'],ENT_QUOTES).') a été déclenchée'; //Définition de la variable subject
                        require "public/commons/mailconfig.php"; //Chargement de la configuration de l'envoi de mail
                        if (!$mail->Send()){ //impossible d'envoyer le mail
			    			echo "Mailer Error: " . $mail->ErrorInfo."\n"; //Affichage de l'erreur
			    		}else{ //envoi du mail effectué avec succès
			    			echo "Le message a bien été envoyé.\n"; //Affichage de la confirmation
                        } 
                        writebuzzerstate($buzzer_pin, 1); //Activation du buzzer
                    }
                }else if($row['type_dalerte']==0){ //if alerte is for more than value
                    if($row['valeur_de_declenchement']<$hum){ //if humidity is higher than humidity alerte
                        //send that humidity is higher than humidity alerte
                        $output = '<p>Bonjour,</p>'; //Définition de la variable output
                        $output.= '<p>Le taux d\'humidité actuel ('.$hum.') est supérieur à '.$row['valeur_de_declenchement'].' %HR, l\'alerte '.html_entity_decode($row['nom'],ENT_QUOTES).' a donc été déclenchée.</p>'; //Définition de la variable output
                        $output.= '<p>Pour votre information, vous avez la possibilité de consulter les données de la salle serveur sur <a href="'.$__WEB_ROOT__.'">'.$__WEB_ROOT__.'</a> en vous connectant à votre compte</p>'; //Définition de la variable output
                        $output.= '<p>Cordialement</p>'; //Définition de la variable output
                        $output.= '<p>L\'équipe Informatique du Lycée Sainte Famille Saintonge</p>'; //Définition de la variable output
                        $output.= '<img src="'.$__WEB_ROOT__.'resources/logo.jpg"/>'; //Définition de la variable output
                        $output.= '<br/>'; //Définition de la variable output
                        $output.= '<p>Merci de ne pas répondre, cette adresse e-mail est gérée par un programme informatique.</p>'; //Définition de la variable output
                        $output.= '<p>Pour contacter l\'équipe Informatique du Lycée Sainte Famille Saintonge, merci d\'envoyer un email à <a href="mailto:informatique@lyceesaintefamille.com">informatique@lyceesaintefamille.com</a></p>'; //Définition de la variable output
			    		$body = $output; //Définition de la variable body
			    		$subject = 'L\'alerte Humidité ('.html_entity_decode($row['nom'],ENT_QUOTES).') a été déclenchée'; //Définition de la variable subject
                        require "public/commons/mailconfig.php"; //Chargement de la configuration de l'envoi de mail
                        if (!$mail->Send()){ //impossible d'envoyer le mail 
			    			echo "Mailer Error: " . $mail->ErrorInfo."\n"; //Affichage de l'erreur
			    		}else{ //envoi du mail effectué avec succès
			    			echo "Le message a bien été envoyé.\n"; //Affichage de la confirmation
                        }
                        writebuzzerstate($buzzer_pin, 1); //Activation du buzzer
                    }
                }
            }
        }
    }
?>