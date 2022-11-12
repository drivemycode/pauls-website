<?php
session_start();
require_once('conn.php');
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

    #updates field in database, indicating user has verified email
    if (isset($_SESSION['logged_in'])) {
        $email = $_SESSION['email'];
        $sql = "UPDATE users SET is_verified = 1 WHERE email = '$email'";

        if (mysqli_query($conn, $sql)) {
            echo ("Email validated! You may close the page.");
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    } else {
        header("Location: index.php");
    }
    ?>
</body>

</html>