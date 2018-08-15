<?php
 
 require_once "vendor/autoload.php";
 $translate = new Audio\Translate();
 $x = $translate->TranslatorArchive('audio/audio_example.flac');
 echo 'Textos: '.$x;
 ?>
