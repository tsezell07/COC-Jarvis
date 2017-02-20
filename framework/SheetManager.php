<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace framework;

use Google_Service_Sheets;
use Config;
use DateTime;

class SheetManager {

    private $client;
    private $service;
    private $cache;

    public function __construct() {
        $this->client = GoogleClientInstance::getInstance();
        $this->service = new Google_Service_Sheets($this->client);
    }

    public function GetDataSheet() {
        $spreadsheet = $this->service->spreadsheets->get(Config::$SpreadsheetId);
        $sheets = $spreadsheet["sheets"];

        foreach ($sheets as $value) {
            if ($value["properties"]["title"] == "Raw Data") {
                return $value["properties"];
            }
        }

        die("Could not find the raw data sheet");
    }

    public function GetColumnFromDate($date) {
        $startingDate = new DateTime(date('Y/m/d', strtotime('2017/02/16')));
        return $startingDate->diff($date)->days + 1;
    }

    public function GetValuesByRange($range) {
        $response = $this->service->spreadsheets_values->get(Config::$SpreadsheetId, $range);
        $values = $response->getValues();

        return $values;
    }

}
