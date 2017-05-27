<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace framework;
use Symfony\Component\HttpFoundation\Request;
/**
 * Description of CommandProcessor
 *
 * @author chris
 */
class CommandProcessorFactory {
    //put your code here
    private $JarvisRegex = '/(U59UGA9HS|jarvis)/i';    
    private $InitiateRegex = '/(initiate|init|begin) (ASC)/i';    
    private $SetupRegex = '/(setup|start) (strike map)/i';
    private $StatusRegex = '/(status)/i';
    private $NodeCallRegex = '/^\d(\.|-)\d$/i';
    
    public function CreateProcessor(Request $request)
    { 
        $data = json_decode($request->getContent(), true);
        $event = $data['event'];
        if ($event['type'] != 'message')
        {
            return null;
        }
        
        $text = $event['text'];
        if (!preg_match($this->JarvisRegex, $text) || preg_match($this->JarvisRegex, $event['user']))
        {
            return null;
        }
        
        if (preg_match($this->InitiateRegex, $text))
        {
            return new InitCommandProcessor($event);
        }
        else if (preg_match($this->StatusRegex, $text))
        {
            return new StatusCommandProcessor($event);
        }
        else if (preg_match($this->SetupRegex, $text))
        {
            return new StrikeCommandProcessor($event);
        }
        return null;
    }
}
