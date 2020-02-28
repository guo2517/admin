<?php 
namespace app\weapi\controller; 
use think\View;
use app\core\controller\Base;
use app\weapi\model\UserModel;
use app\weapi\model\FoodModel;
use app\core\model\SystemModel;
use app\weapi\model\ArticleModel;
use app\weapi\model\TrendModel;
use think\Session;
class Common extends Base
{   
    
    public function verify(){
    	$online="1.0.3";
        $version=isset($this->post['version'])?$this->post['version']:"-1";
        $ret=['status'=>2];
        if($online==$version){
            $ret['status']=1;
        } 
        return $ret;
    }
    public function home(){
        $datas=$this->getCache("fit_home_data");
        if(empty($datas)){
            $fm=new FoodModel(); 
            $datas['elements']=$fm->getAllElements(); 
            $this->cache("fit_home_data",$datas);
        }
        return ['data'=>$datas,'status'=>1];
    }
    public function syncUserInfo(){
        $user=[
            "nickname"=>$this->post['nickname'],
            "openid_we"=>$this->post['openid_we'], 
            "city"=>$this->post['city'],
            "sex"=>$this->post['sex'],
            "birthday"=>$this->post['birthday'],
            'province'=>$this->post['province'],
            'avatar'=>$this->post['avatar'],
            'last_login'=>time(),
            'last_ip'=>$this->request->ip() 
        ]; 
        if(isset($this->post['weight'])&&intval($this->post['weight'])>1){
            $user['weight']=intval($this->post['weight']);
        }
        if(isset($this->post['height'])&&intval($this->post['height'])>1){
            $user['height']=intval($this->post['height']);
        }
        $um=new UserModel();
        $res=$um->saveInfoByOpenID($user);
        return $res;
    }
    public function userInfo(){
        if(!isset($this->post['o']))return ['status'=>"0","error"=>"缺少参数"];
        $um=new UserModel(); 

        if($this->act=="get"){
            $res=$um->getUserInfoByOpenID($this->post["o"]);
        }else{
            $user=[
                "nickname"=>$this->post['nickName'],
                "openid_we"=>$this->post['o'], 
                "city"=>$this->post['city'],
                'sex'=>$this->post['gender'],
                'province'=>$this->post['province'],
                'avatar'=>$this->post['avatarUrl'], 
            ]; 
            $res=$um->updateUserInfo($user);
        }  
        session("core_user",$res );
        $ret=['data'=>$res];
        $ret['status']=empty($res)?0:1;
        return $ret;
    }
    public function getTrend(){
        $am=new TrendModel($this->post);
        if(isset($this->post['type'])&&$this->post['type']=="latest")unset($this->post['type']);
        $as=$am->getTrendPage($this->post);
        $now=time();
        foreach($as['data'] as $k=>$v){
            
            $as['data'][$k]['is_top']=intval($v['top_time'])>$now?1:0;
            $as['data'][$k]['is_hot']=intval($v['hot_time'])>$now?1:0;
            $as['data'][$k]['is_good']=intval($v['good_time'])>$now?1:0;
            $btime=$now-intval($v['createtime']);
            if($btime<3600){
                $as['data'][$k]['before']=ceil($btime/60)."分钟前";
            }else if($btime<86400){
                $as['data'][$k]['before']=floor($btime/3600)."小时前";
            }else{
                $as['data'][$k]['before']=date("m-d",$v['createtime']);
            }
            if(strlen(trim($v['picture']))>0){
                $as['data'][$k]['picture']=explode(";",$v['picture']);
            }else{
                $as['data'][$k]['picture']=[];
            }
            if(strlen(trim($v['tags']))>0){
                $as['data'][$k]['tags']=explode(",",$v['tags']);
            }else{
                $as['data'][$k]['tags']=[];
            }
            
        }
        return $as;
    }
    public function banner(){
        if(!isset($this->post['position'])){
            return ['status'=>0,"error"=>"缺少参数"];
        }
        $pics=[];
        if($this->post['position']=="hometool"){
             $pics=[
                "https://fit365.top/static/images/04cd98714c759ab30e2f44a87039a78c.jpg",
                 "https://fit365.top/static/images/fdeceee42b5fa04c6ef214fd58dab8fb.jpg",
                 "https://fit365.top/static/images/c809016d5e6a23db6906e441f3236605.jpg",
               
                "https://fit365.top/static/images/demo.jpg",
                
                "https://fit365.top/static/images/c4c9476bd28231507f93ca0071d720a5.jpg"
            ];
        }
       
        return ['data'=>$pics,"status"=>1];
    }
}
