<?php
namespace app\core\controller; 
use think\View;
use app\core\controller\Base;
use app\core\model\UserModel;
use app\core\model\SystemModel;
use app\core\model\SearchModel;
class System extends Base
{
    public function getMenus()
    { 
        $datas['user']=session('core_user');
        return view("/admin/main",$datas);
    }
    public function menuset(){
         $sys=new SystemModel();
        if($this->act=="addmenu0"){
            $this->issetPost(['p_id','type',"sort","menu_name","route","icon","target","action","url"]);
            if(isset($this->post['route'])&&is_numeric($this->post['route'])){
                return ['status'=>0,"error"=>"路由不能是全数字"];
            }
            if(!isset($this->post['id']))$this->post["id"]=0;
            if(!empty($this->post['route'])){
                $menu=$sys->getMenuInfo(["route"=>$this->post['route']]);
                if(isset($menu['menu_id'])&&$menu['menu_id']!=$this->post['id']){
                    return ['status'=>0,"error"=>"路由已存在"];
                }  
            } 
            $menu=[
                "p_id"=>intval($this->post['p_id']), 
                "type"=>intval($this->post['type']),
                "sort"=>intval($this->post['sort']),
                'menu_name'=>$this->post['menu_name'],
                'route'=>$this->post['route'],
                'perm'=>$this->post['perm'],
                'icon'=>$this->post['icon'],
                "target"=>intval($this->post['target']), 
                'action'=>$this->post['action'],
                'url'=>$this->post['url'],
            ];
            $ret=$sys->saveMenu(isset($this->post['id'])?$this->post['id']:0,$menu);
            if($ret){
                 $this->updateRoute();
                 return ['status'=>1,"msg"=>"操作成功"];
            }
            return ['status'=>0,"error"=>"操作失败"]; 
        }else if($this->act=="savemenu"){ 
            $this->issetPost(['p_id','type',"sort","menu_name","route","icon","target","action","url"]);
            $menu=[ 
                "p_id"=>intval($this->post['p_id']),
                "sort"=>intval($this->post['sort']),
                'menu_name'=>$this->post['menu_name'],
                'perm'=>$this->post['perm'],
                'icon'=>$this->post['icon'],
                "type"=>intval($this->post['type']),
                 'route'=>$this->post['route'], 
                'action'=>$this->post['action'], 
                "target"=>intval($this->post['target']),
                'url'=>$this->post['url'],
            ];
            $ret=$sys->saveMenu(isset($this->post['id'])?$this->post['id']:0,$menu);
            if($ret){
                 $this->updateRoute();
                 return ['status'=>1,"msg"=>"操作成功","sql"=>$sys->lastSql()];
            }
            return ['status'=>0,"error"=>"操作失败","sql"=>$sys->lastSql()]; 
        }else if($this->act=="del"){
            $user=session("core_user");
            if($user['role_id']!=1){
                return ['status'=>0,"error"=>"权限不足"];
            }else{
                $this->updateRoute();
                return $sys->delMenu($this->post['id']);
            }
        }else{
           
            $menus=$sys->getMenus();
            $menus=childTree($menus);
            $this->assign("menus",$menus); 
            return view("/admin/menuset");
        }
         
    }
    public function updateRoute(){
        $file=APP_PATH."route.php";

        $route=file_get_contents($file);
        $sys=new SystemModel();
        $menus=$sys->getMenus("module_id>0");
        $str=""; 
        foreach ($menus as $key => $val) {
            if(empty($val['action'])||strlen($val['action'])<1)continue;
            $str.="    /*{$val['id']}. {$val['menu_name']}*/\r\n";
            $str.="    '".$val['route']."'=>'".$val['action']."',\r\n";
        }  
        if(strlen($str)>1){
            $str.='];';
            $route=substr($route, 0,stripos($route, "#start#"))."#start#\r\n";
            $route.=$str;  
            
            $h=fopen($file, "w+");
            fwrite($h, $route);
            fclose($h);
        }
    }
    public function roleset(){ 
        $sys=new SystemModel();  
        $sys->setPost($this->post);
        if($this->act=="data"){
            $roles=$sys->getRolesPage($this->post);  
            return ($roles); 
        }else if($this->act=="getDetails"){ 
            $menus=$sys->getUserMenusTree($this->post['rid']); 
            $menus=childTree($menus); 
            $this->assign("menus",$menus);
            return view("/admin/roleset-menu");
        }else if($this->act=="saveDetail"){
            $sys->updateRoleMenu($this->post);
            $sys->setPermission();
            return ['status'=>1];
        }else if($this->act=="saveRole"){
            $data=[];
            if(isset($this->post['name']))
                $data['role_name']=$this->post['name'];
            if(isset($this->post['status']))
                $data['status']=intval($this->post['status']);
            
            return $sys->saveRoleInfo(intval($this->post['id']),$data); 
        }else if($this->act=="delrole"){ 
            $ret=$this->checkPost([['rid',"number","参数错误"]]);
            if($ret!==true){
                return ['status'=>0,"error"=>$ret];
            }
            $sts=$sys->delrole(intval($this->post['rid'])); 
            if($sts){
                return ['status'=>1,"error"=>"操作成功"];
            }else{
                return ['status'=>0,"error"=>"操作失败"];
            }
        }else{ 
            SearchModel::cacheSearch($this->route);
            $search_str=SearchModel::setSearch($this->route);
            $sts=['1'=>"启用","0"=>"禁用"]; 
            $this->assign("search",$search_str);
            return view("/admin/roleset");
        }
    }
    //日志浏览
    public function log(){
        $um=new SystemModel();
        $um->setPost($this->post);
        if($this->act=="data"){
            $log=$um->getLogPage();  
            return $log;
        }else{
            $search=SearchModel::setSearch($this->route); 
            $this->assign("search",$search);
             return view("/admin/log");
        }
    }
    //用户管理
    public function usermanage(){
        $um=new UserModel();
        $sys=new SystemModel();
        $um->setPost($this->post);
        $sys->setPost($this->post);
        $perm=$this->getPerm();  
        if($this->act=="data"){
            $users=$um->getUsersPage($this->post);  
            foreach ($users['data'] as $k => $v) {
               $users['data'][$k]=$um->getSexAge($v);
            }
            $this->jsonReturn($users);
        }else if($this->act=="add"||$this->act=="edit"){//新增用户页面
              
            if($this->checkPost([['uid',"required","缺少参数"]])!==true){
                $uid=intval($this->post['uid']);
                $user=$um->getUserBy(['user_id'=>$uid]);
                $roles=$um->getRoles();

                $this->assign("roles",$roles);
                $this->assign("user",$user);
                return view("/admin/users-edit");
            }else{
                $this->error("缺少参数");exit;
            }
        }else if($this->act=="bindqrcode"){
            $this->issetPost(["uid"]);
            if(isset($this->post['unbind'])&&intval($this->post['unbind'])>0){
                $data=['openid_we'=>'',"openid_wx"=>''];
                $um->saveInfo(intval($this->post['uid']),$data); 
                return ['status'=>1,'msg'=>"操作成功"];
            }
            $wechat=$sys->getSetting("wechat");
             $url0=urlencode($this->siteurl."/redirect?from=wechat&act=binduser&uid=".intval($this->post['uid']));
             $url='https://open.weixin.qq.com/connect/oauth2/authorize?appid='.(isset($wechat['appid'])?$wechat['appid']:'').'&redirect_uri='.$url0.'&response_type=code&scope=snsapi_userinfo&state=binduser#wechat_redirect';
             $this->assign("url",$url);
            return view("/admin/users-qrcode");
        }else if($this->act=="resetpwd"){//管理员重置用户密码
            $ret=$this->checkPost([['uid','number',"无效参数"]]);
            if($ret==true){
                $user=session("core_user");
                if(isset($user['role_id'])&&intval($user['role_id'])==1){
                    $user0=$um->getUserBy(['user_id'=>intval($this->post['uid'])]); 
                    if(empty($user0)){
                        return ['status'=>1,"error"=>"用户不存在"];
                    } 
                    $log="管理员".$user['username']."重置了用户ID:[".$user0['user_code']."]的密码";
                    
                    $um->resetDefaultPwd($user0,$user0['user_id']);
                    $um->log($log,"重置密码",$user['user_id']);
                    return ['status'=>1,"msg"=>"重置成功，请尝试登录"];
                }else{
                    return ['status'=>0,"error"=>"你没有重置密码的权限"];
                }
            }else{
                return ['status'=>0,"error"=>$ret];
            }

        }else if($this->act=="sts"){//管理员停用用户
            $ret=$this->checkPost([['uid','number',"无效参数"],['sts','number',"无效参数"]]);
            if($ret===true){
                $user=session("core_user");
                if(isset($user['role_id'])&&intval($user['role_id'])==1){ 
                    $data=['status'=>intval($this->post['sts'])];
                    $um->saveInfo(intval($this->post['uid']),$data); 
                    return ['status'=>1,"msg"=>"操作成功"];
                }else{
                    return ['status'=>0,"error"=>"你没有对应权限"];
                }
            }else{
                return ['status'=>0,"error"=>$ret];
            }

        }else if($this->act=="save"){//保存用户信息
            $ret=$this->issetPost(['uid','username','mobile','email','birthday',"idcard", 'truename',"role_id"]);  
            $user=[

                "username"=>$this->post['username'],
                'email'=>$this->post['email'],
                'role_id'=>intval($this->post['role_id']),
                'mobile'=>$this->post['mobile'],
                'birthday'=>$this->post['birthday'],
                'idcard'=>$this->post['idcard'],
                'sex'=>isset($this->post['sex'])?intval($this->post['sex']):1,
                'truename'=>$this->post['truename'],
            ];
            if(isset($this->post['expire'])){
                if(!empty($this->post['expire'])){

                    $user['expiretime']=strtotime($this->post['expire']);
                }else{
                    $user['expiretime']=null;
                }
                
            }
            
            if(intval($this->post['uid'])>0){//修改用户

                $user0=$um->getUserBy($user,"OR");
                
                if(!empty($user0)&&intval($user0['user_id'])!=intval($this->post['uid']) ){
                    if(strlen($user['username'])>0&&$user0["username"]==$user['username'])
                        $this->error("保存失败，用户名已存在");
                    if(strlen($user['mobile'])>0&&$user0["mobile"]!=$user['mobile'])
                        $this->error("保存失败，电话号码已存在");
                    if(strlen($user['idcard'])>0&&$user0["idcard"]!=$user['idcard'])
                        $this->error("保存失败，身份证已存在");;
                }  
               
                $sts=$um->saveInfo($this->post['uid'],$user);
                if($sts>0){ 
                    $this->success("保存成功",wurl($this->route)); 
                }else{ 
                    $this->error("保存失败"); 
                }
            }else{//新增用户
                  $pass="123456";
                if(strlen($user['mobile'])>6){
                    $pass=substr($user['mobile'],strlen($user['mobile'])-6,6);
                } 
                $exist=[ 
                    "username"=>$this->post['username'], 
                    'mobile'=>$this->post['mobile'], 
                    'idcard'=>$this->post['idcard'] 
                ];
                $user0=$um->getUserBy($exist,"OR"); 
                if(!empty($user0)){
                    if($user0["username"]==$user['username'])
                        $this->error("保存失败，用户名已存在");
                    if($user0["mobile"]==$user['mobile'])
                        $this->error("保存失败，电话号码已存在");
                    if($user0["idcard"]==$user['idcard'])
                        $this->error("保存失败，身份证已存在"); 
                    exit;
                }
                $user['salt']=randChar(10);
                $user['password']=$um->pwdSalt(md5($pass),$user['salt']); 
                $user['createtime']=time();
                $ret=$um->newUser($user); 
                if($ret){
                    $log="管理员".$this->user['username']."创建了用户".$user['username'];
                    $um->log($log,"创建用户",$this->user_id);                    
                    $this->success("创建成功",wurl($this->route)); 
                }else{
                    $this->error("创建用户失败"); 
                } 
            }
             
        }else{   
            $roles=$sys->getRoles();  
            $search_str=SearchModel::setSearch($this->route); 
            $this->assign("search",$search_str);
            $this->assign("roles",$roles); 
            return view("admin/users-list");
        }
    }
}
