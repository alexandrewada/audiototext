<?php
include "Curl.php";

class YoutubeAudio extends Curl
{
    public $urlService = 'https://youtube7.download';

    public function getDownload($id_youtube)
    {
        // Envia para serviço externo solicitacao de download
        $json = $this->GET([
            'url' => 'https://t1.youtube6download.top/check.php?callback=jQuery&v=' . $id_youtube . '&f=mp3',
        ]);
        
        // Extrai o json que contém o hash para download
        $p = explode("(", $json);
        $json = $p[1];
        $json = json_decode(substr($json, 0, -1));
        
        // Se o hash tiver pronto gospe, o link de download é mostrado
        if ($json->hash) {
            return 'https://t1.youtube6download.top/quq/' . $json->hash . '/' . $id_youtube;
        }

    }

}
