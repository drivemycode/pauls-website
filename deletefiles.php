<?php
require('conn.php');
require('functions.php');
session_start();

if (isset($_SESSION['logged_in'])) {

    if (!isset($_SESSION['is_admin'])) {
        header("Location: index.php");
    } else {
        # main code here
        deleteFromTable("lessons_files", ["file_name", "file_type"], [$_GET["file_name"], $_GET["file_type"]], $conn);
        header("Location: lessonfiles.php");
    }
} else {
    header("Location: index.php");
}
