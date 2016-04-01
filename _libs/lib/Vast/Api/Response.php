<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Response
 *
 * @author bojan
 */
class Vast_Api_Response implements Iterator {

    /**
     * array of Xml::toArray method 
     * @var array 
     */
    protected $xa;
    protected $facets;
    protected $rs;

    public function __construct($arr) {

        $facets = array();
        $results = array();
        foreach ($arr as $k => $v) {
            echo nl2br("$k\n");
            if ($k == 'entry') {

                foreach ($v as $kk => $vv) {
                    echo nl2br("\n$kk {$vv['id']}\n");
                    if ($vv instanceof SimpleXMLElement) {
                        if (strpos($vv->id, 'listings') > 10) {
                            $av = self::sXmlToArray($vv);
                            print_r($av);
                            $results[] = $av;
                        } else {
                            $av = self::sXmlToArray($vv);
                            print_r($av);
                            $facets[] = $av;
                        }
                    } else {
                        if (strpos($vv['id'], 'listings') > 10) {
                            $results[] = $vv;
                        } else {
                            $facets[] = $vv;
                        }
                    }

                    unset($arr[$k][$kk]);
                }
            }
        }
        $this->xa = $arr;
        $this->facets = $facets;
        $this->rs = $results;
    }

    public function getFacets() {
        return $this->facets;
    }

    public function getResults() {
        return $this->rs;
    }

    public function getTotalResults() {
        return $this->xa['totalResults'];
    }

    public function __get($name) {
        if (isset($this->rs[$name])) {
            return $this->rs['name'];
        }
        return null;
    }

    public function __call($name, $arguments) {
        throw new Exception("not implemented");
    }

    public function current() {
        return current($this->rs);
        //return new ApiEntry(current($this->xa['entry']));
    }

    public function key() {
        //return key($this->xa['entry']);
        return $this->current()['item_id'];
    }

    public function next() {
        return next($this->rs);
    }

    public function rewind() {
        return reset($this->rs);
    }

    public function valid() {
        return !is_null(key($this->rs));
    }

    public static function sXmlToArray(SimpleXMLElement $sx) {
        $arr = [];
        foreach ($sx as $k => $v) {
            foreach ($v->attributes() as $aname => $aval) {
                $arr[$k . '$' . $aname] = (string)$aval;
            }
            if ($v->count())
                $arr[$k] = array ($v);
            else
                $arr[$k] = (string) $v;
        }

        return $arr;
    }

}

class ApiEntry implements ArrayAccess, IteratorAggregate {

    protected $item;

    public function __construct($item) {
        $this->item = $item;
    }

    public function offsetExists($offset) {
        return isset($this->item[$offset]);
    }

    public function offsetGet($offset) {
        switch ($offset) {
            case 'detailsUrl': return $this->item['link'][1]["@attributes"]['href'];
        }
        return $this->item[$offset];
    }

    public function offsetSet($offset, $value) {
        
    }

    public function offsetUnset($offset) {
        
    }

    public function getIterator() {
        return array_keys($this->item);
    }

}

class FacetResults {
    
}
