<?php 
session_start();
require_once('conn.php');
require('functions.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
        if(!isset($_SESSION['is_admin'])){
            echo "<title>Make Bookings</title>";
        } else {
            echo "<title>Edit Timeslots</title>";
        }
    ?>
</head>
<body>

<!-- what a normal user sees -->
<?php

    if(isset($_SESSION['logged_in'])){

        if(!isset($_SESSION['is_admin'])){

        echo "<h2>Make Bookings</h2>";
        # use foreach loop to output booking selection details (start time, lesson duration, day, instrument) as html dropdown selection
        $str_start_timings = array();
        $int_start_timings = array();
        $start_timings = array();
        $closed_slots = array();
        $days = array();
        $lesson_slots = array();
        $lesson_lengths = array("30 minutes", "45 minutes", "1 hour", "2 hours and 45 minutes");
        $instruments = ['guitar', 'piano', 'vocal', 'drums'];
        $count_lesson_slots = 0;
        $count = 0;
        
        # making a sql query to fetch available start times and days from table in database
        $sql = "SELECT lesson_slot_id, start_time, day FROM lessons_slots";
        $result = mysqli_query($conn, $sql);
        while($row = mysqli_fetch_assoc($result)){
            array_push($str_start_timings, $row['start_time']);
            array_push($days, $row['day']);
            array_push($lesson_slots, $row);
            $count_lesson_slots = $count_lesson_slots + 1;
        }
        $days_displayed = array_unique($days);
        //var_dump($lesson_slots);
    
        # checking if lesson slots conflicts with unavailable time slots, make appropriate changes
        $sql = "SELECT * FROM closed_slots";
        $result = mysqli_query($conn, $sql);
        while ($row = mysqli_fetch_assoc($result)){
            array_push($closed_slots, $row);
        }
    
        foreach($closed_slots as $closed_slot){
            $counter = 0;
            foreach($lesson_slots as $lesson_slot){
                $counter = $counter + 1;
                if($closed_slot['day'] == $lesson_slot['day']){
                    $closed_start_time = str_to_int_timing($closed_slot['start_time']);
                    $closed_end_time = str_to_int_timing($closed_slot['end_time']);
                    $start_time = str_to_int_timing($lesson_slot['start_time']);

                    # if closed_slot conflicts with lesson_slot
                    $statement = $start_time >= $closed_start_time && $start_time <= $closed_end_time;

                    if($statement){
                        unset($lesson_slots[$counter - 1]);
                        $str_start_time = trim(int_to_str_timing($start_time));
                        $day = $lesson_slot['day'];

                        $sql = "DELETE FROM lessons_slots WHERE day = '$day' AND start_time = '$str_start_time'";
                        mysqli_query($conn, $sql);
                        $message = "Lesson slot of " . $str_start_time . " on " . $day . " was deleted as it conflicted with an unavailable slot.";
                        $title = "Timeslot Conflict";
                        email('ironboldz@gmail.com', $message, $title);
                        //var_dump($statement);
                        //var_dump($start_time, $closed_start_time, $closed_end_time);
                    }
                    
                }
            }
        }

        #since timings in 24 hour format the following code converts each timing into integer format.
        #for example, 17:30:00 becomes 173000
        foreach($str_start_timings as $str_start_timing){
            $int_start_timing = str_to_int_timing($str_start_timing);
            array_push($int_start_timings, $int_start_timing);
            
        }
        
        #getting rid of duplicate times, sorting the timings in ascending order
        $int_start_timings = array_unique($int_start_timings);
        sort($int_start_timings);

        # converting integer start timings back into h:m:s format, adding it to a new array
        # reordering lesson_slots according to start_timing
        foreach($int_start_timings as $int_start_timing){
            $start_timing = int_to_str_timing($int_start_timing);
            array_push($start_timings, $start_timing);

        }

        $_SESSION['lesson_slots'] = $lesson_slots;
        //var_dump($lesson_slots);

        #outputting lesson_slots in the form of a table    
        echo "
        <table>
        <tr>
            <th>lesson slot id</th>
            <th>day</th>
            <th>start time</th>
        </tr> ";

        foreach($lesson_slots as $lesson_slot){

            if(isset($lesson_slot)){
            echo "<tr>";
            echo "<th>";
            echo $lesson_slot['lesson_slot_id'];
            echo "</th>";
            echo "<th>";
            echo $lesson_slot['day'];
            echo "</th>";
            echo "<th>";
            echo $lesson_slot['start_time'];
            echo "</th>";
            echo "</tr>";
            }

        }

        echo "</table>";
        echo "<br>";

        echo "<form action=\"makebookingsbackend.php\" method=\"POST\">";
        echo "<label>Start timings:</label>";
        echo "<select name=\"start_timing\" id=\"start_timing\">";
        
        foreach($start_timings as $start_timing){

            echo '<option value=" ' . htmlspecialchars($start_timing) . ' " name=\'start_timing\'> '. htmlspecialchars($start_timing) . '</option>';
        
        }

        echo "</select>";
        echo "<label>Day:</label>";
        echo "<select name=\"day\" id=\"day\">";
        
        foreach($days_displayed as $day_displayed){

            echo '<option value=" ' . htmlspecialchars($day_displayed) . ' " name=\'day\'> '. htmlspecialchars($day_displayed) . '</option>';
        
        }

        echo "</select>";
        echo "<label>Lesson length:</label>";
        echo "<select name=\"lesson_length\" id=\"lesson_length\">";

        foreach($lesson_lengths as $lesson_length){

            echo '<option value=" ' . htmlspecialchars($lesson_length) . ' " name=\'lesson_length\'> '. htmlspecialchars($lesson_length) . '</option>';
        
        }

        echo "</select>";
        echo "<label>Instrument:</label>";
        echo "<select name=\"instrument\" id=\"instrument\">";

        foreach($instruments as $instrument){

            echo '<option value=" ' . htmlspecialchars($instrument) . ' " name=\'instrument\'> '. htmlspecialchars($instrument) . '</option>';

        }

        echo "</select>";
        echo "<input type=\"submit\" name=\"submit\" value=\"submit\">";
        echo "</form>";

        
        if(isset($_SESSION['usermakebookings_errors'])){
            foreach($_SESSION['usermakebookings_errors'] as $error){
                echo "<br>" . $error . "</br>";
            }
        } else if(empty($_SESSION['usermakebookings_errors'])) {
            echo "";
        }
        
        echo "<p>Lessons are booked in for every week!</p>";
    //var_dump($_SESSION['usermakebookings_errors']);
    
    echo "<a href=\"bookings.php\">Back</a>";
    } else {
        # admin edits available/unavailable timeslots instead of making bookings because that doesnt make sense.
    
    echo "<h2>Edit Timeslots</h2>";
    echo "<a href=\"availabletimeslots.php\">Available Timeslots</a>";
    echo "<a href=\"unavailabletimeslots.php\">Unavailable Timeslots</a>";
    echo "<br>";
    echo "<a href=\"bookings.php\">Back</a>";
    }
    } else {
        header('Location: http://' . $_SERVER['HTTP_HOST'] . '/paul%20v3/login.php');
        exit();
    }
    
# admin alternate calendar? (or maybe separate php file)
?>

</body>
</html>