<?php

require_once "vendor/autoload.php";

$ffmpeg = FFMpeg\FFMpeg::create([
    'ffmpeg.binaries'  => 'bin\ffmpeg.exe',
    'ffprobe.binaries' => 'bin\ffprobe.exe'
]);

$audio = $ffmpeg->open('audio/x.mp3');

$format = new FFMpeg\Format\Audio\Flac();
$format->on('progress', function ($audio, $format, $percentage) {
    echo "$percentage % transcoded";
});

$format
    ->setAudioChannels(1)
    ->setAudioKiloBitrate(256);

$audio->save($format, 'audio/track.flac');

//  $translate = new Audio\Translate();

//  $translate->config['languageCode']     = 'pt-BR';
//  $translate->config['encoding']         = 'LINEAR16';
//  $translate->config['sampleRateHertz']  = 16000;

//  $x = $translate->TranslatorArchiveSync('audio/audio_example.flac');
//  echo 'Textos: '.$x;
//
