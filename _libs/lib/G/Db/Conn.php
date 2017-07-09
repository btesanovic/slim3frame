<?php

/*
 * MYSQLI implementation
 */

class G_Db_Conn {

    public static $CON = null;
    public static $PREVIOUSPROPS = null;
    public static $DB = null;
    protected static $props = null;
    protected static $mysqli = null;

    public static function setProps($props) {
        self::$props = $props;
        if (self::$CON) {
            //trigger_error("Connection already established recconecting with new properties G_Db_Conn", E_USER_WARNING);
        }
        self::$CON = null;
    }

    public static function switchToPreviousConnection() {
        if (self::$PREVIOUSPROPS) {
            self::getConnection(self::$PREVIOUSPROPS);
        } else {
            throw new Exception("no previous connection");
        }
    }

    public static function getMysqli() {
        if (self::$mysqli)
            return self::$mysqli;
        if (self::$props) {
            $conProps = self::$props;
            $mysqli = new mysqli($conProps->host, $conProps->username, $conProps->password, $conProps->dbname);
            $mysqli->set_charset('utf8');
            self::$mysqli = $mysqli;
        }
        return self::$mysqli;
    }

    public static function getConnection($props = null) {

        //echo "GET CONN\n";
        if ($props) {
            if (self::$props) {
                if (self::$props->host != $props->host) {
                    if (self::$CON) {
                        self::$PREVIOUSPROPS = clone self::$props;
                        mysqli_close(self::$CON);
                        self::$CON = null;
                    }
                }
            }
            self::$props = $props;
        }
        $conProps = self::$props;

        if (!self::$CON) {

            //Connect with Compression enabled
            if (isset($conProps->compression) && $conProps->compression) {
                $link = mysqli_init();
                if (!$link) {
                    throw new Exception("mysqli_init failed");
                }
                if (!mysqli_options($link, MYSQLI_OPT_CONNECT_TIMEOUT, 5)) {
                    throw new Exception('Setting MYSQLI_OPT_CONNECT_TIMEOUT failed');
                }
                if (!mysqli_options($link, MYSQLI_INIT_COMMAND, "SET NAMES 'utf8'")) {
                    throw new Exception('Setting UTF8 connection failed');
                }
                if (!mysqli_real_connect($link, $conProps->host, $conProps->username, $conProps->password, null, null, null,  MYSQLI_CLIENT_COMPRESS ) ){
                    throw new Exception("cant connect to DB:" . var_export($conProps, true));
                }

                self::$CON = $link;
                    
            } else {

                // echo "ESTABLISHING A CONNECTION\n";
                self::$CON = mysqli_connect($conProps->host, $conProps->username, $conProps->password);
            }

            if (self::$CON) {
                //echo $conProps->dbname  . "-CON ESTAB\n";
                if (!$conProps->dbname)
                    throw new Exception("you must provide dbname");
                if (!mysqli_select_db(self::$CON, $conProps->dbname)) {
                    throw new Exception(" CANT select DB:" . $conProps->dbname);
                }

                /*
                  If you do not want the server to perform any conversion of result sets or error messages,
                  set character_set_results to NULL or binary:
                  SET character_set_results = NULL;
                 */

                //mysqli_set_charset('utf8',self::$CON);
                mysqli_query(self::$CON, 'set names "utf8"');
                mysqli_query(self::$CON, 'SET character_set_results = NULL');
            } else {
                throw new Exception("cant connect to DB:" . var_export($conProps, true));
            }
        } else {
            if ($props)
                mysqli_select_db(self::$CON, $props->dbname);
            elseif (self::$DB) {
                mysqli_select_db(self::$CON, self::$DB);
                self::$DB = null;
            }
        }

        return self::$CON;
    }

    static function setConn($CON, $db = null) {
        self::$CON = $CON;
        if ($db) {
            if (!mysqli_select_db(self::$CON, $db)) {
                throw new Exception("failed to select DB '$db' err:" . mysqli_error(self::$CON));
            }
            self::$DB = $db;
        }
        //mysqli_set_charset('utf8',self::$CON);
        mysqli_query(self::$CON, 'set names "utf8"');
        mysqli_query(self::$CON, 'SET character_set_results = NULL');
    }

    //reset db connection to another DB
    static function setDB($db) {
        self::$CON = null;
        self::$DB = $db;
    }

}

?>