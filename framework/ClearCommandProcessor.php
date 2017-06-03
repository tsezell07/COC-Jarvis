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
use dal\managers\StrikeRepository;
use framework\slack\SlackApi;

/**
 * Description of ZoneCommandProcessor
 *
 * @author chris
 */
class ClearCommandProcessor implements ICommandProcessor {
    private $eventData;
    private $conquestRepository;
    private $zoneRepository;
    private $nodeRepository;
    private $strikeRepository;
    private $slackApi;
    
    private $response;
    private $ClearRegex = '/(?:clear) (\d{1,2})(\.|-)(\d{1,2})/i';
    
    public function __construct($data) {
        $this->eventData = $data;        
        $this->slackApi = new SlackApi();
        
        $this->conquestRepository = new ConquestRepository();
        $this->zoneRepository = new ZoneRepository();
        $this->nodeRepository = new NodeRepository();
        $this->strikeRepository = new StrikeRepository();
    }

    public function Process() {
        $data = $this->eventData['text'];
        $matches = [];
        if (!preg_match($this->ClearRegex, $data, $matches))
        {
            $this->response = 'Check your syntax!  Hint: clear 1.7';
            return;
        }
        $zoneValue = $matches[1];
        $nodeValue = $matches[3];
        $conquest = $this->conquestRepository->GetCurrentConquest();
        $zone = $this->zoneRepository->GetZone($conquest, $zoneValue);
        $node = $this->nodeRepository->GetNode($zone, $nodeValue);
        $strike = $this->strikeRepository->GetStrike($node);
        $this->strikeRepository->ClearStrike($strike);
    }

    public function SendResponse() 
    {
        $statusCommandProcessor = new StatusCommandProcessor($this->eventData);
        $statusCommandProcessor->Process();
        $statusCommandProcessor->SendResponse();
    }
}
