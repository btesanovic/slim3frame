<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Alert
 *
 * CREATE TABLE `alert` (
  `ctkey` varchar(32) NOT NULL DEFAULT '',
  `atype` varchar(32) NOT NULL DEFAULT '',

  `fr_id` varchar(64) DEFAULT NULL,
  `verified` int(1) NOT NULL DEFAULT '0',
  `msg` varchar(255) DEFAULT NULL,
  `ts` varchar(14) NOT NULL DEFAULT '',
  PRIMARY KEY (`ctkey`,`atype`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 * 
 * @author bojan
 */
class G_Db_Alert {
    //put your code here
    //alert type default
    const AT_DEFAULT=1;
    
    static function i(){
        
    }
    
    static function clearAlert($id , $atype = self::AT_DEFAULT){
        G_Db::deleteArr(array('ctkey'=>$id , 'atype'=>$atype), 'alert');
        
    }
    
    
    static function alert($id,$message,$atype = self::AT_DEFAULT ){
        G_Db::insert(array('ctkey'=>$id,'msg'=>$msg,'atype'=>$atype), 'alert');
    }
    
    
    
}

?>
