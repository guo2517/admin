<?php
namespace app\core\controller; 
use think\View;
use think\Cache;
use app\core\controller\Base;
use app\core\model\UserModel;
use app\core\model\SystemModel;
use wechat\Weapi;
use tool\email\Email;
class User extends Base
{
    public function login(){ 
        session("core_user",null);
        $sm=new SystemModel(); 
        $set=$sm->getSetting(); 
        $login=$set['login'];
        
        $site=$set['site'];
       
        
        if($this->isMobile()){
             $this->assign("site",$site); 
            return view("/user/login-mobile");
        }else{
            $um=new UserModel();
            $ticket=randChar(30); 
            if(isset($login['qrcode'])&&intval($login['qrcode'])==1){
            //快速登录
                $this->assign("showfast",1);
                
                if($login['type']=="weapp"){
                    $weapp=$set['weapp'];
                    $weapi=new Weapi($weapp['appid'],$weapp['secret']);
                    $time=time();
                    
                    $conf=[ 
                        "page"=>"fitness/user/weblogin",
                        "scene"=>$ticket,
                    ];
                    $ret=$weapi->getUnlimited($conf); 
                     $this->assign("qrcode",$ret);
                }else if($login['type']=="wechat"){

                } 
                $um->newLoginTicket($ticket);
                $socket=$sm->getSetting("socket");  
                $this->assign("socket",$socket);
                 
            }  
            $this->assign("site",$site);
            $this->assign("ticket",$ticket);
            $this->assign("set",$set);
            $this->assign("login",$login);  
            return view("/user/login");
        }
    } 
    public function doLogin(){
        $um=new UserModel();
        if( $this->act=="qrcode" ){//小程序授权登录
            if(!isset($this->post['ticket'])||empty($this->post['ticket'])){
                return ["status"=>0,'error'=>"登录凭证错误"];
            }
            $ticket=$um->getLoginTicket($this->post['ticket']);
            if(!empty($ticket)&&isset($ticket['value']['openid_wx'])){
                 $userinfo=$um->getUserBy(['openid_wx'=>$this->$ticket['value']['openid_wx']]); 
               
                if(!empty($userinfo)){
                    if(isset($userinfo['role_status'])&&intval($userinfo['role_status'])!=1){
                        return ['status'=>0,"error"=>"角色：".$userinfo['role_name']."已被禁用"];
                    }
                     unset($userinfo['password']);
                    unset($userinfo['salt']);
                    session("core_user",$userinfo);
                    return ["status"=>1];
                }else{
                    return ["status"=>0];
                }
            }else if(isset($ticket['openid_we'])){

            }else{
                return ['status'=>0];
            }
        }else{
            $um=new UserModel(); 
            $userinfo=$um->getUserBy(['username'=>$this->post['username']]); 
            $match=$um->pwdMatch($this->post['password'],$userinfo['password'],$userinfo['salt']);
            if($match){ 
                if(!empty($userinfo['expiretime'])&&intval($userinfo['expiretime'])<time()){
                    return ['status'=>0,"error"=>"您的账号已过期！"];
                }
                if(isset($userinfo['status'])&&intval($userinfo['status'])!=1){
                    return ['status'=>0,"error"=>"您的账号已被停用！"];
                } 
                 if(isset($userinfo['role_status'])&&intval($userinfo['role_status'])!=1){
                        return ['status'=>0,"error"=>"角色：".$userinfo['role_name']."已被禁用"];
                    }
                $last=[
                    "last_login"=>time(),
                    "last_ip"=>$this->request->ip() 
                ];
                $um->saveInfo($userinfo['user_id'],$last);
                $um->log("登录时间：".date("Y-m-d H:i:s")."，IP:".$this->request->ip(),"用户登录",$userinfo['user_id']);
                unset($userinfo['password']);
                unset($userinfo['salt']);
                session("core_user",$userinfo);

                return ['status'=>1,"msg"=>"登录成功"];
            }else{
                return ['status'=>0,'error'=>"用户名与密码不匹配，登录失败"];
            } 
        }
       
    }
    public function userInfo(){
        $user=session("core_user"); 
        $um=new UserModel(); 
        if($this->act=="resetpwd"){ 
            $password=$this->post['password'];
            $userinfo=$um->getUserBy(['user_id'=>$user['user_id']]);
             
            $pass=$um->pwdSalt($password,$userinfo['salt']);

            if($pass!=$userinfo['password']){
                return ['status'=>0,"error"=>"原密码不正确"];
            }
            $salt=randChar(10);
            $pass=$um->pwdSalt($this->post['newpass'],$salt);
            $userdata=[ 
                "password"=>$pass,
                "salt"=>$salt
            ];
            $um->saveInfo($user['user_id'],$userdata); 
            return ['status'=>1,'msg'=>"保存成功，下次登录时生效"];
        }else if($this->act=="unbind"){
            $data=['openid_we'=>'',"openid_wx"=>'',"avatar"=>'',"nickname"=>''];
            $ret=$um->saveInfo(intval($user['user_id']),$data); 
            if($ret){
                 return ['status'=>1,'msg'=>"操作成功"];
            }else{
                 return ['status'=>0,'error'=>"操作失败"];
            }
        }else if($this->act=="save"){
            
            $userdata=[
                "idcard"=>$this->post['idcard'],
                "birthday"=>$this->post['birthday'],
                'sex'=>$this->post['sex'],
                "mobile"=>$this->post['mobile'],
                'email'=>$this->post['email'],
                "truename"=>$this->post['truename'],
            ];
            if(strlen($this->post['idcard'])>0){
                if(strlen($this->post['idcard'])!=18){
                    $this->error("身份证长度不正确",wurl($this->route),3);exit;
                }else{
                     $userdata['sex']=(substr($userdata['idcard'], 16,1)%2==0?2:1);
                     $userdata['birthday']= substr($userdata['idcard'], 6,4)."-".substr($userdata['idcard'], 10,2)."-".substr($userdata['idcard'], 12,2);
                }
            }
             if(intval($user['user_id'])<1){
                $pass="123456";
                if(strlen($userdata['mobile'])>6){
                    $pass=substr($userdata['mobile'],strlen($userdata['mobile']),6);
                }
                $userdata['salt']=randChar(10);
                $userdata['password']=$um->pwdSalt($pass,$userdata['salt']);
             
            }
             $um->saveInfo($user['user_id'],$userdata); 
            $this->success("保存成功，部分信息需要重新登录！",wurl($this->route));
        }else{
            $sm=new SystemModel();
            $set=$sm->getSetting();
            $userinfo=$um->getUserBy(['user_id'=>$user['user_id']]);
            $this->assign("user",$userinfo);
            $this->assign("set",$set); 
            return view("user/userinfo");
        }
        
    } 
    
    //忘记密码相关操作
    public function forgetPassword(){ 
        if($this->act=="sendverify"){
            $ret=$this->checkPost([
                ['username','length:4,20',"用户名4~20位"],
                ['email','email',"邮箱格式不正确"] 
            ]);
            if($ret!==true){
                return ['status'=>0,"error"=>$ret];
            } 
            $um=new UserModel();
            $user=$um->table("core_user")->exist("username='".$this->post['username']."' and email='".$this->post['email']."'");
            if(empty($user)){
                return ['status'=>0,"error"=>"用户名和邮箱不匹配"];
            }
            $verify=mt_rand(100000,999999);
            $body="你的验证码是：".$verify."，有效时间10分钟";
            $sm=new SystemModel();
            $conf=$sm->getSetting("email");
            $sm->setCoreCache("forget:".$this->post['username'],$verify,time()+600);
            $email=new Email($conf);
            $ret=$email->send($this->post['email'],$body,"重置密码");
            return $ret;
        }if($this->act=="reset"){  
            $sm=new SystemModel();
            $ret=$this->checkPost([
                ['username','length:4,20',"用户名4~20位"],
                ['email','email',"邮箱格式不正确"],
                ['verify','number|length:6',"验证码是数字|验证码不正确"],
                ['newpass','require',"密码6~20位"],
            ]);
            if($ret!==true){
                return ['status'=>0,"error"=>$ret];
            } 
            $verify=$sm->getCoreCache("forget:".$this->post['username']);
            
            if(intval($verify['expire_time'])<time()||$verify['value']!=$this->post['verify']){
                return ['status'=>0,"error"=>"验证码不正确"]; 
            } 
            $um=new UserModel();
            $salt=randChar(10);
            $pass=$um->pwdSalt($this->post['newpass'],$salt);
            $userdata=[ 
                "password"=>$pass,
                "salt"=>$salt
            ];
            
            $um->saveInfoByUM($this->post['username'],$userdata); 
            $sm->delCoreCache("forget:".$this->post['username']);
            $userdata['newpass']=$this->post['newpass'];
            return ['status'=>1,'msg'=>"保存成功，下次登录时生效","userdata"=>$userdata];
        }else{
            return view("/user/forget");
        }
    }
}
