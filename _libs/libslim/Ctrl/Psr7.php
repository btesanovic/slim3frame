<?php
namespace Libslim\Ctrl;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * This class utilizes psr7 reequest 
 * PUT PATCH OPTION etc
 *
 * @author bojan
 */
use Psr\Http\Message\ServerRequestInterface;

abstract class Psr7 extends Base{
    //put your code here
    
    /*
     * id of a current model
     */
    protected $id;


    abstract function index($req,$res,$param);
    


    public function __invoke(ServerRequestInterface $req,$res,$param) {
        $model = isset($param['model']) ? $param['model'] : null;
        $this->id = isset($param['id']) ? $param['id'] : null;
        /*
    GET
    POST
    PUT
    DELETE
    HEAD
    PATCH
    OPTIONS
*/
        $METHOD = $req->getMethod();
        $action = $model.ucfirst(strtolower($METHOD));
        // catPut
        if($model){
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
            //throw new \Exception("action '$action'  not registered ");
            
            //if we have any param that has method defined call that first else call index
            /*foreach ($param as $pname=>$pval){
                if( method_exists($this, $pval) ){
                    return call_user_func_array([$this,$pval] ,[$req,$res,$param] );
                }
            }*/
            return $this->index($req,$res,$param);
        }
    }
}
