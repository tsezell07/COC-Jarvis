<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace framework;

/**
 * Description of DonationMessageDto
 *
 * @author chris
 */
class DonationMessageDto {
    public $value;
    public $members;
    
    public function __construct($value, $members) {
        $this->value = $value;
        $this->members = $members;
    }
}
