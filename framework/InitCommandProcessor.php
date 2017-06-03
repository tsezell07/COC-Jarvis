<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace framework;
use dal\managers\CoreRepository;
use framework\slack\SlackApi;
use StateEnum;
/**
 * Description of InitCommandProcessor
 *
 * @author chris
 */
class InitCommandProcessor implements ICommandProcessor {
    private $eventData;
    private $coreRepository;
    private $slackApi;
    
    private $response;
    
    public function __construct($data) {
        $this->eventData = $data;        
        $this->coreRepository = new CoreRepository();
        $this->slackApi = new SlackApi();
    }
    
    public function Process()
    {
        error_log('icp: ' . $this->eventData['text']);
        
        $stateModel = $this->coreRepository->GetState();
        
        if ($stateModel->state == StateEnum::Sleeping)
        {
            $this->response = "Activating Advanced Strike Coordination Mode";
            $this->coreRepository->SetState(StateEnum::Coordinating);
        }
        else
        {
            $this->response = "I am already assisting with the active conquest!";
        }
        //error_log($test->state);
        
    }
    
    public function SendResponse()
    {
       error_log('sending message');
       $this->slackApi->SendMessage($this->response);
    }
}
