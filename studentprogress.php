<?php
session_start();
require('conn.php');
require('functions.php');

if (isset($_GET)) {
    $user_id = $_GET["user_id"];
    $username = $_GET['username'];
    $instrument_levels = $indexed_instruments = [];
    $skillsets = selectFromTable("SELECT * FROM skillsets WHERE user_id =" . $user_id, $conn);
    $song_logs = selectFromTable("SELECT * FROM songs_learned WHERE user_id =" . $user_id, $conn);
    $instruments = selectFromTable("SELECT instrument FROM instruments", $conn);
    foreach ($instruments as $instrument) {
        array_push($indexed_instruments, trim($instrument['instrument']));
    }
    $_SESSION['studentprogress_errors'] = [];
    if (empty($skillsets)) {
        array_push($_SESSION['studentprogress_errors'], "User does not have any skills saved! Edit to add skills.");
        header("Location: students.php");
    } else {


        # skills acquired and instrument level
        foreach ($skillsets as $skillset) {

            # data for skills acquired and instrument_levels
            $cc = $fp = $bc = $ho = [];
            array_push($cc, $skillset['cowboy_chords']);
            array_push($fp, $skillset['finger_picking']);
            array_push($bc, $skillset['barre_chords']);
            array_push($ho, $skillset['hammer_ons']);

            # handling instrument levels 
            if (in_array(trim($skillset['instrument']), $indexed_instruments)) {
                array_push($instrument_levels, "<li>" . ucfirst($skillset['instrument']) . " - Level " . $skillset['instrument_level'] . "</li>");
            }
        }
        if (empty($instrument_levels)) {
            $instrument_levels = "<h2>Instrument levels</h2>" . "<p>No instruments yet.</p>";
        } else {
            $temp_instrument_levels = $instrument_levels;
            $instrument_levels = "<h2>Instrument levels</h2><ul>";
            foreach ($temp_instrument_levels as $elem) {
                $instrument_levels = $instrument_levels . $elem;
            }
            $instrument_levels = $instrument_levels . "</ul>";
        }

        # skills acquired
        sort($cc);
        sort($fp);
        sort($bc);
        sort($ho);
        if ($cc[0] == "0" && $fp[0] == "0" && $bc[0] == "0" && $ho[0] == "0") {
            $skills_acquired = "<h2>Skills acquired</h2>" . "<p>No skills yet.</p>";
        } else {
            $skills_acquired = "<h2>Skills acquired</h2>" . "<ul>" .
                ($cc[0] == '1' ? "<li>Knows how to play cowboy chords</li>" : "") .
                ($fp[0] == '1' ? "<li>Knows how to finger pick</li>" : "") .
                ($bc[0] == '1' ? "<li>Knows how to play barre chords</li>" : "") .
                ($ho[0] == '1' ? "<li>Knows how to play hammer-ons</li>" : "")
                . "</ul>";
        }

        # songs learned
        if (empty($song_logs)) {
            $songs_learned = "<h2>Songs learned</h2>" . "<p>No songs yet.</p>" . "</h2>";
        } else {
            $songs_learned = "<h2>Songs learned</h2><ul>";
            foreach ($song_logs as $song_log) {
                $song_id = $song_log['song_id'];
                $song = selectFromTable("SELECT * FROM songs WHERE song_id =" . $song_id, $conn)[0];
                $songs_learned = $songs_learned . "<li>" . $song["song_name"] . " by " . $song["song_artist"] . "</li>";
            }
            $songs_learned = $songs_learned . "</ul>";
        }
        $dataPoints = array(
            array("label" => 1000, "y" => 254722.1),
            array("label" => 1998, "y" => 292175.1),
            array("label" => 1999, "y" => 369565),
            array("label" => 2000, "y" => 284918.9),
            array("label" => 2001, "y" => 325574.7),
            array("label" => 2002, "y" => 254689.8),
            array("label" => 2003, "y" => 303909),
            array("label" => 2004, "y" => 335092.9),
            array("label" => 2005, "y" => 408128),
            array("label" => 2006, "y" => 300992.2),
            array("label" => 2007, "y" => 401911.5),
            array("label" => 2008, "y" => 299009.2),
            array("label" => 2009, "y" => 319814.4),
            array("label" => 2010, "y" => 357303.9),
            array("label" => 2011, "y" => 353838.9),
            array("label" => 2012, "y" => 288386.5),
            array("label" => 2013, "y" => 485058.4),
            array("label" => 2014, "y" => 326794.4),
            array("label" => 2015, "y" => 483812.3),
            array("label" => 2016, "y" => 254484)
        );
?>

        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?= $username ?></title>
            <style>
                .grid-container {
                    display: grid;
                    grid-template-columns: 1fr 2fr;
                    grid-auto-rows: minmax(150px, auto);
                    grid-gap: 20px;
                    justify-content: center;
                    align-content: center;
                    height: 100vh;
                }

                .grid-item-1 {
                    grid-column: 1 / -1;
                }

                .grid-item-2 {
                    grid-row: 2 / 4;
                }

                .grid-item-3 {
                    grid-row: span 1;
                }

                .grid-item {
                    position: relative;
                    font-size: 25px;
                    padding: 20px;
                    padding-top: 50px;
                    background-color: #379AD6;
                    color: #222;
                    border: 1px solid white;
                }

                .grid-item:nth-child(odd) {
                    background-color: #5bbdfa;
                }
            </style>
            <script>
                window.onload = function() {

                    const chart = new CanvasJS.Chart("chartContainer", {
                        title: {
                            text: "Finger Exercises"
                        },
                        axisY: {
                            title: "Number of Push-ups"
                        },
                        data: [{
                            type: "line",
                            dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
                        }]
                    });
                    chart.render();
                }
            </script>
        </head>

        <body>
            <div class="grid-container">
                <div class="grid-item grid-item-1">
                    <?= "<a href=\"students.php\">Go back</a>" ?>
                    <h1><?= $username . "'s profile" ?></h1>
                </div>
                <div class="grid-item grid-item-2"><?= $skills_acquired ?></div>
                <div class="grid-item grid-item-3"><?= $songs_learned ?></div>
                <div class="grid-item grid-item-4"><?= $instrument_levels ?></div>
                <div class="grid-item grid-item-5"></div>
            </div>
            <div id="chartContainer" style="height: 370px; display:auto;"></div>
            <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>

        </body>

        </html>
<?php

    }
} else {
    header("Location: index.php");
}
