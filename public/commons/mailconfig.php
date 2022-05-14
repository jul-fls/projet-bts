<?php
    $mail = new PHPMailer\PHPMailer\PHPMailer(); //Création d'un nouvel objet PHPMailer
    $mail->CharSet = 'UTF-8'; //Définition du charset
    $mail->Encoding = 'base64'; //Définition de l'encodage
    $mail->IsSMTP(); //Définition du mode SMTP
    $mail->Host = $_SERVER['MAIL_HOST']; //Définition de l'adresse du serveur SMTP
    $mail->SMTPAuth = true; //Définition de l'authentification SMTP
    $mail->Username = $_SERVER['MAIL_USERNAME']; //Définition de l'identifiant de connexion
    $mail->Password = $_SERVER['MAIL_PASSWORD']; //Définition du mot de passe de connexion
    $mail->Port = $_SERVER['MAIL_PORT']; //Définition du port de connexion
    $mail->IsHTML(true); //Définition du format HTML
    $mail->From = $_SERVER['MAIL_USERNAME']; //Définition de l'adresse d'expéditeur
    $mail->FromName = $_SERVER['MAIL_FROMNAME']; //Définition du nom d'expéditeur
    $mail->Subject = $subject; //Définition du sujet
    $mail->Body = $body; //Définition du corps du message
    if(is_array($email_to)){ //Si l'adresse du destinataire est un tableau
        foreach($email_to as $email){ //Pour chaque adresse du destinataire
            $mail->AddAddress($email); //Ajout de l'adresse du destinataire
        } 
    }else{ //Sinon
        $mail->AddAddress($email_to); //Ajout de l'adresse du destinataire
    }
?>