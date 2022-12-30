<?php
session_start();
require('conn.php');
require('functions.php');
$_SESSION['uploadfiles_errors'] = [];

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: lessonfiles.php");
}
if (empty($_FILES)) {
    array_push($_SESSION['uploadfiles_errors'], '$_FILES is empty - is file_uploads set to "Off" in php.ini?');
}
if ($_FILES["file"]["error"] !== UPLOAD_ERR_OK) {

    switch ($_FILES["file"]["error"]) {
        case UPLOAD_ERR_PARTIAL:
            array_push($_SESSION['uploadfiles_errors'], 'File only partially uploaded');
            break;
        case UPLOAD_ERR_NO_FILE:
            array_push($_SESSION['uploadfiles_errors'], 'No file was uploaded');
            break;
        case UPLOAD_ERR_EXTENSION:
            array_push($_SESSION['uploadfiles_errors'], 'File upload stopped by a PHP extension');
            break;
        case UPLOAD_ERR_FORM_SIZE:
            array_push($_SESSION['uploadfiles_errors'], 'File exceeds MAX_FILE_SIZE in the HTML form');
            break;
        case UPLOAD_ERR_INI_SIZE:
            array_push($_SESSION['uploadfiles_errors'], 'File exceeds upload_max_filesize in php.ini');
            break;
        case UPLOAD_ERR_NO_TMP_DIR:
            array_push($_SESSION['uploadfiles_errors'], 'Temporary folder not found');
            break;
        case UPLOAD_ERR_CANT_WRITE:
            array_push($_SESSION['uploadfiles_errors'], 'Failed to write file');
            break;
        default:
            array_push($_SESSION['uploadfiles_errors'], 'Unknown upload error');
            break;
    }
}
if ($_FILES["file"]["size"] > 41943040) {
    array_push($_SESSION['uploadfiles_errors'], 'File too large (max 40MB).');
}
if (!isset($_POST)) {
    array_push($_SESSION['uploadfiles_errors'], 'Make sure you filled in all fields!');
}
if (empty($_POST['email'])) {
    array_push($_SESSION['uploadfiles_errors'], 'Make sure you selected a user!');
}
if (!empty($_SESSION['uploadfiles_errors'])) {
    header("Location: lessonfiles.php");
} else {
    $pathinfo = pathinfo($_FILES["file"]["name"]);
    # file name that is saved to
    $raw_filename = $pathinfo['filename'];
    $filename = preg_replace("/[^\w-]/", "_", $raw_filename);
    $destination = __DIR__ . "/lessonfiles/" . $filename;
    $filetype = explode("/", $_FILES["file"]["type"])[0];
    # check if file already exists
    if (empty(selectFromTable("SELECT * FROM lessons_files WHERE file_name = '$filename' AND file_type = '$filetype'", $conn))) {
        if (!move_uploaded_file($_FILES["file"]["tmp_name"], $destination)) {
            array_push($_SESSION['uploadfiles_errors'], 'File was unable to be moved; try again.');
            header("Location: lessonfiles.php");
        } else {
            if ($_POST['email'] !== "all") {
                $user_id = fetchID("user_id", [trim($_POST['email'])], $conn);
                insertIntoTable("lessons_files", [$user_id, $filename, $filetype, $destination], $conn);
                header("Location: lessonfiles.php");
            } else {
                $users = selectFromTable("SELECT * FROM users", $conn);
                foreach ($users as $user) {
                    header("Location: lessonfiles.php");
                    insertIntoTable("lessons_files", [$user['user_id'], $filename, $filetype, $destination], $conn);
                }
            }
        }
    } else {
        array_push($_SESSION['uploadfiles_errors'], 'File with the same name already exists in database.');
        header("Location: lessonfiles.php");
    }
}
