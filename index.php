<?php

require_once "vendor/autoload.php";

$con = new Audio\Manipulation();
// $con->mp3toFlac('audio/x.mp3','audio/track.flac');
echo $con->getDurationAudio('audio/x.mp3');

