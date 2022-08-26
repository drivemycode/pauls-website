<?php
require('conn.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function str_to_int_timing($str_timing){
    $hour_minute_seconds = explode(":", $str_timing);
    $int_timing = intval($hour_minute_seconds[0] . $hour_minute_seconds[1] . $hour_minute_seconds[2]);
    return $int_timing;
}

function int_to_str_timing($int_timing){
    $str_start_timing = strval($int_timing);
    $str_hour_minute = str_split($str_start_timing);
    $str_timing = trim($str_hour_minute[0] . $str_hour_minute[1] . ":" . $str_hour_minute[2] . $str_hour_minute[3] . ":" . $str_hour_minute[4] . $str_hour_minute[5]);
    return $str_timing;
}

function email($to, $message, $title){
    session_start();
    require_once('conn.php');
    $_SESSION['email_errors'] = array();

    //if(file_exists($template_file)){
      //  $message = file_get_contents($template_file);
        //$_SESSION['email_message'] =  $message;

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
            $mail->Subject = $title;
            $mail->Body    = $message;
            $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $mail->send();
            echo "Email has been sent! Check your inbox to verify account. Then, please reopen Paul's Website.";

        } catch (Exception $e) {

            $error = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            array_push($_SESSION['email_errors'], $error);
        }   

   // } else {

     //   array_push($_SESSION['email_errors'], "Unable to locate template file.");
   // }
}

function fetchEmail($paul_usersid){
    require('conn.php');
    $sql = "SELECT paul_usersemail FROM users WHERE paul_usersid = '$paul_usersid'";
    $result = mysqli_fetch_assoc(mysqli_query($conn, $sql));
    $email = $result['paul_usersemail'];
    return $email;

}

function isValidDate(string $date, string $format = 'Y-m-d'): bool
{
    $dateObj = DateTime::createFromFormat($format, $date);
    return $dateObj && $dateObj->format($format) == $date;
}

function isValidDay(string $day){
    $days_of_the_week = array("monday", "tuesday", "wednesday", "thursday", "friday", "saturday", "sunday");
    return in_array(strtolower($day), $days_of_the_week);  
}
