<?php


class G_Tools_Hash {

    public static function getVastHash($str) {

    }

    const FNV_offset_basis_32 = 2166136261;

    //fnv1 hash
    public static function fnv1($txt) {

        throw new Exception("this method doesnt return same value on 32bit and 64bit");
        $buf  = str_split($txt);
        $hash = self::FNV_offset_basis_32; //2166136261
        foreach ($buf as $chr)
        {
            $hash += ($hash << 1) + ($hash << 4) + ($hash << 7) + ($hash << 8) + ($hash << 24);
            $hash = $hash ^ ord($chr);
        }
        $hash = $hash & 0x0ffffffff;
        return $hash;
    }


// 1M itterations 5sec on mac, 2sec on server TOR
    static function md5_32int($s) {
        $m = md5($s);
        //$int = sprintf("%d" , substr($s,0,8));
        //$int = hexdec(substr($m, 0, 8)); //
        $int = hexdec(substr($m, 0, 8)  );
        $int2 = hexdec(substr($m, 8, 8)  );
        $int3 = hexdec(substr($m, 16, 8)  );
        $int4 = hexdec(substr($m, 24, 8)  );
        $intmul = hexdec(substr($m, 29, 2)  );
        if(!$intmul){
            $intmul = hexdec(substr($m, 12, 2)  );
            if(!$intmul){
                $intmul = hexdec(substr($m, 5, 2)  );
            }
        }
        if(abs($int)==2147483647){
            echo "md5:" . substr($m, 0, 8) ."\n greater than MAX_INT\n floatval:";
            echo sprintf("%f" , hexdec(substr($m, 0, 8) ) ) ;
            sleep(2);
            throw new Exception("MAX INT32 overflow ?");
        }
        //    $int = hexdec(substr($m, 0, 4) . substr($m,16,4)); //
        if( (PHP_INT_MAX == 2147483647) ){
            //32bit systems, we need BC lib

            if(!$intmul){
                throw new Exception("$s md5:$m gives intmul = 0");
            }

            $s =   bcdiv( bcadd(bcadd( bcadd($int , $int2 ) , $int3 ) , $int4 ) , $intmul , 3 );
            if(strpos($s , '.') ){
                list($ret,$junk) = explode('.', $s);
                return $ret.$junk;
            }

            return $s;
        }else{
            //on 64bit systems use native
            $i = $int+$int2+$int3+$int4;
            $i = round( $i/$intmul , 3 );
            return intval(str_replace( '.' , '' , strval($i) ));
            //return $int+$int2+$int3+$int4;
        }
        //return $int;
    }

    static function sha1_32int($s) {
        $m = sha1($s);
        //$int = sprintf("%d" , substr($s,0,8));
        $int = hexdec(substr($m, 0, 4) . substr($m, 16, 4)); //
        return $int;
    }



}
