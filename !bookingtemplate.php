<?php
session_start();
    if(isset($_POST['submit'])){
       header("Location: validatesuccess.php");
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div style="max-width: 600px; min-width: 200px; background-color: #ffffff">
        <h1 style="background-color: pink; font-size: 16px; font: arial; padding: 25px; margin: auto">
            Booking confirmed</h1>
        <h2><?php
            print_r($_SESSION['email_message']);
        ?></h2>
    </div>
</body>
</html>