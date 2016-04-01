<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Libslim\Url;

/**
 * Description of Resources
 *
 * @author bojan
 */
class Resources {
    //put your code here
    /**
     *
     * @var type \Libslim\Views\HelpersExtension
     */
    static $ext;
    public static $assetbase='./';
    
    static function asset($uri){
        return self::$assetbase . $uri;
    }
    
    
    static function JQ(){
        self::__init();
        return self::$ext->url('cdn','js/jquery/jquery.min.js');
    }
    
    static function JScdn($name){
        //TODO implement minified version for production ENV
        $url = "js/$name/$name.js";
        self::__init();
        return sprintf('<script src="%s"></script>%s' , self::$ext->url('cdn',$url) ,PHP_EOL);
    }
    
    static function UNDER(){
        self::__init();
        return self::$ext->url('cdn','js/underscore/underscore-min.js');
    }
    
    static function BB(){
        self::__init();
        return self::$ext->url('cdn','js/backbone/backbone.js');
    }
    
    
    static function JQUI(){
        self::__init();
        return self::$ext->url('cdn','js/jquery/jquery.min.js');
    }
    
    static function JQUIcss(){
        self::__init();
        return self::$ext->url('cdn','js/jquery/jquery.min.js');
    }
    
    /*
     * bootstrap
     */
    static function BOOT(){
        self::__init();
        return self::$ext->url('cdn','js/bootstrap/bootstrap.min.js');
    }
    static function BOOTcss(){
        self::__init();
        return self::$ext->url('cdn','css/bootstrap/bootstrap.min.css');
    }
    
    static function genJQBOOT(){
        $js = sprintf('<script src="%s"></script>%s' , self::JQ() ,PHP_EOL);
        $js .= sprintf('<script src="%s"></script>%s' , self::UNDER() ,PHP_EOL);
        $js .= sprintf('<script src="%s"></script>%s' , self::BOOT(), PHP_EOL);
        $js .= sprintf('<script src="%s"></script>%s' , self::BB(), PHP_EOL);
        return $js;
    }
    
    static function genJQBOOTcss(){
        $css = sprintf( '<link href="%s" rel="stylesheet">' , self::BOOTcss());
        $css .='';
        return $css;
    }


    public static function __init() {
        if(self::$ext)            return;
        
        self::$ext = new \Libslim\Views\HelpersExtension() ;
    }
}
