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
    public $encode;
    public $hertz;

    public function __construct($tipo, $youtubeOrAudio,$encode='FLAC',$hertz='48000')
    {
        $this->encode = $encode;
        $this->hertz  = $hertz;

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

        if($this->tipo == 'youtube'){
            $this->_startProcessYoutube();
        } else if($this->tipo == 'url') {
            $this->_startProcessUrl();
        }

    }

    public function _startProcessUrl()
    {
        // Checa se URL é valida e também se ela é um force download direto
        $this->_checkUrlValid($this->audioUrl);
        // Atribui um ID unico para audio
        $id_audio = md5($this->audioUrl);

        $yd = new YoutubeDownloader();
        $m = new Manipulation();
        $t = new Translate();

        $t->config['encoding'] = $this->encode;
        $t->config['sampleRateHertz'] = $this->hertz;

        $patchMp3 = 'storage/mp3_audios/url/' . $id_audio . '.mp3';
        $patchFlac = 'storage/flac_audios/url/' . $id_audio . '.flac';
        $patchText = 'storage/texts/url/' . $id_audio . '.txt';

        if (file_exists($patchText)) {
            echo file_get_contents($patchText);
            exit;
        }

        // Checa se o mp3 já foi baixado se nao foi ele baixa
        if (!file_exists($patchMp3)) {
            $saveMP3     = $yd->saveFile($this->audioUrl, $patchMp3);
        // Se o mp3 já foi baixado atribui o save mp3 com 1
        } else {
            $saveMP3 = 1;
        }


        // Se o mp3 existir
        if (is_numeric($saveMP3)) {
            
            // Pega duração do mp3
            $tempoAudio = $m->getDurationAudio($patchMp3);
            // Cada audio tem que ter 60 segundos
            $tempoPorAudio = 60;

            // Se o audio for maior que 1 minutos
            // Temos que recorta em partes para que google, processe por causa que ele aguenta só 1 minuto por audio
            if ($tempoAudio >= $tempoPorAudio) {
                // Adiciona a variavel quantas partes precisamos dividir
                $parts = ceil($tempoAudio / $tempoPorAudio);
                // Variavel aonde os texto serão armazenados
                $texto = [];

                // Laças total de audio que será divido
                for ($i = 0; $i < $parts; $i++) {
                    // Salve o áudios na seguencia
                    $patchFlac  = 'storage/flac_audios/url/' . $id_audio . '_' . $i . '.flac';
                    // Se for primeiro audio e for 0 tem que colocar 0.1 para conversor entender
                    $inicio     = ($i == 0) ? 0.1 : $tempoPorAudio * $i;
                    // Pega MP3 e converte para flac e recorta de acordo com tempo especificado
                    $convert    = $m->mp3toFlac($patchMp3, $patchFlac, $inicio, $tempoPorAudio);
                    // Grava os texto traduzidor no array texto
                    $texto[]    = $t->TranslatorArchive($patchFlac);
                }

                // Ao final do loop joga tudo que foi traduzido no arquivo de texto
                file_put_contents($patchText, implode("\r\n",$texto));
            // Se o áudio for menos que 60 minutos então podemos traduzir sem recortar
            } else {
                // Fornece local aonde irá armazenar
                $patchFlac = 'storage/flac_audios/url/' . $id_audio .'.flac';
                // Transforma mp3 para flac
                $m->mp3toFlac($patchMp3, $patchFlac, 0.1, $tempoAudio);
                // Traduz o audio e joga para um variavle texto
                $texto = $t->TranslatorArchive($patchFlac);
                // Guarda o texto 
                file_put_contents($patchText, $texto);
            }

    
        }

        // Se o arquivo de tradução existir guspir o texto 
        if (file_exists($patchText)) {
            echo file_get_contents($patchText);
            exit;
        }

    }



    public function _startProcessYoutube()
    {
        $yd = new YoutubeDownloader();
        $m = new Manipulation();
        $t = new Translate();

        $t->config['encoding'] = $this->encode;
        $t->config['sampleRateHertz'] = $this->hertz;

        $patchMp3 = 'storage/mp3_audios/youtube/' . $this->id_youtube . '.mp3';
        $patchFlac = 'storage/flac_audios/youtube/' . $this->id_youtube . '.flac';
        $patchText = 'storage/texts/youtube/' . $this->id_youtube . '.txt';

        if (file_exists($patchText)) {
            echo file_get_contents($patchText);
            exit;
        }

        // Checa se o mp3 já foi baixado se nao foi ele baixa
        if (!file_exists($patchMp3)) {
            $urlDownload = $yd->getDownload($this->id_youtube);
            $saveMP3     = $yd->saveFile($urlDownload, $patchMp3);
        // Se o mp3 já foi baixado atribui o save mp3 com 1
        } else {
            $saveMP3 = 1;
        }

        // Se o mp3 existir
        if (is_numeric($saveMP3)) {
            
            // Pega duração do mp3
            $tempoAudio = $m->getDurationAudio($patchMp3);
            // Cada audio tem que ter 60 segundos
            $tempoPorAudio = 60;

            // Se o audio for maior que 1 minutos
            // Temos que recorta em partes para que google, processe por causa que ele aguenta só 1 minuto por audio
            if ($tempoAudio >= $tempoPorAudio) {
                // Adiciona a variavel quantas partes precisamos dividir
                $parts = ceil($tempoAudio / $tempoPorAudio);
                // Variavel aonde os texto serão armazenados
                $texto = [];

                // Laças total de audio que será divido
                for ($i = 0; $i < $parts; $i++) {
                    // Salve o áudios na seguencia
                    $patchFlac  = 'storage/flac_audios/youtube/' . $this->id_youtube . '_' . $i . '.flac';
                    // Se for primeiro audio e for 0 tem que colocar 0.1 para conversor entender
                    $inicio     = ($i == 0) ? 0.1 : $tempoPorAudio * $i;
                    // Pega MP3 e converte para flac e recorta de acordo com tempo especificado
                    $convert    = $m->mp3toFlac($patchMp3, $patchFlac, $inicio, $tempoPorAudio);
                    // Grava os texto traduzidor no array texto
                    $texto[]    = $t->TranslatorArchive($patchFlac);
                }

                // Ao final do loop joga tudo que foi traduzido no arquivo de texto
                file_put_contents($patchText, implode("\r\n",$texto));
            // Se o áudio for menos que 60 minutos então podemos traduzir sem recortar
            } else {
                // Fornece local aonde irá armazenar
                $patchFlac = 'storage/flac_audios/' . $this->id_youtube . '_0.flac';
                // Transforma mp3 para flac
                $m->mp3toFlac($patchMp3, $patchFlac, 0.1, $tempoAudio);
                // Traduz o audio e joga para um variavle texto
                $texto = $t->TranslatorArchive($patchFlac);
                // Guarda o texto 
                file_put_contents($patchText, $texto);
            }

    
        }

        // Se o arquivo de tradução existir guspir o texto 
        if (file_exists($patchText)) {
            echo file_get_contents($patchText);
            exit;
        }

    }

    // Função para checar se o vídeo do youtube existe
    public function _checkIdYoutubeExist($videoID)
    {
        $theURL = "http://www.youtube.com/oembed?url=http://www.youtube.com/watch?v=$videoID&format=json";
        $headers = get_headers($theURL);
        return (substr($headers[0], 9, 3) !== "404");
    }

    public function _checkUrlValid($url)
    {

        
        $headers = get_headers($url);

        if($headers[0] != 'HTTP/1.1 200 OK'){
            echo "A URL informada '".$url."' está errada, você precisa informar um URL com download direito de um arquivo MP3";
            exit;
        }

        if($headers[2] != 'Content-Type: application/force-download'){
            echo "A URL informada '".$url."' está errada, você precisa informar um URL com download direito de um arquivo MP3 com Content-Type: application/force-download";
            exit;
        }

        return true;

    }

}
