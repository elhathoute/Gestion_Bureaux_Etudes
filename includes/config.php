<?php

//define('DATABASE_HOST', getenv('IP'));
define('DATABASE_HOST', "localhost");
define('DATABASE_NAME', 'xbmenph_beplan');
define('DATABASE_USER', 'xbmenph_beplan');
define('DATABASE_PASS', '?O=20Qb1#b}x');


$cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);

if(session_status()=== PHP_SESSION_NONE){
    session_start();
}

if(mysqli_connect_errno()){
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
}
?>