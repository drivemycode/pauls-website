<?php
session_start();
require('./processing/conn.php');

# for first time initialisation or further initialisations of attempts related session variables after cooldown expiry
if (!isset($_SESSION['attempts']) && !isset($_SESSION['attempts_flag'])) {
    $_SESSION['attempts'] = 0;
    $_SESSION['attempts_flag'] = 0;
}
$attempts = $_SESSION['attempts'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>

<body>
    <?php

    # first time initialisation of time related session variables or for after cooldown expiry
    if (!isset($_SESSION['current_time']) && !isset($_SESSION['cooldown_time'])) {

        $session_id = session_id();
        $_SESSION['current_time'] = time();
        $current_time = $_SESSION['current_time'];

        # inserting record of current section
        $sql = "INSERT INTO login_attempts (session_id, currenttime) VALUES ('$session_id', '$current_time')";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            //echo "Works";
        } else {
            //echo "error";
        }

        # getting current time from database because time() in php always just fetches time at the moment
        $sql = "SELECT currenttime FROM login_attempts WHERE session_id = '$session_id'";
        $result = mysqli_query($conn, $sql);
        $info = mysqli_fetch_assoc($result);
        $_SESSION['current_time'] = $info['currenttime'];
        $_SESSION['cooldown_time'] = $current_time + 120;
    }

    # runs if more 3 or more attempts detected
    if ($_SESSION['attempts'] >= 3) {

    ?>
        <h2>Log in here!</h2>
        <form action="loginbackend.php" method="POST">

            <label>Email:</label>
            <input type="text" name="email" value="<?php echo $_SESSION['email'] ?? ''; ?>">

            <label>Password:</label>
            <input type="password" name="password" value="">

            <input type="submit" name="submit" value="submit">
        </form>

        <?php

        if (time() <= $_SESSION['cooldown_time']) {
            echo "Too many attempts! Please wait: " . $_SESSION['cooldown_time'] - time() . " second(s).";
        } else {
            $_SESSION['attempts_flag'] = 1;
            $session_id = session_id();

            # delete session id from login_attempts table
            $sql = "DELETE FROM login_attempts WHERE session_id = '$session_id'";
            $result = mysqli_query($conn, $sql);
            $_SESSION['current_time'] = null;
            $_SESSION['cooldown_time'] = null;

            if ($result) {
                //echo "Works";
            } else {
                //echo "error";
            }
        }

        # runs if login attempts < 3
    } else {

        ?>

        <div>
            <h2>Log in here!</h2>
            <form action="loginbackend.php" method="POST">

                <label>Email:</label>
                <input type="text" name="email" value="<?php echo $_SESSION['email'] ?? ''; ?>">

                <label>Password:</label>
                <input type="password" name="password" value="">

                <input type="submit" name="submit" value="submit">

            <?php
            if (isset($_SESSION['login_errors'])) {
                foreach ($_SESSION['login_errors'] as $error) {
                    echo "<br>" . $error . "</br>";
                }
            } else if (empty($_SESSION['login_errors'])) {
                echo "";
            }
        }
        # echo "<p>Attempts " . $attempts . "</p>";
        # echo "<p>Time " . time() . "</p>";
        # echo "<p>Cooldown time " . $_SESSION['cooldown_time'] . "</p>";
        # echo "<p>Current time " . $_SESSION['current_time'] . "</p>";
        # echo "<p>Attempt flag " . $_SESSION['attempts_flag'] . "</p>";
            ?>
            </form>
            <a href="index.php">Back</a>
        </div>
</body>

</html>