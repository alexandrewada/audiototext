<?php
namespace Audio;

use Audio\Curl;

class YoutubeDownloader extends Curl
{
    public $urlService = 'https://youtube7.download';

    public function getDownload($id_youtube)
    {
        // Envia para serviço externo solicitacao de download
        $json = $this->GET([
            'url' => 'https://t1.youtube6download.top/check.php?callback=jQuery&v=' . $id_youtube . '&f=mp3',
        ]);

       # print_r(get_headers('https://t1.youtube6download.top/check.php?callback=jQuery&v=' . $id_youtube . '&f=mp3'));
        
        preg_match_all("#\"(.*?)\"#",$json,$json);


        if($json[1][2] != 'hash'){
            echo 'Hash não encontrado';
            exit;
        }

        $hash = $json[1][3];

    
        $urlDownload   = 'https://t1.youtube6download.top/quq/' . $hash . '/' . $id_youtube;
        $downloadReady = false;

        while($downloadReady == false){
            sleep(1);
            $header = get_headers($urlDownload);
            if($header[2] == 'Content-Type: application/force-download'){
                $downloadReady = true;
                return $urlDownload;
            } 
        }

    }

    public function saveFile($url,$patch) {
        if($url && $patch){
            return file_put_contents($patch, fopen($url, 'r'));
        }
    }

}
