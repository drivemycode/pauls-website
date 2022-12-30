<?php
require('conn.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function str_to_int_timing($str_timing)
{
    $hour_minute_seconds = explode(":", $str_timing);
    $int_timing = intval($hour_minute_seconds[0] . $hour_minute_seconds[1] . $hour_minute_seconds[2]);
    return $int_timing;
}

function int_to_str_timing($int_timing)
{
    $str_start_timing = strval($int_timing);
    $str_hour_minute = str_split($str_start_timing);
    $str_timing = trim($str_hour_minute[0] . $str_hour_minute[1] . ":" . $str_hour_minute[2] . $str_hour_minute[3] . ":" . $str_hour_minute[4] . $str_hour_minute[5]);
    return $str_timing;
}

function email($to, $message, $title)
{
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

function fetchEmail($user_id)
{
    require('conn.php');
    $sql = "SELECT email FROM users WHERE user_id = '$user_id'";
    $result = mysqli_fetch_assoc(mysqli_query($conn, $sql));
    $email = $result['email'];
    return $email;
}

function fetchID(string $idType, array $arguments, mysqli $conn)
{
    # accepts email => returns user id
    if ($idType === 'user_id') {
        $sql = "SELECT user_id FROM users WHERE email = '$arguments[0]'";
        $result = mysqli_fetch_assoc(mysqli_query($conn, $sql));
    }

    # accepts user id => returns booked lesson id
    if ($idType === 'booked_in_slot_id') {
        $sql = "SELECT booked_in_slot_id FROM booked_in_slots WHERE user_id = '$arguments[0]' AND instrument = '$arguments[1]'";
        $result = mysqli_fetch_assoc(mysqli_query($conn, $sql));
    }

    # accepts song name => returns song id
    if ($idType === 'song_id') {
        $sql = "SELECT song_id FROM songs WHERE song_name = '$arguments[0]'";
        $result = mysqli_fetch_assoc(mysqli_query($conn, $sql));
    }

    # accepts finger exercise name => returns fe id
    if ($idType === "finger_exercise_id") {
        $sql = "SELECT finger_exercise_id FROM finger_exercises WHERE finger_exercise_name = '$arguments[0]'";
        $result = mysqli_fetch_assoc(mysqli_query($conn, $sql));
    }

    $id = $result[$idType];
    return $id;
}

function isValidDate(string $date, string $format = 'Y-m-d'): bool
{
    $dateObj = DateTime::createFromFormat($format, $date);
    return $dateObj && $dateObj->format($format) == $date;
}

function isValidDay(string $day)
{
    $days_of_the_week = array("monday", "tuesday", "wednesday", "thursday", "friday", "saturday", "sunday");
    return in_array(strtolower($day), $days_of_the_week);
}

# callback function to differentiate data type between number and string
function typeAdjuster($item)
{
    return is_int($item) ? $item : '\'' . $item . '\'';
}

# callback function to convert underscore string to normal string
function modify($str)
{
    return ucwords(str_replace("_", " ", $str));
}

function insertIntoTable(string $tableName, array $data, mysqli $conn)
{
    # fetch columns from table as provided by $tableName variable
    $cols = [];
    $sql_show = "SHOW COLUMNS FROM " . $tableName;
    $res = mysqli_query($conn, $sql_show);
    while ($col = mysqli_fetch_assoc($res)) {
        $col = $col['Field'];
        array_push($cols, $col);
    };

    $start_index = count($cols) - count($data);
    $adjusted_cols = implode(", ", array_slice($cols, $start_index));
    $adjusted_data = implode(", ", array_map('typeAdjuster', $data));
    $sql = "INSERT INTO $tableName ($adjusted_cols) VALUES (" . $adjusted_data . ")";
    mysqli_query($conn, $sql);
}

function deleteFromTable(string $tableName, array $argumentNames, array $arguments, mysqli $conn)
{
    if (count($argumentNames) !== count($arguments)) return;
    if (count($argumentNames) === 1) {
        $sql = "DELETE FROM $tableName WHERE $argumentNames[0] = '$arguments[0]'";
    } else {
        $sql = "";
        for ($i = 0; $i < count($argumentNames); $i++) {
            if ($i === 0) {
                $sql = "DELETE FROM $tableName WHERE $argumentNames[$i] = '$arguments[0]'";
            } else {
                $sql = $sql . " AND $argumentNames[$i] = '$arguments[$i]'";
            }
        }
    }
    mysqli_query($conn, $sql);
}

function updateTable(string $tableName, array $argumentNames, array $oldArr, array $newArr, mysqli $conn)
{
    if (count($argumentNames) !== count($oldArr) || count($argumentNames) !== count($newArr) || count($oldArr) !== count($newArr)) return;
    if (count($argumentNames) === 1) {
        $sql = "UPDATE $tableName SET $argumentNames[0] = '$newArr[0]' WHERE $argumentNames[0] = '$oldArr[0]'";
    } else {
        $sql_one = "";
        $sql_two = "";
        for ($i = 0; $i < count($argumentNames); $i++) {
            if ($i === 0) {
                $sql_one = "UPDATE $tableName SET $argumentNames[$i] = '$newArr[0]'";
                $sql_two = "WHERE $argumentNames[$i] = '$oldArr[0]'";
            } else {
                $sql_one = $sql_one . ", $argumentNames[$i] = '$newArr[$i]'";
                $sql_two = $sql_two . " AND $argumentNames[$i] = '$oldArr[$i]'";
            }
        }
        $sql = $sql_one . " " . $sql_two;
        mysqli_query($conn, $sql);
    }
}


# input $sql, $conn => return array of objects
function selectFromTable(string $sql, mysqli $conn)
{
    $arr = [];
    $res = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($res)) {
        array_push($arr, $row);
    }
    return $arr;
}


function returnKeyArray(array $arr, string $key)
{
    $arr_new = array();
    foreach ($arr as $elem) {
        array_push($arr_new, $elem[$key]);
    }
    return $arr_new;
}

function writeToJSONFile(array $arr, string $fileName)
{
    $json = json_encode($arr);
    file_put_contents($fileName, $json);
}


/* function filter(array $arr, array $arguments, int $count)
{
    if ($count > 0) {
        $array = [];
        for ($i = 0; $i < count($arr); $i++) {
            $match_flag = false;
            for ($j = 0; $j < $count; $j++) {
                if ($i > 0) {
                    if ($arr[$i][$arguments[$j]] === $arr[$i - 1][$arguments[$j]] && !$match_flag) {
                        array_push($array, $arr[$i]);
                        $match_flag = !$match_flag;
                    }
                }
            }
        }
    }
    return $array;
} */

function calculateFee(int $lesson_length_minutes)
{
    return (intval($lesson_length_minutes) / 60) * 500;
}

/* accepts the instrument desired in string, accepts array of "skillsets" as formatted
 in database with corresponding instrument levels. 

function processInstrumentLevels(string $instrument, array $skillsets)
{
    # data for instrument levels
    foreach ($skillsets as $skillset) {
        $instruments = [];
        switch (trim($instrument)) {
            case "guitar":
                array_push($instruments);
                break;
        }
    }
} */

function getFileType(string $destination)
{
    $mime_content_type = mime_content_type($destination);
    return (explode("/", $mime_content_type)[0]);
}
