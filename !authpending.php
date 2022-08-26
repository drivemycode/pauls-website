<?php

session_start();
echo("email sent, authentication pending...");
$time = time();
$expiry_time = $time + 15*60;

while($time <= $expiry_time){
    if($_SESSION['auth'] == true){
        exit("Email authenticated, user account created!");
    }
}
