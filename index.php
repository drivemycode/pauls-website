<?php
session_start();
require('conn.php');
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

    if (isset($_SESSION['logged_in'])) {
        $email = $_SESSION['email'];
        $sql = "SELECT email FROM users WHERE is_admin = 1 AND email = '$email'";
        $result = mysqli_query($conn, $sql);
        $info = mysqli_fetch_assoc($result);

        if ($info) {
            $_SESSION['is_admin'] = 1;
        }

        echo "<a href=\"logout.php\">Logout</a>";

        # check if email validated
        if (!isset($_SESSION['is_admin'])) {
            $email = $_SESSION['email'];
            $sql = "SELECT is_verified FROM users WHERE email = '$email'";
            $result = mysqli_query($conn, $sql);
            $info = mysqli_fetch_assoc($result);
            $email_validated = $info['is_verified'];

            if ($email_validated == 0) {
                echo "<a href=\"email.php\">Verify Email</a>";
            } else {
                echo "<a href=\"bookings.php\">Bookings</a>";
                echo "<a href=\"studentprofile.php\">Student Profile</a>";
            }
        } else {
            echo "<a href=\"bookings.php\">Bookings</a>";
            echo "<a href=\"studentprofile.php\">Student Profiles</a>";
            echo "<a href=\"finance.php\">Finance</a>";
        }
    } else {
        echo "<a href=\"register.php\">Register</a>";
        echo "<a href=\"login.php\">Login</a>";
    }

    //var_dump($_SESSION);

    ?>
</body>

</html>