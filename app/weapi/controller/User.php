<?php
namespace app\weapi\controller; 
use wechat\Weapi;
use think\View; 
class User extends Api
{
    public function login(){ 
        $ret=$this->getWeapi()->getOpenID($this->post['code']);
        session("core_user",$ret);
        if($ret['status']>0){
            session(['session_key'=>$ret['session_key']]);
            unset($ret['session_key']);    
        } 
        return $ret;
    } 
    public function doLogin(){
        $um=new UserModel();
        $userinfo=$um->getUserByUN($this->post['username']);
        $match=$this->pwdMatch($this->post['password'],$userinfo['password'],$userinfo['salt']);
        if($match){
            $last=[
                "last_login"=>time(),
                "last_ip"=>$this->request->ip() 
            ];
            $um->saveInfo($userinfo['user_id'],$last);
            unset($userinfo['password']);
            unset($userinfo['salt']);
            session("core_user",$userinfo);
            return ['status'=>1,"msg"=>"登录成功"];
        }else{
            return ['status'=>0,'error'=>"用户名与密码不匹配，登录失败"];
        }
    }
    public function userRegist(){
        $um=new UserModel();
        $password=$this->post['password'];
        $salt=randChar(10);
        $pass=$this->pwdSalt($password,$salt);
        $user=[
            "username"=>$this->post['username'],
            "password"=>$pass,
            "salt"=>$salt
        ];
        $um->userReg($user);
        return $this->post;
    }
    public function pwdSalt($password,$salt){
        return MD5($salt.md5($password));
    }
    public function pwdMatch($pass1,$pass2,$salt){
        return MD5($salt.md5($pass1))==$pass2;
    }
}
