<?php
session_start();
require('conn.php');
require('functions.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lesson Files</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    <style>
        .styled-table {
            border-collapse: collapse;
            margin: 25px 0;
            font-size: 0.9em;
            font-family: sans-serif;
            min-width: 400px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
        }

        .styled-table thead tr {
            background-color: #009879;
            color: #ffffff;
            text-align: left;
        }

        .styled-table th,
        .styled-table td {
            padding: 12px 15px;
        }

        .styled-table tbody tr {
            border-bottom: 1px solid #dddddd;
        }

        .styled-table tbody tr:nth-of-type(even) {
            background-color: #f3f3f3;
        }

        .styled-table tbody tr:last-of-type {
            border-bottom: 2px solid #009879;
        }

        .file-type-icon {
            max-width: 5em;
        }
    </style>
</head>

<body>

    <?php

    if (isset($_SESSION['logged_in'])) {

        if (!isset($_SESSION['is_admin'])) {
            header("Location: index.php");
        } else {
            # main code here
            $users = selectFromTable("SELECT * FROM users", $conn);
            $lessons_files = selectFromTable("SELECT * FROM lessons_files", $conn);
            $distinct_lessons_files = selectFromTable("SELECT DISTINCT file_name, file_type FROM lessons_files", $conn);
            $user_accesses = [];
            foreach ($distinct_lessons_files as $file) {
                $user_accesses[trim($file['file_name'])] = [];
            }

            # process 2D array $lessons_files, return $user_accesses array for Edit user access section
            foreach ($distinct_lessons_files as $file_name) {
                foreach ($lessons_files as $record) {
                    if (trim($file_name['file_name']) == trim($record['file_name'])) {
                        array_push($user_accesses[$file_name['file_name']], $record['user_id']);
                    }
                }
            }
            print_r($user_accesses);
    ?>
            <label for="lessonfiles">All lesson files</label>
            <form action="editaccess.php" enctype="multipart/form-data" method="post">
                <table id="lessonfiles" class="styled-table">
                    <thead>
                        <tr>
                            <th>File name</th>
                            <th>File type</th>
                            <th>
                                Edit user access
                                <button style="background-color: white; color:#009879;">Save changes</button>
                            </th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($distinct_lessons_files as $file) {
                        ?>
                            <tr>
                                <td><?= $file['file_name'] ?></td>
                                <td>
                                    <?= $file['file_type'] ?>
                                    <img class="file-type-icon" src=<?= "./assets/" . $file['file_type'] . ".png" ?> alt=<?= "Icon of " . $file['file_type'] ?>>
                                </td>
                                <td>
                                    <?php
                                    foreach ($user_accesses[$file['file_name']] as $user_id) {
                                        $users_with_no_access = [];
                                        $username = selectFromTable("SELECT username FROM users WHERE user_id =" . $user_id, $conn)[0]["username"];
                                        $flag = false;
                                        foreach ($users as $j => $user) {
                                            if (trim($user['username']) == trim($username)) {
                                                $flag = true;
                                                $index = $j;
                                            } else {
                                                array_push($users_with_no_access, $user);
                                            }
                                        }
                                        if ($flag) {
                                            unset($users_with_no_access[$j]);
                                        }
                                    ?>
                                        <input type="checkbox" name=<?= $user['user_id'] ?> value=<?= $user_id ?> checked>
                                        <label for=<?= $user_id ?>><?= $username ?></label><br><br>
                                        <?php }
                                    if (count($user_accesses) >= count($users_with_no_access)) {

                                        foreach (array_unique($users_with_no_access, SORT_REGULAR) as $user) {
                                        ?>
                                            <input type="checkbox" name=<?= $user['user_id'] ?> value=<?= $user['user_id'] ?>>
                                            <label for=<?= $user['user_id'] ?>><?= $user['username'] ?></label><br><br>
                                    <?php }
                                    }
                                    ?>
                                </td>
                                <td>
                                    <a href=<?= "deletefiles.php?file_name=" . $file['file_name'] . "&file_type=" . $file['file_type'] ?> class="">Delete file</a>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </form>
            <form method="post" enctype="multipart/form-data" action="uploadfiles.php">
                <label for="file">Upload lesson file</label>
                <input type="file" id="file" name="file">
                <label for="username">Assign a user to file</label>
                <select name="email" id="username">
                    <?php
                    foreach ($users as $user) {
                    ?> <option value=<?= $user['email'] ?>><?= $user['username'] ?></option>
                    <?php
                    }
                    ?>
                    <option value="all">All users</option>
                </select>
                <button>Upload</button>
            </form>
    <?php
            if (isset($_SESSION['uploadfiles_errors'])) {
                foreach ($_SESSION['uploadfiles_errors'] as $error) {
                    echo "<br>" . $error . "</br>";
                }
            } else if (empty($_SESSION['uploadfiles_errors'])) {
                echo "";
            }
            echo "<a href=\"index.php\">Back</a>";
        }
    } else {
        header("Location: index.php");
    }

    ?>
</body>

</html>