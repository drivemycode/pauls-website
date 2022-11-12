<?php
require('conn.php');
require('functions.php');

if (isset($_REQUEST)) {
?>
    <h1><?= "YOLO :D" ?></h1>
    <h1><?php print_r($_REQUEST) ?></h1>
<?php
}
