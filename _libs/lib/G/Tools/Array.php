<?php

/**
 * Created by Bojan Tesanovic.
 * Date: 4/6/12
 * Time: 1:12 PM
 */
class G_Tools_Array {

    /**
     * The main function for converting to an XML document.
     * Pass in a multi dimensional array and this recrusively loops through and builds up an XML document.
     *
     * @param array $data
     * @param string $rootNodeName - what you want the root node to be - defaultsto data.
     * @param SimpleXMLElement $xml - should only be used recursively
     * @return string XML
     */
    public static function toXml($data, $rootNodeName = 'data', $xml = null) {
        // turn off compatibility mode as simple xml throws a wobbly if you don't.
        if (ini_get('zend.ze1_compatibility_mode') == 1) {
            ini_set('zend.ze1_compatibility_mode', 0);
        }

        if ($xml == null) {
            $xml = simplexml_load_string("<?xml version='1.0' encoding='utf-8'?><$rootNodeName />");
        }

        // loop through the data passed in.
        foreach ($data as $key => $value) {
            // no numeric keys in our xml please!
            if (is_numeric($key)) {
                // make string key...
                $key = "unknownNode_" . (string) $key;
            }

            ///          var_dump($key);
            // replace anything not alpha numeric
            $key = preg_replace('/[^a-z0-9_-]/i', '', $key);
//            var_dump($key);
            // if there is another array found recrusively call this function
            if (is_array($value)) {
                $node = $xml->addChild($key);
                // recrusive call.
                self::toXml($value, $rootNodeName, $node);
            } else {
                // add single node.
                $value = htmlentities($value);
                $xml->addChild($key, $value);
            }
        }
        // pass back as string. or simple xml object if you want!
        return $xml->asXML();
    }

    /**
     * Returns subbset of array that has $keys ie a=>1 b=>2 ,c=>3 , 'b,c' will retunr a(b=>2,c=>3)
     * @param type $array acctual array
     * @param type $keys can be array or comma separated string of keys to extract from $array
     * @param type $orderbykeys order output array as in $keys array provided
     * @return array new array
     */
    public static function filterByKeys($array, $keys, $orderbykeys = true) {
        if (is_string($keys)) {
            $keys = explode(',', $keys);
        }
        $keys = array_flip($keys);
        $newArray = array();
        if ($orderbykeys) {
            foreach ($keys as $k => $kv) {
                $newArray[$k] = $array[$k];
            }
        } else {
            foreach ($array as $k => $v) {
                if (isset($keys[$k])) {
                    $newArray[$k] = $v;
                }
            }
        }
        return $newArray;
    }

    /*
     * Will return var_dump version in one line a=(a=>3,b=>4) output a='3' b='4' ....
     */

    public static function dumpAssocInLine($arr) {
        $l = '';
        foreach ($arr as $k => $v) {
            $l.= "$k='$v' ";
        }
        return $l . "\n";
    }

    public static function multySort($array, $keytosortby) {
        $args = func_get_args();
        $data = array_shift($args);
        foreach ($args as $n => $field) {
            if (is_string($field)) {
                $tmp = array();
                foreach ($data as $key => $row)
                    $tmp[$key] = $row[$field];
                $args[$n] = $tmp;
            }
        }
        $args[] = &$data;
        call_user_func_array('array_multisort', $args);
        return array_pop($args);
    }

}
