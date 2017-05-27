<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace dal\managers;
use dal\models\ConquestModel;
use dal\DataAccessAdapter;
use dal\ModelBuildingHelper;
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
        $sql = 'SELECT z.id as zone_id, z.conquest_id, z.zone, z.battle_count, z.is_owned, ' . 
                    'c.date, c.phase, c.commander_id, ' .
                    'u.id as user_id, u.name, u.vip ' .
                'FROM conquest_zones z ' .
                'INNER JOIN conquest c ON c.id = z.conquest_id ' .
                'LEFT JOIN users u ON u.id = c.commander_id ' .
                'WHERE conquest_id = ' . $conquest->id . ' ' .
                'AND zone = ' . $zone . ' ' .
                'ORDER BY battle_count DESC';
        $result = $this->adapter->query_single($sql);
        if ($result == null)
        {
            return null;
        }
        $zone = ModelBuildingHelper::BuildZoneModel($result);
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
}
