<?php 
/**
* [用户模型]
* @author   MrGuo <[gj2517@qq.com]>
* @date     2019-07-02
*/
namespace app\weapi\model;
use app\core\model\BaseModel;
class UserModel extends BaseModel
{    
	/**
	* [新增用户] 
	* @param 	$post
	* @return 	array
	*/
    public function regist($user){  
        return $this->table("core_user")->insert($user);  
    } 
    /**
    * [通过用户名或者openid_we获取用户] 
    * @param    $post
    * @return   array
    */
    public function getUserUnique($username=null,$openid_we=null,$user_id=null,$unse=true){
        $where="1=1";
        if($username){
            $where.=" AND username='".$username."'";
        }
        if($openid_we){
            $where.=" AND openid_we='".$openid_we."'";
        }
         if($user_id){
            $where.=" AND user_id='".$user_id."'";
        }
        $user=$this->table("core_user")->where($where)->get();
        if($unse&&$user){
            unset($user['password']);
            unset($user['salt']);
        }
         if(!empty($user)){
            $user->age=floor((time()-strtotime($user['birthday']." 00:00:00"))/(86400*365));
        }
        return $user;
    }

    /**
    * [登陆] 
    * @param    $post
    * @return   array
    */
    public function doLogin($post){
        $user=$this->getUserUnique($post['username'],null,false);
        if(!isset($user['password'])){//未设置密码
            $user['password']=null;
            $user['salt']=null;
        }
        if(Tool::pwdMatch($post['password'],$user['password'],$user['salt'])){
            unset($user['password']);
            unset($user['salt']);
            $this->table("core_user")->where("user_id=".$user->user_id)->limit(1)->save(['last_login'=>time()]);
            session(["core_user"=>$user]); 
            return true;
        }
        return false;
    }
     /**
    * [通过openid_we获取用户信息] 
    * @param    string $openid_we
    * @return   array
    */ 
    public function getUserInfoByOpenID($openid_we){
        return $this->table("core_user u")->where('openid_we="'.$openid_we.'"')->field("user_id as id,openid_we,nickname,avatar,sex,province,city,birthday,mobile,username,0 as age,last_ip,FROM_UNIXTIME(u.last_login,'%Y-%m-%d %H:%i:%s') as last_login,font_size as fontSize,weight,height")->get();
    }

     /**
    * [更新用户基本信息] 
    * @param    $post
    * @return   array
    */
    public function updateUserInfo($user0){
        $user0['last_login']=time();
        $user=$this->getUserInfoByOpenID($user0['openid_we']);
         
        if(!empty($user)){
            if(isset($user['sex'])){unset($user0['sex']);}
            $ret=$this->table("core_user")->where("openid_we='".$user0['openid_we']."'")->save($user0);
        }else{
            $id=$this->table("core_user")->insert($user0);
            $user=$this->getUserInfoByOpenID($user0['openid_we']);
        }
        if(!empty($user)){
            $user['age']=floor((time()-strtotime($user['birthday']." 00:00:00"))/(86400*365));
        }
        return $user;
    }
    public function saveInfoByOpenID($user){
        $sts=$this->table("core_user")->where("openid_we='".$user['openid_we']."'")->save($user); 
        return ['status'=>$sts];
    }
     /**
    }
    * [用户分页] 
    * @param    $post
    * @return   array
    */
    public function getUserPage($post=[]){ 
        $ret=$this->table("core_user")->field("user_id,openid_we,nickname,username,city,avatar,sex,birthday,province,last_login")->order("last_login desc")->page();
        return $ret;
    }
}
