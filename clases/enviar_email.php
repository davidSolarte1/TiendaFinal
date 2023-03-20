<?php

use PHPMailer\PHPMailer\{PHPMailer,SMTP,Exception};

require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';
require '../phpmailer/src/Exception.php';


$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;// SMTP::DEBUG_OFF;                      
    $mail->isSMTP();                                          
    $mail->Host       = 'smtp.gmail.com';                    
    $mail->SMTPAuth   = true;                                   
    $mail->Username   = 'ventastiendabarrio@gmail.com';              
    $mail->Password   = 'fhpdezstvrdvnvfn';                               
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;           
    $mail->Port       = 587;                                    // use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom('ventastiendabarrio@gmail.com', 'TiendaMerca');
    $mail->addAddress('davicho28092000@outlook.com', 'davi');  

    //Content
    $mail->isHTML(true);
    $mail->Subject = 'Detalles de su compra';
    $cuerpo = '<h4>Gracias por su compra</h4>';
    $cuerpo .= '<p> El ID de su compra es <b>'. $id_transaccion. '</b></p>';

    $mail->Body    = utf8_decode($cuerpo);
    $mail->AltBody = 'Le enviamos los detalles de su compra.';

    $mail->setLanguage('es', '../phpmailer.lang=es.php');
    $mail->send();
} catch (Exception $e) {
    echo "Error al enviar el recibo de pago: {$mail->ErrorInfo}";
}
?>