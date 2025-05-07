<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'lib/PHPMailer/src/Exception.php';
require 'lib/PHPMailer/src/PHPMailer.php';
require 'lib/PHPMailer/src/SMTP.php';

if(isset($_POST['submit'])){
    $retorno = [];
    if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])){
        $secretKey = "6Lfn4SorAAAAAA_LHfmOGrS3iN_kz3paIhJUMz_0";
        $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secretKey.'&response='.$_POST['g-recaptcha-response']);
        $response = json_decode($verifyResponse);
        
        if($response->success){
            $mail = new PHPMailer(true);
            
            try {
                //Server settings
                $mail->SMTPDebug = SMTP::DEBUG_OFF;
                //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
                $mail->isSMTP();                                            //Send using SMTP
                $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
                $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
                $mail->Username   = 'gabrielsteffens@gmail.com';                     //SMTP username
                $mail->Password   = 'qwou wham aomd udhr';                               //SMTP password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
                $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
            
                //Recipients
                $mail->setFrom('gabrielsteffens@gmail.com', filter_var($_POST['name'], FILTER_SANITIZE_STRING));
                $mail->addAddress("gabrielsteffens@gmail.com", "Site");     //Add a recipient
            
                //Content
                $mail->isHTML(true);                                  //Set email format to HTML
                $mail->Subject = 'E-mail vindo do site - ' . filter_var($_POST['subject'], FILTER_SANITIZE_STRING);
                $mail->Body    = '<b>Nome:</b> ' . filter_var($_POST['name'], FILTER_SANITIZE_EMAIL) . '<br><b>Telefone:</b> ' . filter_var($_POST['phone'], FILTER_SANITIZE_STRING) . '<br><b>Assunto:</b> ' . filter_var($_POST['subject'], FILTER_SANITIZE_STRING) . '<br><b>Mensagem:</b> ' . filter_var($_POST['message'], FILTER_SANITIZE_STRING);
            
                $mail->send();
            
                $retorno['success'] = true;
            } catch (Exception $e) {
                $retorno['success'] = false;
                $retorno['error'] = $mail->ErrorInfo;
            }
        }
        else{
            $retorno['success'] = false;
            $retorno['error'] = "reCaptcha não foi validado.";
        }
    }
    else{
        $retorno['success'] = false;
        $retorno['error'] = "reCaptcha não foi enviado.";
    }
    
    echo json_encode($retorno);
}