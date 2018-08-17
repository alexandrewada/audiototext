<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL & ~E_NOTICE);
    set_time_limit(0);
    require_once "vendor/autoload.php";  
    $bootstrap = new Audio\Bootstrap($_GET['tipo'],$_GET['v'],$_GET['encode'],$_GET['hertz']);
