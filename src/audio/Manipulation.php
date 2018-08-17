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

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $this->ffmpeg = FFMpeg\FFMpeg::create([
                'ffmpeg.binaries' => 'bin\ffmpeg.exe',
                'ffprobe.binaries' => 'bin\ffprobe.exe',
            ]);
    
            $this->ffprobe = FFMpeg\FFProbe::create([
                'ffmpeg.binaries' => 'bin\ffmpeg.exe',
                'ffprobe.binaries' => 'bin\ffprobe.exe',
            ]);
            
        } else {
            $this->ffmpeg = FFMpeg\FFMpeg::create([
                'ffmpeg.binaries' => 'bin-linux/ffmpeg',
                'ffprobe.binaries' => 'bin-linux/ffprobe',
            ]);
    
            $this->ffprobe = FFMpeg\FFProbe::create([
                'ffmpeg.binaries' => 'bin-linux/ffmpeg',
                'ffprobe.binaries' => 'bin-linux/ffprobe',
            ]);
        }
    }

    // Pegar duração do audio
    public function getDurationAudio($audioPatch)
    {
        return $this->ffprobe->format($audioPatch)->get('duration');
    }

    // Transformar mp3 para flac
    public function mp3toFlac($mp3Patch, $flacPatch,$cutInit='false',$cutFinish='false')
    {
        // Só faz algo se flac não existir
        if (!file_exists($flacPatch)) {
            // Só faz algo de mp3 exisitr
            if (file_exists($mp3Patch)) {
                // Abri o mp3
                $audio = $this->ffmpeg->open($mp3Patch);
                
                $format = new FFMpeg\Format\Audio\Flac();
                // Se ta o channel para 1
                $format
                    ->setAudioChannels(1)
                    ->setAudioKiloBitrate(256);

                // Se for informado tempo de cut então iremos recortar o mp3
                if($cutInit != false && $cutFinish != false){
                    $audio->filters()->clip(FFMpeg\Coordinate\TimeCode::fromSeconds($cutInit), FFMpeg\Coordinate\TimeCode::fromSeconds($cutFinish));
                    
                }
                
                return $audio->save($format, $flacPatch);
            }
        }
    }

}
