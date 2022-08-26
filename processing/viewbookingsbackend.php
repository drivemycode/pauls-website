<?php
session_start();
require('conn.php');
require('functions.php');

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Bookings</title>
</head>

<body>
    <?php

    if (isset($_SESSION['logged_in'])) {

        if (!isset($_SESSION['is_admin'])) {

            if (isset($_POST)) {

                $email = $_SESSION['email'];
                $booked_in_lessons = $_SESSION['booked_in_lessons'];
                $arg = array_key_first($_POST);
                $i = intval($arg[-1]);
                $instrument = $booked_in_lessons[$i - 1]['instrument'];
                $day = $booked_in_lessons[$i - 1]['day'];
                $start_time = $booked_in_lessons[$i - 1]['start_time'];
                $end_time = $booked_in_lessons[$i - 1]['end_time'];
                # session variable as edit booking process takes place in separate page.
                $_SESSION['edit_booking'] = array($instrument, $day, $start_time, $end_time);

                #redirect to separate page
                if (str_contains($arg, "edit")) {
                    //var_dump($_SESSION['edit_booking']);
                    header('../booking/editbookings.php');
                }

                if (str_contains($arg, "delete")) {

                    # finding paul_usersid
                    $sql = "SELECT paul_usersid FROM users WHERE paul_usersemail = '$email'";
                    $result = mysqli_query($conn, $sql);

                    if ($result) {
                        $user_array = mysqli_fetch_assoc($result);
                        $paul_usersid = $user_array['paul_usersid'];
                    }
                    //echo "delete";
                    $sql = "DELETE FROM booked_in_slots WHERE paul_usersid = '$paul_usersid' AND start_time = '$start_time' AND end_time = '$end_time' AND day = '$day' AND instrument = '$instrument'";
                    mysqli_query($conn, $sql);
                    $message = $instrument . " lesson from " . $start_time . " to " . $end_time . " booked in for " . $day . " of every week is cancelled.";
                    $title = "Lesson Successfully Removed";
                    email($email, $message, $title);
                    header("../booking/viewbookings.php");
                }
            }

            #admin side
        } else {

            $booked_in_lessons = $_SESSION['booked_in_lessons'];
            $arg = array_key_first($_POST);
            $i = intval($arg[-1]);
            $email = $booked_in_lessons[$i - 1]['email'];
            $instrument = $booked_in_lessons[$i - 1]['instrument'];
            $day = $booked_in_lessons[$i - 1]['day'];
            $start_time = $booked_in_lessons[$i - 1]['start_time'];
            $end_time = $booked_in_lessons[$i - 1]['end_time'];
            # session variable as edit booking process takes place in separate page.
            $_SESSION['edit_booking'] = array($instrument, $day, $start_time, $end_time);

            //# getting email by getting id first
            //$sql = "SELECT paul_usersid FROM booked_in_slots WHERE start_time = '$start_time' AND end_time = '$end_time' AND day = '$day' AND instrument = '$instrument'";
            // $result = mysqli_query($conn, $sql);
            //$id = mysqli_fetch_row($result);

            if (str_contains($arg, "delete")) {
                $sql = "DELETE FROM booked_in_slots WHERE start_time = '$start_time' AND end_time = '$end_time' AND day = '$day' AND instrument = '$instrument'";
                mysqli_query($conn, $sql);
                $message = $instrument . " lesson from " . $start_time . " to " . $end_time . " booked in for " . $day . " of every week is cancelled.";
                $title = "Lesson Successfully Removed";
                email($email, $message, $title);
                header("../booking/viewbookings.php");
            }

            if (str_contains($arg, "edit")) {
                //var_dump($_SESSION['edit_booking']);
                header('../booking/editbookings.php');
            }
        }
    } else {
        header("../index.php");
    }
    //var_dump($_SESSION);
    ?>
</body>

</html>