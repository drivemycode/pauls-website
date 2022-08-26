<?php
session_start();
echo "logging out...";
session_destroy();
header("Location: index.php");

