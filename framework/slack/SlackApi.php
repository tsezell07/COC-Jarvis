<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace framework\slack;

/**
 * Description of SlackApi
 *
 * @author chris
 */
class SlackApi {
    private $SlackApiUri = 'https://slack.com/api/chat.postMessage';
    
    public function SendMessage($message)
    {
        $queryString = "token=" . \Config::$JarvisBotAuthToken;
        $queryString .= "&channel=" . "test2";
        $queryString .= "&as_user=" . "true";
        $queryString .= "&text=" . $message;
        
        $uri = $this->SlackApiUri . "?" . $queryString;
        
        //error_log($uri);
        
        $response = \Httpful\Request::post($uri)
               ->addHeader('Content-Type', 'text/plain; charset=utf-8')
               ->body($message)
               ->send();
        
        //error_log($response);
    }
}
