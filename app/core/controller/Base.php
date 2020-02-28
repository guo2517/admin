<?php 
namespace app\core\controller;
use think\Controller; 
use think\View;
use think\Validate;
use think\Request;
use think\Session;
use think\Cache;
use app\core\model\SystemModel;
class Base extends \think\Controller
{   
    private $assign_datas=[];

    private $ignores_login_route=["login","dologin","install","forgetpwd"];//不需要登录验证的
    private $ignores_permit_route=["login","dologin","index","/","api/"];
    public $request; 
    public $post;
    public $act="";
      public $user_id=0;
    public $user=null;//当前登录用户
    public function __construct(Request $req){
        /*
        1.菜单权限根据数据可进行缓存
        2.token验证，post中带有act时
        */
        parent::__construct();
        $this->request=Request::instance(); 
        $this->post=$req->param();  
        foreach ($this->post as $k => $v) {
            if(gettype($v)=="string"){
                $this->post[$k]=trim($v);
            } 
        } 
        $this->act=isset($this->post['act'])?$this->post['act']:null; 
        $this->route=$this->request->pathinfo(); 
        $this->url=$this->request->url(true);
        if(stripos($this->url, "index.php")!==false){
            $this->siteurl=substr($this->url, 0,stripos($this->url, "index.php"));
        }else{
            $this->siteurl=$this->request->root(true);
        }
        define("SITEURL", $this->siteurl);
        $this->assign("route",$this->route);  
        $this->assign('siteurl',$this->siteurl);
        $this->assign("post",$this->post); 
        $user=session("core_user"); 
        $this->user=$user;     
        if(isset($user['user_id']))$this->user_id=$user['user_id'];
        if(strlen($this->route)>5&&substr($this->route,0,4)=="api/"){
            return ;
        } 
        if(empty($user)&&!in_array($this->route, $this->ignores_login_route)){
            header("Location:/login");exit;
        }
    }
    //获取指定路由下的所有权限
    public function getPerm(string $route=null){

        $user=session("core_user");
        if(empty($user)||!isset($user['role_id'])){
            return null;
        }
        $sm=new SystemModel();  
        if(empty($route)) $route=$this->route; 
        $ret=$sm->getPermission(intval($user['role_id']),$route); 
        
        $this->assign("perm",$ret);
        return $ret;
    }
    //验证用户是否有权限
    public function checkPerm(string $action="default",string $route=null){ 
        $perm=$this->getPerm($route);
        $user=session("core_user"); 
        if((!empty($perm)&&isset($perm[$action]))&&intval($perm[$action]['status'])==1){ 
            return true;
        }else if(empty($perm)){
            $this->setPermission();
            $perm=$this->getPerm($route);  
            $this->assign("perm",$perm);
            if((!empty($perm)&&isset($perm[$action]))&&intval($perm[$action]['status'])==1){ 
                return true;
            }
        }  
        return false;
    }
    /*验证POST参数 
    $ret=$this->checkPost([
        ['username','require|length:4,20',"用户名必填|用户名4~20位"],
        ['email','email|length:5,50',"邮箱必填|邮箱格式不正确"],  
    ]);
    //验证通过返回true，验证不通过返回错误信息字符串*/
    public function issetPost($arr,$exit=true){
        $ret=true;

        foreach($arr as $key=>$val){
            if(!isset($this->post[$val])){
               $ret=false;
               break;
            }
        } 

        if(!$ret&&$exit){
            if($this->request->isAjax()){
                header("Content-Type:application/json");
                echo json_encode(['status'=>0,"error"=>"缺少必要参数"],true);
            }else{
                $this->error("缺少必要参数");
            } 
            exit;
        }

        return $ret;
    }
    public function error($msg="",$url="",$data="",$wait=5,array $header=[]){
         
        parent::error($msg,$url,$data,$wait,$header);
        die();;
    }
    public function checkPost($arr=[]){
        $validate = new Validate($arr); 
        if (!$validate->check($this->post)) {
            return ($validate->getError());
        }
        return true;
    }
    public function exit2($ret,$url="/"){
        if($this->request->isAjax()){
            echo json_encode($ret,JSON_UNESCAPED_UNICODE);
        }else{
            $this->error($ret['error'],$url);
        } 
        exit;
    } 
    public function getUser(){
        return session("fit_user");
    }
    public function cache($key,$value,$type="file",$expire=0){
        $options = [ 
            'type'  =>  'File',  
            'expire'=>  0,  
            'prefix'=>  APP_NAME, 
            'path'  =>  APP_PATH.'runtime/cache/',
        ];
        Cache::connect($options);
        Cache::set($key,$value,$expire);
    }
    public function getCache($key){
        $options = [ 
            'type'  =>  'File',  
            'expire'=>  0,  
            'prefix'=>  APP_NAME, 
            'path'  =>  APP_PATH.'runtime/cache/',
        ];
        Cache::connect($options);
        Cache::get($key);
    }
     
    public function checkLogin(){
        $user=session("fit_user"); 
        if(empty($user)||!isset($user['uid'])){ 
            return false; 
        }
        return true;
    }
    public function randChar($length){
        $str="0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $tmp="";
        for($i=0;$i<$length;$i++){
          $tmp.=$str[mt_rand(0,61)];
        }  
        return $tmp;
    } 
    public function setToken($key){
        $token=sha1(time()."-".$this->randChar(10));  
       // Session::set($token,$this->request->ip()); 
        return $token;
    }
    public function checkToken($key){
       // $t1=Session::get($key);   
        if(!empty($t1)){
            //Session::delete($key);
            return true;
        }
        return true; 
    }
    
    /**
    * [渲染页面] 
    * @param    $view
    * @return   view
    */
    public function page($view){ 
    	return view($view,$this->assign_datas);
    }
    public function jsonReturn($data,$rep=true,$token=true){
        header("Content-Type:application/json; charset=utf-8"); 
        $json= EACHARR($data,$rep,"urlencode");  
        echo urldecode(json_encode($json,JSON_UNESCAPED_UNICODE)); 
        exit;
    }
    public function wlog($log ){   
        $log_dir=ROOT_PATH."runtime/wlog/";
        if (!is_dir($log_dir)) {
            mkdir($log_dir, 0755, true);
        }  
        $file=$log_dir.date("Y-m-d").".log";
        if(gettype($log)!="string"){
            $log=json_encode($log,JSON_UNESCAPED_UNICODE);
        }
        $f=fopen($file, "a+");
        $time=date("Y-m-d H:i:s",time()); 
        fwrite($f,"\r\n");
        fwrite($f, "[".$time."]  ".$log);
        fwrite($f,"\r\n");
        fclose($f); 
    }
    function CheckSubstrs($substrs,$text){   
        foreach($substrs as $substr)   
            if(false!==strpos($text,$substr)){   
                return true;   
            }   
            return false;   
    } 
    function isMobile(){   
        $header=$this->request->header();   
        $useragent=$header['user-agent'];
        $useragent_commentsblock=preg_match('|\(.*?\)|',$useragent,$matches)>0?$matches[0]:'';     
        
        $mobile_os_list=array('Google Wireless Transcoder','Windows CE','WindowsCE','Symbian','Android','armv6l','armv5','Mobile','CentOS','mowser','AvantGo','Opera Mobi','J2ME/MIDP','Smartphone','Go.Web','Palm','iPAQ'); 
        $mobile_token_list=array('Profile/MIDP','Configuration/CLDC-','160×160','176×220','240×240','240×320','320×240','UP.Browser','UP.Link','SymbianOS','PalmOS','PocketPC','SonyEricsson','Nokia','BlackBerry','Vodafone','BenQ','Novarra-Vision','Iris','NetFront','HTC_','Xda_','SAMSUNG-SGH','Wapaka','DoCoMo','iPhone','iPod');   
                     
        $found_mobile=$this->CheckSubstrs($mobile_os_list,$useragent_commentsblock) ||   
                  $this->CheckSubstrs($mobile_token_list,$useragent);   
                     
        if ($found_mobile){   
            return true;   
        }else{   
            return false;   
        }  
    } 
    //获取图片服务器路径
    public function getPicServer(){
        $sys=new SystemModel();
        $upload=$sys->getSetting("upload");
        $server=$this->request->root(true)."/uploads/";
        if(isset($upload['server_type'])&&intval($upload['server_type'])==2){
            $qiniu=$sys->getSetting("qiniu");
            if(isset($qiniu['server'])){
                $server=$qiniu['server'];
            }
        }
        return $server;
    }
}
