<?php

namespace Libslim\Parsers;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Xml
 *
 * @author bojan
 */
class Xml {

    //put your code here
    static function parse($xml) {
        $x = new \SimpleXMLElement($xml);
        $arr = self::parseel($x);
        \G::dump($arr);
    }

    static function parseClenNs($xml, $nses = ['<v:', '<o:', '</v:', '</o:']) {
        $xml = str_replace($nses, array('<', '<', '</', '</'), $xml);
        //\G::dump($xml);
        //exit();
        $x = new \SimpleXMLElement($xml);
        $arr = self::parseel($x);
        \G::dump($arr);
    }

    static function clenNs($xml, $nses = ['<v:', '<o:', '</v:', '</o:']) {
        return str_replace($nses, array('<', '<', '</', '</'), $xml);
    }

    static protected function parseel(\SimpleXMLElement $elems) {
        $arr = [];
        foreach ($elems as $k => $e) {

            $ch = $e->children();
            if ($cnt = count($ch)) {
                echo " $k:$cnt <br>";
                $arr[$k] = self::parseel($e);
            } else {
                $arr[$k] = (string) $e;
            }


            $nses = $e->getNamespaces(true);
            foreach ($nses as $ns => $uri) {
                if ($ns) {
                    //http://php.net/manual/en/simplexmlelement.children.php
                    $nsElements = $e->children($ns, true);

                    foreach ($nsElements as $kk => $nse) {
                        $nselCh = $nse->children();
                        if ($cnt2 = count($nselCh)) {
                            echo " $ns:$kk:$cnt2 <br>";
                            $arr[$kk] = self::parseel($nse);
                        } else {
                            echo " $ns:$kk:$cnt2 <br>";
                            $arr[$kk] = (string) $nse;
                        }
                    }
                }
            }
        }



        return $arr;
    }

    static function toJson($xml) {
        $xml = simplexml_load_string(self::clenNs($xml));
        $json = json_encode($xml);
        return $json;
        //$array = json_decode($json,TRUE);
    }

    static function toArray($xml) {
        //$xml = simplexml_load_string(self::clenNs($xml));
        return json_decode(self::toJson($xml), 1);
        //return (array) $xml;
        //$array = json_decode($json,TRUE);
    }

    static function toArrayNative($xml) {
       $xml = simplexml_load_string(self::clenNs($xml));
       return (array) $xml;
    }

    static function xml2php($xml) {
      
    }

}
