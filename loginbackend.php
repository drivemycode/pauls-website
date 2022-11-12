<?php
session_start();
require_once('conn.php');
if (!isset($_SESSION['total_attempts'])) {
    $_SESSION['total_attempts'] = 0;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checking...</title>
</head>

<body>
    <?php

    if (isset($_POST['submit'])) {

        # reset attempts upon cooldown expiry
        if ($_SESSION['attempts'] >= 3 && $_SESSION['attempts_flag'] == 1) {

            $_SESSION['attempts'] = 0;
            $_SESSION['attempts_flag'] = 0;
            header('Location: http://' . $_SERVER['HTTP_HOST'] . '/paul%20v3/login.php');
            die();
        }

        # updating login_attempt count
        $attempts = $_SESSION['total_attempts'] + intval($_SESSION['attempts']);
        $session_id = session_id();
        $attempted_email = htmlspecialchars($_POST['email']);
        $sql = "UPDATE login_attempts SET attempts = attempts + '$attempts', attempted_email = '$attempted_email' WHERE session_id = '$session_id'";
        mysqli_query($conn, $sql);

        # check if more than 3 attempts achieved; otherwise normal validation/verification is performed
        if ($_SESSION['attempts'] >= 3) {

            $_SESSION['attempts'] = $_SESSION['attempts'] + 1;
            header('Location: http://' . $_SERVER['HTTP_HOST'] . '/paul%20v3/login.php');
            die();
        } else {

            $email = htmlspecialchars($_POST['email']);
            $_SESSION['email'] = $email;
            $password = htmlspecialchars($_POST['password']);
            $_SESSION['login_errors'] = array();

            # getting hashed password based on email inputted by user.
            $sql = "SELECT password FROM users WHERE email = '$email'";
            $result = mysqli_query($conn, $sql);
            $info = mysqli_fetch_assoc($result);
            $password_hashed = $info['password'];
            $password_check = password_verify($password, $password_hashed);

            # making sql query to find if user with same email exists in database
            $sql = "SELECT * FROM users WHERE email = '$email'";
            $result = mysqli_query($conn, $sql);
            $info = mysqli_fetch_assoc($result);

            if ($info && $password_check) {

                echo "Login success!";
                $_SESSION['logged_in'] = 1;
                header("Location: index.php");
                die();
            } else {

                $_SESSION['attempts'] = $_SESSION['attempts'] + 1;
                array_push($_SESSION['login_errors'], "Incorrect email/password.");
                header('Location: http://' . $_SERVER['HTTP_HOST'] . '/paul%20v3/login.php');
                die();  # exits anything, stops from continuing

            }
        }
    }
    ?>
</body>

</html>