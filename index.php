<?php
    set_time_limit(0);
    require_once "vendor/autoload.php";  
    $bootstrap = new Audio\Bootstrap($_GET['tipo'],$_GET['v'],$_GET['encode'],$_GET['hertz']);
