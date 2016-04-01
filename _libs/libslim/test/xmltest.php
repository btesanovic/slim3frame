<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require __DIR__ .'/../../vendor/autoload.php';
$xml = file_get_contents(__DIR__ .'/../../www/478d9dc2be60ab5a6e5f5b8457a33415');
$d = microtime(true);
ob_start();
for($i=0;$i<3;$i++){
    
    $data =Libslim\Parsers\Xml::parse($xml);
    
}
ob_clean();

echo (microtime(true) - $d) ."\n";



$d = microtime(true);
ob_start();
for($i=0;$i<3;$i++){
    
    $data =Libslim\Parsers\Xml::parseClenNs($xml);
    
}
ob_clean();

echo (microtime(true) - $d) ."\n";



$d = microtime(true);
ob_start();
for($i=0;$i<3;$i++){
    
    $data =Libslim\Parsers\Xml::toJson($xml);
    var_dump($data);
    
}
echo ob_get_clean();

echo (microtime(true) - $d) ."\n";




$d = microtime(true);
ob_start();
for($i=0;$i<3;$i++){
    
    $data =Libslim\Parsers\Xml::toArray($xml);
    //var_dump($data);
    
}
echo ob_get_clean();

echo (microtime(true) - $d) ."\n";
