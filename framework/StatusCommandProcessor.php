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
use dal\managers\CoreRepository;

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
    private $coreRepository;
    private $slackApi;
    
    private $response;
    private $attachments;
    private $forceMessage;
    
    public function __construct($data, $forceMessage=false) {
        $this->eventData = $data;
        $this->forceMessage = $forceMessage;
        $this->slackApi = new SlackApi();
        
        $this->conquestRepository = new ConquestRepository();
        $this->zoneRepository = new ZoneRepository();
        $this->nodeRepository = new NodeRepository();
        $this->strikeRepository = new StrikeRepository();
        $this->coreRepository = new CoreRepository();
    }
    
    public function Process()
    {
        $conquest = $this->conquestRepository->GetCurrentConquest();
        $zones = $this->zoneRepository->GetAllZones($conquest);
        
        $attachments = array();
        foreach ($zones as $zone)
        {
            $strikes = $this->strikeRepository->GetStrikesByZone($zone);
            $response = '';
            foreach ($strikes as $strike)
            {
                $response .= $strike->node->zone->zone . '.' . $strike->node->node . '  - ';
                if ($strike->node->is_reserved)
                {
                    $response .= '(reserved) ';
                }
                if ($strike->user != null)
                {
                    $response .= "<@" . $strike->user->name . ">";
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
        $this->response = empty($zones) ? 'I am currently not tracking any zones :)' 
                : 'Here are the active zones I am tracking:';
        error_log(print_r($attachments, 1));
        $this->attachments = $attachments;
    }

    public function SendResponse() 
    {
        $channel = $this->coreRepository->GetMessageChannel();
        $ts = $this->coreRepository->GetMessageTimestamp();
        if (!$this->forceMessage 
                && $channel == $this->eventData['channel'] 
                && $channel != null 
                && $ts != null)
        {
            $response = $this->slackApi->GetGroupMessagesSince($ts, $channel);
            $shouldUpdate = !$response->body->has_more;
        }
        if ($shouldUpdate)
        {
            $this->slackApi->UpdateMessage($ts, $channel, $this->response, $this->attachments);
        }
        else
        {            
            $response = $this->slackApi->SendMessage($this->response, $this->attachments, $this->eventData['channel']);
            $this->coreRepository->SetMessageProperties($response->body->ts, $response->body->channel);  
        }
    }

}
