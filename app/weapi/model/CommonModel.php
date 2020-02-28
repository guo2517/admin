<?php 
namespace app\weapi\model;  
use think\Request;
use app\core\model\BaseModel;
class SystemModel extends BaseModel
{    
	 public function getWeapi(){
    	if($this->weapi!=null)return $this->weapi;
    	$system=new SystemModel();
        $weapp=$system->getSetting("weapp");
        $this->weapi=new Weapi($weapp['appid'],$weapp['secret']);
        return $this->weapi;
    }
}