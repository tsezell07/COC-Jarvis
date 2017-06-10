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
    private $SetupRegex = '/(setup|start) (zone)/i';
    private $StatusRegex = '/(status)/i';
    private $NodeCallRegex = '/(^\d{1,2})(\.|-)(\d{1,2})$/i';
    private $ZoneCompleteRegex = '/(zone) (\d{1,2}) (completed|is ours|finished|done|lost)/i';
    private $HoldRegex = '/(hold) (\d{1,2})(\.|-)(\d{1,2})/i';
    private $ClearRegex = '/(clear) (\d{1,2})(\.|-)(\d{1,2})/i';
    private $StatsRegex = '/(stats)/i';
    private $SummaryRegex = '/(summary)/i';
    
    public function CreateProcessor(Request $request)
    { 
        $data = json_decode($request->getContent(), true);
        $event = $data['event'];
        if ($event['type'] != 'message' || $event['subtype'] == 'message_changed')
        {
            return null;
        }
        
        if (preg_match($this->JarvisRegex, $event['user']))
        {
            return;
        }
        
        $text = $event['text'];
        if (preg_match($this->NodeCallRegex, $text))
        {
            return new NodeCallCommandProcessor($event);
        }
        if (preg_match($this->ZoneCompleteRegex, $text))
        {
            return new ZoneCommandProcessor($event);
        }
        if (!preg_match($this->JarvisRegex, $text))
        {
            return null;
        }
        
        if (preg_match($this->InitiateRegex, $text))
        {
            return new InitCommandProcessor($event);
        }
        else if (preg_match($this->StatusRegex, $text))
        {
            return new StatusCommandProcessor($event, true);
        }
        else if (preg_match($this->HoldRegex, $text))
        {
            return new HoldCommandProcessor($event);
        }
        else if (preg_match($this->SetupRegex, $text))
        {
            return new StrikeCommandProcessor($event);
        }
        else if (preg_match($this->ClearRegex, $text))
        {
            return new ClearCommandProcessor($event);
        }
        else if (preg_match($this->StatsRegex, $text))
        {
            return new StatsCommandProcessor($event);
        }
        else if (preg_match($this->SummaryRegex, $text))
        {
            return new SummaryCommandProcessor($event);
        }
        return null;
    }
}
