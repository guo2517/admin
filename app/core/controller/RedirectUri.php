<?php 
namespace app\core\controller;
use think\Controller;  
use think\Request;
use think\Session;
use think\Cache;
use app\core\model\SystemModel;
use app\core\model\UserModel;
use wechat\Weapi;
class RedirectUri extends controller
{   

	protected $request;
	protected $post;
	protected $act;
	//第三方接口统一回调地址
	//路由地址:  /redirect?from=wechat&act=login
	//from 哪个平台  act 要干什么
	function __construct(){
		$this->request=Request::instance(); 
        $this->post=$this->request->param();  
        $this->act=isset($this->post['act'])?$this->post['act']:'';
        $from=isset($this->post['from'])?$this->post['from']:'';

        if($from=="wechat"){
        	$this->wechat();
        }
	}
	function index(){

	}
	function wechat(){
		$act=isset($this->post['act'])?$this->post['act']:'';
		if($act=="binduser"){
			$sm=new SystemModel();
			$wechat=$sm->getSetting("wechat");
			$weapi=new Weapi($wechat['appid'],$wechat['secret'],0);
			 
			$ret=$weapi->getOpenID($this->post['code']);
			 
			if(isset($ret['openid'])){
				$um=new UserModel();
				$user=[
					"openid_wx"=>$ret['openid']
				];
				$um->saveInfo($this->post['uid'],$user); 
				$this->success("绑定成功","",null,-1);
			}else{ 
				$this->error("获取OPENID失败，".$ret['errmsg'],"",null,-1);
			}
		}else if($act=="userlogin"){
			$ticket=$this->post['ticket'];
			$sm=new SystemModel();
			$wechat=$sm->getSetting("wechat");
			$weapi=new Weapi($wechat['appid'],$wechat['secret'],0);
			 
			$res=$weapi->getOpenID($this->post['code']);
			 
			if(isset($res['openid'])){
				$um=new UserModel();
				 
				$ret=$um->getUserUnique(null,$res['openid']);
				if(!empty($ret)){ 
					$data=[
						"openid_wx"=>$res['openid']
					];
					$um->setLoginTicket($ticket,$data);
					$this->success("登录成功，请稍后",'',null,-1);
				}else{
					$this->error("未绑定用户","",null,-1);
				}
			}else{ 
				$this->error("获取OPENID失败，".$res['errmsg'],"",null,-1);
			}
		}
//		var_dump($this->post);
		
	}
}