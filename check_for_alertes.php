<?php
    require_once "public/commons/global.php";
    require_once "public/commons/dbconfig.php";
    $buzzer_pin = 0;
    $co2;
    $temp;
    $hum;
    $email_to = [];
    $email_to[] = "j.flusin@lyceesaintefamille.com";
    $sql0 = "SELECT * FROM donnees_capteurs ORDER BY id DESC LIMIT 1;";
    $stmt0 = mysqli_prepare($conn, $sql0);
    mysqli_stmt_execute($stmt0);
    $result0 = mysqli_stmt_get_result($stmt0);
    if(mysqli_num_rows($result0)>0){
        while ($rowA = mysqli_fetch_assoc($result0)){
            $co2 = $rowA['co2'];
            $temp = $rowA['temp'];
            $hum = $rowA['hum'];
        }
    }
    function writebuzzerstate($pin, $state){
        $pin = $pin;
        $state = $state;
        exec("gpio mode $pin out");
        if($state == 1){
            exec("gpio write $pin 1");
            sleep(3);
            exec("gpio write $pin 0");
        }
        else{
            exec("gpio write $pin 0");
        }
    }
    $sql = "SELECT * FROM alertes WHERE alertes.active = 1";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if(mysqli_num_rows($result)>0){
        //desactiver l'alerte
        writebuzzerstate($buzzer_pin, 0);
        while ($row = mysqli_fetch_assoc($result)){
            //if co2 is higher than co2 alerte
            if($row['type_de_donnees']==0){ //if co2
                if($row['type_dalerte']==1){ //if alerte is for less than value
                    if($row['valeur_de_declenchement']>$co2){ //if co2 is lower than co2 alerte
                        //then send alert that co2 is lower than co2 alerte
                        $output = '<p>Bonjour,</p>';
                        $output.= '<p>Le taux de Co2 actuel ('.$co2.') est inférieur à '.$row['valeur_de_declenchement'].' Ppm, l\'alerte '.html_entity_decode($row['nom'],ENT_QUOTES).' a donc été déclenchéee.</p>';
                        $output.= '<p>Pour votre information, vous avez la possibilité de consulter les données de la salle serveur sur <a href="'.$__WEB_ROOT__.'">'.$__WEB_ROOT__.'</a> en vous connectant à votre compte</p>';
                        $output.= '<p>Cordialement</p>';
                        $output.= '<p>L\'équipe Informatique du Lycée Sainte Famille Saintonge</p>';
                        $output.= '<img src="'.$__WEB_ROOT__.'resources/logo.jpg"/>';
                        $output.= '<br/>';
                        $output.= '<p>Merci de ne pas répondre, cette adresse e-mail est gérée par un programme informatique.</p>';
                        $output.= '<p>Pour contacter l\'équipe Informatique du Lycée Sainte Famille Saintonge, merci d\'envoyer un email à <a href="mailto:informatique@lyceesaintefamille.com">informatique@lyceesaintefamille.com</a></p>';
			    		$body = $output;
			    		$subject = 'Alerte Co2 ('.html_entity_decode($row['nom'],ENT_QUOTES).') a été déclenchée';
                        require "public/commons/mailconfig.php";
                        if (!$mail->Send()){ //impossible d'envoyer le mail
			    			echo "Mailer Error: " . $mail->ErrorInfo."\n";
			    		}else{ //envoi du mail effectué avec succès
			    			echo "Le message a bien été envoyé.\n";
                        }
                        writebuzzerstate($buzzer_pin, 1);
                    }
                }else if($row['type_dalerte']==0){ //if alerte is for more than value
                    if($row['valeur_de_declenchement']<$co2){ //if co2 is higher than co2 alerte
                        //send that co2 is higher than co2 alerte
                        $output = '<p>Bonjour,</p>';
                        $output.= '<p>Le taux de Co2 actuel ('.$co2.') est supéieur à '.$row['valeur_de_declenchement'].' Ppm, l\'alerte '.html_entity_decode($row['nom'],ENT_QUOTES).' a donc été déclenchée.</p>';
                        $output.= '<p>Pour votre information, vous avez la possibilité de consulter les données de la salle serveur sur <a href="'.$__WEB_ROOT__.'">'.$__WEB_ROOT__.'</a> en vous connectant à votre compte</p>';
                        $output.= '<p>Cordialement</p>';
                        $output.= '<p>L\'équipe Informatique du Lycée Sainte Famille Saintonge</p>';
                        $output.= '<img src="'.$__WEB_ROOT__.'resources/logo.jpg"/>';
                        $output.= '<br/>';
                        $output.= '<p>Merci de ne pas répondre, cette adresse e-mail est gérée par un programme informatique.</p>';
                        $output.= '<p>Pour contacter l\'équipe Informatique du Lycée Sainte Famille Saintonge, merci d\'envoyer un email à <a href="mailto:informatique@lyceesaintefamille.com">informatique@lyceesaintefamille.com</a></p>';
			    		$body = $output;
			    		$subject = 'L\'alerte Co2 ('.html_entity_decode($row['nom'],ENT_QUOTES).') a été déclenchée';
                        require "public/commons/mailconfig.php";
                        if (!$mail->Send()){ //impossible d'envoyer le mail
			    			echo "Mailer Error: " . $mail->ErrorInfo."\n";
			    		}else{ //envoi du mail effectué avec succès
			    			echo "Le message a bien été envoyé.\n";
                        }
                        writebuzzerstate($buzzer_pin, 1);
                    }
                }
            }else if($row['type_de_donnees']==1){ //if temperature
                if($row['type_dalerte']==1){ //if alerte is for less than value
                    if($row['valeur_de_declenchement']>$temp){ //if temperature is lower than temperature alerte
                        //send that temperature is lower than temperature alerte
                        $output = '<p>Bonjour,</p>';
                        $output.= '<p>La température actuelle ('.$temp.') est inférieure à '.$row['valeur_de_declenchement'].' °C, l\'alerte '.html_entity_decode($row['nom'],ENT_QUOTES).' a donc été déclenchée.</p>';
                        $output.= '<p>Pour votre information, vous avez la possibilité de consulter les données de la salle serveur sur <a href="'.$__WEB_ROOT__.'">'.$__WEB_ROOT__.'</a> en vous connectant à votre compte</p>';
                        $output.= '<p>Cordialement</p>';
                        $output.= '<p>L\'équipe Informatique du Lycée Sainte Famille Saintonge</p>';
                        $output.= '<img src="'.$__WEB_ROOT__.'resources/logo.jpg"/>';
                        $output.= '<br/>';
                        $output.= '<p>Merci de ne pas répondre, cette adresse e-mail est gérée par un programme informatique.</p>';
                        $output.= '<p>Pour contacter l\'équipe Informatique du Lycée Sainte Famille Saintonge, merci d\'envoyer un email à <a href="mailto:informatique@lyceesaintefamille.com">informatique@lyceesaintefamille.com</a></p>';
			    		$body = $output;
			    		$subject = 'L\'alerte Température ('.html_entity_decode($row['nom'],ENT_QUOTES).') a été déclenchée';
                        require "public/commons/mailconfig.php";
                        if (!$mail->Send()){ //impossible d'envoyer le mail
			    			echo "Mailer Error: " . $mail->ErrorInfo."\n";
			    		}else{ //envoi du mail effectué avec succès
			    			echo "Le message a bien été envoyé.\n";
                        }
                        writebuzzerstate($buzzer_pin, 1);
                    }
                }else if($row['type_dalerte']==0){ //if alerte is for more than value
                    if($row['valeur_de_declenchement']<$temp){ //if temperature is higher than temperature alerte
                        //send that temperature is higher than temperature alerte
                        $output = '<p>Bonjour,</p>';
                        $output.= '<p>La température actuelle ('.$temp.') est supérieure à '.$row['valeur_de_declenchement'].'°C, l\'alerte '.html_entity_decode($row['nom'],ENT_QUOTES).' a donc été déclenchée.</p>';
                        $output.= '<p>Pour votre information, vous avez la possibilité de consulter les données de la salle serveur sur <a href="'.$__WEB_ROOT__.'">'.$__WEB_ROOT__.'</a> en vous connectant à votre compte</p>';
                        $output.= '<p>Cordialement</p>';
                        $output.= '<p>L\'équipe Informatique du Lycée Sainte Famille Saintonge</p>';
                        $output.= '<img src="'.$__WEB_ROOT__.'resources/logo.jpg"/>';
                        $output.= '<br/>';
                        $output.= '<p>Merci de ne pas répondre, cette adresse e-mail est gérée par un programme informatique.</p>';
                        $output.= '<p>Pour contacter l\'équipe Informatique du Lycée Sainte Famille Saintonge, merci d\'envoyer un email à <a href="mailto:informatique@lyceesaintefamille.com">informatique@lyceesaintefamille.com</a></p>';
			    		$body = $output;
			    		$subject = 'L\'alerte Température ('.html_entity_decode($row['nom'],ENT_QUOTES).') a été déclenchée';
                        require "public/commons/mailconfig.php";
                        if (!$mail->Send()){ //impossible d'envoyer le mail
			    			echo "Mailer Error: " . $mail->ErrorInfo."\n";
			    		}else{ //envoi du mail effectué avec succès
			    			echo "Le message a bien été envoyé.\n";
                        }
                        writebuzzerstate($buzzer_pin, 1);
                    }
                }
            }else if($row['type_de_donnees']==2){ //if humidity
                if($row['type_dalerte']==1){ //if alerte is for less than value
                    if($row['valeur_de_declenchement']>$hum){ //if humidity is lower than humidity alerte
                        //send that humidity is lower than humidity alerte
                        $output = '<p>Bonjour,</p>';
                        $output.= '<p>Le taux d\'humidité actuel ('.$hum.') est inférieur à '.$row['valeur_de_declenchement'].' %HR, l\'alerte '.html_entity_decode($row['nom'],ENT_QUOTES).' a donc été déclenchée.</p>';
                        $output.= '<p>Pour votre information, vous avez la possibilité de consulter les données de la salle serveur sur <a href="'.$__WEB_ROOT__.'">'.$__WEB_ROOT__.'</a> en vous connectant à votre compte</p>';
                        $output.= '<p>Cordialement</p>';
                        $output.= '<p>L\'équipe Informatique du Lycée Sainte Famille Saintonge</p>';
                        $output.= '<img src="'.$__WEB_ROOT__.'resources/logo.jpg"/>';
                        $output.= '<br/>';
                        $output.= '<p>Merci de ne pas répondre, cette adresse e-mail est gérée par un programme informatique.</p>';
                        $output.= '<p>Pour contacter l\'équipe Informatique du Lycée Sainte Famille Saintonge, merci d\'envoyer un email à <a href="mailto:informatique@lyceesaintefamille.com">informatique@lyceesaintefamille.com</a></p>';
			    		$body = $output;
			    		$subject = 'L\'alerte Humidité ('.html_entity_decode($row['nom'],ENT_QUOTES).') a été déclenchée';
                        require "public/commons/mailconfig.php";
                        if (!$mail->Send()){ //impossible d'envoyer le mail
			    			echo "Mailer Error: " . $mail->ErrorInfo."\n";
			    		}else{ //envoi du mail effectué avec succès
			    			echo "Le message a bien été envoyé.\n";
                        }
                        writebuzzerstate($buzzer_pin, 1);
                    }
                }else if($row['type_dalerte']==0){ //if alerte is for more than value
                    if($row['valeur_de_declenchement']<$hum){ //if humidity is higher than humidity alerte
                        //send that humidity is higher than humidity alerte
                        $output = '<p>Bonjour,</p>';
                        $output.= '<p>Le taux d\'humidité actuel ('.$hum.') est supérieur à '.$row['valeur_de_declenchement'].' %HR, l\'alerte '.html_entity_decode($row['nom'],ENT_QUOTES).' a donc été déclenchée.</p>';
                        $output.= '<p>Pour votre information, vous avez la possibilité de consulter les données de la salle serveur sur <a href="'.$__WEB_ROOT__.'">'.$__WEB_ROOT__.'</a> en vous connectant à votre compte</p>';
                        $output.= '<p>Cordialement</p>';
                        $output.= '<p>L\'équipe Informatique du Lycée Sainte Famille Saintonge</p>';
                        $output.= '<img src="'.$__WEB_ROOT__.'resources/logo.jpg"/>';
                        $output.= '<br/>';
                        $output.= '<p>Merci de ne pas répondre, cette adresse e-mail est gérée par un programme informatique.</p>';
                        $output.= '<p>Pour contacter l\'équipe Informatique du Lycée Sainte Famille Saintonge, merci d\'envoyer un email à <a href="mailto:informatique@lyceesaintefamille.com">informatique@lyceesaintefamille.com</a></p>';
			    		$body = $output;
			    		$subject = 'L\'alerte Humidité ('.html_entity_decode($row['nom'],ENT_QUOTES).') a été déclenchée';
                        require "public/commons/mailconfig.php";
                        if (!$mail->Send()){ //impossible d'envoyer le mail
			    			echo "Mailer Error: " . $mail->ErrorInfo."\n";
			    		}else{ //envoi du mail effectué avec succès
			    			echo "Le message a bien été envoyé.\n";
                        }
                        writebuzzerstate($buzzer_pin, 1);
                    }
                }
            }
        }
    }
?>