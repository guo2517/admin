<?php
namespace app\core\controller; 
use think\View;
use app\core\controller\Base;
use app\core\model\UserModel;
use app\core\model\SystemModel; 
use cloud\Qiniu;
class Setting extends Base
{ 
    public function save(){
        $sm=new SystemModel();
        unset($this->post['_token']);
        unset($this->post['act']);
        $set0=$sm->getSetting(); 
        $post=$this->post;
        foreach ($set0 as $key => $val) {  
            if(isset($post[$key])){
                if(!empty($val)){
                    foreach($val as $k=>$v){
                        if(!isset($post[$key][$k])){
                            $post[$key][$k]=$v;
                        }
                    }   
                }
                
            }  
            
        } 
            
        foreach ($this->post as $k => $v) {
            $sm->saveSetting($k,$v);
        }    
        $qiniu=$sm->getSetting("qiniu");
        $qin=new Qiniu($qiniu);
        $qin->getToken();
        $this->success("保存成功",wurl($this->route));
    }
    public function site()
    {    
        $sm=new SystemModel();
        if($this->act=="save"){
			$this->save();
        }else{
        	
        	$set=$sm->getSetting();  
        	$this->assign("set",$set);
        	return view("/setting/site");
        }
        
    }
    public function weapp(){
         $sm=new SystemModel();
        if($this->act=="save"){
            $this->save();
        }else{ 
            $set=$sm->getSetting(); 
            $this->assign("set",$set);
            return view("/setting/weapp");
        }
        return;
        $sm=new SystemModel();
        if($this->act=="save"){
        	unset($this->post['_token']);
        	unset($this->post['act']);
        	$set0=$sm->getSetting("weapp");
        	foreach ($set0 as $key => $value) { 
        			$set0[$key]=isset($this->post[$key])?$this->post[$key]:$value;
        	}
        	$set=$sm->saveSetting("weapp",$set0);
        	$this->success("保存成功",wurl($this->route));
        }else{
        	
        	$set=$sm->getSetting("weapp");
        	$this->assign("weapp",$set); 
        	return view("/setting/weapp",$set);
        }
    }
    public function notReadMsg(){
        $sys=new SystemModel();
        $msg=$sys->getNotReadMsg($this->user_id);
        return $msg;
    }
}
