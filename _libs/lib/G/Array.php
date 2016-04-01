<?php

/**
 * Class Description
 * @author Bojan
 * @package V
 * @version 2.0
 */
class G_Array implements Iterator, Countable ,  ArrayAccess {

    public $_data;
    private $readOnly;

    /**
     * Constructor
     * @param $array input array can be empty
     * @param $readOnly if we want disable array modification set this to true
     */
    function __construct($array = array(), $readOnly = false) {
        if (!is_array($array))
            $array = array();
        foreach ($array as $key => $value) {
            if (!is_array($value)) {
                $array[$key] = $value;
            } else {
                //foreach ($value as $k=>$v)
                $array[$key] = new G_Array($value);
            }
        }
        $this->_data = $array;
        $this->readOnly = $readOnly;
    }

    public function __get($name) {
        $result = null;
        if ($name == 'readOnly')
            return $this->readOnly;
        
        if (array_key_exists($name, $this->_data)) {
            $result = $this->_data[$name];
        } else {
           // trigger_error("No data set for '$name'");
            //echo debug_print_backtrace();
            //var_dump($this);
            //if($name=='dname_nomakes') var_dump ($this->_data[$name]);
              //  else echo "key:$name\n";
            //exit();
        }

        return $result;
    }

    public function __set($name, $value) {
        if ($this->readOnly) {
            throw new Vast_Exception("Can't set property write denied.");
        }

        $this->_data[$name] = $value;
    }

    public function __isset($key) {
        return isset($this->_data[$key]);
    }

    public function __unset($key) {
        unset($this->_data[$key]);
    }

    function _unset($key) {
        unset($this->_data[$key]);
    }

    /**
     * returns current Object as array
     * @return array
     */
    function toArray() {
        return $this->_data;
    }

    /**
     * Checks if array has any data
     * @return number of elements in array
     */
    function hasData() {
        return count($this->_data);
    }

    public function rewind() {
        reset($this->_data);
    }

    public function current() {
        $var = current($this->_data);
        return $var;
    }

    public function key() {
        $var = key($this->_data);
        return $var;
    }

    public function next() {
        $var = next($this->_data);
        return $var;
    }

    public function valid() {
        $var = $this->current() !== false;
        return $var;
    }

    public function count() {

        return count($this->_data);
    }

    public function addData($arr) {
        foreach ($arr as $k => $v) {
            $this->_data[$k] = $v;
        }
    }
    
    public function addSingle($arr) {
        $this->_data[] = $arr;
    }
    
    public function nice($kvSep=':',$pairSeparator='|'){
        $str=array();
        foreach ($this->_data as $k=>$v){
            $str[] = "$k$kvSep$v";
        }
        return implode($pairSeparator, $str);
    }

    /*
     * Adds key counter to array , ie how many occurances of some key 
     */

    public function keyplusplus($key) {
        if (!array_key_exists($key, $this->_data)) {
            $this->_data[$key] = 0;
        }
        $this->_data[$key] ++;
    }
    
    /**
     * Adds data to $key ie $arr['fruits']=array('x','y' ...
     */
    public function dataplusplus($key,$data) {
        if (!array_key_exists($key, $this->_data)) {
            $this->_data[$key] = array();
        }
        $this->_data[$key][]=$data;
    }

    /**
     * 
     * id $data alrady exist in $key it wont be added
     * 
     * @param type $key
     * @param type $data
     */
    public function dataplusplusuniq($key,$data) {
        if (!array_key_exists($key, $this->_data)) {
            $this->_data[$key] = new G_Array();
        }
        
        $this->_data[$key]->keyplusplus($data);
    }
    
    public function offsetExists($offset) {
        return $this->__isset($offset);
    }

    public function offsetGet($offset) {
        return $this->__get($offset);
    }

    public function offsetSet($offset, $value) {
        $this->_data[$offset] = $value;
    }

    public function offsetUnset($offset) {
        unset($this->_data[$offset]);
    }

}
