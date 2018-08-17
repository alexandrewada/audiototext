<?php

namespace Audio;

use Google\Cloud\Speech\SpeechClient;

class Translate
{
    public $sp;
    public $config = [
        'languageCode'      => 'pt-BR',
        'keyFilePath'       => 'keyfile.json',
        'encoding'          => 'FLAC',
        'sampleRateHertz'   => 44100,
        'retries'           => 1,
    ];

    public function __construct()
    {
        $this->sp = new SpeechClient([
            'languageCode' => $this->config['languageCode'],
            'keyFilePath' => $this->config['keyFilePath'],
            'retries' => $this->config['retries'],
        ]);
    }
    
    public function TranslatorArchiveLine($dir)
    {
         $results = $this->sp->recognize(
            fopen($dir, 'r'), [
                'encoding' => $this->config['encoding'],
                'sampleRateHertz' => $this->config['sampleRateHertz'],
            ]);
     
        return $results;
    }


    public function TranslatorArchive($dir)
    {

        $operation = $this->sp->beginRecognizeOperation(
            fopen($dir, 'r'), [
                'encoding' => $this->config['encoding'],
                'sampleRateHertz' => $this->config['sampleRateHertz'],
            ]);

        $isComplete = $operation->isComplete();

        while (!$isComplete) {
            sleep(1); //
            $operation->reload();
            $isComplete = $operation->isComplete();
        }

        $result = $operation->results()[0];

        return $result->topAlternative()['transcript'];
    }
}
