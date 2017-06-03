<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace framework\conquest;
use \DateTime;
use dal\Phases;
use dal\managers\ConquestRepository;
use dal\managers\ZoneRepository;
use dal\managers\NodeRepository;
use dal\managers\StrikeRepository;

/**
 * Description of ConquestManager
 *
 * @author chris
 */
class ConquestManager {
    private $conquestRepository;
    private $zoneRepository;
    private $nodeRepository;
    private $strikeRepository;
    
    public function __construct() 
    {
        $this->conquestRepository = new ConquestRepository();
        $this->zoneRepository = new ZoneRepository();
        $this->nodeRepository = new NodeRepository();
        $this->strikeRepository = new StrikeRepository();
    }
    
    public function GetLastPhaseStats()
    {
        $now = new DateTime();
        //echo 'Now: ' . $now->format('Y-m-d h:i:s') . ' <br/>';
        $lastPhaseDate = $this->GetLastPhaseDate($now);
        $conquest = $this->conquestRepository->GetConquestByDate($lastPhaseDate);
        //echo $lastPhaseDate->format('Y-m-d H:i:s');
        if ($conquest == null || $conquest->id == null)
        {
            return null;
        }
        
        $zones = $this->zoneRepository->GetAllZonesByConquest($conquest);
        $nodes = $this->nodeRepository->GetAllNodesByConquest($conquest);
        $strikes = $this->strikeRepository->GetStrikesByConquest($conquest);
        
        $toReturn = new StatsDto();
        $toReturn->forDate = $lastPhaseDate;
        $toReturn->endDate = null;
        $toReturn->conquests = array($conquest);
        $toReturn->zones = $zones;
        $toReturn->nodes = $nodes;
        $toReturn->strikes = $strikes;
        
        return $toReturn;
    }
    
    private function GetLastPhaseDate(DateTime $dateTime)
    {
        $dayOfWeek = $dateTime->format('l');
        $hour = $dateTime->format('H');
        
        $date = new DateTime($dateTime->format('m/d/Y'));
        switch ($dayOfWeek)
        {
            case 'Tuesday':                
                if ($hour < Phases::Phase3 + Phases::PhaseLength)
                {
                    $date->modify('-1 day');
                    $date->setTime(Phases::Phase2, 0, 0);
                }
                else
                {
                    $date->setTime(Phases::Phase3, 0, 0);
                }
                break;
            case 'Wednesday':
                $date->modify('-1 day');
                $date->setTime(Phases::Phase3, 0, 0);
                break;
            case 'Thursday':
                $date->modify('-2 day');
                $date->setTime(Phases::Phase3, 0, 0);
                break;
            case 'Friday':
                if ($hour < Phases::Phase1 + Phases::PhaseLength)
                {
                    $date->modify('-3 day');
                    $date->setTime(Phases::Phase3, 0, 0);
                }
                else if ($hour < Phases::Phase2 + Phases::PhaseLength)
                {
                    $date->setTime(Phases::Phase1, 0, 0);
                }
                else
                {
                    $date->setTime(Phases::Phase2, 0, 0);
                }
            case 'Saturday':
            case 'Sunday':
            case 'Monday':
                if ($hour < Phases::Phase3 + Phases::PhaseLength)
                {
                    $date->modify('-1 day');
                    $date->setTime(Phases::Phase2, 0, 0);
                }
                else if ($hour < Phases::Phase1 + Phases::PhaseLength)
                {
                    $date->setTime(Phases::Phase3, 0, 0);
                }
                else if ($hour < Phases::Phase2 + Phases::PhaseLength)
                {
                    $date->setTime(Phases::Phase1, 0, 0);
                }
                else
                {
                    $date->setTime(Phases::Phase2, 0, 0);
                }
                break;
            default:
                break;
        }
        
        return $date;
    }
}
