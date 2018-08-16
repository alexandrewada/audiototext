<?php

    require_once "vendor/autoload.php";
    
    $tipo    = $_GET['tipo'];
    $audio   = $_GET['audio'];
    $id_youtube = $_GET['id_youtube'];

    if($tipo == 'youtube'){
        $yd             = new Audio\YoutubeDownloader();
        $m              = new Audio\Manipulation();
        $t              = new Audio\Translate();
        
        $t->config['encoding'] = 'FLAC';

        $urlDownload    = $yd->getDownload($id_youtube);

        $patchMp3       = 'storage/mp3_audios/'.$id_youtube.'.mp3';
        $patchFlac      = 'storage/flac_audios/'.$id_youtube.'.flac';

        $yd->saveFile($urlDownload,$patchMp3);
        $m->mp3toFlac($patchMp3,$patchFlac);
        $text = $t->TranslatorArchiveSync($patchFlac);
        
        echo $text;

    }