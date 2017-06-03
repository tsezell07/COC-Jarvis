<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace dal\managers;
use dal\DataAccessAdapter;
use dal\models\StateModel;
use StateEnum;

/**
 * Description of StateManager
 *
 * @author chris
 */
class StateRepository {
    //put your code here
    private $adapter;
    
    public function __construct() {
        $this->adapter = new DataAccessAdapter();
    }
    
    public function GetState()
    {
        $sql = 'SELECT state FROM core';
        $result = $this->adapter->query_single($sql);
        if ($result == null)
        {
            $this->initializeState();
        }
        //error_log(print_r($result, true));
        $toReturn = new StateModel();
        $toReturn->state = $result['state'];
        return $toReturn;
    }
    
    public function SetState($state)
    {
        $sql = 'UPDATE core SET state = ' . $state;
        $this->adapter->query($sql);
    }
    
    private function initializeState()
    {
        $sql = 'INSERT INTO core(id, state) VALUES(1,' . StateEnum::Sleeping . ')';
        $this->adapter->query($sql);
    }
}
