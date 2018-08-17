<?php

namespace Audio;

use Audio\Manipulation;
use Audio\Translate;
use Audio\YoutubeDownloader;

class Bootstrap
{
    public $tipo;
    public $id_youtube;
    public $audioUrl;

    public function __construct($tipo, $youtubeOrAudio)
    {

        if (empty($youtubeOrAudio)) {
            echo ('Você precisa informar o valor de youtube id ou se for uma url');
            exit;
        }

        if ($tipo != 'youtube' && $tipo != 'url') {
            echo ("Você deve especificar tipo se é 'youtube' ou 'url'");
        } else {
            $this->tipo = $tipo;

            if ($this->tipo == 'youtube') {
                if (!$this->_checkIdYoutubeExist($youtubeOrAudio)) {
                    echo "Este ID do youtube não existe " . $youtubeOrAudio;
                    exit;
                } else {
                    $this->id_youtube = $youtubeOrAudio;
                }
            } else if ($this->tipo == 'url') {
                $this->audioUrl = $youtubeOrAudio;
            }
        }

        $this->_startProcess();

    }

    public function _startProcess()
    {
        $yd = new YoutubeDownloader();
        $m = new Manipulation();
        $t = new Translate();

        $t->config['encoding'] = 'FLAC';
        $t->config['sampleRateHertz'] = 44100;

        $patchMp3 = 'storage/mp3_audios/' . $this->id_youtube . '.mp3';
        $patchFlac = 'storage/flac_audios/' . $this->id_youtube . '.flac';
        $patchText = 'storage/texts/youtube/' . $this->id_youtube . '.txt';

        if (file_exists($patchText)) {
            echo file_get_contents($patchText);
            exit;
        }

        // Pegar url do mp3 do youtube
        $urlDownload = $yd->getDownload($this->id_youtube);

        if (!file_exists($patchMp3)) {
            $saveMP3 = $yd->saveFile($urlDownload, $patchMp3);
        } else {
            $saveMP3 = 1;
        }

        // Se o mp3 existir
        if (is_numeric($saveMP3)) {
            $tempoAudio = $m->getDurationAudio($patchMp3);
            $tempoPorAudio = 60;

       

            if ($tempoAudio >= $tempoPorAudio) {
                $parts = ceil($tempoAudio / $tempoPorAudio);
             
                $texto = [];
        
                for ($i = 0; $i < $parts; $i++) {
                    // $init      = ($tempoPorAudio*$i);
                    // $fim       = ($i == $parts-1 && $sobra != 0) ? ($init + $sobra) :  ($tempoPorAudio*($i+1));
                    $patchFlac = 'storage/flac_audios/'.$this->id_youtube .'_'. $i . '.flac';

                    $inicio =  ($i == 0) ? 0.1 : $tempoPorAudio * $i;

                    $convert = $m->mp3toFlac($patchMp3, $patchFlac, $inicio, $tempoPorAudio);

                    $texto[] = $t->TranslatorArchiveLine($patchFlac);


                }

        
            } else {
                $patchFlac = 'storage/flac_audios/'.$this->id_youtube.'.flac';
                $m->mp3toFlac($patchMp3, $patchFlac,0,1,60);
                $texto[] = $t->TranslatorArchiveLine($patchFlac);
        
            }



            #    $convert = $m->mp3toFlac($patchMp3, $patchFlac,0,10);
            // $texto = $t->TranslatorArchive($patchFlac);

            // if ($texto) {
            //     file_put_contents($patchText, $texto);
            //     echo $texto;
            // }

            print_r($texto);

        }

    }

    public function _checkIdYoutubeExist($videoID)
    {
        $theURL = "http://www.youtube.com/oembed?url=http://www.youtube.com/watch?v=$videoID&format=json";
        $headers = get_headers($theURL);
        return (substr($headers[0], 9, 3) !== "404");
    }

}
