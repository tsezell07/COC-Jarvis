<?php
require_once __DIR__.'/../vendor/autoload.php';

use framework\SheetManager;

define('APPLICATION_NAME', 'Google Sheets API PHP Quickstart');
define('CREDENTIALS_PATH', '~/.credentials/sheets.googleapis.com-php-quickstart.json');
define('CLIENT_SECRET_PATH', __DIR__ . '/../client_secret.json');
// If modifying these scopes, delete your previously saved credentials
// at ~/.credentials/sheets.googleapis.com-php-quickstart.json
define('SCOPES', implode(' ', array(
  Google_Service_Sheets::SPREADSHEETS_READONLY)
));

/**
 * Returns an authorized API client.
 * @return Google_Client the authorized client object
 */
function getClient() {
  $client = new Google_Client();
  $client->setApplicationName(APPLICATION_NAME);
  $client->setScopes(SCOPES);
  //$client->setAuthConfig(CLIENT_SECRET_PATH);
  $client->setDeveloperKey(Config::$APIKey);
  $client->setAccessType('offline');
  
  
  return $client;
}

$manager = new SheetManager();
//echo $manager->GetDataSheetId();

// Get the API client and construct the service object.
$client = getClient();
$service = new Google_Service_Sheets($client);

// Prints the names and majors of students in a sample spreadsheet:
// https://docs.google.com/spreadsheets/d/1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgvE2upms/edit
//$spreadsheetId = '1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgvE2upms';
$spreadsheetId = Config::$SpreadsheetId;
$range = 'Week 1!A2:D';
//$response = $service->spreadsheets_values->get($spreadsheetId, $range);
//$values = $response->getValues();
$test = $service->spreadsheets->get($spreadsheetId);
var_dump($test["sheets"]);
if (count($values) == 0) {
  print "No data found.\n";
} else {
  
  foreach ($values as $row) {
    // Print columns A and E, which correspond to indices 0 and 4.
    echo $row[0] . ":" . $row[1];
  }
}


?>