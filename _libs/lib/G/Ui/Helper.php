<?php



class G_Ui_Helper {

	public static function mkOptions($arr,$sel=''){
		$str='';
		if($sel){
			foreach ($arr as $s){
				if(strcasecmp($s,$sel) == 0){
					$str .= "<option value='$s' selected='selected'>$s</option>";
				}else {
					$str .= "<option value='$s' >$s</option>";
				}
			}
		}else{
			foreach ($arr as $s){
				$str .= "<option value='$s' >$s</option>";
			}
		}

		return $str;
	}

        /*
         * $firstOption should be array for a first option ie array('0'=>'all') or similar
         */
	public static function mkOptionsDual($arr,$sel='',$firstOption=null){
		$str='';
		/*if($plsSel){
			array_unshift($arr , 'Please Select');
		}*/
                if($firstOption){
                    $str .= "<option value='".key($firstOption) ."' selected='selected'>".  current($firstOption) ."</option>"; 
                }
		if($sel){
			foreach ($arr as $k=>$s){
				if(strcasecmp($k,$sel) == 0){
					$str .= "<option value='$k' selected='selected'>$s</option>";
				}else {
					$str .= "<option value='$k' >$s</option>";
				}
			}
		}else{
			foreach ($arr as $k=>$s){
				$str .= "<option value='$k' >$s</option>";
			}
		}

		return $str;
	}


    public static function renderTable($data , $header=null , $skipKeys=array() , $tableAddHtml ='' , $title=''){

        if(!$skipKeys) $skipKeys = array();
        echo "<table $tableAddHtml>";
        if($title) echo "<caption>$title</caption>";
        if($header){
            echo "<tr>";
            foreach($header as $h){
                if( in_array($h , $skipKeys)) continue;
                echo "<th>$h</th>";
            }
            echo "</tr>";
        }
        foreach($data as $row){
            echo "<tr>";
            foreach($row as $k=>$v){
                if( in_array($k , $skipKeys)) continue;
                echo "<td>$v</td>";
            }
            echo "</tr>";
        }

        echo "</table>";

    }
    
    
    public static function renderTableByHeader($data , $header , $skipKeys=array() , $tableAddHtml ='' , $title=''){

        if(!$skipKeys) $skipKeys = array();
        echo "<table $tableAddHtml>";
        if($title) echo "<caption>$title</caption>";
        if($header){
            echo "<tr>";
            foreach($header as $h){
                if( in_array($h , $skipKeys)) continue;
                echo "<th>$h</th>";
            }
            echo "</tr>";
        }
        foreach($data as $trow){
            echo "<tr>";
            if($trow instanceof G_Array) $row = $trow->toArray ( );
            else $row = $trow;
            
            foreach($header as $h){
                if( in_array($h , $skipKeys)) continue;
                $v = isset($row[$h]) ?$row[$h] : 'NAN';
                echo "<td>$v</td>";
            }
            
            
            echo "</tr>";
        }

        echo "</table>";

    }


}

?>
