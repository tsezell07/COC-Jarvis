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

/**
 * Description of StatusCommandProcessor
 *
 * @author chris
 */
class StatusCommandProcessor implements ICommandProcessor{
    private $eventData;
    private $conquestRepository;
    private $zoneRepository;
    private $nodeRepository;
    private $strikeRepository;
    private $slackApi;
    
    private $response;
    private $attachments;
    
    public function __construct($data) {
        $this->eventData = $data;        
        $this->slackApi = new SlackApi();
        
        $this->conquestRepository = new ConquestRepository();
        $this->zoneRepository = new ZoneRepository();
        $this->nodeRepository = new NodeRepository();
        $this->strikeRepository = new StrikeRepository();
    }
    
    public function Process()
    {
        $conquest = $this->conquestRepository->GetCurrentConquest();
        $zones = $this->zoneRepository->GetAllZones($conquest);
        
//        $response = '';
//        foreach ($zones as $zone)
//        {
//            $strikes = $this->strikeRepository->GetStrikesByZone($zone);
//            foreach ($strikes as $strike)
//            {
//                $response .= $strike->node->zone->zone . '.' . $strike->node->node . '  - ';
//                if ($strike->user != null)
//                {
//                    $response .= $strike->user->name;
//                }
//                $response .= "\n";
//            }
//        }
        $attachments = array();
        foreach ($zones as $zone)
        {
            $strikes = $this->strikeRepository->GetStrikesByZone($zone);
            $response = '';
            foreach ($strikes as $strike)
            {
                $response .= $strike->node->zone->zone . '.' . $strike->node->node . '  - ';
                if ($strike->user != null)
                {
                    $response .= $strike->user->name;
                }
                $response .= "\n";
            }
            array_push($attachments, array(
                'color' => "#FDC528",
                'text' => '',
                'fields' => array(
                    array(
                        'title' => '',
                        'value' => $response
                    )
                )
            ));
        }
        $this->response = 'Here are the active zones I am tracking:';
        $this->attachments = $attachments;
    }

    public function SendResponse() 
    {
        $this->slackApi->SendMessage($this->response, $this->attachments);
    }

}
