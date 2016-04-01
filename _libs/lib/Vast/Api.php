<?php

use GuzzleHttp\Client;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Api
 *
 * @author bojan
 */
class Vast_Api {
    //put your code here
    protected $params;
    protected $request;
    
    /**
     * http://guzzle.readthedocs.org/en/latest/clients.html
     * @var \GuzzleHttp\Client 
     */
    protected $client;
    protected $config;
    
    protected $err;
            
            
    function __construct() {
        $this->client = new Client();
    }
    
    //common config located apps/PROJECT/conf/api.php
    function config( $c = ['host'=>'','port'=>80 , 'key'=>'0'] ){
        $this->config = $c;
    }
    
    function setApiParams($params){
        $this->params = $params;
    }
    
    
    
    function send(){
        $this->err=false;
        try{
            return $this->client->send($this->request);
        }catch(\Exception $e){
            $this->err = $e->getResponse();
        }
        
        return null;
    }
    
    function getErrorResp(){
        if(!$this->err) return false;
        return $this->err->getReasonPhrase();
    }
}
