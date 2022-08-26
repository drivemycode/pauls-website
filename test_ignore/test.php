<?php

# location of template file
$template_file = "./template.php";

$to = "ironboldz@gmail.com";
$subject = "Email Authentication";

# create email headers
$headers = "From: Paul Lee <paulwebsite.verify@gmail.com>\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

# create html message
if(file_exists($template_file)){
    $message = file_get_contents($template_file);
} else {
    die("Unable to locate template file.");
}

echo $to . $subject . $message . $headers;

if(mail($to, $subject, $message, $headers) == true){
    echo "Success!";
}