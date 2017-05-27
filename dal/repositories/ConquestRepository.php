<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace dal\managers;
use \DateTime;
use dal\models\UserModel;
use dal\models\ConquestModel;
use dal\DataAccessAdapter;
/**
 * Description of ConquestRepository
 *
 * @author chris
 */
class ConquestRepository {
    private $adapter;
    const Phase3 = 4;
    const Phase1 = 12;
    const Phase2 = 20;
    const PhaseLength = 2;
    
    
    public function __construct() {
        $this->adapter = new DataAccessAdapter();
    }
    
    public function SetCommander(UserModel $user)
    {
        $conquest = $this->GetCurrentConquestWithUser($user);
        $sql = 'UPDATE conquest ' .
                "SET commander_id = '" . $user->id . "' " .
                'WHERE id = ' . $conquest->id;
        $this->adapter->query($sql);
    }
    
    public function GetCurrentConquestWithUser(UserModel $user)
    {
        $today = new DateTime();
        $day = $this->GetClosestDay($today);
        $phase = $this->GetPhase($day);
        
        return $this->GetConquest($day, $phase, $user);
    }
    
    public function GetCurrentConquest()
    {
        $today = new DateTime();     
        echo $today->format('Y-m-d H:i:s') . '<br/>';
        $day = $this->GetClosestDay($today);
        $phase = $this->GetPhase($day);
        
        var_dump($day);
        var_dump($phase);
        
        return $this->GetConquest($day, $phase);
    }
    
    private function GetConquest(DateTime $dateTime, $phase, UserModel $user=null)
    {
        $sql = 'SELECT c.id, c.commander_id, c.date, c.phase, u.id as user_id, u.name, u.vip ' .
                'FROM conquest c ' .
                'LEFT JOIN users u ON u.id = c.commander_id ' .
                "WHERE c.date = '" . $dateTime->format('Y-m-d H:i:s') . "'";
        $result = $this->adapter->query_single($sql);
        if ($result == null)
        {
            $this->CreateConquest($dateTime, $phase, $user);
            $result = $this->adapter->query_single($sql);
        }
        
        $toReturn = new ConquestModel();
        $toReturn->id = $result['id'];
        $toReturn->date = $result['date'];
        $toReturn->phase = $result['phase'];
        $toReturn->commander_id = $result['commander_id'];
        
        if ($toReturn->commander_id != null)
        {
            $commander = new UserModel();
            $commander->id = $result['user_id'];
            $commander->name = $result['name'];
            $commander->vip = $result['vip'];
            $toReturn->commander = $commander;
        }
        return $toReturn;
    }
    
    private function CreateConquest(DateTime $dateTime, $phase, UserModel $user=null)
    {
        switch ($phase)
        {
            case ConquestRepository::Phase1: 
                $phaseNumber = 1;
                break;
            case ConquestRepository::Phase2:
                $phaseNumber = 2;
                break;
            default:
                $phaseNumber = 3;
                break;                
        }
        $userId = $user == null ? 'null' : "'" . $user->id . "'";
        $sql = 'INSERT INTO conquest (commander_id, date, phase) ' .
                'VALUES (' . $userId . ", '" . $dateTime->format('Y-m-d H:i:s') . "', " . $phaseNumber . ')';
        $this->adapter->query($sql);
    }
    
    private function GetPhase(DateTime $dateTime)
    {
        $hour = $dateTime->format('H');
        if ($hour == ConquestRepository::Phase1)
        {
            return ConquestRepository::Phase1;
        }
        else if ($hour == ConquestRepository::Phase2)
        {
            return ConquestRepository::Phase2;
        }
        else
        {
            return ConquestRepository::Phase3;
        }
    }
    
    private function GetClosestDay(DateTime $dateTime)
    {
        $dayOfWeek = $dateTime->format('l');
        $hour = $dateTime->format('H');
        
        $date = new DateTime($dateTime->format('m/d/Y'));
        switch ($dayOfWeek)
        {
            case 'Tuesday':                
                if ($hour <= ConquestRepository::Phase3 + ConquestRepository::PhaseLength)
                {
                    $date->setTime(ConquestRepository::Phase3, 0, 0);
                }
                else
                {
                    $date->modify('+3 day');
                    $date->setTime(ConquestRepository::Phase1, 0, 0);
                }
                break;
            case 'Wednesday':
                $date->modify('+2 day');
                $date->setTime(ConquestRepository::Phase1, 0, 0);
                break;
            case 'Thursday':
                $date->modify('+1 day');
                $date->setTime(ConquestRepository::Phase1, 0, 0);
                break;
            case 'Friday':
                if ($hour <= ConquestRepository::Phase1 + ConquestRepository::PhaseLength)
                {
                    $date->setTime(ConquestRepository::Phase1, 0, 0);
                }
                else
                {
                    $date->setTime(ConquestRepository::Phase2, 0, 0);
                }
            case 'Saturday':
            case 'Sunday':
                if ($hour <= ConquestRepository::Phase3 + ConquestRepository::PhaseLength)
                {
                    $date->setTime(ConquestRepository::Phase3, 0, 0);
                }
                else if ($hour <= ConquestRepository::Phase1 + ConquestRepository::PhaseLength)
                {
                    $date->setTime(ConquestRepository::Phase1, 0, 0);
                }
                else
                {
                    $date->setTime(ConquestRepository::Phase2, 0, 0);
                }
                break;
            default:
                break;
        }
        
        return $date;
    }
}
