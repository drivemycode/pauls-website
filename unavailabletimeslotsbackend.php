<?php
session_start();
require_once('conn.php');
require('functions.php');

if (isset($_SESSION['logged_in'])) {

    if (isset($_SESSION['is_admin'])) {

        if (isset($_POST)) {
            //var_dump($_POST);
            $messages = array();
            $closed_slots = $_SESSION['closed_slots'];
            $_SESSION['availabletimeslots_messages'] = array();
            $day = trim(htmlspecialchars($_POST['day']));
            $start_time = trim(htmlspecialchars($_POST['start_time']));
            $end_time = trim(htmlspecialchars($_POST['end_time']));

            # if either field is empty
            if (empty($day) || empty($start_time) || empty($end_time)) {
                $error = 1;
                array_push($messages, "All fields must be entered. ");
            }

            # if day is valid
            if (!isValidDay($day)) {
                $error = 1;
                array_push($messages, "Please enter a valid day. ");
            }

            # if date is valid
            if (!(isValidDate($start_time, 'H:i:s') && isValidDate($end_time, 'H:i:s'))) {
                $error = 1;
                array_push($messages, "Please enter valid timings in 24h format (for example, 16:00:00 would be valid).");
            } else {

                # if start time is earlier than end time
                if (str_to_int_timing($start_time) >= str_to_int_timing($end_time)) {
                    $error = 1;
                    array_push($messages, "Start time must be before end time.");
                }

                # if closed slot already exists
                foreach ($closed_slots as $closed_slot) {
                    $existing_day = $closed_slot['day'];
                    $existing_start_time = str_to_int_timing($closed_slot['start_time']);
                    $existing_end_time = str_to_int_timing($closed_slot['end_time']);
                    $int_start_time = str_to_int_timing($start_time);
                    $int_end_time = str_to_int_timing($end_time);

                    if ($existing_day == $day) {
                        # checking if timing conflicts with already existing unavailable timeslots
                        if ($existing_start_time == $int_start_time && $existing_end_time == $int_end_time) {
                            $conflict_error = 1;
                        }

                        $statement = ($existing_start_time > $int_start_time && $int_end_time <= $existing_start_time) ||
                            ($int_end_time > $existing_end_time && $int_start_time >= $existing_end_time);

                        if (!$statement) {
                            $conflict_error = 1;
                        }
                    }
                }
            }

            # separate array_push outside of foreach loop
            if (isset($conflict_error)) {
                array_push($messages, "Time slot conflicts with already existing timeslots. ");
            }

            if ((isset($conflict_error) || isset($error)) && !in_array("delete", $_POST)) {
                $_SESSION['unavailabletimeslots_messages'] = $messages;
                header("Location: unavailabletimeslots.php");
                exit();
            } else if (isset($_POST['submit'])) {
                $sql = "INSERT INTO closed_slots (day, start_time, end_time) VALUES ('$day', '$start_time', '$end_time')";
                mysqli_query($conn, $sql);
                array_push($messages, "Success!");
                $_SESSION['unavailabletimeslots_messages'] = $messages;
                header("Location: unavailabletimeslots.php");
                exit();
            }


            # delete unavailable timeslot
            if (in_array("delete", $_POST) && !isset($_POST['submit'])) {
                # prepping for closed slot deletion
                $arg = array_key_first($_POST);

                foreach ($closed_slots as $closed_slot) {
                    if ($arg == $closed_slot[1]) {
                        $closed_slot_id = $closed_slot['closed_slot_id'];
                        $day = trim($closed_slot['day']);
                        $start_time = trim($closed_slot['start_time']);
                        $end_time = trim($closed_slot['end_time']);
                        $closed_conflicts = array();
                        $booked_slots = array();

                        $sql = "DELETE FROM closed_slots WHERE closed_slot_id = '$closed_slot_id' AND start_time = '$start_time' AND end_time = '$end_time' AND day = '$day'";
                        mysqli_query($conn, $sql);
                        $message = "Unavailable slot starting from " . $start_time . " to " . $end_time . " on " . $day . " created.";
                        array_push($messages);
                        $_SESSION['unavailabletimeslots_messages'] = $messages;

                        #removing bookedin lessons that conflict with this lesson slot deletion
                        #this select statement fetches details relating to said lesson to email person of interest
                        $sql = "SELECT * FROM booked_in_slots";
                        $result = mysqli_query($conn, $sql);
                        while ($row = mysqli_fetch_assoc($result)) {
                            array_push($booked_slots, $row);
                        }

                        foreach ($booked_slots as $booked_slot) {
                            $booked_day = $booked_slot['day'];
                            $booked_start_time = str_to_int_timing($booked_slot['start_time']);
                            $booked_end_time = str_to_int_timing($booked_slot['end_time']);
                            $int_start_time = str_to_int_timing($start_time);
                            $int_end_time = str_to_int_timing($end_time);

                            if ($booked_day == $day) {
                                # checking if timing conflicts with already existing unavailable timeslots
                                $statement = ($booked_start_time == $int_start_time && $booked_end_time == $int_end_time) ||
                                    ($booked_start_time < $int_start_time && $booked_end_time <= $int_start_time) ||
                                    ($booked_end_time > $int_end_time && $booked_start_time >= $int_end_time);

                                if (!$statement) {
                                    array_push($closed_conflicts, $booked_slot);
                                }
                            }
                        }
                    }
                }

                foreach ($closed_conflicts as $closed_conflict) {
                    $user_id = $closed_conflict['user_id'];
                    $start_time = $closed_conflict['start_time'];
                    $end_time = $closed_conflict['end_time'];
                    $instrument = $closed_conflict['instrument'];
                    $day = $closed_conflict['day'];
                    # finding email
                    $email = fetchEmail($user_id);
                    $sql = "DELETE FROM booked_in_slots WHERE user_id = '$user_id' AND day = '$day' AND start_time = '$start_time' AND end_time = '$end_time' AND instrument = '$instrument'";
                    mysqli_query($conn, $sql);
                    $message = $instrument . " lesson from " . $start_time . " to " . $end_time . " booked in for " . $day . " of every week is cancelled due to scheduling conflicts.";
                    $title = "Lesson Conflict Notice";
                    email($email, $message, $title);
                }

                $_SESSION['closed_conflicts'] = $closed_conflicts;
                header("Location: unavailabletimeslots.php");
                exit();
            }
        }
    } else {
        header("makebookings.php");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
