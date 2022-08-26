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
    <title>Unavailable Timeslots</title>
</head>
<body>
<?php
    
    if(isset($_SESSION['logged_in'])){
        
        if(!isset($_SESSION['is_admin'])){
            header("Location: bookings.php");
            exit();
           
        } else {

            # admin side
            $closed_slots = array();
            $delete_buttons = array();
            $sql = "SELECT * FROM closed_slots";
            $result = mysqli_query($conn, $sql);
            while($row = mysqli_fetch_assoc($result)){
                array_push($closed_slots, $row);
            }

            # following lines will be similiar to code written for available timeslots
            echo "<form action=\"unavailabletimeslotsbackend.php\" method=\"post\">";
            echo "
        <table>
        <tr>
            <th>unavailable slot id</th>
            <th>day</th>
            <th>start time</th>
            <th>end time</th>
        </tr> ";

        for($i = 0; $i <= count($closed_slots) - 1; $i ++){
            $delete_html = "<th>
            <input type=\"submit\" name=\"delete" . $i + 1 . "\" value=\"delete\">
            </th>";
            $delete_id = "delete" . $i + 1;
            #adding delete buttons to closed slots 2d array so that foreach loop works on it
            array_push($closed_slots[$i], $delete_html, $delete_id);     
        }

        foreach($closed_slots as $closed_slot){

            if(isset($closed_slot)){
            echo "<tr>";
            echo "<th>";
            echo $closed_slot['closed_slot_id'];
            echo "</th>";
            echo "<th>";
            echo $closed_slot['day'];
            echo "</th>";
            echo "<th>";
            echo $closed_slot['start_time'];
            echo "</th>";
            echo "<th>";
            echo $closed_slot['end_time'];
            echo "</th>";
            echo "<th>";
            echo $closed_slot[0];
            echo "</th>";
            }
        }   

        echo "</table>";
        
        echo "<label>Insert Day: ";
        echo "<input name=\"day\" type=\"day\"> ";
        echo "<label>Insert Start Time: ";
        echo "<input name=\"start_time\" type=\"start_time\"> ";
        echo "<label>Insert End Time: ";
        echo "<input name=\"end_time\" type=\"end_time\"> ";
        echo "<input type=\"submit\" name=\"submit\" value=\"Add New Timing\"> ";
        echo "</form>";

        $_SESSION['closed_slots'] = $closed_slots;
        
        if(isset($_SESSION['unavailabletimeslots_messages']) && !empty($_SESSION['unavailabletimeslots_messages'])){
            foreach($_SESSION['unavailabletimeslots_messages'] as $message){
                echo "<p>" . $message . "</p>";
            }
        }

        if(isset($_SESSION['closed_conflicts']) && !empty($_SESSION['closed_conflicts'])){
            foreach($_SESSION['closed_conflicts'] as $closed_conflict){
                echo "<p>" . $closed_conflict['instrument'] . " lesson on " . $closed_conflict['day'] . " from " . $closed_conflict['start_time'] . " to " . $closed_conflict['end_time'] . " is cancelled.";
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