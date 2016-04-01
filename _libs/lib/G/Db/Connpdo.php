<?php

class G_Db_Connpdo {

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
            //self::$CON = mysql_pconnect($conProps->host, $conProps->username, $conProps->password);
            if( ! $conProps->dbname ) throw new Exception("you must provide dbname");

            self::$CON = new PDO("mysql:host=$conProps->host;dbname=$conProps->dbname", $conProps->username, $conProps->password, array(
                PDO::ATTR_PERSISTENT => true
            ));

            self::$CON->setAttribute( PDO::ATTR_ERRMODE , PDO::ERRMODE_EXCEPTION );
            if (self::$CON) {
                //echo $conProps->dbname  . "-CON ESTAB\n";


                /*
                     If you do not want the server to perform any conversion of result sets or error messages,
                         set character_set_results to NULL or binary:
                        SET character_set_results = NULL;
                    */

                //mysql_set_charset('utf8',self::$CON);
                self::$CON->exec('set names "utf8"');
                self::$CON->exec('SET character_set_results = NULL');

            }else{
                throw new Exception("cant connect to DB" . var_export($conProps,true) );
            }
        }else{
            if($props){
                //mysql_select_db($props->dbname);
                self::$CON->exec("use " . self::$DB);
            }elseif(self::$DB){
                //mysql_select_db(self::$DB);
                self::$CON->exec("use " . self::$DB);
                self::$DB=null;
            }

        }


        return self::$CON;

    }

    static function setConn($CON,$db=null) {
        self::$CON = $CON;
        if($db) {
            //mysql_select_db($db);
            self::$CON->exec("use " . $db);
            self::$DB = $db;

        }
        //mysql_set_charset('utf8',self::$CON);
        //mysql_query('set names "utf8"', self::$CON);
        //mysql_query('SET character_set_results = NULL', self::$CON);

        self::$CON->exec('set names "utf8"');
        self::$CON->exec('SET character_set_results = NULL');
    }

    //reset db connection to another DB
    static function setDB($db) {
        self::$CON = null;
        self::$DB  = $db;
    }





}

?>
