<?php

class G_Dbg{
	public static $instance=null;

	protected $debugInfo=array();
	protected $debugOn=false;
	protected $timer=array();
	protected $allTimes=array();
	protected $errorInfo = array();
	protected $startTime;



	public static $dontDebug=null;

	function __construct(){
		try{
		//$config = Zend_Registry::get('config');
		//$this->debugOn = $config->debug->status;
		$this->debugOn = true;
		$this->startTime = microtime(true);
		//$this->errorInfo = array('error'=>'elo');
		}catch (Exception $e){
			$this->debugOn = true;
		}
	}

	/**
	* Returns instance of Vast debugger
	* @param void
	* @return G_Dbg
	*/
	static function i(){
		if(!self::$instance){
			self::$instance = new self();
		}
		return self::$instance;
	}

	static function dontDebug(){
		self::$dontDebug = true;
	}

	static function doDebug(){
		self::$dontDebug = false;
	}

	function debugOff(){
		$this->debugOn = false;
	}

	/**
	* Sets debug message
	*
	* @param mixed $messsage
	* @param String $label
	* @return void
	*/
	function set($message,$label,$color=null,$error=false){
		if(self::$dontDebug && !$error) return ;
		$msg='';
		if($color){
			$color = "style = 'color: $color'";
		}
		$tdiff = $this->getTimeDiff();
		if($this->debugOn){
			//$this->debugInfo[] = $label .' : '.htmlentities($messsage);
			if(is_array($message) || is_object($message)){
				//$msg = Vast_Utils::imlodeKeyValue(':','<br/>',$messsage);
				//ob_start();
				$msg = Zend_Debug::dump($message,$tdiff.' <b $color>'.$label.'</b>',false);
				//$msg = ob_end_clean();
				//$msg = implode(' - ',$messsage);
			}else{
				if(preg_match('!https?://(.*?)!', $message)){
					$message = '<a href="'.$message.'">'.$message.'</a>';
				}
				$msg = "$tdiff <b $color>".$label .'</b> : '.$message;
			}
			if(! self::$dontDebug)	$this->debugInfo[] =  $msg . ' mem used : <b style="color:red" >'.self::getCurrentMemUsage() .'</b> Mb';
		}
		return $msg;
	}

	function setError($message,$label){
		if(Vast_Server::isLiveOrBeta()) return ;
		//$btrace = debug_backtrace();
		//if ( self::$dontDebug ) return ;
		$msg = $this->set($message,$label,'red',true);
		//$error = var_export($btrace,true);
		//Vast::d($error);
		//var_dump($message);
		//array_push($this->errorInfo,$message);
		$this->errorInfo[] = $msg;
	}

	function getTimeDiff(){
		return round(microtime(true) - $this->startTime,5);
	}

	function timerStart($label){
		if(self::$dontDebug) return ;
		$this->timer[$label] = microtime(true);
		$cnt = count($this->timer);
		$count = $cnt * 4;
		$c = array('#00000','#c9e9fa','#FFFACD','#E0FFFF','#ffffff','#cccccc');
		$color = $c[array_rand($c)];
		$color = $c[$cnt];
		$this->debugInfo[] = "<div style='background-color: $color;margin-left: {$count}px;padding: 3px'><h3> Time Section for $label</h3>";
	}
	function timerStop(){
		if(self::$dontDebug) return ;
		end($this->timer);
		$label = key($this->timer);
		$lastTime = array_pop($this->timer);
		$dT = round(microtime(true)- $lastTime,5);
		$this->set($dT,$label);
		if(isset($this->allTimes[$label])){
			$this->allTimes[$label] = $this->allTimes[$label] + $dT;
		}else {
			$this->allTimes[$label] = $dT;
		}
		$this->debugInfo[] = "</div>";

	}

	function getInfo(){
		if(self::$dontDebug) return '';
		if($this->debugOn){
			//return Vast_Utils::imlodeKeyValue(':','<br/>',$this->debugInfo);
			$t='<h3>TIMES</h3><br>';
			foreach ($this->allTimes as $k=>$v) {
				$t .= "<b>$k</b> $v <br>";
			}
			//$memUsage =  memory_get_usage()/ (1024 * 100 ) . "\n";
			$memUsage =  "<p> Peak Memory used:" . self::getPeakMemUsage() . "Mb</p>";
			return  '<div id="dbginfo" style="z-index:10"><pre style="text-align:left; padding:0 10px; font-size:11px; width:100%;background-color:#FCF4E0" >'.implode('<br>',$this->debugInfo) . $t .$memUsage . '</pre></div>';
		}

	}

	//return current mem usage in Mb
	public static function getCurrentMemUsage(){
		if( function_exists('memory_get_usage'))
		return  round ( memory_get_usage()/ (1024 * 1024 ) , 2 ) ;
		else return '-1';
	}

	public static function getPeakMemUsage(){
		if( function_exists('memory_get_usage'))
		return  round ( memory_get_peak_usage()/ (1024 * 1024 ) , 2 ) ;
		else return '-1';
	}

	function getErrors(){

		//if(self::$dontDebug) return '';
		if( ! $this->errorInfo)	return false;
		if($this->debugOn){
			return  '<pre style="background:#ccc; text-align:left; position:absolute; z-index:99999; top:0; right:0; margin:0; padding:10px; filter:alpha(opacity=50); opacity:0.6;"><h3 style="font-weight: bold;">ERRORS</h3>'.implode('<br/>',$this->errorInfo).'</pre>';
		}
	}
}


?>