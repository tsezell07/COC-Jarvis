<?php

require_once __DIR__ . '/../vendor/autoload.php';

use framework\DonationManager;
use framework\SheetManager;

$datetime = new DateTime(date('Y/m/d', time()));
$donationManager = new DonationManager();
$sheetManager = new SheetManager();

$sheets = $sheetManager->GetDataSheet();
$columnIndex = $sheets["gridProperties"]["columnCount"];

$totalDonations = $donationManager->GetTotalDonationsByColumn($columnIndex);
$topDonaterDto = $donationManager->GetTopDonatersByColumn($columnIndex);
$hoarderDto = $donationManager->GetHoardersByColumn($columnIndex);

$letter = $donationManager->GetColumnNameFromNumber($columnIndex);
$today = $sheetManager->GetValuesByRange("Raw Data!" . $letter . "1:" . $letter . "1");

if (sizeof($topDonaterDto->members) <= 0) {
    die("data not yet entered");
}

echo $today[0][0] . "<br/>";
echo $totalDonations . "<br/>";

var_dump($topDonaterDto);
echo "<br/>";
var_dump($hoarderDto);
echo "<br/>";
echo "<br/>";
sendStatus($$today[0][0], totalDonations, $topDonaterDto);
if (sizeof($hoarderDto->members) > 0) {
    sendHoarderStatus($hoarderDto);
}

function sendStatus($today, $totalDonations, $topDonatorDto) {
    $slackUri = 'https://hooks.slack.com/services/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';
    $message = "Good morning.  I've collected some numbers for *" . $today . "* that you might find interesting.  " .
            "The total donations collected from everyone is *" . number_format($totalDonations * 10000) .
            "* gold, a handsome sum I must say!";

    $attachments = array(
        array(
            "fallback" => "Daily Donation Report Delivered",
            "color" => "#FDC528",
            "fields" => array(
                array(
                    "title" => "Top Contributors Donated " . number_format($topDonatorDto->value * 10000),
                    "value" => implode(", ", $topDonatorDto->members),
                    "short" => false
                )
            ),
    ));

    $payload = "payload=" . json_encode(array(
                "text" => $message,
                "response_type" => "in_channel",
                "attachments" => $attachments,
    ));

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $slackUri);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    curl_exec($ch);
    curl_close($ch);
}

function sendHoarderStatus($hoarderDto) {
    $message = "Oh dear. This can't be right.  There appears to be a couple of members not pulling their weight.  " .
            "Perhaps they should try donating within 12 hours of the reset so I can capture the data?";

    $hoarderAttachment = array(
        "fallback" => "Daily Hoarder Report Delivered",
        "color" => "#BE1D15",
        "fields" => array(
            array(
                "title" => "0 Donations",
                "value" => implode(", ", $hoarderDto->members),
                "short" => false
            )
        ),
    );

    $payload = "payload=" . json_encode(array(
                "text" => $message,
                "response_type" => "in_channel",
                "attachments" => array($hoarderAttachment),
    ));
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, Config::$SlackUri);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    curl_exec($ch);
    curl_close($ch);
}

?>