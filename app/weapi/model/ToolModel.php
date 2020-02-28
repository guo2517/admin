<?php 
namespace app\weapi\model;  
use think\Request;
use app\core\model\BaseModel;
class ToolModel extends BaseModel
{    
	 public function aipicIns($data){
    	return $this->table("fit_tool_aipic")->insert($data);
    }
    public function aipicHis($uid){
    	$res=$this->table("fit_tool_aipic")->order("createtime desc")->where("user_id={$uid} AND createtime>".(time()-86400*7))->limit(20)->getall();
    	foreach($res as $k =>$v){
			$res[$k]['result']=json_decode($v['result']);	
    	}
    	return $res;
    }
}