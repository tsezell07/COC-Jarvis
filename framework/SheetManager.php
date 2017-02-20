<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace framework;

use Google_Service_Sheets;
use Config;

class SheetManager
{
    private $client;
    private $service;
    
    public function __construct() {
        $this->client = GoogleClientInstance::getInstance();
        $this->service = new Google_Service_Sheets($this->client);
    }
    
    public function GetDataSheetId()
    {
        $spreadsheet = $this->service->spreadsheets->get(Config::$SpreadsheetId);
        $sheets = $spreadsheet["sheets"];
        
        foreach ($sheets as $value)
        {
            if ($value["properties"]["title"] == "Raw Data")
            {
                return $value["properties"]["sheetId"];
            }
        }
        
        die("Could not find the raw data sheet");
    }
    
}