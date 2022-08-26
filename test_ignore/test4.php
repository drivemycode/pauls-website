<?php
require('conn.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar</title>
</head>
<body>
    <?php
    $slots = array();
    $calendar = array();
    $sql = "SELECT * FROM booked_in_slots";
    $result = mysqli_query($conn, $sql);
    while($row= mysqli_fetch_assoc($result)){
        array_push($slots, $row);
    }
    $date = new DateTime(date("Y-m-d")); // Y-m-d
    $date->add(new DateInterval('P30D'));
    $now = date("Y-m-d");
    $end = $date->format('Y-m-d') . "\n";
    $now_array = explode("-", $now);
    $end_array = explode("-", $end);

    echo "<table>";
    echo "<tr>";

    # outputting day row
    for($i = 0; $i <= 6; $i ++){
        echo "<th>" . date("l", mktime(0,0,0, date("m"), date("d")+$i, date("Y"))) . "</th>";
    }
    echo "</tr>";
    
    # getting hour specific timings into array
    for($i = 0; $i <= 6; $i ++){
        $calendar_slots = array();
        for($k = 0; $k <= count($slots) - 1; $k ++){
            if(date("l", mktime(0,0,0, date("m"), date("d")+$i, date("Y"))) == trim($slots[$k]['day'])){
               array_push($calendar_slots, "<td>" . $slots[$k]['start_time'] . " to " . $slots[$k]['end_time'] . ", " . $slots[$k]['instrument'] . " lesson." . "</td>");
            //    $i = $i + 1;
            } else {
                array_push($calendar_slots, "<td>" . "nothing" . "</td>");
            }
            array_push($calendar, $calendar_slots);
        }
    }

    echo "<tr>";
    # outputting slots in table using array
    for($i = 0; $i <= 6; $i ++){
        foreach($calendar as $calendar_slots){
            print_r($calendar_slots);
        }
    }
    
    echo "</tr>";
    echo "</table>";

    ?>
</body>
</html>

