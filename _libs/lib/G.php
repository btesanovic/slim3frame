<?php 

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class G {
    static function dump($var){
        echo '<pre style="font-size:11px""">';
        htmlspecialchars( var_dump($var) );
        echo '</pre>';
    }
}

