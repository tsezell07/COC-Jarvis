<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace framework;

/**
 * Description of InitCommandProcessor
 *
 * @author chris
 */
class InitCommandProcessor {
    private $EventData;
    
    public function __construct($data) {
        $this->EventData = $data;
    }
    
    public function Process()
    {
        $this->EventData['text'];
    }
    
    public function SendResponse()
    {
       
    }
}
