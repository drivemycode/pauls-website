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
    <title>Bookings</title>
</head>
<body>
    <?php
        if(isset($_SESSION['logged_in'])){
        
            if(!isset($_SESSION['is_admin'])){
    ?>
                <a href="makebookings.php">Make Bookings</a>
                <a href="viewbookings.php">View Bookings</a>
                <a href="index.php">Back</a>
    <?php
            } else {
    ?>
                <a href="makebookings.php">Edit Timeslots</a>
                <a href="viewbookings.php">View Bookings</a>
                <a href="index.php">Back</a>
    <?php
            }
        
        } else {
            header('Location: http://' . $_SERVER['HTTP_HOST'] . '/paul%20v3/index.php');
        }
        
        //var_dump($_SESSION);
    ?>
</body>
</html>