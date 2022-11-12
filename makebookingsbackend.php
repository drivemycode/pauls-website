<?php
session_start();
require_once('conn.php');
require('functions.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Make Booking</title>
</head>

<body>

    <?php

    if (isset($_POST['submit'])) {

        #user side
        if (!isset($_SESSION['is_admin'])) {

            $email = $_SESSION['email'];
            $start_timing = $_POST['start_timing'];
            $day = $_POST['day'];
            $lesson_length = $_POST['lesson_length'];
            $instrument = trim($_POST['instrument']);
            $error_messages = array();

            # if dropdown selection is somehow empty
            if (empty($start_timing) || empty($day) || empty($lesson_length) || empty($instrument)) {
                $error = 1;
                array_push($error_messages, "All fields must be entered!");
            }

            # figuring out the end timing
            $lesson_length = explode(" ", $lesson_length);

            # converting start_timing and lesson_length into necessary format
            $hour_minute = explode(":", $start_timing);
            $int_start_hour = intval($hour_minute[0]);
            $int_start_minute = intval($hour_minute[1]);

            if (isset($lesson_length[4], $lesson_length[5])) { # <- checks if lesson length is in format x_hour(s)_and_(xx)minutes
                // $str_lesson_length = $lesson_length[1] . " " . $lesson_length[2] . " " . $lesson_length[4] . " " . $lesson_length[5];
                $int_hour = intval($lesson_length[1]);
                $int_minute = intval($lesson_length[4]);
            } else {
                // $str_lesson_length = $lesson_length[1] . " " . $lesson_length[2];
                $int_minute = intval($lesson_length[1]);
                $int_hour = 0;
            }

            # calculating end timing
            $int_end_minute = $int_start_minute + $int_minute;

            if ($int_end_minute >= 60) {
                $quotient = floor($int_end_minute / 60);
                $int_end_hour = $int_start_hour + $int_hour + $quotient;
                $int_end_minute = $int_end_minute - 60 * $quotient;
            } else {
                $int_end_hour = $int_start_hour + $int_hour;
            }

            # lesson length in minutes
            $lesson_length_minutes = $int_minute + $int_hour * 60;

            #final conversion of end timing into h:m:s format
            $str_end_hour = strval($int_end_hour);

            if ($int_end_minute == 0) {
                $str_end_minute = "00";
            } else {
                $str_end_minute = strval($int_end_minute);
            }
            $end_timing = $str_end_hour . ":" . $str_end_minute . ":" . "00";

            # checking if chosen timing conflicts whatsoever with existing timeslots

            #checking if selected endtime conflicts with existing timeslots
            $booked_in_slots = array();
            $sql = "SELECT start_time, end_time, day FROM booked_in_slots";
            $result = mysqli_query($conn, $sql);
            while ($row = mysqli_fetch_assoc($result)) {
                array_push($booked_in_slots, $row);
            }

            foreach ($booked_in_slots as $booked_in_slot) {
                if (trim($booked_in_slot['day']) == trim($day)) {
                    $int_start_timing = str_to_int_timing(trim($start_timing));
                    $int_end_timing = str_to_int_timing(trim($end_timing));
                    $booked_start_timing = str_to_int_timing($booked_in_slot['start_time']);
                    $booked_end_timing = str_to_int_timing($booked_in_slot['end_time']);

                    #statement for if the timing the user inputted conflicts with existing booked in slots, return false
                    $statement = ($int_start_timing >= $booked_end_timing && $int_end_timing >= $booked_end_timing) ||
                        ($int_start_timing <= $booked_start_timing && $int_end_timing <= $booked_start_timing);

                    if (!$statement) {
                        array_push($error_messages, "Please select a different timing or length. ");
                    }
                }
            }

            #checking if selected timing truly exists (given individual dropdowns do not truly account for this)
            $lesson_slots = $_SESSION['lesson_slots'];

            foreach ($lesson_slots as $lesson_slot) {
                //var_dump($lesson_slot['start_time'], $start_timing, $lesson_slot['day'], $day);
                if (trim($lesson_slot['start_time']) == trim($start_timing) && trim($lesson_slot['day']) == trim($day)) {
                    $lesson_found = 1;
                    break 1;
                }
            }

            if (!isset($lesson_found)) {
                array_push($error_messages, "Please ensure that the timing and day matches up. ");
            }

            # finding user_id
            $sql = "SELECT user_id FROM users WHERE email = '$email'";
            $result = mysqli_query($conn, $sql);

            if ($result) {
                $user_array = mysqli_fetch_assoc($result);
                $user_id = $user_array['user_id'];
            } else {
                header('Location: http://' . $_SERVER['HTTP_HOST'] . '/paul%20v3/makebookings.php');
                exit();
            }

            #checking if user already has existing booking for the same instrument
            $sql = "SELECT * FROM booked_in_slots WHERE user_id = '$user_id' AND instrument LIKE '$instrument'";
            $array = selectFromTable($sql, $conn);

            if (!empty($array)) {
                array_push($error_messages, "You already have a lesson for $instrument! If you want to edit the timing of your booking, go to View Booking.");
            }

            $_SESSION['usermakebookings_errors'] = $error_messages;

            if (count($error_messages) > 0) {

                //var_dump($lesson_slots);
                header('Location: http://' . $_SERVER['HTTP_HOST'] . '/paul%20v3/makebookings.php');
                //echo $lesson_found;
                exit();
            } else {

                $date = date('Y-m-d H:i:s');
                # inserting lesson into booked in slots
                $sql = "INSERT INTO booked_in_slots (user_id, start_time, end_time, day, instrument, date) VALUES ('$user_id', '$start_timing', '$end_timing', '$day', '$instrument', '$date')";
                $result = mysqli_query($conn, $sql);
                $booked_in_slot_id = fetchID('booked_in_slot_id', [$user_id, $instrument], $conn);
                if (str_contains($instrument, 'guitar')) {
                    $fe_counter = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM finger_exercises"));
                    for ($i = 1; $i < $fe_counter + 1; $i++) {
                        $finger_exercise_name = "Finger Exercise " . $i;
                        $finger_exercise_id = fetchID("finger_exercise_id", [$finger_exercise_name], $conn);
                        insertIntoTable('finger_exercise_log', [$finger_exercise_id, $user_id, $booked_in_slot_id, 0, $date], $conn);
                    }
                    insertIntoTable('skillsets', [$user_id, $instrument, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0], $conn);
                } else {
                    insertIntoTable('skillsets', [$user_id, $instrument, null, null, null, null, 0, 0, 0, 0, 0, 0, 0, 0], $conn);
                }

                # figuring out lesson fee amount
                # calculating using = lesson length (hours) * 500 RMB /hour 
                # info related to booking inserted into finance table
                insertIntoTable('finance', [$booked_in_slot_id, $user_id, calculateFee($lesson_length_minutes), 0], $conn);

                $template_file = "./bookingtemplate.php";
                $message = $instrument . " lesson from " . $start_timing . " to " . $end_timing . " booked in for " . $day . " of every week.";
                $title = "Lesson Successfully Booked!";
                email($email, $message, $title);
                echo "Lesson added!";
                header('Location: http://' . $_SERVER['HTTP_HOST'] . '/paul%20v3/bookings.php');
            }


            exit();

            #admin side
        } else {
        }
    } else {
        header('Location: http://' . $_SERVER['HTTP_HOST'] . '/paul%20v3/makebookings.php');
        exit();
    }
    ?>
</body>

</html>