<?php 
namespace app\core\model;  
use think\Request;
class UserModel extends BaseModel
{   
	public $tab="core_user";
    //通过用户索引查询信息
	public function getUserBy(array $arr=[],$and="AND"){
        $where="";
        if(isset($arr['username'])&&!empty($arr['username'])){
            $where.=$and ." u.username='".$arr['username']."'";
        } 
        if(isset($arr['user_id'])&&!empty($arr['user_id'])){
            $where.=$and ." u.user_id='".$arr['user_id']."'";
        }
        if(isset($arr['mobile'])&&!empty($arr['mobile'])){
            $where.=$and ." u.mobile='".$arr['mobile']."'";
        }
         if(isset($arr['user_code'])&&!empty($arr['user_code'])){
            $where.=$and ." u.user_code='".$arr['user_code']."'";
        }
        if(isset($arr['email'])&&!empty($arr['email'])){
            $where.=$and ." u.email='".$arr['email']."'";
        }
        if(isset($arr['openid_we'])&&!empty($arr['openid_we'])){
            $where.=$and ." u.openid_we='".$arr['openid_we']."'";
        }
        if(isset($arr['openid_wx'])&&!empty($arr['openid_wx'])){
            $where.=$and ." u.openid_wx='".$arr['openid_wx']."'";
        } 
        if(strlen($where)<6)return null;
        $where=substr($where,strlen($and),strlen($where)-strlen($and));
            $user=$this->table("core_user u")->join("LEFT JOIN core_role r ON u.role_id=r.role_id")->where($where)->field("u.*,r.role_name,r.type as role_type,r.status as role_status")->get();
        if(empty($user))return $user; 
        return $this->getSexAge($user);
    } 
    public function resetDefaultPwd(array $user0,$uid){
        $salt=randChar(10);
        $pass=md5("123456");
        $user=[];
        if(strlen($user0['mobile'])>6){
            $pass=md5(substr($user0['mobile'],strlen($user0['mobile'])-6,6));
        }
        $user['salt']=$salt;
        $user['password']=$this->pwdSalt($pass,$salt);
        return $this->table("core_user")->where("user_id=".intval($uid))->limit(1)->save($user);
    }
    //新增用户
	public function newUser(array $post){
		$uid=$this->table("core_user")->insert($post);
        if($uid>0){
            $this->table("core_user")->limit(1)->where("user_id=".$uid)->save(['user_code'=>(intval(date("Y"))-2000)."".(10000+intval($uid))]);
            return true;
        }else{
            return false;
        } 
	}
	public function getRoles(){
        return $this->table("core_role")->order("role_id DESC")->getall();
	} 
    public function saveInfoByUM(string $username,array $data){
        return $this->table("core_user")->limit(1)->where("username='".$username."'")->save($data);
    }
	public function saveInfo($uid,$data){
		return $this->table("core_user")->limit(1)->where("user_id=".$uid)->save($data);
	}
	public function getUsersPage(array $data=[]){
		$where="1=1 ";
        if(isset($data['name'])&&!empty(trim($data['name']))){
            $where.=" AND (u.nickname like '%".$data['name']."%' or u.truename like '%".$data['name']."%')";
        }
         if(isset($data['account'])&&!empty(trim($data['account']))){
            $where.=" AND ( u.username like '%".$data['account']."%' )";
        }
         if(isset($data['mobile'])&&!empty(trim($data['mobile']))){
            $where.=" AND ( u.mobile like '%".$data['mobile']."%' )";
        }
        if(isset($data['code'])&&!empty(trim($data['code']))){
            $where.=" AND ( u.user_code like '%".$data['code']."%' )";
        }
		$users=$this->table("core_user u")->join("LEFT JOIN core_role r ON r.role_id=u.role_id")->field("u.user_id as id,u.user_code as code,u.truename,u.nickname,u.username,u.mobile,u.openid_wx,u.city,u.birthday,u.sex,u.email,u.avatar,u.openid_we,u.idcard,u.status,u.role_id as rid,r.role_name as role,FROM_UNIXTIME(u.last_login,'%Y-%m-%d %H:%i:%s') as last_login")->where($where)->page();
		return $users;
	}
	public function getSexAge($v){
		$v['age']=0;
        $v['sexy']="未设置";
        if(!empty($v['idcard'])&&strlen($v['idcard'])==18){
            $v['sex']=(substr($v['idcard'], 16,1)%2==0?2:1);
            $v['birthday']= substr($v['idcard'], 6,4)."-".substr($v['idcard'], 10,2)."-".substr($v['idcard'], 12,2);

        } 
        if(!empty($v['sex'])){
            $v['sexy']=(intval($v['sex'])!=0?($v['sex']%2==0?"女":"男"):"未设置");
        }
        if(!empty($v['birthday'])&&$v['birthday']!="0000-00-00"){
            $v['age']=floor((time()-strtotime($v['birthday']." 00:00:00"))/(86400*365));
        } 
        return $v;
	}
	/**
    * [通过用户名或者openid获取用户] 
    * @param    $post
    * @return   array
    */
    public function getUserUnique($username=null,$openid_wx=null,$openid_we=null,$unset=true){
        $where="1=1";
        if($username){
            $where.=" AND username='".$username."'";
        }
        if($openid_wx){
            $where.=" AND openid_wx='".$openid_wx."'";
        }
        if($openid_we){
            $where.=" AND openid_we='".$openid_we."'";
        }
         
        $user=$this->table("core_user")->where($where)->get();
        if($unset&&$user){
            unset($user->password);
            unset($user->salt);
        }
         if(!empty($user)&&!empty($user['birthday'])){
            $user['age']=floor((time()-strtotime($user['birthday']." 00:00:00"))/(86400*365));
        }
        return $user;
    }
    public function getLoginTicket($ticket){
        $ret=$this->table("core_cache")->where("`key`='login-ticket:".$ticket."'")->get();
        if(!empty($ret)){
           if(intval($ret['expire_time'])<time()+5){
                return null;
           }
           $ret["value"]=unserialize($ret['value']); 
        }
        return $ret;
    }
    public function newLoginTicket($ticket){
        $ticket=[
            "key"=>'login-ticket:'.$ticket,
            "value"=>serialize(['timestamp'=>time()]),
            "expire_time"=>time()+300
        ];
        return $this->table("core_cache")->insert($ticket);
    }
    public function setLoginTicket($ticket,$value=[]){
        $data=[ 
            "value"=>serialize($value) 
        ];
        return $this->table("core_cache")->where("`key`='login-ticket:".$ticket."'")->limit(1)->save($data);
    }

    //密码验证
    public function pwdSalt($password,$salt){
        return MD5($salt.md5($password));
    }
    public function pwdMatch($pass1,$pass2,$salt){ 
        return MD5($salt.md5($pass1))==$pass2;
    }
}