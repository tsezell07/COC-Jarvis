<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace framework;

/**
 * Description of DonationManager
 *
 * @author chris
 */
class DonationManager {

    private $sheetManager;
    private $userMap;

    function __construct() {
        $this->sheetManager = new SheetManager();
        $this->createUsernameMap();
    }

    public function GetTotalDonationsByDay($date) {
        $columnIndex = $this->sheetManager->GetColumnFromDate($date);
        return $this->GetTotalDonationsByColumn($columnIndex);
    }

    public function GetTotalDonationsByColumn($columnIndex) {
        $todaySum = $this->getSumByColumn($columnIndex);
        if ($columnIndex <= 1) {
            return $todaySum;
        }

        $yesterdaySum = $this->getSumByColumn($columnIndex - 1);
        return $todaySum - $yesterdaySum;
    }

    public function GetTopDonatersByDay($date) {
        $columnIndex = $this->sheetManager->GetColumnFromDate($date);
        return $this->GetTopDonatersByColumn($columnIndex);
    }

    public function GetTopDonatersByColumn($columnIndex) {
        $todayValues = $this->getValuesByColumn($columnIndex);
        $yesterdayValues = $this->getValuesByColumn($columnIndex - 1);

        $topDonation = 0;
        $topDonaters = array();
        $length = sizeof($todayValues);
        for ($i = 0; $i < $length; $i++) {
            $todayDonation = $todayValues[$i][0] - $yesterdayValues[$i][0];
            if ($todayDonation > $topDonation) {
                $topDonation = $todayDonation;
                $topDonaters = array($this->userMap[$i]);
            } else if ($todayDonation == $topDonation) {
                array_push($topDonaters, $this->userMap[$i]);
            }
        }

        return new DonationMessageDto($topDonation, $topDonaters);
    }

    public function GetHoardersByDay($date) {
        $columnIndex = $this->sheetManager->GetColumnFromDate($date);
        return $this->GetHoardersByColumn($columnIndex);
    }

    public function GetHoardersByColumn($columnIndex) {
        $todayValues = $this->getValuesByColumn($columnIndex);
        $yesterdayValues = $this->getValuesByColumn($columnIndex - 1);

        $hoarders = array();
        $length = sizeof($todayValues);
        for ($i = 0; $i < $length; $i++) {
            $todayDonation = $todayValues[$i][0] - $yesterdayValues[$i][0];
            if ($todayDonation == 0) {
                array_push($hoarders, $this->userMap[$i]);
            }
        }

        return new DonationMessageDto(0, $hoarders);
    }

    private function getValuesByColumn($columnIndex) {
        $letter = $this->GetColumnNameFromNumber($columnIndex);
        $range = 'Raw Data!' . $letter . '2:' . $letter . '41';
        $values = $this->sheetManager->GetValuesByRange($range);

        return $values;
    }

    private function getSumByColumn($columnIndex) {
        $values = $this->getValuesByColumn($columnIndex);
        $sum = 0;
        if ($values == null) {
            return $sum;
        }
        foreach ($values as $value) {
            $sum += $value[0];
        }
        return $sum;
    }

    public function GetColumnNameFromNumber($num) {
        $numeric = ($num - 1) % 26;
        $letter = chr(65 + $numeric);
        $num2 = intval(($num - 1) / 26);
        if ($num2 > 0) {
            return getNameFromNumber($num2) . $letter;
        } else {
            return $letter;
        }
    }

    private function createUsernameMap() {
        $users = $this->sheetManager->GetValuesByRange('Raw Data!A2:A41');
        $totalItems = sizeof($users);
        $this->userMap = array();
        for ($i = 0; $i < $totalItems; $i++) {
            $this->userMap[$i] = $users[$i][0];
        }
    }

}
