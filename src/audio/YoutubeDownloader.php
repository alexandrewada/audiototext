<?php
namespace Audio;

use Audio\Curl;

class YoutubeDownloader extends Curl
{
    // Url do serviço que irá converter o youtube para mp3
    public $urlService = 'https://youtube7.download';

    public function getDownload($id_youtube)
    {
        // Envia para serviço externo solicitacao de download
        $obterHash = false;
        // Enquanto a hash do MP3 não for encontrada ele tenta
        while($obterHash == false){
            sleep(1);
            $json = $this->GET([
                'url' => 'https://t1.youtube6download.top/check.php?callback=jQuery&v=' . $id_youtube . '&f=mp3',
            ]);
        
            preg_match_all("#\"(.*?)\"#",$json,$json);

            // Se o hash for encontrado, segue o fluxo
            if($json[1][2] == 'hash'){
                $obterHash = true;
                $hash = $json[1][3];
            }
        }

        // Com hash conseguimos acessar o link direto para baixar o mp3
        $urlDownload   = 'https://t1.youtube6download.top/quq/' . $hash . '/' . $id_youtube;
        
        $downloadReady = false;
        // Enquanto LINK de download não fica pronto ficamos tentando a cada 1 segundo
        while($downloadReady == false){
            sleep(1);
            // Obtem se o header do site libera download para nós
            $header = get_headers($urlDownload);
            // Se o site liberar então obtemos a url do mp3
            if($header[2] == 'Content-Type: application/force-download'){
                $downloadReady = true;
                return $urlDownload;
            } 
        }

    }
    
    // Salva um arquivo a partir de uma URL
    public function saveFile($url,$patch) {
        if($url && $patch){
            return file_put_contents($patch, fopen($url, 'r'));
        }
    }

}
