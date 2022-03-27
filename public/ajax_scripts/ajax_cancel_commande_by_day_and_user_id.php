<?php
    session_start();
    if(isset($_SESSION['loggedin'])&&isset($_SESSION['role'])&&$_SESSION['role']>=0&&isset($_POST['date'])){
        require("../commons/dbconfig.php");
        require "../commons/global.php";
        $now = new DateTime();
        $response;
        $sanitized_date = filter_var($_POST['date'],FILTER_SANITIZE_STRING);
        $sanitized_id_utilisateur = filter_var($_SESSION['id'],FILTER_SANITIZE_NUMBER_INT);
        $sql = "SELECT donnees_capteurs.id, donnees_capteurs.id_utilisateur, donnees_capteurs.jour, donnees_capteurs.montant FROM donnees_capteurs WHERE donnees_capteurs.id_utilisateur = ? AND donnees_capteurs.jour = ? AND donnees_capteurs.valide = 1";
        $stmt = $conn->prepare($sql);
        $date = DateTime::createFromFormat('d/m/Y',$sanitized_date);
        $formatteddate = $date->format('Y-m-d');
        $stmt->bind_param("is",$sanitized_id_utilisateur,$formatteddate);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows>0){
            $row = $result->fetch_assoc();
            $original_montant_reduction = $montant_reduction = $row['montant'];
            $date_commande = Datetime::createFromFormat('Y-m-d',$row['jour']);
            $date_commande->setTime(12,5,0);
            $interval = $now->diff($date_commande);
            if($interval->format('%d')>=3){
                $sql2 = "DELETE FROM donnees_capteurs WHERE id_utilisateur = ? AND jour = ? AND valide = 1";
                $stmt2 = $conn->prepare($sql2);
                $stmt2->bind_param("is",$sanitized_id_utilisateur,$formatteddate);
                $stmt2->execute();
                $nb_of_utilisations = 0;
                while($montant_reduction>3){
                    $montant_reduction /= 2;
                    $nb_of_utilisations+=2;
                }
                $sql3 = "INSERT IGNORE INTO codes_reductions (code_reduction, montant_reduction, utilisations_max) VALUES (?, ?, ?)";
                $stmt3 = $conn->prepare($sql3);
                $code = "CRA-".$sanitized_id_utilisateur.'-'.rand(000000,999999).'-'.$montant_reduction;
                $stmt3->bind_param("sdi",$code,$montant_reduction,$nb_of_utilisations);
                $stmt3->execute();
                $sql4 = 'SELECT utilisateurs.email, utilisateurs.prenom_utilisateur, utilisateurs.nom_utilisateur, utilisateurs.login FROM utilisateurs WHERE utilisateurs.id = ? LIMIT 1;';
				$stmt4 = mysqli_prepare($conn,$sql4);
				mysqli_stmt_bind_param($stmt4, 'i',$sanitized_id_utilisateur);
				mysqli_stmt_execute($stmt4);
				$result4 = mysqli_stmt_get_result($stmt4);
				if(mysqli_num_rows($result4)>0){ //utilisateur trouvé
					while ($row4 = mysqli_fetch_assoc($result4)){
                        $output = '<p>Bonjour '.$row4['prenom_utilisateur'].' '.$row4['nom_utilisateur'].',</p>';
                        $output.= '<p>Vous venez de demander l\'annulation de votre commande du '.$date->format('d/m/Y').' d\'un montant de '.$original_montant_reduction.'€</p>';
                        $output.= '<p>Vous pouvez dès à présent utiliser le code de réduction suivant pour économiser '.$original_montant_reduction.'€ sur vos '.$nb_of_utilisations.' prochaines donnees_capteurs :</p>';
                        $output.= '<p><strong>'.$code.'</strong></p>';
                        $output.= '<p>Pour votre information, vous avez la possibilité de consulter la totalité de vos donnees_capteurs de repas sur <a href="'.$__WEB_ROOT__.'">'.$__WEB_ROOT__.'</a> en vous connectant à votre compte avec l\'identifiant <u><b>'.$row4['login'].'</b></u></p>';
                        $output.= '<p>Cordialement</p>';
                        $output.= '<p>L\'équipe Informatique du Lycée Sainte Famille Saintonge</p>';
                        $output.= '<img src="'.$__WEB_ROOT__.'resources/logo.jpg"/>';
                        $output.= '<br/>';
                        $output.= '<p>Merci de ne pas répondre, cette adresse e-mail est gérée par un programme informatique.</p>';
                        $output.= '<p>Pour contacter l\'équipe Informatique du Lycée Sainte Famille Saintonge, merci d\'envoyer un email à <a href="mailto:informatique@lyceesaintefamille.com">informatique@lyceesaintefamille.com</a></p>';
                        $body = $output;
                        $subject = "Annulation de votre commande du ".$date->format('d/m/Y');
                        $email_to = $row4['email'];
                        require_once "../commons/mailconfig.php";
                        if (!$mail->Send()){ //impossible d'envoyer le mail
                            $response = array(
                                "status" => "warning",
                                "message" => "La commande a été annulée mais le mail de confirmation n'as pas pu vous être envoyé.",
                                "code_reduction" => "Le code de réduction \"".$code."\" vous permettra d'économiser ".$montant_reduction."€ sur vos ".$nb_of_utilisations." prochaines donnees_capteurs."
                            );
                        }else{ //envoi du mail effectué avec succès
                            $response = array(
                                "status" => "success",
                                "message" => "La commande a été annulée et le mail de confirmation vous a été envoyé.",
                                "code_reduction" => "Le code de réduction \"".$code."\" vous permettra d'économiser ".$montant_reduction."€ sur vos ".$nb_of_utilisations." prochaines donnees_capteurs."
                            );
                        }
                    }
                }
            }else{
                $response = array(
                    "status" => "error",
                    "message" => "Vous ne pouvez pas annuler une commande qui est en cours de préparation.",
                    "code_reduction" => ''
                );
            }
        }else{
            $response = array(
                "status" => "error",
                "message" => "Aucune commande valide n'a été trouvée pour cette date.",
                "code_reduction" => ''
            );
        }
    }else{
        $response = array(
            "status" => "error",
            "message" => "Vous n'êtes pas connecté",
            "code_reduction" => ''
        );
    }
    header('Content-Length: '.strlen(json_encode($response)));
    header('Content-type: application/json');
    echo json_encode($response);
?>