<?php

namespace Libslim\Url;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
 * This should parse urls like /param1-value1/param2-value2?par1=val1
 */

class Parser {

    protected $params = [];
    protected $keysep = '-';

    /**
     * @var Array \Libslim\UrlParser
     */
    static $instances = [];

    function __construct($url = null) {
        ;
    }

    /**
     * 
     * @param string $arrayParams
     * @return Libslim\UrlParser
     */
    static function instance($arrayParams = null) {
        if (!$arrayParams)
            $arrayParams = ['__default__'];
        // /make-new-etc
        $md5 = md5(implode('|', $arrayParams));
        if (!isset(self::$instances[$md5])) {
            $in = new Parser();
            self::$instances[$md5] = $in;
        }

        return self::$instances[$md5];
    }

    function parse($arrayParams) {
        foreach ($arrayParams as $pathSeg) {
            list($param, $val) = explode($this->keysep, $pathSeg, 2);
            $this->params[$param] = $val;
        }
        return $this->getParams();
    }

    public function getParams() {
        return $this->params;
    }

}
