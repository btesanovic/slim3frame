<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

;namespace Form;

/**
 * Description of newPHPClass
 *
 * @author bojan
 */
class G_Ui_Form {
    
    /*
     * sm-2 | sm-10
     */
    const T_2_10 = 'T_2_10';
    
    protected $groups=[];
    protected $html=[];
    protected $flow=[];
    
    protected $tmpfld=[];


    //put your code here
    public function __construct($name,$id='null') {
        ;
    }
    
    public function label($name,$for=''){
        $this->tmpfld[] = array('t'=>'l' , 'n'=>$name , 'f'=>$for);
        
    }
    public function input($name,$placeholder,$before='',$after='') {
        $this->tmpfld[] = array('t'=>'input' , 'n'=>$name , 'p'=>$placeholder,'b'=>$before,'a'=>$after);
        
    }
    
    public function end(){
        $this->groups[] = $this->tmpfld;
        $this->tmpfld = array();
    }

    //public static select()
    

    public function test(){
        $this   ->input('name','placeholder','before','after')
                ->input('lname','lastname')
                ->end()
                ->select('grad','Grad','Prepend Option ie Choose')
                ->option('value','display=OPTIONAL')
                ->end()
                ->checkbox('')
                ->checkbox('')
                ->end()
                ->submit('submit','value');
        $this->type(G_Ui_Form::T_2_10);
        
                
        
    }
}
