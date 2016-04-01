<?php

class G_Db_Conn {

    public static $CON = null;
    public static $DB = null;
    protected static $props = null;

    public static function setProps($props) {
        self::$props = $props;
        if (self::$CON) {
            //trigger_error("Connection already established recconecting with new properties G_Db_Conn", E_USER_WARNING);
        }
        self::$CON = null;
    }

    public static function getConnection($props=null) {

        //echo "GET CONN\n";
        if($props){
            if(self::$props){
                if(self::$props->host != $props->host){
                    if(self::$CON){
                        mysql_close(self::$CON);
                        self::$CON=null;
                    }
                }
            }
            self::$props = $props;

        }
        $conProps = self::$props;

//        debug_print_backtrace();
        if (!self::$CON) {
           // echo "ESTABLISHING A CONNECTION\n";
            self::$CON = mysql_pconnect($conProps->host, $conProps->username, $conProps->password);
            if (self::$CON) {
                //echo $conProps->dbname  . "-CON ESTAB\n";
                if( ! $conProps->dbname ) throw new Exception("you must provide dbname");
                if( ! mysql_select_db($conProps->dbname , self::$CON ) ){
                    echo mysql_error();
                    throw new Exception( " CANT select DB:" . $conProps->dbname);
                }

                /*
                     If you do not want the server to perform any conversion of result sets or error messages,
                         set character_set_results to NULL or binary:
                        SET character_set_results = NULL;
                    */

                //mysql_set_charset('utf8',self::$CON);
                mysql_query('set names "utf8"', self::$CON);
                mysql_query('SET character_set_results = NULL', self::$CON);

            }else{
                throw new Exception("cant connect to DB" . var_export($conProps,true) );
            }
        }else{
            if($props)
            mysql_select_db($props->dbname);
            elseif(self::$DB){
                mysql_select_db(self::$DB);
                self::$DB=null;
            }

        }

        return self::$CON;

    }

    static function setConn($CON,$db=null) {
        self::$CON = $CON;
        if($db) {
            mysql_select_db($db);
            self::$DB = $db;

        }
        //mysql_set_charset('utf8',self::$CON);
        mysql_query('set names "utf8"', self::$CON);
        mysql_query('SET character_set_results = NULL', self::$CON);
    }

    //reset db connection to another DB
    static function setDB($db) {
        self::$CON = null;
        self::$DB  = $db;
    }





}

?>
