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
    <?php
    if (!isset($_SESSION['is_admin'])) {
        echo "<title>Student Profile</title>";
    } else {
        echo "<title>Student Profiles</title>";
    }
    ?>
    <style>
        .studentprofile {
            flex-wrap: wrap;
            width: 800px;
            height: auto;
            border-style: dashed;
            border-color: black;
            margin-bottom: 10px;
        }

        .studentprofilecell {
            margin: 15px;
            padding: 10px;
            border-style: solid;
            border-color: black;
            max-width: 50%;
        }

        .unchecked {
            opacity: 0.4;
        }

        .checked {
            opacity: 1;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
</head>

<body>
    <?php

    if (isset($_SESSION['logged_in'])) {

        if (!isset($_SESSION['is_admin'])) {
        } else {
            # displaying all student profiles
    ?>
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Edit student profile</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="form-edit" action="post">
                            <div class="modal-body">
                                <select class="form-select" aria-label="dropdown">
                                    <label for="form-select">Student</label>
                                    <option selected>Try this</option>
                                    <option id="option1" value="1">Op 1</option>
                                    <option id="option2" value="2">Op 2</option>
                                </select>
                                <select class="form-select" aria-label="dropdown">
                                    <label for="form-select">Yawn</label>
                                    <option selected>Try this</option>
                                    <option id="option3" value="3">Op 3</option>
                                    <option id="option4" value="4">Op 4</option>
                                </select>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button id="submit" type="button" class="btn btn-primary">Save changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php
            $students = [];
            $sql = "SELECT * FROM users";
            $result = mysqli_query($conn, $sql);
            while ($row = mysqli_fetch_assoc($result)) {
                $name = $row['username'];
                $id = $row['user_id'];
                array_push($students, ["name" => $name, "id" => $id]);
            };
            // next: fetch data from related student progress tables
            $skillsets = selectFromTable("SELECT * FROM skillsets", $conn);
            $songs_learned = selectFromTable("SELECT * FROM songs_learned", $conn);
            $json = json_encode($skillsets);
            file_put_contents("data.json", $json);

            foreach ($students as $student) {
                // request student progress stats from mysql
                $skillsets = selectFromTable("SELECT * FROM skillsets WHERE user_id =" . $student["id"], $conn);
                $songs_learned = selectFromTable("SELECT * FROM songs_learned WHERE user_id =" . $student["id"], $conn);
                /* $skillsjson = json_encode($skillsets);
                $songsjson = json_encode($songs_learned);
                print_r($skillsjson);
                print_r($songsjson); */
                echo "
                <div id=\"" . $student["id"] . "\" style=\"display: flex;\">
                <p>" . $student['name'] . "</p>
                <button class=\"profilebutton\" style=\"height: 50%; margin-left: 5px;\">Show Profile</button>
                </div>
                <div class=\"studentprofile\" id=\""  . $student["id"] . "\" style=\"display: none;\">
                ";
            ?>
                <!-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                    Edit
                </button> -->

        <?php
                if (array_key_exists(0, $skillsets)) {
                    echo "<button type=\"button\" class=\"btn btn-primary\" data-bs-toggle=\"modal\" data-bs-target=\"#exampleModal\">
                    Edit
                </button>";
                    echo "<div id=\"skillset\" class=\"studentprofilecell\">";
                    $skillsets_names = array_map("modify", array_keys($skillsets[0]));
                    echo "<h1>Skills acquired</h1>";
                    foreach ($skillsets[0] as $key => $value) {
                        $key = modify($key);
                        if (str_contains(strtolower($key), "instrument")) {
                            echo "<div class=\"checked\"><span>$key</span> - <span>$value</span></div>";
                        } else {
                            if (!str_contains(strtolower($key), "id")) {
                                if ($value == 1) {
                                    echo "<div class=\"checked\">$key</div>";
                                } /* else {
                                    echo "<div class=\"unchecked\">$key</div>";
                                } */
                            }
                        }
                    }
                } else {
                    echo "<div id=\"skillset\" class=\"studentprofilecell\">";
                    echo "<div>--This student has no profile yet!--</div>";
                }
                // closing div tag for skillsets
                echo "</div>";

                if (array_key_exists(0, $songs_learned)) {
                    echo "<div id=\"songs\" class=\"studentprofilecell\">";
                    echo "<h1>Songs learned</h1>";
                    foreach ($songs_learned[0] as $key => $value) {
                        if (str_contains($key, "song_id")) {
                            $songs = selectFromTable("SELECT * FROM songs WHERE song_id = '$value'", $conn);
                            // echo "<div>" . $songs["song_name"] . " by " . $songs["song_artist"] . "</div>";
                            if (array_key_exists(0, $songs)) {
                                echo "<div>" . $songs[0]["song_name"] . " by " . $songs[0]["song_artist"] . "</div>";
                            }
                        }
                    }
                    echo "</div>";
                } else {
                }


                // closing div tag for student profile
                echo "</div>";
            }
        }
        ?>
        <div id="edit"></div>
    <?php
        //var_dump($songs_learned);
    } else {
        header("Location: index.php");
    }
    ?>
    <a href="index.php">Back</a>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            /* $('div.modal-body') */
            $('button.profilebutton').on('click', function() {
                const str = $(this).html() === "Show Profile" ? "Hide Profile" : "Show Profile";
                $(this).html(str);
                const profile = $(this).parent().next();
                const studentID = profile.attr("id");
                profile.toggle();
                $.ajax({
                    url: "student.php",
                    data: "studentID=" + studentID,
                    success: function() {
                        profile.load('student.php');
                    }
                })
            })

            /*  $('#submit').on('click', function(e) {
                 e.preventDefault();
                 const form = $('#form-edit');
                 const data = form.serialize;
                 $.ajax({
                     url: 'editstudentprofile.php',
                     data: data,
                     success: function() {
                         $('#edit').load('editstudentprofile.php');
                     }
                 })
             }) */
        })
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-u1OknCvxWvY5kfmNBILK2hRnQC3Pr17a+RTT6rIHI7NnikvbZlHgTPOOmMi466C8" crossorigin="anonymous"></script>
</body>

</html>