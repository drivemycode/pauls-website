<?php
require('conn.php');
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Bookings</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous">
    </script>
    <script>

    </script>
</head>

<body>

    <?php

    if (isset($_SESSION['logged_in'])) {

        if (!isset($_SESSION['is_admin'])) {


            $email = $_SESSION['email'];
            $_SESSION['booked_in_lessons'] = array();
            $instruments = array();
            $days = array();
            $start_times = array();
            $end_times = array();
            $edit_buttons = array();
            $delete_buttons = array();

            # finding user_id
            $sql = "SELECT user_id FROM users WHERE email = '$email'";
            $result = mysqli_query($conn, $sql);

            if ($result) {
                $user_array = mysqli_fetch_assoc($result);
                $user_id = $user_array['user_id'];
            }

            $sql = "SELECT * FROM booked_in_slots WHERE user_id = '$user_id'";
            $result = mysqli_query($conn, $sql);
            while ($row = mysqli_fetch_assoc($result)) {
                #separate arrays for ease of displaying
                array_push($instruments, $row['instrument']);
                array_push($days, $row['day']);
                array_push($start_times, $row['start_time']);
                array_push($end_times, $row['end_time']);
                #associative array with lesson details (instrument, day, start time end time)
                array_push($_SESSION['booked_in_lessons'], $row);
            }

            for ($i = 0; $i <= count($instruments) - 1; $i++) {
                $edit_html = "<th>
                    <input type=\"submit\" name=\"edit" . $i + 1 . "\" value=\"edit\">
                    </th>";
                $delete_html = "<th>
                    <input type=\"submit\" name=\"delete" . $i + 1 . "\" value=\"delete\">
                    </th>";
                array_push($edit_buttons, $edit_html);
                array_push($delete_buttons, $delete_html);
            }


            echo "
                <table>
                <tr>
                <th>instrument</th>
                <th>day</th>
                <th>start time</th>
                <th>end time</th>
                <th></th>
                <th></th>
                </tr> ";

    ?>
            <form action="viewbookingsbackend.php" method="POST">
                <?php

                for ($i = 0; $i <= count($instruments) - 1; $i++) {

                    $buttons = "<th>
                    <input type=\"submit\" name=\"edit\" value=\"edit\">
                    </th>
                    <th>
                    <input type=\"submit\" name=\"delete\" value=\"delete\">
                    </th>";

                    echo "<tr>";
                    echo "<th>";
                    echo $instruments[$i];
                    echo "</th>";
                    echo "<th>";
                    echo $days[$i];
                    echo "</th>";
                    echo "<th>";
                    echo $start_times[$i];
                    echo "</th>";
                    echo "<th>";
                    echo $end_times[$i];
                    echo "</th>";
                    echo $edit_buttons[$i];
                    echo $delete_buttons[$i];
                    echo "</tr>";
                }

                echo "</table>";
                echo "<br>";
                ?>
            </form>
        <?php

            # admin side
        } else {
            # making sql statement to fetch all booked_in_slots from database

            $email = $_SESSION['email'];
            $_SESSION['booked_in_lessons'] = array();
            $instruments = array();
            $days = array();
            $start_times = array();
            $end_times = array();
            $names = array();
            $edit_buttons = array();
            $delete_buttons = array();

            $sql = "SELECT users.username, users.email, booked_in_slots.start_time, booked_in_slots.end_time, booked_in_slots.day, booked_in_slots.instrument FROM booked_in_slots INNER JOIN users ON users.user_id=booked_in_slots.user_id";
            $result = mysqli_query($conn, $sql);
            while ($row = mysqli_fetch_assoc($result)) {
                #separate arrays for ease of displaying
                array_push($instruments, $row['instrument']);
                array_push($days, $row['day']);
                array_push($start_times, $row['start_time']);
                array_push($end_times, $row['end_time']);
                array_push($names, $row['username']);
                #associative array with lesson details (instrument, day, start time end time)
                array_push($_SESSION['booked_in_lessons'], $row);
            }

            for ($i = 0; $i <= count($instruments) - 1; $i++) {
                $edit_html = "<th>
                    <input type=\"submit\" name=\"edit" . $i + 1 . "\" value=\"edit\">
                    </th>";
                $delete_html = "<th>
                    <input type=\"submit\" name=\"delete" . $i + 1 . "\" value=\"delete\">
                    </th>";
                array_push($edit_buttons, $edit_html);
                array_push($delete_buttons, $delete_html);
            }


            echo "
                <table>
                <tr>
                <th>name</th>
                <th>instrument</th>
                <th>day</th>
                <th>start time</th>
                <th>end time</th>
                <th></th>
                <th></th>
                </tr> ";

        ?>
            <form action="viewbookingsbackend.php" method="POST">
        <?php

            for ($i = 0; $i <= count($instruments) - 1; $i++) {

                $buttons = "<th>
                    <input type=\"submit\" name=\"edit\" value=\"edit\">
                    </th>
                    <th>
                    <input type=\"submit\" name=\"delete\" value=\"delete\">
                    </th>";

                echo "<tr>";
                echo "<th>";
                echo $names[$i];
                echo "</th>";
                echo "<th>";
                echo $instruments[$i];
                echo "</th>";
                echo "<th>";
                echo $days[$i];
                echo "</th>";
                echo "<th>";
                echo $start_times[$i];
                echo "</th>";
                echo "<th>";
                echo $end_times[$i];
                echo "</th>";
                echo $edit_buttons[$i];
                echo $delete_buttons[$i];
                echo "</tr>";
            }

            echo "</table>";
            echo "<br>";
        }
    } else {
        header("Location: index.php");
    }
    var_dump($_SESSION);

        ?>
        <a href="bookings.php">Back</a>
</body>

</html>