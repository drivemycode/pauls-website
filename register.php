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
    <title>Register</title>
</head>

<body>
    <div>
        <h2>Register here!</h2>
        <form action="registerbackend.php" method="POST">
            <label>Name:</label>
            <input type="text" name="name" value="<?php echo $_SESSION['name'] ?? ''; ?>">

            <label>Email:</label>
            <input type="text" name="email" value="<?php echo $_SESSION['email'] ?? ''; ?>">

            <label>Password:</label>
            <input type="password" name="password" value="">

            <label>Repeat Password:</label>
            <input type="password" name="password-repeat" value="">

            <input type="submit" name="submit" value="submit">
            <?php
            if (isset($_SESSION['register_errors'])) {
                foreach ($_SESSION['register_errors'] as $error) {
                    echo "<br>" . $error . "</br>";
                }
            } else if (empty($_SESSION['register_errors'])) {
                echo "";
            }
            session_unset();
            ?>
        </form>
        <a href="index.php">Back</a>
    </div>
</body>

</html>