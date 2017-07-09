<?php
/*
 * MYSQLi implementation for mysql implementation look at G_Db_1.php file
 */
class G_Db {

    static private $dbLink = null;
    static private $dbAndTblPrefix = '';
    static public $debug = 0;
    static public $logfile = 0;
    static public $pluginDbAndPre;
    static public $Iextended = array();
    static public $dbTimer = 0;

    static function setDbLink($link) {
        if (!$link instanceof mysqli)
            throw new Exception("not a valid mysqli link");
        G_Db_Conn::setConn($link);
    }

    static function getConn() {
        return G_Db_Conn::getConnection();
    }

    static function logSql($logfile) {
        self::$logfile = $logfile;
    }

    static function log($sql) {
        if (!self::$logfile)
            return;
        $ts = date('Y-m-d h:i:s');
        $line = "$ts $sql\n";
        file_put_contents(self::$logfile, $line, FILE_APPEND);
    }

    static function setDbAndTblPrefix($dbAndTblPrefix) {
        self::$dbAndTblPrefix = $dbAndTblPrefix;
    }

    static function dbAndPre($tblPartName) {
        $ret = null;
        if (self::$pluginDbAndPre)
            $ret = self::$pluginDbAndPre->dbAndPre($tblPartName);
        if ($ret)
            return $ret;
        return self::$dbAndTblPrefix . $tblPartName . '`';
    }

    static function getDbLink() {
        return self::getConn();
    }

    static function lastId() {
        return mysqli_insert_id(self::getConn());
    }

    static function getAssoc($sql, $one = false, $setKeyColumn = false) {
        self::getConn();
        if (self::$debug) {
            self::debug($sql);
        }
        self::log($sql);
        $rs = mysqli_query( self::getConn() , $sql);



        $res = array();
        if (!$rs) {
            if (APPLICATION_ENV != 'production') {
                // F::d($sql , 'mysql error');
                throw new Exception($sql . ' ' . mysqli_error());
                die(mysqli_error());
            } else {
                throw new Exception($sql . ' ' . mysqli_error());
                die();
            }

            return array();
        }
        if (!$rs instanceof mysqli_result) {
            //this is update delete or drop
            if ($rs) {
                return mysqli_affected_rows();
            }
            return null;
        }


        if ($one) {
            $row = mysqli_fetch_assoc($rs);
            return $row;
        }

        if ($setKeyColumn) {
            while ($row = mysqli_fetch_assoc($rs)) {
                $res[$row[$setKeyColumn]] = $row;
            }
        } else {
            while ($row = mysqli_fetch_assoc($rs)) {
                $res[] = $row;
            }
        }


        return $res;
    }

    /**
     * @param $setKeyColumn columns name eg 'item_id' for returning array eg $arr['item_id']=.....
     * */
    static function get($sql, $one = false, $assoc = true, $setKeyColumn = false) {

        self::getConn();
        if (self::$debug) {
            self::debug($sql);
        }
        self::log($sql);
        $rs = mysqli_query( self::getConn() , $sql) ;



        $res = array();
        if (!$rs) {
            if (APPLICATION_ENV != 'production') {
                // F::d($sql , 'mysql error');
                throw new Exception($sql . ' ' . mysqli_error(self::getConn()));
            } else {
                throw new Exception($sql . ' ' . mysqli_error(self::getConn()));
            }

            return new G_Array();
        }
        //var_dump($rs);
        if ( !$rs instanceof  mysqli_result) {
            //this is update delete or drop
            if ($rs) {
                return mysqli_affected_rows(self::getConn());
            }
            return null;
        }


        if ($assoc) {
            if ($one) {
                $row = mysqli_fetch_assoc($rs);
                return new G_Array($row);
            }

            if ($setKeyColumn) {
                while ($row = mysqli_fetch_assoc($rs)) {
                    $res[$row[$setKeyColumn]] = new G_Array($row);
                }
            } else {
                while ($row = mysqli_fetch_assoc($rs)) {
                    $res[] = new G_Array($row);
                }
            }
        } else {
            if ($one) {
                $row = mysqli_fetch_array($rs, MYSQLI_NUM);
                return new G_Array($row);
            }

            if ($setKeyColumn) {
                while ($row = mysqli_fetch_assoc($rs, MYSQLI_NUM)) {
                    $res[$row[$setKeyColumn]] = new G_Array($row);
                }
            } else {
                while ($row = mysqli_fetch_assoc($rs)) {
                    $res[] = new G_Array($row);
                }
            }
        }

        return $res;
    }

    /**
     * returns array of fist values eg select id from table; array of ids
     * @return array
     * */
    static function getFirstValues($sql) {
        self::getConn();

        if (self::$dbTimer) {
            $chunks = explode('=', $sql);
            $tmrKey = 'sql:getFirstValues';
            if (count($chunks) > 1) {
                $tmrKey = $chunks[0];
                if (isset($chunks[2])) {
                    $ch = explode(' ', $chunks[1]);
                    $ch = array_reverse($ch);
                    $tmrKey = $tmrKey . '--' . $ch[0];
                }
            }
            G_Dbg::getInstance()->timerStart($tmrKey);
        }
        $res = array();
        if (self::$debug) {
            self::debug($sql);
        }

        self::log($sql);

        $rs = mysqli_query( self::getConn() , $sql);
        if (!$rs) {
            throw new Exception($sql . ' ' . mysqli_error(self::getConn()));
            die(mysqli_error(self::getConn()));
        }


        if (!$rs instanceof mysqli_result) {
            if (self::$dbTimer) {
                G_Dbg::getInstance()->timerStop();
            }
            //this is update delete or drop
            
            return $rs;
        }
        while ($row = mysqli_fetch_array($rs, MYSQLI_NUM)) {
            $res[] = current($row);
        }
        if (self::$dbTimer) {
            G_Dbg::getInstance()->timerStop();
        }
        return $res;
    }

    /**
     * returns first row of sql as G_Array
     * @return G_Array
     *
     * */
    static function getFirstRow($sql) {
        return self::get($sql, true);
    }

    /**
     * returns single value eg select id from table limit 1  returns id
     * */
    static function getFirstValuesValue($sql) {
        $res = self::get($sql, true, false);
        return $res->{0};
        return null;
    }

    /**
     * return key=>val array eg select key,val from table , key must me unique
     * @param $unshift  useful for making drop downs eg "plese select" , key for this is -1
     * @return array
     * */
    public static function getPairs($sql, $unshift = false) {
        self::getConn();

        if (self::$debug) {
            self::debug($sql);
        }

        self::log($sql);


        $rs = mysqli_query(self::getConn() , $sql);

        if (!$rs) {
            if (APPLICATION_ENV != 'production') {
                echo "$sql\n";
                echo mysqli_error();
                throw new Exception(mysqli_error());
            } else {
                return array();
            }
        }

        $res = array();
        if ($unshift) {
            $res[-1] = $unshift;
        }
        while ($row = mysqli_fetch_array($rs, MYSQLI_NUM)) {
            $res[$row[0]] = $row[1];
        }

        return $res;
    }

    public static function insertsql($array, $table, $onDuplicate = null, $ignore = false, $delayed = false) {
        if ($ignore)
            $ignore = ' IGNORE ';
        $sql = "INSERT $ignore INTO $table ( `" . implode('`,`', array_keys($array)) . "` ) values ( ";
        foreach ($array as $k => $val) {
            $val = self::escape($val);
            if (is_null($val))
                $array[$k] = 'null';
            else {
                if ($val && $val[0] == '#') {
                    //litteral value eg '#substr(field,0,3)' remove # and dont soround it by '
                    if (strpos($val, '(') > 1) {
                        $array[$k] = substr($val, 1);
                    } else {
                        $array[$k] = "'$val'";
                    }
                } else {
                    $array[$k] = "'$val'";
                }
            }
        }
        $sql .= implode(',', $array) . ')';


        if ($onDuplicate) {
            $sql .= " ON DUPLICATE KEY UPDATE $onDuplicate";
        }

        return $sql;
    }

    /**
     * 	$onDuplicate cena_pdv = values(cena_pdv),naziv= values(naziv) etc
     *
     * @param $onDuplicate npr cena_pdv = values(cena_pdv),naziv= values(naziv)
     * @param $ignore ignore errors if duplicate dont throw mysqlerror
     * */
    public static function insert($array, $table, $onDuplicate = null, $ignore = false, $delayed = false) {
        self::getConn();
        $sql = self::insertsql($array, $table, $onDuplicate, $ignore, $delayed);
//        if($ignore) $ignore = ' IGNORE ';
//        $sql = "INSERT $ignore INTO $table ( `" . implode('`,`' , array_keys($array) ) . "` ) values ( ";
//        foreach ($array as $k=>$val ){
//            $val = self::escape($val);
//            if(is_null($val)) $array[$k] = 'null';
//            else{
//                if($val && $val[0] =='#' ){
//                    //litteral value eg '#substr(field,0,3)' remove # and dont soround it by '
//                    $array[$k] = substr($val,1);
//
//                }else{
//                $array[$k] = "'$val'";
//                }
//            }
//        }
//        $sql .= implode( ',' , $array ) . ')';
//
//
//        if($onDuplicate){
//            $sql .= " ON DUPLICATE KEY UPDATE $onDuplicate";
//        }




        return self::getFirstValues($sql);
    }

    /*
     * Inserts delayed TODO
     */

    public static function insertExtended($array, $table, $onDuplicate = null, $ignore = false) {
        if (!isset(self::$Iextended[$table]) || !self::$Iextended[$table]) {
            if ($ignore)
                $ignore = ' IGNORE ';
            $sql = "INSERT $ignore INTO $table ( `" . implode('`,`', array_keys($array)) . "` ) values ";
            $sql2 = ' ';
            if ($onDuplicate) {
                $sql2 = " ON DUPLICATE KEY UPDATE $onDuplicate";
            }
            self::$Iextended[$table] = array();
            self::$Iextended[$table]['sql'] = $sql;
            self::$Iextended[$table]['sql2'] = $sql2;
            self::$Iextended[$table]['val'] = array();
        }
        self::$Iextended[$table]['val'][] = self::getInsertValues($array);
    }

    public static function flushInsertExt() {
        $hasInsert = false;
        foreach (self::$Iextended as $tbl => $data) {
            if (!$data)
                continue;
            $sql = $data['sql'];
            $sql2 = $data['sql2'];
            $vals = implode(' , ', $data['val']);
            $S = $sql . $vals . $sql2;
            $hasInsert = 1;
            self::getFirstValues($S);
            self::$Iextended[$tbl] = null;
        }

        return $hasInsert;
    }

    public static function getInsertValues($array) {
        foreach ($array as $k => $val) {
            $val = self::escape($val);
            if (is_null($val))
                $array[$k] = 'null';
            else {
                if ($val && $val[0] == '#') {

                    //litteral value eg '#substr(field,0,3)' remove # and dont soround it by '
                    if (strpos($val, '(') > 1) {
                        $array[$k] = substr($val, 1);
                    } else {
                        $array[$k] = "'$val'";
                    }
                } else {
                    $array[$k] = "'$val'";
                }
            }
        }

        return '(' . implode(',', $array) . ')';
    }

    public static function updatesql($id, $array, $table, $debug = 0) {
        if (is_array($id)) {
            return self::updateArrsql($id, $array, $table, false, $debug);
        }
        $sql = "UPDATE  $table SET ";
        $SET = array();
        foreach ($array as $k => $val) {
            $val = self::escape($val);
            if (is_null($val))
                $array[$k] = 'null';
            else
                $SET [] = "$k ='$val' ";
        }
        $sql .= implode(',', $SET) . " where id='$id' limit 1";
        return $sql;
    }

    public static function update($id, $array, $table, $debug = 0) {
        self::getConn();

        if (is_array($id)) {
            return self::updateArr($id, $array, $table);
        }
        $sql = self::updatesql($id, $array, $table, $debug);

        //	echo $sql;
        return self::getFirstValues($sql);
    }

    public static function updateArrsql($idArr, $array, $table, $ignore = false, $debug = 0) {
        if ($ignore)
            $ignore = "IGNORE";
        else
            $ignore = '';
        $sql = "UPDATE  $ignore $table SET ";
        $SET = array();
        foreach ($array as $k => $val) {
            $val = self::escape($val);
            if (is_null($val))
                $SET [] = "$k =null ";
            else
                $SET [] = "$k ='$val' ";
        }
        $wr = array();
        foreach ($idArr as $nm => $vl) {
            $vl = self::escape($vl);
            $wr[] = "$nm='$vl'";
        }
        $where = implode(' and ', $wr);
        $sql .= implode(',', $SET) . " where $where";
        return $sql;
    }

    public static function updateArr($idArr, $array, $table, $ignore = false, $debug = 0) {
        self::getConn();
        $sql = self::updateArrsql($idArr, $array, $table, $ignore, $debug);


        if (self::$debug)
            return self::debug($sql);

        return self::getFirstValues($sql);
    }

    public static function deleteArr($whereArr, $table) {
        self::getConn();

        if (!is_array($whereArr))
            throw new Exception("whereArr must be array");
        $wr = array();
        foreach ($whereArr as $k => $val) {
            $val = self::escape($val);
            if (is_null($val))
                $wr [] = "$k=null ";
            else
                $wr [] = "$k ='$val'";
        }
        $where = implode(' and ', $wr);
        $sql = "delete from $table where $where";
        return self::getFirstValues($sql);
    }

    /**
     * creates in values given array of values eg '2323','23212','89192891'
     * */
    public static function createInString($arr) {
        if (!$arr)
            return '';
        return "'" . implode("','", $arr) . "'";
    }

    /**
     * 
     * @param type $arr [bdfield]=value ...
     * @return string
     */
    public static function createOnDuplicate($arr) {
        if (!$arr)
            return '';
        // cena_pdv = values(cena_pdv),naziv= values(naziv)
        $dup=[];
        foreach ($arr as $fld=>$val){
            $dup[] = "$fld = values($fld)";
        }
        return implode(', ' , $dup);
    }
    
    public static function sel($table, $sel, $wh = '', $groupby = '', $order = '', $add = '') {

        $tb = $table;
        if (is_array($table)) {
            $tb = implode(',', $table);
        }
        if ($wh) {
            if (is_array($wh)) {
                $w = array();
                foreach ($wh as $k => $v) {
                    $v = self::escape($v);
                    if (is_null($v))
                        $w [] = "$k=null ";
                    else
                        $w [] = "$k ='$v'";
                }
                $wh = 'where ' . implode(' and ', $w);
            }else {
                $wh = "where $wh";
            }
        }
        if ($groupby)
            $groupby = "group by $groupby";
        if ($order)
            $order = "order by $order";
        $sql = "select $sel from $tb $wh $groupby $order $add ";
        return $sql;
    }

    public static function escape($s) {
        // Stripslashes
        if (get_magic_quotes_gpc()) {
            $s = stripslashes($s);
        }
        if (is_null($s) || strtolower($s) == 'null')
            return null;
        // Quote if not number
        if (!is_numeric($s)) {
            $s = mysqli_real_escape_string(self::getConn() ,$s);
            //$s = addslashes($s);
        }
        return $s;
    }
    
    public static function affectedRows(){
        return   mysqli_affected_rows(self::getConn());
    }

    public static function debug($sql) {
        //trigger_error("SQL IN DEBUG MODE NO QUERIES EXECUTED" , E_USER_NOTICE );
        //G::d($sql,"SQL:");
        echo "$sql;\n";
    }

}
