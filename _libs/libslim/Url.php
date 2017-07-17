<?php

namespace Libslim;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Url
 *
 * @author bojan
 */
class Url {

    //put your code here
    static $params;
    private static $base = '';
    static $subpath = '';
    static $sep = '-';
    static $rangesep = '-';
    static $multisep = ',';
    static $DEC_CHARMAP_STRING = array(
        '---' => ' - ',
        '+-' => '/',
        '--' => ', ',
        '=' => ' ',
        '~' => ','
    );
    static $ENC_CHARMAP_STRING = array(
        ' - ' => '---',
        '/' => '+-',
        ', ' => '--',
        ' ' => '=',
        ',' => '~'
    );
    
    static $CLEAR='_clear_';

    function __construct($urlParams, $subpath = '') {
        foreach ($urlParams as $k => $v) {
            if (strlen($v) === 0)
                unset($urlParams[$k]);
        }
        self::$params = $urlParams;
    }
    
    static function setParams($params){
        self::$params = $params;
    }

    //TODO return base url
    static function base() {
        return self::$base;
    }

    static function setBase($base) {
        self::$base = rtrim($base, '/') . '/';
    }

    static function encode($val) {
        return strtr($val, self::$ENC_CHARMAP_STRING);
    }

    static function decode($urlval) {
        return strtr($urlval, self::$DEC_CHARMAP_STRING);
    }
    
    static function enc($val) {
        return strtr($val, ['/'=>'|']);
    }
    static function dec($urlval) {
        return strtr($urlval, ['|'=>'/']);
    }

    /*
     * if toggle present and value is same as current value in params,  than instead of removing value, rplace it with toggle value
     */
    static function to($key, $value , $removeIfSameValue=true  , $toggle=false) {
        //if($key=='backfill') \G::dump ($value,$toggle);
        $p = self::$params;
        $value=self::enc($value);
        $p[$key] = $value;
        if($removeIfSameValue) {
          if(get($key) == $value){
              if($toggle){
                $p[$key] = $toggle;
              }else{
                return self::remove($key);
              }
          }
        }
        //if($value==='') unset ($p[$key]);
        $unset = $key == 'start_index' ? false : 'start_index';
        return self::build($p, $unset);
    }

    static function torange($key, $val1, $val2) {
        $p = self::$params;
        $p[$key] = $val1 . self::$rangesep . $val2;
        return self::build($p);
    }

    static function tomulti($key, $val) {
        $p = self::$params;
        if (isset($p[$key])) {
            $cur = explode(self::$multisep, $p[$key]);
            //if this is same value remove it from url ir toggle it
            if (($i = array_search($val, $cur)) !== false) {
                unset($cur[$i]);
            } else {
                $cur[] = $val;
            }
            asort($cur, SORT_STRING);
            array_unique($cur);
            $p[$key] = implode(self::$multisep, $cur);
        } else {
            $p[$key] = $val;
        }
        return self::build($p);
    }

    static function removemulti($key, $val) {
        $p = self::$params;
        if (isset($p[$key])) {
            $cur = explode(self::$multisep, $p[$key]);
            $cur = array_combine($cur, $cur);
            unset($cur[$val]);
            //last multi value removed from this param , so remove it alltogether
            if (empty($cur)) {
                return self::remove($key);
            }
            asort($cur, SORT_STRING);
            array_unique($cur);
            $p[$key] = implode(self::$multisep, $cur);
        }
        return self::build($p);
    }

    static function remove($key) {
        $p = self::$params;
        unset($p[$key]);
        return self::build($p);
    }

    static function removeArr($keyArr) {
        $p = self::$params;
        foreach ($keyArr as $key) {
            unset($p[$key]);
        }

        return self::build($p);
    }

    static function build($params, $unset = 'start_index') {
        if ($unset) {
            unset($params[$unset]);
        }
        ksort($params);
        $segs = [];
        foreach ($params as $k => $v) {
            if ((trim($v) === '') || $v == self::$rangesep)
                continue;
            $segs[] = $k . self::$sep . $v;
        }
        return rtrim(self::$base . self::$subpath . '/' . implode('/', $segs), '/');
    }

    static function self(){
    $p = self::$params;
    return self::build($p);



    }

static function nicekey($urlkey) {
    $fields = conf('params')->fields;
    if (isset($fields->$urlkey)) {
        if (isset($fields->$urlkey->nice))
            return $fields->$urlkey->nice;
    }
    return ucwords(str_replace(array('_', '-'), ' ', $urlkey));
}

/**
 * Legacy method for carstory URLs
 * @param type $key
 * @return type
 */
static function encodeKey($key) {
    return str_replace(array(' ', '-'), '_', $key);
}

/**
 * 
 * @return UndoLink[]
 */
static function getUndos() {
    $undo = [];
    $hiddenfields = conf('params')->hiddenfromundo;
    $statics = getStaticFacets();
    foreach (self::$params as $k => $v) {
        if (array_search($k, $hiddenfields) !== false)
            continue;
        $a = [];
        $a['url'] = self::remove($k);
        $toggle = 
        $a['url'] = drillurl($k, $v);
        if (isset($statics[$k])) {
            if($v!=self::$CLEAR){
            $t = [];
            if(isset($statics[$k]->values()[$v]) && isset($statics[$k]->values()[$v]['toggle']))
            $a['url'] = drillurl($k, $v , $statics[$k]->values()[$v]['toggle']);
            foreach (explode(Url::$multisep, $v) as $_v) {
                $t[] = $statics[$k]->values()[$_v]->value;
            }
            $a['text'] = implode(', ', $t);
            }else{
                continue;
            }
            $a['label'] = $statics[$k]->title;
        } else {
            $a['label'] = self::nicekey($k);
            $a['text'] = $v;
        }
        $undo[] = $a;
    }
    return new \G_Array($undo);
}

}

class UndoLink {

public $url;
public $text;
public $label;

}
