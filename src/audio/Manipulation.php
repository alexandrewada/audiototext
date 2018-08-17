<?php

namespace Audio;

use FFMpeg;
use FFProbe;

class Manipulation
{

    public $ffmpeg;
    public $ffprobe;

    public function __construct()
    {

        $this->ffmpeg = FFMpeg\FFMpeg::create([
            'ffmpeg.binaries' => 'bin\ffmpeg.exe',
            'ffprobe.binaries' => 'bin\ffprobe.exe',
        ]);

        $this->ffprobe = FFMpeg\FFProbe::create([
            'ffmpeg.binaries' => 'bin\ffmpeg.exe',
            'ffprobe.binaries' => 'bin\ffprobe.exe',
        ]);

    }

    public function getDurationAudio($audioPatch)
    {
        return $this->ffprobe->format($audioPatch)->get('duration');
    }

    public function mp3toFlac($mp3Patch, $flacPatch,$cutInit='false',$cutFinish='false')
    {
        if (!file_exists($flacPatch)) {
            if (file_exists($mp3Patch)) {
                $audio = $this->ffmpeg->open($mp3Patch);
                $format = new FFMpeg\Format\Audio\Flac();
                // $format->on('progress', function ($audio, $format, $percentage) {
                //     echo "$percentage % transcoded";
                // });

                $format
                    ->setAudioChannels(1)
                    ->setAudioKiloBitrate(256);

                if($cutInit != false && $cutFinish != false){
                    $audio->filters()->clip(FFMpeg\Coordinate\TimeCode::fromSeconds($cutInit), FFMpeg\Coordinate\TimeCode::fromSeconds($cutFinish));

                }

                return $audio->save($format, $flacPatch);
            }
        }
    }

}
