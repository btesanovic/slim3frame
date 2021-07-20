<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Conf
 *
 * @author bojan
 */
class Conf {

    //put your code here
    protected static $conf;
    
    protected static $envs=['production','beta','alpha','dev'];

    static function add($conf) {
        
    }
    
    
    /**
     * Loads configs from folder and maps filenames to fileCOntent
     * TODO we may need to have subfolder loke dev alpha beta and load those based on ENV
     * 
     * @param type $folderPath
     */
    static function loadFolder($folderPath){
        $folderPath = rtrim($folderPath,'/') .'/';
        self::_init();
        foreach (glob($folderPath.'*.php') as $file ){
            
            $base = basename($file, '.php');
            $aConf = include $file;
            self::$conf[$base] = json_decode(json_encode($aConf), FALSE);
        
        }
        
        foreach (self::$envs as $env){
            if(!isset(self::$conf[$env]))                continue;
            self::$conf[$env] = array_merge_recursive( self::$conf['default'] , self::$conf[$env] );
        }
    }
    
    public static function get($name,$key=false){
        return self::$conf[$name];
    }

    protected static function _init(){
        if(is_array(self::$conf))            return;
        self::$conf = [];
    }
}
