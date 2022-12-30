<?php
require('conn.php');
require('functions.php');
session_start();

if (isset($_SESSION['logged_in'])) {

    if (!isset($_SESSION['is_admin'])) {
        header("Location: index.php");
    } else {
        # main code here
        print_r($_POST);
    }
} else {
    header("Location: index.php");
}
