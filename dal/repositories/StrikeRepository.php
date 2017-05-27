<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace dal\managers;
use dal\models\UserModel;
use dal\models\NodeModel;
use dal\models\ZoneModel;
use dal\DataAccessAdapter;
use dal\ModelBuildingHelper;

/**
 * Description of StrikeRepository
 *
 * @author chris
 */
class StrikeRepository {
    private $adapter;
    
    public function __construct() {
        $this->adapter = new DataAccessAdapter();
    }
    
    public function UpdateStrike(NodeModel $node, UserModel $user)
    {
        $sql = 'UPDATE conquest_strikes ' .
                "SET user_id = '" . $user->id . "' " .
                'WHERE node_id = ' . $node->id;
        $this->adapter->query($sql);
    }
    
    public function CreateStrike(NodeModel $node, UserModel $user)
    {
        $sql = 'INSERT INTO conquest_strikes (user_id, node_id, status) ' .
                "VALUES ('" . $user->id . "', " . $node->id . ', 0)';
        $this->adapter->query($sql);
    }
    
    public function GetStrike(NodeModel $node)
    {
        $sql = 'SELECT s.id as strike_id, s.user_id as strike_user_id, s.node_id as strike_node_id, s.status, ' .
                    'a.id as a_user_id, a.name as a_name, a.vip as a_vip, ' .
                    'n.id as node_id, n.zone_id, n.node, n.is_reserved, ' .
                    'z.conquest_id, z.zone, z.battle_count, z.is_owned, ' .
                    'c.commander_id, c.date, c.phase, ' .
                    'u.id as user_id, u.name, u.vip ' .
                'FROM conquest_strikes s ' .
                'INNER JOIN users a ON a.id = s.user_id ' .
                'INNER JOIN conquest_nodes n ON n.id = s.id ' . 
                'INNER JOIN conquest_zones z ON z.id = n.zone_id ' .
                'INNER JOIN conquest c ON c.id = z.conquest_id ' .
                'LEFT JOIN users u ON u.id = c.commander_id ' .
                'WHERE s.node_id = ' . $node->id ;
        $result = $this->adapter->query_single($sql);
        $strike = ModelBuildingHelper::BuildStrikeModel($result);
        return $strike;      
        //echo '<pre>' . print_r($strike, 1) . '</pre>';
    }
    
    public function GetStrikesByZone(ZoneModel $zone)
    {
        $sql = 'SELECT s.id as strike_id, s.user_id as strike_user_id, s.node_id as strike_node_id, s.status, ' .
                    'a.id as a_user_id, a.name as a_name, a.vip as a_vip, ' .
                    'n.id as node_id, n.zone_id, n.node, n.is_reserved, ' .
                    'z.conquest_id, z.zone, z.battle_count, z.is_owned, ' .
                    'c.commander_id, c.date, c.phase, ' .
                    'u.id as user_id, u.name, u.vip ' .
                'FROM conquest_strikes s ' .
                'INNER JOIN users a ON a.id = s.user_id ' .
                'INNER JOIN conquest_nodes n ON n.id = s.id ' . 
                'INNER JOIN conquest_zones z ON z.id = n.zone_id ' .
                'INNER JOIN conquest c ON c.id = z.conquest_id ' .
                'LEFT JOIN users u ON u.id = c.commander_id ' .
                'WHERE z.id = ' . $zone->id ;
        $results = $this->adapter->query($sql);
        $toReturn = [];
        foreach ($results as $item)
        {
            $strike = ModelBuildingHelper::BuildStrikeModel($item);
            array_push($toReturn, $strike);
        }        
        return $toReturn;
    }
}
