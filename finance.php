<?php
require('./processing/conn.php');
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finance</title>
</head>

<body>

    <?php

    if (isset($_SESSION['logged_in'])) {

        if (!isset($_SESSION['is_admin'])) {
            header("Location: index.php");
        } else {
            # main code here
        }
    } else {
        header("Location: index.php");
    }

    ?>
</body>

</html>