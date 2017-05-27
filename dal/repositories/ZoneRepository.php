<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace dal\managers;
use dal\models\ZoneModel;
use dal\models\ConquestModel;
use dal\DataAccessAdapter;
/**
 * Description of ZonesRepository
 *
 * @author chris
 */
class ZoneRepository {
    private $adapter;
    
    public function __construct() {
        $this->adapter = new DataAccessAdapter();
    }
    
    public function GetZone(ConquestModel $conquest, $zone)
    {
        $sql = 'SELECT z.id, z.conquest_id, z.zone, z.battle_count, z.is_owned, c.date, c.phase, c.commander_id ' .
                'FROM conquest_zones z ' .
                'INNER JOIN conquest c ON c.id = z.conquest_id ' .
                'WHERE conquest_id = ' . $conquest->id . ' ' .
                'AND zone = ' . $zone . ' ' .
                'ORDER BY battle_count DESC';
        $result = $this->adapter->query_single($sql);
        if ($result == null)
        {
            return null;
        }
        $zone = $this->BuildZoneModel($result);        
        var_dump($zone);
        return $zone;
    }
    
    public function CreateZone(ConquestModel $conquest, $zone)
    {
        $battleCount = 1;
        $currentZone = $this->GetZone($conquest, $zone);
        if ($currentZone != null)
        {
            $battleCount = $currentZone->battle_count + 1;
        }
        $sql = 'INSERT INTO conquest_zones (conquest_id, zone, battle_count, is_owned) ' .
                'VALUES (' . $conquest->id . ', ' . $zone . ', ' . $battleCount . ', 0)';
        $this->adapter->query($sql);
    }
    
    private function BuildZoneModel($result)
    {
        $toReturn = new ZoneModel();
        $toReturn->id = $result['id'];
        $toReturn->conquest_id = $result['conquest_id'];
        $toReturn->conquest = $this->BuildConquestModel($result);
        $toReturn->zone = $result['zone'];
        $toReturn->battle_count = $result['battle_count'];
        $toReturn->is_owned = $result['is_owned'];
        return $toReturn;
    }
    
    private function BuildConquestModel($result)
    {
        $toReturn = new ConquestModel();
        $toReturn->id = $result['conquest_id'];
        $toReturn->date = $result['date'];
        $toReturn->phase = $result['phase'];
        $toReturn->commander_id = $result['commander_id'];
        return $toReturn;
//        if ($toReturn->commander_id != null)
//        {
//            $commander = new UserModel();
//            $commander->id = $result['user_id'];
//            $commander->name = $result['name'];
//            $commander->vip = $result['vip'];
//            $toReturn->commander = $commander;
//        }
    }
}
