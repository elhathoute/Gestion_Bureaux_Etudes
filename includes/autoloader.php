<?php

spl_autoload_register(function($class){
    require 'classes/' . strtolower($class) . '.class.php';
    // require 'classes/role.class.php'; 
});



?>