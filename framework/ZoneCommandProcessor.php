<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace framework;
use dal\managers\ZoneRepository;
use dal\managers\NodeRepository;
use dal\managers\ConquestRepository;
use framework\slack\SlackApi;

/**
 * Description of ZoneCommandProcessor
 *
 * @author chris
 */
class ZoneCommandProcessor implements ICommandProcessor {
    private $eventData;
    private $conquestRepository;
    private $zoneRepository;
    private $nodeRepository;
    private $slackApi;
    
    private $response;
    private $ZoneRegex = '/(\d{1,2})/i';
    private $WinRegex = '/(completed|is ours|finished|done)/i';
    
    public function __construct($data) {
        $this->eventData = $data;        
        $this->slackApi = new SlackApi();
        
        $this->conquestRepository = new ConquestRepository();
        $this->zoneRepository = new ZoneRepository();
        $this->nodeRepository = new NodeRepository();
    }
    
    public function Process() 
    {
        $data = $this->eventData['text'];        
        $matches = [];
        if (!preg_match($this->ZoneRegex, $data, $matches))
        {
            return;
        }
        $zone = $matches[1];
        $conquest = $this->conquestRepository->GetCurrentConquest();
        
        $trackedZone = $this->zoneRepository->GetZone($conquest, $zone);        
        if ($trackedZone == null)
        {
            return;
        }        
        
        $this->zoneRepository->UpdateZone($conquest, $zone, true);        
        $this->response = preg_match($this->WinRegex, $data) ? "Amazing work!  I'll go ahead and remove that zone from the list."
                : "No worries, better luck next time!";
    }

    public function SendResponse()
    {
        $this->slackApi->SendMessage($this->response, null, $this->eventData['channel']);
        $statusCommandProcessor = new StatusCommandProcessor($this->eventData);
        $statusCommandProcessor->Process();
        $statusCommandProcessor->SendResponse();
    }
}
