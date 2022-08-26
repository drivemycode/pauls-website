<?php
session_start();
require('./processing/conn.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    if (!isset($_SESSION['is_admin'])) {
        echo "<title>Student Profile</title>";
    } else {
        echo "<title>Student Profiles</title>";
    }
    ?>
</head>

<body>
    <?php

    if (isset($_SESSION['logged_in'])) {

        if (!isset($_SESSION['is_admin'])) {
        } else {
        }
    } else {
        header("Location: index.php");
    }
    //var_dump($_SESSION);
    ?>
</body>

</html>