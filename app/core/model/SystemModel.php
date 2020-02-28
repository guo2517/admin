<?php 
namespace app\core\model;  
use think\Request;
use think\Cache;
class SystemModel extends BaseModel
{   
	public $tab="core_menu";
	public function getUserMenus(){
		$data=$this->table("core_role_menu rm")->field("m.menu_id as id,m.p_id as pid,m.menu_name,m.perm,m.icon,m.target,m.url,m.route")->join("LEFT JOIN core_menu m ON rm.menu_id=m.menu_id")->where("role_id='".$this->user['role_id']."'  and rm.status=1 and m.status=1 and m.type=1")->order("m.sort desc,m.menu_id desc")->group("m.menu_id")->getall();
        return $data;
	}
	public function updateRoleMenu($post){
		$mine= $this->table("core_role_menu rm")->where("role_id='".$post['rid']."'") ->getall(); 
		$menus=array_unique(array_column($mine, "menu_id"));  
		$when='';
		foreach($mine as $k=>$v){
			$v['status']=0;
			foreach ($post['menu'] as $id => $sts) {
				if($v['menu_id']==$id){
					$v['status']=($sts=="on")?1:intval($sts);
					unset($post['menu'][$id]);
				}
			}  
			$when.=" WHEN menu_id=".intval($v['menu_id'])." THEN ".intval($v['status']);
		}
		
		if(strlen($when)>5){
			$up="UPDATE core_role_menu set  status=( CASE  {$when} ELSE 0 END )WHERE role_id={$post['rid']}"; 
			 
			$this->execute($up);
		} 
		if(count($post['menu'])){
			$vals="";

			foreach($post['menu'] as $id=> $sts){
                 $sts=($sts=="on")?1:intval($sts);
				if(intval($sts)){
					$vals.="({$post['rid']},{$id},{$sts}),";
				}
			};
			if(strlen($vals)>5){
				$vals=substr($vals, 0,strlen($vals)-1);
				$in="INSERT INTO core_role_menu (role_id,menu_id,status) values {$vals} "; 
				$this->execute($in); 
			}
			
		} 
	}
	public function delMenu($menu_id){
		$menu=$this->getMenuInfo(['menu_id'=>intval($menu_id)]);
		if(!empty($menu)&&$menu['module_id']==0){
			return ['status'=>0,"error"=>"系统菜单不能删除"];
		}
		$this->table("core_menu")->where("menu_id=".$menu_id)->delete();
        $this->table("core_role_menu")->where("menu_id=".$menu_id)->delete();
		return ['status'=>1,"msg"=>"操作成功"];
	}

	public function getUserMenusTree($role_id){
		$mine= $this->table("core_role_menu rm")->field("m.menu_id as id,m.p_id as pid,m.menu_name,m.perm,rm.status,case when rm.status=1 then 'checked' else '' end as `checked`")->join("LEFT JOIN core_menu m ON rm.menu_id=m.menu_id")->where("role_id='".$role_id."'  and rm.status=1 and m.status=1")->order("m.sort asc")->group("m.menu_id") ->getall();
		 
		$menu=$this->table("core_menu")->field("menu_id as id,menu_name,perm,p_id as pid,p_route,0 as status,'' as checked")->getall();

		foreach($menu as $key=>$val){ 
			foreach($mine as $k=>$v){
				if($v['id']==$val['id']){
					$menu[$key]['status']=intval($v['status']); 
					$menu[$key]['checked']=($v['checked']); 
				}
				
			}
		} 
		return $menu;
	}
    //获取角色权限，用于缓存和权限验证的
    public function rolePermissions(){
        $where="m.status=1"; 
        $menus=$this->table("core_menu m")->field("menu_id as id,p_id as pid,p_route,menu_name,perm,route,sort,icon,target,url,status")->where($where)->order("m.p_id asc")->getall(); 
        $role=$this->table("core_role rm")->getall();
         $where.=" AND rm.status=1";
        $rolemenu=$this->table("core_role_menu rm")->join("LEFT JOIN core_menu m ON m.menu_id=rm.menu_id")->field("rm.id,rm.menu_id,rm.role_id,m.perm,m.menu_name,rm.status,m.route,m.p_id,m.p_route")->order("m.p_id asc")->where($where)->getall();

        $ret=[];
        $temp_route=[];
        foreach($role as $k=>$v){ 
            foreach($rolemenu as $k2=>$v2){
                if($v2['role_id']==$v['role_id']){
                    $temp_route[$v2['menu_id']]=$v2['route'];
                    if(!empty($v2['route'])){
                        $ret[$v['role_id']][$v2['route']]["default"]=$v2;
                        $ret[$v['role_id']][$v2['route']][$v2['perm']]=$v2;
                    }
                    
                    if(intval($v2['p_id'])>0){
                        if(isset($temp_route[$v2['p_id']]))$v2['p_route']=$temp_route[$v2['p_id']];
                        $ret[$v['role_id']][$v2['p_route']][$v2['perm']]=$v2;
                    } 
                    
                }
            }
        }
        return $ret;
    }
    //设置缓存角色权限验证
    public function setPermission(){ 
        $role=$this->rolePermissions();  
        foreach ($role as $k => $v) { 
            Cache::set("role_permit_".$k,$v); 
        }  
    }
    public function getPermission(int $role_id,string $route=''){ 
        
        $perm=Cache::get("role_permit_".$role_id ); 
        if(empty($perm)){
            $this->setPermission();
            $perm=Cache::get("role_permit_".$role_id ); 
        }  
        if(isset($perm[$route])){
            return $perm[$route];
        }
        return null;
    }
	public function saveMenu($id,$data){
		if(intval($id)>0){ 
			if(isset($data['p_id'])&&intval($data['p_id'])>0){
				$pa=$this->table("core_menu")->where("menu_id=".intval($data['p_id']))->get();
				$data['p_route']=$pa['perm'];
			}

			return $this->table("core_menu")->where("menu_id=".$id)->save($data);
		}else{
			return $this->table("core_menu")->insert($data);
		}
	}
	public function getMenuInfo($where){
		return $this->table("core_menu")->field("*")->where($where)->get();
	}
	public function getMenus($where=""){
		return $this->table("core_menu")->field("menu_id as id,p_id as pid,p_route,menu_name,perm,module_id,action,sort,icon,target,url,status,type,route")->where($where)->order("sort desc")->getall();
	}
    //保存角色信息
    public function saveRoleInfo($rid,array $data){
        $ret=false;
        $role=$this->table("core_role")->where("role_name='".$data['role_name']."'")->get();
        if(intval($rid)){
            if(!empty($role)&&intval($role['role_id'])!=intval($rid)){
                return ['status'=>0,"error"=>"角色名词已存在"];
            }
            $ret=$this->table("core_role")->limit(1)->where("role_id=".$rid)->save($data);
            return ['status'=>1,"msg"=>"保存成功"];
        }else{
            if(!empty($role)){
                return ['status'=>0,"error"=>"角色名词已存在"];
            }
            $id=$this->table("core_role")->insert($data);
            if($id){
                return ['status'=>1,"msg"=>"保存成功"];
            }else{
                return ['status'=>0,"msg"=>"保存失败"];
            }
        }

    }
    //删除角色
    public function delrole(int $rid){
        return $this->table("core_role")->limit(1)->where("role_id=".$rid)->delete();
    }
    //获取所有角色
    public function getRoles(){
        return $this->table("core_role")->order("role_id DESC")->getall();
    }
    //获取角色分页
	public function getRolesPage($post=[]){
        $where="";
        if(isset($post['keyword'])&&strlen(trim($post['keyword']))>0){
            $where.=" role_name like '%".$post['keyword']."%'";
        }
		return $this->table("core_role")->field("role_id as id,role_name as name,status,type")->where($where)->page();
	}
    //获取角色的权限
	public function getRolesUser($uid){ 
		$data=$this->table("core_role")->field("role_id as id,role_name as name,0 as selected")->getall();
		$user=$this->table("core_user")->where("user_id={$uid}")->field("user_id,role_id")->get();
		return ['data'=>$data,'rid'=>intval($user['role_id']),"status"=>1];
	}

	public function userReg($post){
		return $this->table("core_user")->insert($post);
	}
    
	public function getNotReadMsg($user_id){
		$ret=$this->table("core_message m")->join("LEFT JOIN core_user u ON u.user_id=m.from_id")->where("m.status=0 and to_id=".$user_id)->field("m.id,m.title,m.from_id,m.to_id,m.status,ifnull(u.truename,u.nickname) as send_name,username,u.avatar,m.send_time,m.type,m.title")->limit(10)->order("m.send_time desc")->getall();
		foreach ($ret as $key => $val) {
			$ret[$key]['send_date']=date("Y-m-d H:i:s",$val['send_time']);
			if($val['type']==0){
				$ret[$key]['send_name']="系统通知";continue;
			}
			if(empty($val['name'])||strlen(trim($val['name']))){
				$ret[$key]['send_name']=$val['username'];
			} 
		}
		$count=$this->table("core_message")->where("status=0 AND to_id=".$user_id)->count();
		return ['status'=>1,'data'=>$ret,'total'=>$count];
	}
	 /**
    * [获取文件分页] 
    * @param    array $post    
    * @return   array
    */
    public function getFilePage($post,$user_id=0){
        $where="(user_id=".$user_id." or user_id is null)";
        if(isset($post['type'])&&intval($post['type'])>0){
            $where.=" AND type=".$post['type'];
        }else if(!isset($post['type'])){
            $where.=" AND type=1";//默认查询图片类型
        }
        if(isset($post['group_id'])&&intval($post['group_id'])>0){
            $where.=" AND group_id=".$post['group_id'];
        }
        return $this->table("core_upload")->where($where)->field("id,path,filename,type,group_id,src,server,server_type,0 as checked")->order("createtime desc")->page();
    }
     
      /**
    * [新增图片] 
    * @param    array $post    
    * @return   array
    */
    public function addUpload($data){
        return $this->table("core_upload")->insert($data);
    }
    public function editGroup($act,$post,$group_id=0){ 
    	unset($post['_token']);

        if($act=="getGroup"){
            $res=$this->table("core_upload_group")->where("user_id=".$post['user_id'])->getall();

            return ['status'=>1,"data"=>$res];
        }else if($act=="editGroup"){
            $res=$this->table("core_upload_group")->where('id='.$group_id." and user_id=".$post['user_id'])->limit(1)->save($post);
            return ['status'=>1,"data"=>$res];
        }else if($act=="addGroup"){ 
            $res=$this->table("core_upload_group")->insert($post); 
            return ['status'=>1,"data"=>$res];
        }else if($act=="delGroup"){
            $res=$this->table("core_upload_group")->where('id='.$post['group_id'])->delete();
            $this->table("core_upload")->where('group_id='.$post['group_id'])->save(['group_id'=>'']);
            return ['status'=>1];
        }else if($act=="moveGroup"){ 
            $ids="";
            $group_id=$post['group_id'];
            foreach($post['fids'] as $k=>$v){
                $ids.=intval($v['id']).","; 
            }
            if(strlen($ids)>0){
                $ids=substr($ids,0,strlen($ids)-1);
                $this->table("core_upload")->where('id in ('.$ids.')')->save(['group_id'=>$group_id]);
            }
           
            return ['status'=>1];
        }
    }
    /**
    * [通过key获取配置] 
    * @param    string $key    
    * @return   array
    */
    public function getSetting($key="all"){
    	$cache=cache($key); 
    	if(!empty($cache))return $cache; 
        $ret=$this->updateSettingCache($key);  
        return $ret;
    }
    /**
    * [保存key配置] 
    * @param    string $key    
    * @return   array
    */
    public function saveSetting($key,$val){
        $ser=$this->table("core_setting")->where("`key`='".$key."'")->get();
        $data=['key'=>$key,"value"=>serialize($val)];
        if($ser){
            $ret=$this->table("core_setting")->where("`key`='".$key."'")->limit(1)->save($data);
        }else{
            $ret=$this->table("core_setting")->insert($data);
        }
        $this->updateSettingCache($key);
        return $ret;
    }
    public function updateSettingCache($key="all"){
    	if($key=="all"){//获取所有配置
            $confs=$this->table("core_setting")->getall();
            if(empty($confs)){
                $con=[
                    ['key'=>"login","value"=>''], 
                    ['key'=>"wechat","value"=>''],
                    ['key'=>"weapp","value"=>''],
                    ['key'=>"socket","value"=>''],
                    ['key'=>"site","value"=>''],
                    ['key'=>"qiniu","value"=>''],
                ];
                $this->table("core_setting")->insertAll($con);
                $confs=$this->table("core_setting")->getall();
            }
            $ret=[];
            foreach ($confs as $key => $val) {
                $ret[$val['key']]=unserialize($val['value']);
            }
            return $ret;
        }else{//获取一个配置
           $ser=$this->table("core_setting")->where("`key`='".$key."'")->get();
            $ret=[];
            if($ser){
                $ret=unserialize($ser['value']);
            } 
        }  
    	if(isset($ser['key'])){cache($key,$ret);};
    }
    public function getUpload($id=0,$path=null){
        $where="1=1";
        if($id!=0){
            $where.=" and id=".$id;
        }
        if(!empty($path)){
            $where.=" and path='".$path."'";
        }
        return $this->table("core_upload")->where($where)->get();
    }
    //删除单个文件
    public function delUpload($id,$uid=-1){
        $ret= $this->table("core_upload")->where("id = ".intval($id))->get(); 
        $this->table("core_upload")->where("id = ".intval($id))->limit(1)->delete();
        return $ret;
    }
    public function delUploads($ids,$uid=-1){
        $ret= $this->table("core_upload")->where("id in (".$ids.") and user_id=".intval($uid))->getall("id,server,type,group_id,src,path,server_type,server,ext"); 
        $this->table("core_upload")->where("id in (".$ids.") and user_id=".intval($uid))->delete();
        return $ret;
    }

     /**
    * [获取文件分页] 
    * @param    array $post    
    * @return   array
    */
    public function getLogsPage($post,$onlyMy=false){
        $where="";
        if(isset($post['type'])&&intval($post['type'])>0){
            $where.=" AND type=".$post['type'];
        }
        if($onlyMy){
            $user=session("core_user");
            $where.=" AND user_id=".$user['user_id'];
        }
        return $this->table("core_logs")->where($where)->order("createtime desc")->field("id,type,title,user_id,createtime")->page();
    } 
    //更新缓存表
    public function setCoreCache($key,$val='',$expire_time=null){ 
        $ret=$this->getCoreCache($key);
         $data=['key'=>$key,"value"=>$val];
         if(!empty($expire_time))$data['expire_time']=intval($expire_time); 
        if(empty($ret)){  
            return $this->table("core_cache")->insert($data);
        }else{  
            return $this->table("core_cache")->where("`key`='".$key."'")->save($data);
        }

    }
    //获取缓存表数据
    public function getCoreCache($key){
        return $this->table("core_cache")->where("`key`='".$key."'")->get();
    }
    //删除缓存表数据
     public function delCoreCache($key){
        return $this->table("core_cache")->limit(1)->where("`key`='".$key."'")->delete();
    }
    //获取日志分页
    public function getLogPage($post=[]){ 
        $where="";
        if(isset($post['keyword'])&&strlen(trim($post['keyword']))>0){
            $where.=" `action` like '%".$post['keyword']."%' or `log` like '%".$post['keyword']."%'";
        }
        return $this->table("core_log l")->join("LEFT JOIN core_user u ON l.user_id=u.user_id")->field("l.id,l.action,FROM_UNIXTIME(l.createtime,'%Y-%m-%d %H:%i:%S') as `time`,u.username,u.user_code as code,l.log")->order("l.createtime desc")->where($where)->page();
    }
}