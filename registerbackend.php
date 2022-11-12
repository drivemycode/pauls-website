<?php
session_start();
require_once('conn.php');
require('functions.php');

$error = 0;
$error_messages = array();
$existing_emails = array();

if (isset($_POST['submit'])) {
    $name = trim(htmlspecialchars($_POST['name']));
    $email = trim(htmlspecialchars($_POST['email']));
    $password = htmlspecialchars($_POST['password']);
    $password_repeat = htmlspecialchars($_POST['password-repeat']);

    # sql query / code used to check if email already exists later on
    $sql = "SELECT * FROM users";
    $result = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($result)) {
        array_push($existing_emails, $row['email']);
    }

    # no empty fields
    if (empty($name) || empty($email) || empty($password) || empty($password_repeat)) {
        $error = 1;
        array_push($error_messages, "All fields must be entered!");
    }

    # name has illegal characters?
    if (!preg_match('/[a-zA-Z\s]+$/', $name)) {
        $error = 1;
        array_push($error_messages, "Name must not contain illegal characters!");
    }

    # name must not just be 1 character in length
    if (strlen($name) == 1) {
        $error = 1;
        array_push($error_messages, "Name must be longer than 1 character!");
    }

    # email is actually email?
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 1;
        array_push($error_messages, "Email is an illegal email!");
    }

    # if passwords match?
    if ($password !== $password_repeat) {
        $error = 1;
        array_push($error_messages, "Passwords do not match!");
    }

    # loop that checks if registered email already exists
    foreach ($existing_emails as $existing_email) {
        if ($existing_email == $email) {
            $error = 1;
            array_push($error_messages, "Email already exists! Use another one!");
        }
    }

    # password must contain letters, numbers and symbols
    if (preg_match('/[a-zA-Z\s0-9]+$/', $password)) {
        $error = 1;
        array_push($error_messages, "Password must contain letters, numbers and symbols!");
    }

    # password must be 8 or more characters long
    if (strlen($password) < 8 && strlen($password > 0)) {
        $error = 1;
        array_push($error_messages, "Password must be 8 or more characters long!");
    }

    //print_r($error_messages);

    $_SESSION['register_errors'] = $error_messages;
    $_SESSION['name'] = htmlspecialchars($name);
    $_SESSION['email'] = htmlspecialchars($email);

    if (count($error_messages) > 0) {
        header('Location: http://' . $_SERVER['HTTP_HOST'] . '/paul%20v3/register.php');
        die();  # exits anything, stops from continuing
    } else {

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $_SESSION['hashed_password'] = $hashed_password;
        $sql = "INSERT INTO users (username, email, password, is_verified) VALUES ('$name', '$email', '$hashed_password', 0)";
        if (mysqli_query($conn, $sql)) {
            echo "User created!";
            $id = fetchID('user_id', [$email], $conn);
            header("Location: index.php");
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    }

    // notes: include timeout system, figure out how session variables and button are gonna work 
}





  //password_verify() for login?  
