<?php
namespace Libslim;

trait Views{
    function menulinks($links , $active ){
        $all = [];
        foreach ($links as $ln=>$name){
            $l = new \stdClass();
            $l->url = '';
        }
    }
}