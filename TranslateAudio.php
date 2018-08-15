<?php
require 'vendor/autoload.php';

use Google\Cloud\Speech\SpeechClient;

class TranslateAudio
{
    public $_language = 'pt-BR';
    public $sp;

    public function __construct()
    {
        $this->sp = new SpeechClient([
            'languageCode' => $this->_language,
        ]);
    }

    public function getText()
    {
        // Recognize the speech in an audio file.
        $results = $this->sp->recognize(
            fopen(__DIR__ . '/audio_sample.flac', 'r')
        );
        return $results;
    }

}
