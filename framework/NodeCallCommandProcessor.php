<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace framework;
use framework\slack\SlackApi;
use dal\managers\ConquestRepository;
use dal\managers\ZoneRepository;
use dal\managers\NodeRepository;
use dal\managers\StrikeRepository;
use dal\managers\UserRepository;
/**
 * Description of NodeCallCommandProcessor
 *
 * @author chris
 */
class NodeCallCommandProcessor implements ICommandProcessor{
    private $eventData;
    private $conquestRepository;
    private $zoneRepository;
    private $nodeRepository;
    private $strikeRepository;
    private $userRepository;
    private $slackApi;
    
    private $response;
    
    public function __construct($data) {
        $this->eventData = $data;        
        $this->slackApi = new SlackApi();
        
        $this->conquestRepository = new ConquestRepository();
        $this->zoneRepository = new ZoneRepository();
        $this->nodeRepository = new NodeRepository();
        $this->strikeRepository = new StrikeRepository();
        $this->userRepository = new UserRepository();
    }
    
    public function Process() {
        $zoneNodeArray = preg_split('/(\.|-)/', $this->eventData['text']);
        
        $zoneValue = $zoneNodeArray[0];
        $nodeValue = $zoneNodeArray[1];
        
        //$this->response = $zoneValue . ':' . $nodeValue;
        $conquest = $this->conquestRepository->GetCurrentConquest();
        $zone = $this->zoneRepository->GetZone($conquest, $zoneValue);
        if ($zone->is_owned)
        {
            $this->response = "That zone (*$zoneValue*) is no longer active, please double check your call!";
            return;
        }
        
        $node = $this->nodeRepository->GetNode($zone, $nodeValue);
        $user = $this->userRepository->GetUserById($this->eventData['user']);
        
        $currentStrike = $this->strikeRepository->GetStrike($node);
        if ($currentStrike->user_id != null)
        {
            $this->response = "<@" . $this->eventData['user'] . ">: " . 
                    "$zoneValue.$nodeValue is already assigned to <@" .
                    $currentStrike->user->name . ">!".
                    "  Please call another target!";
            return;
        }
        $this->strikeRepository->UpdateStrike($node, $user);
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
