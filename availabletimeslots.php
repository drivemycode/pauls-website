<?php
session_start();
require('conn.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Timeslots</title>
</head>
<body>
<?php
    
    if(isset($_SESSION['logged_in'])){
        
        if(!isset($_SESSION['is_admin'])){
           header("Location: bookings.php");
           exit();
        } else {
            $open_slots = array();
            $delete_buttons = array();
            $sql = "SELECT * FROM lessons_slots";
            $result = mysqli_query($conn, $sql);
            while($row = mysqli_fetch_assoc($result)){
                array_push($open_slots, $row);
            }

            echo "<form action=\"availabletimeslotsbackend.php\" method=\"post\">";
            echo "
        <table>
        <tr>
            <th>lesson slot id</th>
            <th>day</th>
            <th>start time</th>
        </tr> ";

        for($i = 0; $i <= count($open_slots) - 1; $i ++){
            $delete_html = "<th>
            <input type=\"submit\" name=\"delete" . $i + 1 . "\" value=\"delete\">
            </th>";
            $delete_id = "delete" . $i + 1;
            #adding delete buttons to open slots 2d array so that foreach loop works on it
            array_push($open_slots[$i], $delete_html, $delete_id);     
        }

        
        foreach($open_slots as $open_slot){

            if(isset($open_slot)){
            echo "<tr>";
            echo "<th>";
            echo $open_slot['lesson_slot_id'];
            echo "</th>";
            echo "<th>";
            echo $open_slot['day'];
            echo "</th>";
            echo "<th>";
            echo $open_slot['start_time'];
            echo "</th>";
            echo "<th>";
            echo $open_slot[0];
            echo "</th>";
            }
        }       

        echo "</table>";
        
        echo "<label>Insert Day: ";
        echo "<input name=\"day\" type=\"day\"> ";
        echo "<label>Insert Start Time: ";
        echo "<input name=\"start_time\" type=\"start_time\"> ";
        echo "<input type=\"submit\" name=\"submit\" value=\"Add New Timing\">";
        echo "</form>";

        $_SESSION['open_slots'] = $open_slots;
        
        if(isset($_SESSION['availabletimeslots_messages']) && !empty($_SESSION['availabletimeslots_messages'])){
            foreach($_SESSION['availabletimeslots_messages'] as $message){
                echo "<p>" . $message . "</p>";
            }
        }

        if(isset($_SESSION['lesson_conflicts']) && !empty($_SESSION['lesson_conflicts'])){
            foreach($_SESSION['lesson_conflicts'] as $lesson_conflict){
                echo "<p>" . $lesson_conflict['instrument'] . " lesson on " . $lesson_conflict['day'] . " from " . $lesson_conflict['start_time'] . " to " . $lesson_conflict['end_time'] . " is cancelled.";
            }
        }
        }
    } else {
        header("Location: index.php");
    }
    //var_dump($_SESSION);
?>
<a href="makebookings.php">Back</a>
</body>
</html>