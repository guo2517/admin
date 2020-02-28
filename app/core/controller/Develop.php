<?php
namespace app\core\controller; 
use think\View;
use app\core\controller\Base;
use app\core\model\UserModel;
use app\core\model\SystemModel; 
use app\core\model\SearchModel;
use think\Config;
use think\Request;
/*
开发人员专用，根据实际情况保留或删除
*/
class Develop extends Base
{
	public function __construct(Request $req){
		parent::__construct($req);
		$user=session("core_user");
		if(!empty($user)){
			if(intval($user['user_id'])!=1){
				$this->error("只有超级管理员才能使用",'/login',null,-1);
			}
		} 
	}
    public function phpinfo(){
        phpinfo();
    }
    public function design(){ 
    	if($this->act=="save"){
    		$sm= new SearchModel();
            $sys=new SystemModel();
            $menu=$sys->getMenuInfo(['menu_id'=>intval($this->post['menu_id'])]);
            if(empty($menu)){
                $this->error("菜单不存在");exit;
            }
            $where=[
                "menu_id"=>$this->post['menu_id'], 
                "search_name"=>$this->post['search_name'],
                "search_field"=>$this->post['search_field']
            ];
            $search=$sm->getOneSearch($where);
    		$data=[
    			"menu_id"=>$this->post['menu_id'],
                "route"=>$menu['route'],
                "search_type"=>$this->post['type'],
                "search_name"=>$this->post['search_name'],
                "search_field"=>$this->post['search_field'],
                "default_val"=>$this->post['default_val']  ,
                "extend_val"=>$this->post['extend_val'] ,
                "placeholder"=>$this->post['placeholder'],
                "interface"=>$this->post['interface'] 
    		];

            if(isset($this->post['serialize'])&&intval($this->post['serialize'])==1){ 
                $d=["2"=>'启用',"3"=>"禁用"]; 
                $data['extend_val']=serialize(json_decode($data['extend_val'],true)); 
            }
            $id=0;
            if(!empty($search)){
                if(isset($this->post['addtype'])&&intval($this->post['addtype'])==2){
                    $id=$search['id'];
                }else{
                    $this->error("名称或字段重复");exit;
                }
            }
    		$ret=$sm->design($data,$id);
    		$this->success("保存成功",wurl($this->route));
    	}else{
    		$sm=new SystemModel();
	    	$menus=$sm->getMenus();
	        $menus=childTree($menus); 
	        $this->assign("menus",$menus); 
	    	$set=$sm->getSetting();
	    	return view("/admin/develop-design");
    	}
    	
    }
}
