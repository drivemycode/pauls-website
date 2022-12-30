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
    <title>Student Profiles</title>
</head>

<body>
    <?php

    if (isset($_SESSION['logged_in'])) {

        if (!isset($_SESSION['is_admin'])) {
            header("Location: index.php");
        } else {
            # main code here
            $students = selectFromTable("SELECT * FROM users", $conn);
            echo "<ol>";
            foreach ($students as $student) {
                echo "<li><a href=\"studentprogress.php?user_id=" . $student['user_id'] . "&username=" . $student['username'] . "\">" . $student['username'] . "</a></li>";
            }
            echo "</ol>";
            if (isset($_SESSION['studentprogress_errors'])) {
                foreach ($_SESSION['studentprogress_errors'] as $error) {
                    echo "<br>" . $error . "</br>";
                }
            } else if (empty($_SESSION['studentprogress_errors'])) {
                echo "";
            }
            echo "<a href=\"index.php\">Back</a>";
        }
    } else {
        header("Location: index.php");
    }

    ?>
</body>

</html>