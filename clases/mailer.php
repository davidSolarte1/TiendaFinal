<?php

use PHPMailer\PHPMailer\{PHPMailer,SMTP,Exception};


class Mailer{
    function enviarEmail($email,$asunto,$cuerpo){

        require_once __DIR__ . '/../config/config.php';
        require __DIR__ . '/../phpmailer/src/PHPMailer.php';
        require __DIR__ . '/../phpmailer/src/SMTP.php';
        require __DIR__ . '/../phpmailer/src/Exception.php';

        $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_OFF;// SMTP::DEBUG_OFF;                      
        $mail->isSMTP();                                          
        $mail->Host       = 'smtp.gmail.com';                    
        $mail->SMTPAuth   = true;                                   
        $mail->Username   = 'labellaquea13@gmail.com';              
        $mail->Password   = 'juaqjzzkikhongoa';                               
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;           
        $mail->Port       = 587;                                    // use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom('labellaquea13@gmail.com', 'La Bellaquea');
        $mail->addAddress($email);  

        //Content
        $mail->isHTML(true);
        $mail->Subject = $asunto;


        $mail->Body    = utf8_decode($cuerpo);

        $mail->setLanguage('es', '../phpmailer.lang=es.php');
        if($mail->send()){
            return true;
        }else{
            return false;
        }

        } catch (Exception $e) {
            echo "Error al enviar el recibo de pago: {$mail->ErrorInfo}";
            return false;
        }
    }

}

?>