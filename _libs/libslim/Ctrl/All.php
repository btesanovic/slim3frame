<?php
namespace Libslim\Ctrl;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of All
 *
 * @author bojan
 */
use Psr\Http\Message\ServerRequestInterface;

abstract class All extends Base{
    //put your code here
    
    abstract function index($req,$res,$param);
    


    public function __invoke(ServerRequestInterface $req,$res,$param) {
        $action = isset($param['action']) ? $param['action'] : null;
        if($action){
            if( method_exists($this, $action) ){
                //return call_user_method($action, $this ,[$req,$res,$param]);
                return call_user_func_array([$this,$action] ,[$req,$res,$param] );
            }else{
                throw new \Exception("action '$action'  not registered");
            }
            
        }else{
            //$attribs = $req->getAttributes();
             //array(1) { ["category"]=> string(5) "props" } 
            //var_dump($param);
            
            //if we have any param that has method defined call that first else call index
            foreach ($param as $pname=>$pval){
                if( method_exists($this, $pval) ){
                    return call_user_func_array([$this,$pval] ,[$req,$res,$param] );
                }
            }
            return $this->index($req,$res,$param);
        }
    }
}
