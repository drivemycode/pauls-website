<?php
session_start();
require_once('conn.php');
require('functions.php');

if (isset($_SESSION['logged_in'])) {

    if (isset($_SESSION['is_admin'])) {

        if (isset($_POST)) {
            //var_dump($_POST);
            $messages = array();
            $open_slots = $_SESSION['open_slots'];
            $_SESSION['availabletimeslots_messages'] = array();
            $day = trim(htmlspecialchars($_POST['day']));
            $start_time = trim(htmlspecialchars($_POST['start_time']));

            # validation checking for adding available slots
            if (isset($_POST['submit'])) {

                #if empty
                if (empty($day) || empty($start_time)) {
                    $error = 1;
                    array_push($messages, "Please fill in the fields.");
                }

                if (!isValidDate($start_time, 'H:i:s')) {
                    $error = 1;
                    array_push($messages, "Please enter start time in the 24h format (Example: 16:00:00).");
                }

                if (!isValidDay($day)) {
                    $error = 1;
                    array_push($messages, "Please enter a valid day.");
                }

                foreach ($open_slots as $open_slot) {
                    if (trim($open_slot['start_time']) == $start_time && trim($open_slot['day']) == $day) {
                        $error = 1;
                        array_push($messages, "That open slot already exists!");
                    }
                }

                if (isset($error)) {
                    $_SESSION['availabletimeslots_messages'] = $messages;
                    header("Location: ../booking/availabletimeslots.php");
                    exit();
                } else {
                    $sql = "INSERT INTO lessons_slots (day, start_time) VALUES ('$day', '$start_time')";
                    mysqli_query($conn, $sql);
                    array_push($messages, "Success!");
                    $_SESSION['availabletimeslots_messages'] = $messages;
                    header("../booking/availabletimeslots.php");
                    exit();
                }
            }

            # deleting abailable slot
            if (in_array("delete", $_POST) && !isset($_POST['submit'])) {
                # prepping for open slot deletion
                $arg = array_key_first($_POST);

                foreach ($open_slots as $open_slot) {
                    if ($arg == $open_slot[1]) {
                        $lesson_slot_id = $open_slot['lesson_slot_id'];
                        $day = $open_slot['day'];
                        $start_time = $open_slot['start_time'];
                        $lesson_conflicts = array();

                        $sql = "DELETE FROM lessons_slots WHERE lesson_slot_id = '$lesson_slot_id' AND start_time = '$start_time' AND day = '$day'";
                        mysqli_query($conn, $sql);
                        array_push($messages, "Lesson slot starting from " . $start_time . " on " . $day . " deleted.");
                        $_SESSION['availabletimeslots_messages'] = $messages;

                        #removing bookedin lessons that conflict with this lesson slot deletion
                        #this select statement fetches details relating to said lesson to email person of interest
                        $sql = "SELECT * FROM booked_in_slots WHERE day = 'day' AND start_time = 'start_time'";
                        $result = mysqli_query($conn, $sql);
                        while ($row = mysqli_fetch_assoc($result)) {
                            array_push($lesson_conflicts, $row);
                        }

                        foreach ($lesson_conflicts as $lesson_conflict) {
                            $paul_usersid = $lesson_conflict['paul_usersid'];
                            $start_time = $lesson_conflict['start_time'];
                            $end_time = $lesson_conflict['end_time'];
                            $instrument = $lesson_conflict['instrument'];
                            $day = $lesson_conflict['day'];
                            # finding email
                            $email = fetchEmail($paul_usersid);
                            $sql = "DELETE FROM booked_in_slots WHERE paul_usersid = '$paul_usersid' AND day = '$day' AND start_time = '$start_time' AND end_time = '$end_time' AND instrument = '$instrument'";
                            mysqli_query($conn, $sql);
                            $message = $instrument . " lesson from " . $start_time . " to " . $end_time . " booked in for " . $day . " of every week is cancelled due to scheduling conflicts.";
                            $title = "Lesson Conflict Notice";
                            email($email, $message, $title);
                        }

                        $_SESSION['lesson_conflicts'] = $lesson_conflicts;
                        header("../booking/availabletimeslots.php");
                        exit();
                    }
                }
            }
        }
    } else {
        header("Location: ../booking/bookings.php");
        exit();
    }
}
