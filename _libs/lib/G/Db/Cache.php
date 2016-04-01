<?php

class G_Db_Cache {

	/**
	 * @var G_Cache
	 * */
	static $C;

	public static function query($sql,$one = false){
		self::_init();
		$md = md5($sql);
		$rsGA = self::$C->get($md);
		if($rsGA){
			return unserialize($rsGA);
		}else{
			if($one){
				$R = G_Db::getone($sql);
			}else{
				$R = G_Db::get($sql);
			}
			$rsGA = serialize($R);
			self::$C->set($md,$rsGA);
		}

		return $R;
	}

	public static function _init(){
		if( self::$C) return ;
		self::$C = G_Cache::getInstance('48');
	}
}