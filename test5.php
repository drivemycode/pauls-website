<?php
require('conn.php');
require('functions.php');

/* $date = date('Y-m-d H:i:s');
insertIntoTable('finger_exercise_1', [100, $date], $conn); */
/* insertIntoTable('songs', ['Not At All', 'Rick Ross'], $conn); */
/* $id1 = fetchID('paul_usersid', ["ironboldz@gmail.com"], $conn);
$id2 = fetchID('booked_in_slot_id', [26, " guitar "], $conn);
$id3 = fetchID('song_id', ['Not At All'], $conn); */
/* echo $id1 . '<br>';
echo $id2 . '<br>';
echo $id3 . '<br>'; */
/* insertIntoTable('skillsets', [$id, 'unset', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0], $conn); */
/* deleteFromTable('songs', ['song_name', 'song_artist'], ['Not At All', 'Rick Ross'], $conn); */
/* $haystack = "lol9000";
$statement = str_contains($haystack, "jlol") ? "true" : "false";
echo $statement; */
/* $students = ["Alice" => "1", "Bob" => "2", "J_o" => "3", "Eileen" => "4",];
foreach ($students as $key => $value) {
    if (str_contains($key, "bob")) {
        echo $key;
    };
    echo "<br>";
}
 */
/* $user_id = 31;
$instrument = "guitar";
$sql = "SELECT * FROM booked_in_slots WHERE user_id = '$user_id' AND instrument LIKE '$instrument'";
$error_messages = ['im empty'];
$array = selectFromTable($sql, $conn);

if (!empty($array)) {
    array_push($error_messages, "You already have a lesson for $instrument! If you want to edit the timing of your booking, go to View Booking.");
}

var_dump($array);
print_r($error_messages);
 */
/* 
$arr = [['name' => 'bob', 'age' => 9, 'gender' => "M"], ['name' => 'min', 'age' => 28, 'gender' => "F"], ['name' => 'jame', 'age' => 23, 'gender' => "M"]];
print_r(returnKeyArray($arr, 'gender')); */
/* $skillsets = json_decode(file_get_contents('skillsets.json'), true);
print_r(filter($skillsets, ['user_id', 'instrument'], 2)); */


echo updateTable('booked_in_slots', ['user_id', 'start_time', 'end_time', 'day', 'instrument'], ['old 1', 'old 2', 'old 3', 'old 4', 'old 5'], ['new 1', 'new 2', 'new 3', 'new 4', 'new 5'], $conn);
