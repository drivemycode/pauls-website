<?php

require('functions.php');
require('conn.php');

writeToJSONFile(selectFromTable("SELECT * FROM songs_learned", $conn), "songslearned.json");
writeToJSONFile(selectFromTable("SELECT * FROM skillsets", $conn), "skillsets.json");
$songs_learned = json_decode(file_get_contents('songslearned.json'), true);
$skillsets = json_decode(file_get_contents('skillsets.json'), true);
$students = selectFromTable("SELECT * FROM users", $conn);
/* var_dump($skillsets); */
foreach ($students as $student) {
?>
    <div class="main" id=<?= $student['user_id'] ?>>
        <h1><?= $student['username'] ?></h1>
        <div class="module">
            <h3>Skills</h3>

        </div>
    </div>
<?php
}
