<?php
namespace Libslim\Ctrl;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Base
 *
 * @author bojan
 */
class Base {
    //put your code here
    
    protected $c;
    
    public function __construct($c) {
        $this->c = $c;
    }


    public function hdrText($res){
        //header("Content-Type: text/plain");
        return $res->withHeader('Content-Type', 'text/plain');
    }
    
    public function hdrJson($res){
        //header("Content-Type: application/json");
        return $res->withHeader('Content-Type', 'application/json');
    }
    
    public function hdr404($res){
        //header("HTTP/1.0 404 Not Found");        
        return $res->withStatus(404);
    }
    
    /*
     * generartes navigation links
     */
    public function navlinks(array $routNameMap , $current = null ){
        $router = $this->c['router'];
        if(!$current){
            $current = $this->c['request']->getUri()->getPath();
        }
        $links = [];
        foreach ($routNameMap as $rtname=>$urlData){
            $anchorName = $urlData['text'];
            $urlParams = $urlData['data'];
            $url = $router->pathFor($rtname, $urlParams);
            $c = new \stdClass;
            $c->url = $url;
            $c->text = $anchorName;
            $c->active = false;
            if($url==$current) $c->active = true;
            $links[] = $c;
        }
        return $links;
    }
    
    public function jsonResponse($res , $dataArr , $msg=''){
        $a = [];
        $a['status']=200;
        $a['ok']=true;
        $a['data'] = $dataArr;
        $a['msg'] = $msg;
        return $this->hdrJson($res)->write(json_encode($a));
    }
    
    public function throwJsonException($res , $msg ){
        $a = [];
        $a['status']=500;
        $a['ok'] = false;
        $a['msg'] = $msg;
        return $this->hdrJson($res)->write(json_encode($a));
    }
    
    
}
