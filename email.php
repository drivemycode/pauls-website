<?php
session_start();
require_once('conn.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paul's Website</title>
</head>
<body>
    <?php
    
    if(isset($_SESSION['logged_in'])){

        # email verification + html implementation hopefully?
        $to = $_SESSION['email'];
        $subject = "Email Verification";
        $_SESSION['verify_errors'] = array();

        # location of template file
        $template_file = "./verifytemplate.php";
             
        # create email headers
        //$headers = "From: Paul Lee <paulverifyemail@yahoo.com>\r\n";
        //$headers .= "MIME-Version: 1.0\r\n";
        //$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
    
        # create html message from external file "templeate.php"
        if(file_exists($template_file)){
            $message = file_get_contents($template_file);

            //Load Composer's autoloader
            require 'vendor/autoload.php';

            #PHPMailer integration
            $mail = new PHPMailer(true);

            try {
                //Server settings
                //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
                $mail->isSMTP();                                            //Send using SMTP
                $mail->Host       = 'smtp.mail.yahoo.com';                     //Set the SMTP server to send through
                $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
                $mail->Username   = 'paulverifyemail@yahoo.com';                     //SMTP username
                $mail->Password   = 'usquufvczsddffrn';                               //SMTP password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
                $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

                //Recipients
                $mail->setFrom('paulverifyemail@yahoo.com', 'Paul Lee');
                $mail->addAddress($to);     //Add a recipient

                //Content
                $mail->isHTML(true);                                  //Set email format to HTML
                $mail->Subject = "Paul's Website Account Verification";
                $mail->Body    = $message;
                $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

                $mail->send();
                echo "Email has been sent! Check your inbox to verify account. Then, please reopen Paul's Website.";

            } catch (Exception $e) {

                $error = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                array_push($_SESSION['verify_errors'], $error);
            }   

        } else {

            array_push($_SESSION['verify_errors'], "Unable to locate template file.");
        }
    
    } else {
        header("Location: index.php");
    }
    ?>
</body>
</html>
    