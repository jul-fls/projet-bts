<?php
    $mail = new PHPMailer\PHPMailer\PHPMailer();
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';
    $mail->IsSMTP();
    $mail->Host = $_SERVER['MAIL_HOST'];
    $mail->SMTPAuth = true;
    $mail->Username = $_SERVER['MAIL_USERNAME'];
    $mail->Password = $_SERVER['MAIL_PASSWORD'];
    $mail->Port = $_SERVER['MAIL_PORT'];
    $mail->IsHTML(true);
    $mail->From = $_SERVER['MAIL_USERNAME'];
    $mail->FromName = $_SERVER['MAIL_FROMNAME'];
    $mail->Subject = $subject;
    $mail->Body = $body;
    if(is_array($email_to)){
        foreach($email_to as $email){
            $mail->AddAddress($email);
        }
    }else{
        $mail->AddAddress($email_to);
    }
?>