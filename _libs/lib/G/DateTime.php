<?php

/**
 * Created by Bojan Tesanovic.
 * Date: 4/6/12
 * Time: 6:44 PM
 */
class G_DateTime {

    const OneHour = 3600;
    const OneDay = 86400;
    const SevenDays = 604800;
    const ThirtyDays = 2592000;
    const SixMonths = 15811200;
    const OneYear = 31536000;

    static function dateTimeNow($ts = null, $format = 'Y-m-d H:i:s') {
        $ts = self::checkTs($ts);
        //if (!$ts)
          //  $ts='';
        return date($format, $ts);
    }

    static function timeNow($ts = null) {
        $ts = self::checkTs($ts);
        var_dump($ts);
        return date('H:i:s', $ts);
    }

    static function date($time = null) {
        if(!$time) $time = time ();
        return date('Y-m-d', $time);
    }
    
    /*
     * @return DateTime $dt
     */
    static function dateTimeObj($date=null){
        $date = $date ? $date : self::date() ;
        return DateTime::createFromFormat('Y-m-d', $date);
    }

    static function checkTs($ts) {
       
        if (!$ts)
            return time();
         $ts = strval($ts);
        if (strlen($ts) == 13)
            $ts= substr($ts, 0, 10);

        return intval($ts);
    }

    static function makeDateSpan($ymdStart, $ymdEnd, $dayInterval = 1) {
        $begin = new DateTime( $ymdStart );
        $end = new DateTime( $ymdEnd );

        $interval = new DateInterval("P{$dayInterval}D");
        $daterange = new DatePeriod($begin,$interval, $end );
        $dates = array();
        foreach ($daterange as $date) {
            $dates[] = $date->format("Y-m-d");
        }
        
        return $dates;
    }
    
    static $timer=0;
    public static function startTimer(){
        self::$timer = time();
    }
    
    public static function endTimer(){
        return time() - self::$timer;
    }

    /**
     * @static
     * @param $time if time is timestamp the diff will be form that timestamp to now else $time is treated as delta time
     * @param array $format
     * @param bool $usePlurals
     * @return string
     */
    static function timeDiff($time, $format = array('day' => 'd',
        'hour' => 'h',
        'minute' => 'm',
        'second' => 's'), $usePlurals = false) {
        $ts = self::checkTs($time);

        if ($ts > (self::OneYear * 10)) {
            $now = time();
            $diff = $now - $ts;
        } else {
            $diff = $time;
        }

        $day = 0;
        $hours = 0;
        $min = 0;
        $sec = 0;

        $v = $diff;
        // Days.
        if ($v >= self::OneDay) {
            $day = floor($v / self::OneDay);
            $v = $v - ($day * self::OneDay);
        }

        // Hours.
        if ($v >= self::OneHour) {
            $hours = floor($v / self::OneHour);
            $v = $v - ($hours * self::OneHour);
        }

        // Minutes.
        if ($v >= 60) {
            $min = floor($v / 60);
            $v = $v - ($min * 60);
            $sec = $v;
        }



        $ret = "";
        if (!$day && !$hours && !$min && !$sec)
            return;

        if ($day > 0)
            $ret = $ret . $day . "d ";

        $ret = $ret . sprintf("%02dh", $hours) . ":";

        $ret = $ret . sprintf("%02dm", $min);
        //remove SECONDS useless
        //$ret = $ret . sprintf("%02dm", $min) . ".";
        // if ($sec > 0)
        //   $ret = $ret . sprintf("%02d", $sec);

        return $ret;

        $val = '';
        $key = '';
        if (self::OneDay) {
            $val = self::OneDay;
            $key = $format['day'];
        }
        if ($hour) {
            $val = $hour;
            $key = $format['hour'];
        }
        if ($minute) {
            $val = $minute;
            $key = $format['minute'];
        }
        if (!$val) {
            $val = $diff;
            $key = $format['second'];
        }


        if ($val > 1 && $usePlurals)
            return "$val {$key}s";
        if ($_hour)
            return "$val $key {$_hour}";
        return "$val $key";
    }

}
