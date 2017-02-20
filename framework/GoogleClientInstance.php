<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace framework;

use Google_Client;
use Google_Service_Sheets;

/**
 * Description of GoogleClientInstance
 *
 * @author chris
 */
class GoogleClientInstance {

    const APPLICATION_NAME = 'Google Sheets API PHP Quickstart';
    const SCOPES = Google_Service_Sheets::SPREADSHEETS_READONLY;

    public static function getInstance() {
        $client = new Google_Client();
        $client->setApplicationName(self::APPLICATION_NAME);
        $client->setScopes(self::SCOPES);
        $client->setDeveloperKey("AIzaSyBjUAjH5sZz0gWJjjV7lbJiZ78e2GxQOtw");
        $client->setAccessType('offline');
        return $client;
    }

}
