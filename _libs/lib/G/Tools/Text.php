<?php


class G_Tools_Text{


    public static $colorsArr;

    /**
     * Append slash at the end of the string if it does not exist
     * @param String $string
     * @return String $slashed_string
     */
    public static function appendSlash($string){
        if(self::endsWith($string,'/')){
            return  $string;
        }else{
            return $string.'/';
        }
    }

    /**
     * Will replace all non alphanumeric characters with $replace char
     * @param string $string
     * @return string
     */
    public static function sanitizeText($string , $replace='-'){
        $string = trim(strtolower($string));
        $string = preg_replace('/[^a-z0-9_]/', $replace, $string);
        while (strpos($string, $replace.$replace)!==false)
            $string = str_replace($replace.$replace,$replace, $string);

        return $string;
    }

    /**
     * returns if $string contains $needle
     * @param string $needle
     * @param string $string
     * @return boolean
     */
    public static function contains($needle,$string){
        if(strpos($string,$needle) !== false) return true;
        return false;
    }

    /**
     * String starts with something
     *
     * This function will return true only if input string starts with
     * niddle
     *
     * @param string $string Input string
     * @param string $niddle Needle string
     * @return boolean
     */
    static function startsWith($string, $niddle , $caseInsensitive) {
        //return substr($string, 0, strlen($niddle)) == $niddle;
        if($caseInsensitive){
            if(stripos($string,$niddle) === 0) return true; else return false;
        }else{
            if(strpos($string,$niddle) === 0) return true; else return false;
        }
    } // end func str_starts with

    /**
     * String ends with something
     *
     * This function will return true only if input string ends with
     * niddle
     *
     * @param string $string Input string
     * @param string $niddle Needle string
     * @return boolean
     */
    static function endsWith($string, $niddle) {
        return substr($string, strlen($string) - strlen($niddle), strlen($niddle)) == $niddle;
    } // end func str_ends_with



    //TODO OLD FUNCTIONS SHOULD BE DELETED OR IMPROVED

    public static  function getDayDiff($time,$returnInt=false){
        static $today,$currYear;

        if(!isset($today)){
            //$today = strtotime(date("m/d/Y")); // getting timestamp for date only - 01/31/2007 00:00
            $today=time();
        }

        if(!is_numeric($time))
            $time   = strtotime($time);

        $dsec = $today - $time;
        $H='';
        $mod = $dsec % 86400;
        if($mod){
            $hours = floor($mod/3600);
            if($hours)
            $H="{$hours}h ";

        }
        //exit();
        $delta = floor( $dsec  / 86400);

        $delta = $delta > 0 ? $delta : $delta;

        if($returnInt)
            return (integer)$delta;

        if(!$delta || $delta < 1)
            return 'Today';
        elseif ($delta == 1)
            return "1 day {$H}ago";
        else
            return $delta . " days {$H}ago";
    }

    public static  function getDayDiffAllowHours($timeO,$returnInt=false){
        static $today,$currYear;

        //var_dump($time);

        if(!isset($today))
            $today = time(); // getting timestamp for date only - 01/31/2007 00:00

        if(!is_numeric($timeO))
            $time   = strtotime($timeO);
        else $time = $timeO;

        $delta = floor( ($today - $time) / 3600);
        //return $delta;
        if($delta>24) return self::getDayDiff($timeO,$returnInt);

        $delta = $delta > 0 ? $delta : $delta;

        if($returnInt)
            return (integer)$delta;

        if(!$delta || $delta < 1)
            return 'Now';
        elseif ($delta == 1)
            return '1 hour ago';
        else
            return $delta . ' hours ago';
    }

    public static function parseDate($date){
        $dates = explode('T', $date);

        $day = array_reverse(explode('-', $dates[0]));
        $time = explode('+', $dates[1]);

        return implode('.', $day).' '.$time[0];
    }






    public static function highlightStem($content,$stems,$isLink =false)
    {
        if(trim($content) =='') return '';
        if(!$stems) return $content;

        $stems = explode(' ',$stems);
        $pattern = array();

        foreach ($stems as $p )
        {
            if(strlen($p) < 2)
                continue;

            $p = self::getJustAlphaNum($p);
            $pattern[] = "/\b{$p}[^\s\W]*/im";
        }

        $class = $isLink ? 'hiliA' : 'highlite';
        return preg_replace($pattern,"<b class='$class'>\$0</b>",$content);
    }


    public static function getJustAlphaNum($str){
        $final='';
        $remove = array('!','#','$','%','^','&','*','(',')','/','\\',':',';','+','`','~');
        $space = array('     ', '    ','   ','  ');
        $final = str_replace($remove,' ',$str);
        $final = str_replace($space,' ',$final);

        return trim($final);
    }

    public static function getMonths($value){
        $days = 365 * $value;
        return floor($days/30);
    }

    public static function getWeeks($value){
        $days = 365 * $value;
        return floor($days/7);
    }

    /**
     * Retreive symbol by currency USD=>$
     *
     * @param string $currency 	- eg. USD
     * @param integer $price 	- if no price is specified only currency symbol if found or $ will be return. If isset $price, the return value will be formated
     */
    public static function getCurrencySymbol($currency, $price=null){
        if(!$currency) return false;
        $map = array(
            'USD'	=> '$',
            'GBP'	=> '&pound;',
            'CAD' 	=> '$',
            'EUR' 	=> '&euro;',
            'ARS' 	=> '$',
            'MXN' 	=> '$',
            'INR' 	=> 'Rs ', // &#x20A8;
            'BRL'	=> 'R$',
            'JPY'	=> '&#165;',
        );
        $currency = strtoupper(trim($currency));

        if(array_key_exists($currency,$map)) $symbol = $map[$currency];

        if(!isset($price)) return (isset($symbol))?$symbol:'$';

        if(isset($symbol))
            $return = $symbol.number_format((double)$price);
        else
            $return = number_format((double)$price).' ('.$currency.')';

        return $return;
    }

    /**
     * Sanaitize POST or GET data or some other string
     * @param
     * @return
     */
    /* public static function sanitizeExternalData(mixed $value){
     $arr = array(',','\'','?','&','.',',');
     }
     */

    // check varibla for aeiouy (words validation)
    public static function checkFieldsAeiouy($field){

        $keywords = preg_split ("/[aeiouy\d ]+/", $field);
        //		var_dump($keywords);
        //		$error = false;
        for($i=0; $i<count($keywords); $i++){
            if(strlen($keywords[$i])>=4) return false;
        }
        return true;
    }

    public static function isBetween($value, $min, $max, $inc = TRUE)
    {
        if ($value > $min &&
            $value < $max) {
            return TRUE;
        }

        if ($value >= $min &&
            $value <= $max &&
            $inc) {
            return TRUE;
        }

        return FALSE;
    }

    public static function isZip($value)
    {
        return (bool) preg_match('/(^\d{5}$)|(^\d{5}-\d{4}$)/', $value);
    }

    public static function isAlpha($value)
    {
        return ctype_alpha($value);
    }



    public static function getNiceDomain($url){
        $url = str_replace(array('http://','https://') , '' , strtolower( $url) );
        $url = 'http://'.$url;
        $chunks = parse_url($url);

        $domain = isset($chunks['host']) ? $chunks['host'] : '';
        $domain = str_replace('www.','',$domain);
        if ( $domain ){
            $chunk = explode('.',$domain);
            $count = count($chunk);
            if($count == 1 ){
                return $chunk[0];
            }elseif($count==2){
                return $chunk[0];
            }elseif ($count==3){
                $len1 = strlen($chunk[0]);
                $len2 = strlen($chunk[1]);
                $len3 = strlen($chunk[2]);
                if($len2>2){
                    return $chunk[0].'.'.$chunk[1];
                }else{
                    return $chunk[0];
                }
            }else{
                return $chunk[0].'.'.$chunk[1];
            }

        }
    }

    /**
     * Returns partner domain properly formatted to be valid url
     *
     *
     *
     * @param string $partnerDomain
     * @return string
     */
    public static function getPartnerDomainLink($partnerDomain='') {
        $domainElements = parse_url($partnerDomain);
        $scheme = isset($domainElements['scheme']) ? $domainElements['scheme'] : 'http';

        $return = '';
        if ( isset($domainElements['host']) ) {
            $return = $scheme . '://' . $domainElements['host'];
        }
        else if ( isset($domainElements['path']) ) {
            $return = $scheme . '://' . $domainElements['path'];
        }

        return $return;
    }

    public static function truncate($string, $length = 80, $etc = '...',$break_words = false)
    {
        if ($length == 0)
            return '';

        if (strlen($string) > $length) {
            $length -= strlen($etc);
            if (!$break_words)
                $string = preg_replace('/\s+?(\S+)?$/', '', substr($string, 0, $length+1));

            return substr($string, 0, $length).$etc;
        } else
            return $string;
    }

    public static function obfuscateMail($strEmail,$strInnerTxt='', $short=FALSE, $style='', $class='') {
        ### USAGE: #######################################################################################
        # Function obfuscates Email 'mailto:' hyperlinks using jscript making it hard for evil harvesters to pick it up
        # If given only $strEmail param, function returns <a href="mailto:$strEmail">$strEmail</a>
        # If given $strEmail and $strInnerTxt params, function returns <a href="mailto:$strEmail">$strInnerTxt</a>
        # If given $strEmail and $short=TRUE params, function returns <a href="mailto:$strEmail">
        # If given $strEmail and $style params, function returns <a href="mailto:$strEmail" style="$style">$strEmail</a>
        # If given $strEmail and $class params, function returns <a href="mailto:$strEmail" class="$class">$strEmail</a>
        ##################################################################################################
        $strNewAddress = '';
        for($intCounter = 0; $intCounter < strlen($strEmail); $intCounter++) {
            $strNewAddress .= "&#" . ord(substr($strEmail,$intCounter,1)) . ";";
        }
        $arrEmail = explode("&#64;", $strNewAddress);
        $strTag = "<script language="."Javascript"." type="."text/javascript".">\n";
        $strTag .= "<!--\n";
        $strTag .= "document.write('<a href=\"mai');\n";
        $strTag .= "document.write('lto');\n";
        $strTag .= "document.write(':" . $arrEmail[0] . "');\n";
        $strTag .= "document.write('@');\n";
        $strTag .= "document.write('" . $arrEmail[1] . "\"');\n";
        if ($class!='')		$strTag .= "document.write(' class=\"" . $class ."\" ');\n";
        if ($style!='')		$strTag .= "document.write(' style=\"" . $style ."\" ');\n";
        $strTag .= "document.write('>');\n";
        if (!$short) {
            if ($strInnerTxt=='') {
                $strTag .= "document.write('" . $arrEmail[0] . "');\n";
                $strTag .= "document.write('@');\n";
                $strTag .= "document.write('" . $arrEmail[1];
            } else {
                $strTag .= "document.write('" . $strInnerTxt;
            }
            $strTag .= "<\/a>');\n";
        }
        $strTag .= "// -->\n";
        $strTag .= "</script><noscript>" . $arrEmail[0] . " at \n";
        $strTag .= str_replace("&#46;"," dot ",$arrEmail[1]) . "</noscript>";
        return $strTag;
    }

    /**
     * Check if ZIP Code is in USA or Canadian format
     * USA format - #####
     * Canadian   - A#A #A#
     *
     * @param {String} str
     * @return {Boolean}
     */
    public function zipUSAorCanada($str){
        return (bool) preg_match('/^\d{5}$|^\D\d\D ?\d\D\d$/', $str);
    }


}

?>
