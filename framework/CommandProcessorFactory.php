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
    
    public function CreateProcessor(Request $request)
    { 
        $data = json_decode($request->getContent(), true);
        $event = $data['event'];
        if ($event['type'] != 'message')
        {
            return;
        }
        
        $text = $event['text'];
        if (!preg_match($this->JarvisRegex, $text))
        {
            return;
        }
        
        if (preg_match($this->InitiateRegex, $text))
        {
            $slackApi = new slack\SlackApi();
            $slackApi->SendMessage('test');
            //return new InitCommandProcessor($event);
        }
        
        return;
    }
}
