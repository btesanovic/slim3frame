<?php

/**
 * Created by JetBrains PhpStorm.
 * User: bojan
 * Date: 5/22/13
 * Time: 10:12 PM
 * To change this template use File | Settings | File Templates.
 */
class G_Tools_Svncmd {

    public static function up($path) {
        return G::linux("svn up " . $path);
    }

    public static function cat($svnpath, $r = '') {
        //TODO use tmp file to redirect outout than read it
        $r = trim($r, '-');
        $r = $r ? '-' . $r : '';
        ob_start();
        G::linux("svn cat $r " . $svnpath);
        $catout = ob_get_clean(); 
        return $catout;
    }

    public static function ci($path, $m) {
        return G::linux("svn ci $path -m'$m'");
    }

    public static function getrevisions($path, $limit='' ,DateTime $fromdate = null) {
        
        $tsfrom=0;
        if($fromdate){
            $tsfrom = $fromdate->getTimestamp();
        }
        
        $revs=array();
        if($limit) $limit = " --limit $limit ";
        if (!file_exists($path)) {
            throw new Exception("file not exist '$path' ");;
        }

        $svnlog = `svn log $limit $path`;
        echo "SVN cmd: svn log $limit $path\n";
        //echo "SVN LOG\n";
        $lines = explode("\n", $svnlog);
        foreach ($lines as $n=>$l) {
            $row = explode("|", $l);
            //array_walk($row, 'trim');
            $row = array_map( 'trim' , $row);
            //var_dump($row);

            //r487060 | bojan | 2013-11-27 17:27:53 +0100 (Wed, 27 Nov 2013) | 1 line
            if ((strlen($l) > 10) && $row[0]{0} == 'r' && $row[2]{0} == '2') {
                //var_dump($row);
                //echo "$l\n";
                list($dt,$junk) = explode('+', $row[2],2);
                //date_parse_from_format('Y-m-d H:i:s', '2014-05-21 08:59:18 +0200 (Wed, 21 May 2014)');
                $dtobj = DateTime::createFromFormat('Y-m-d H:i:s', trim($dt));
                $ts = strtotime($row[2]);
                $ts = $dtobj->getTimestamp();
                $newdate = date('Y-m-d-H\h-i\m' , $ts);
                $newdateSimple = date('Y-m-d' , $ts);
                if($tsfrom){
                    if($tsfrom > $ts)                        continue;
                }
                $revs[$row[0]] = array('date'=>$row[2], 'comment'=>$lines[$n+1] ,'dateymd'=>$newdateSimple,'datenorm' => $newdate ,'ts'=>$ts );
                //echo `svn cat -{$row[0]} $f`;
                //echo "\n\n";
            }
        }
        
        return $revs;
    }
    
    /*
     * Get the last revision from specified day
     */
    public static function getLastRevOfDay($path , $dateymd , $limit=50){
         //$tsfrom = $date->getTimestamp();
        
        $revs=array();
        if($limit) $limit = " --limit $limit ";
        if (!file_exists($path)) {
            throw new Exception("file not exist '$path' ");;
        }

        $svnlog = `svn log $limit $path`;
        echo "SVN cmd: svn log $limit $path\n";
        //echo "SVN LOG\n";
        $lines = explode("\n", $svnlog);
        foreach ($lines as $n=>$l) {
            $row = explode("|", $l);
            //array_walk($row, 'trim');
            $row = array_map( 'trim' , $row);
            //var_dump($row);

            //r487060 | bojan | 2013-11-27 17:27:53 +0100 (Wed, 27 Nov 2013) | 1 line
            if ((strlen($l) > 10) && $row[0]{0} == 'r' && $row[2]{0} == '2') {
                //var_dump($row);
                //echo "$l\n";
                list($dt,$junk) = explode('+', $row[2],2);
                //date_parse_from_format('Y-m-d H:i:s', '2014-05-21 08:59:18 +0200 (Wed, 21 May 2014)');
                $dtobj = DateTime::createFromFormat('Y-m-d H:i:s', trim($dt));
                $ts = $dtobj->getTimestamp();
                $newdate = date('Y-m-d-H\h-i\m' , $ts);
                $newdateSimple = date('Y-m-d' , $ts);
                
                if($newdateSimple == $dateymd){
                    return array('rev'=>$row[0] , 'date'=>$row[2], 'comment'=>$lines[$n+1] ,'dateymd'=>$newdateSimple,'datenorm' => $newdate ,'ts'=>$ts );
                }
                //echo `svn cat -{$row[0]} $f`;
                //echo "\n\n";
            }
        }
        
        return null;
    }

}
