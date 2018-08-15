<?php
class Curl
{
    public $useragent = "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0)";
    public $cookie    = 'cookie.txt';
    
    public function POST($param = array()) {
        $ch = curl_init();
        
        if (!empty($param['headers'])) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $param['headers']);
        }
        
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent);
        curl_setopt($ch, CURLOPT_URL, $param['url']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param['dados']);
        curl_setopt($ch, CURLOPT_POST, 1);
        $retorno = curl_exec($ch);
        curl_close($ch);
        return $retorno;
    }
    
    public function GET($param = array()) {
        $ch = curl_init();
        
        if (!empty($param['headers'])) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $param['headers']);
        }
        
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent);
        curl_setopt($ch, CURLOPT_URL, $param['url']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $retorno = curl_exec($ch);
        curl_close($ch);
        return $retorno;
    }
}
?>