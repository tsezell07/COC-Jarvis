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
 * Description of HoldCommandProcessor
 *
 * @author chris
 */
class HoldCommandProcessor implements ICommandProcessor{
    private $eventData;
    private $conquestRepository;
    private $zoneRepository;
    private $nodeRepository;
    private $slackApi;
    
    private $response;
    private $HoldRegex = '/(?:hold) (\d{1,2})(\.|-)(\d{1,2})/i';
    
    public function __construct($data) {
        $this->eventData = $data;        
        $this->slackApi = new SlackApi();
        
        $this->conquestRepository = new ConquestRepository();
        $this->zoneRepository = new ZoneRepository();
        $this->nodeRepository = new NodeRepository();
    }

    public function Process() {
        $data = $this->eventData['text'];
        $matches = [];
        if (!preg_match($this->HoldRegex, $data, $matches))
        {
            $this->response = 'Check your syntax!  Hint: hold 1.7';
            return;
        }
        $zoneValue = $matches[1];
        $nodeValue = $matches[3];
        $conquest = $this->conquestRepository->GetCurrentConquest();
        $zone = $this->zoneRepository->GetZone($conquest, $zoneValue);
        $node = $this->nodeRepository->GetNode($zone, $nodeValue);
        $node->is_reserved = true;
        $this->nodeRepository->UpdateNode($node);
    }

    public function SendResponse() {
        if ($this->response != null)
        {
            $this->slackApi->SendMessage($this->response, null, $this->eventData['channel']);
        }
        else
        {
            $statusCommandProcessor = new StatusCommandProcessor($this->eventData);
            $statusCommandProcessor->Process();
            $statusCommandProcessor->SendResponse();
        }
    }

}
