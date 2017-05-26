<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace framework;
use dal\managers\StateRepository;
use framework\slack\SlackApi;
use StateEnum;
/**
 * Description of InitCommandProcessor
 *
 * @author chris
 */
class InitCommandProcessor {
    private $eventData;
    private $stateRepository;
    private $slackApi;
    
    private $response;
    
    public function __construct($data) {
        $this->eventData = $data;        
        $this->stateRepository = new StateRepository();
        $this->slackApi = new SlackApi();
    }
    
    public function Process()
    {
        //error_log('icp: ' . $this->eventData['text']);
        
        $stateModel = $this->stateRepository->GetState();
        
        if ($stateModel->state == StateEnum::Sleeping)
        {
            $this->response = "Activating Advanced Strike Coordination Mode";
            $this->stateRepository->SetState(StateEnum::Coordinating);
        }
        else
        {
            $this->response = "I am already assisting with the active conquest!";
        }
        //error_log($test->state);
        
    }
    
    public function SendResponse()
    {
       $this->slackApi->SendMessage($this->response);
    }
}
