<?php 
namespace app\core\controller; 
use think\View;
use app\core\controller\Base;
use app\core\model\UserModel;
use app\core\model\SystemModel; 
use clound\Qiniu; 
class Common  extends Base
{    
    public function uploadPicGroup(){
         $post=$this->post; 
        $uid=$this->user_id; 
        $res=['status'=>0,"error"=>""];
        if($this->post['act']=="moveGroup"){ 
            $this->post['user_id']=$uid;
            $m=new SystemModel(); 
            $res=$m->editGroup($this->post['act'],$this->post); 
        }else if($this->post['act']=="getGroup"){//获取分类
            $data=[
                "user_id"=>$uid 
            ];
            $m=new SystemModel();
            $res= $m->editGroup($this->post['act'],$data);
        }else if($this->post['act']=="editGroup"){//更新分类名
            $data=[
                "user_id"=>$uid, 
                'name'=>$this->post['name']
            ];
            $m=new SystemModel();
            $res= $m->editGroup($this->post['act'],$data,$this->post['group_id']);
        }else if($this->post['act']=="addGroup"){//添加
            $data=[
                "user_id"=>$uid, 
                'name'=>$this->post['name']
            ];
            $m=new SystemModel();
            $res= $m->editGroup($this->post['act'],$data);
        }else if($this->post['act']=="delGroup"){//删除
            $m=new SystemModel();
            $res= $m->editGroup($this->post['act'],$post);
        } 
        $this->jsonReturn($res);
    }
    //自定义上传文件，private=1传到APP_PATH/uploads,其他public/uploads,
    public function uploadFile(){//type:image,audio,video,doc,
        $this->issetPost(['type',"private"]);
        $post=$this->post; 
        $uid=$this->user_id; 
        $root=intval($this->post['private'])==1?APP_PATH:ROOT_PATH."public".DS;
        $root.="uploads".DS;
        if($this->post['type']=="doc"){
            $ret=['status'=>0];
            $file = request()->file('item');
            if(!$file){
                return ['status'=>0,"error"=>"未检测到文件"];
            }  
            $month=date("Ym");
            $oldname=$_FILES['item']['name']; 
            $arr=explode(".",$oldname);
            $ext=$arr[count($arr)-1];

            $store_path=$root.$month;
            mk_dir($store_path);
            $file = request()->file('item');

            // 移动到框架应用根目录/public/uploads/ 目录下
            if($file){
                $info = $file->rule('unique_id')->move($store_path);
                if($info){
                    // 成功上传后 获取上传信息
                    // 输出 jpg
                    // echo $info->getExtension();
                    // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
                    $filename=$month."/".$info->getSaveName();
                    // 输出 42a79759f284b767dfcb2a0197904287.jpg
                    // echo $info->getFilename(); 
                }else{
                    return ['status'=>0,"error"=>$file->getError()];
                }
            }
            if(isset($filename)&&strlen($filename)>5){
                 $m=new SystemModel();
                $ret['status']=1;
                $data=[
                    "filename"=>$oldname,
                    "user_id"=>$uid,
                    "createtime"=>time(),
                    "group_id"=>0,
                    "path"=>$filename, 
                    "ext"=>$ext
                ];
                $data['server_type']=1; 
                $imgs=["jpg","jpeg",'gif','png','wmp','bmp'];
                $audio=['mp3','wav'];
                $video=['mp4','mpg','rmvb','flv','avi','mkv'];
                $word=['doc','docx','xls',"xlsx",'pdf','wps','xmind','txt','html'];
                if(in_array(strtolower($ext),$imgs)){
                    $data['type']=1;
                }else if(in_array(strtolower($ext),$audio)){
                    $data['type']=2;
                }else if(in_array(strtolower($ext),$video)){
                    $data['type']=3;
                }else if(in_array(strtolower($ext),$word)){
                    $data['type']=4;
                }else {
                    $data['type']=5;
                }
                $res=$m->addUpload($data);
                if($res){
                    $ret['data']=$data;
                }
            }
            return $ret;
        }
    }
    public function uploadPic(){//上传图片公共方法
        $post=$this->post; 
        $uid=$this->user_id; 
        if($this->post['act']=="data"){
            $m=new SystemModel();
            $m->setPost($post);  
            $res=$m->getFilePage($post,$uid);
            $sys=new SystemModel(); 
            $upload=$sys->getSetting("upload"); 
            $server_path="";
            if(isset($upload['server_type'])&&intval($upload['server_type'])==2){//
                 $qiniu=$sys->getSetting("qiniu");
                 $server_path=$qiniu['server'];
            }else{
                
                $server_path=$this->request->root(true)."/uploads/";
            }
            foreach ($res['data'] as $key => $val) {
                $res['data'][$key]['src']=$server_path.$val['path'];    
            }
            return $res;
        }else if($this->post['act']=="upload"){//上传图片
            $ret=['status'=>0];
             $file = request()->file('item');
            if(!$file){
                return ['status'=>0,"error"=>"未检测到图片"];
            }  
            $month=date("Ym");
            $oldname=$_FILES['item']['name']; 
            $arr=explode(".",$oldname);
            $ext=$arr[count($arr)-1];
            
            $sys=new SystemModel(); 
            $upload=$sys->getSetting("upload");

            if(isset($upload['server_type'])&&intval($upload['server_type'])==2){//传到七牛云
                $isQiniu=true;
                 // 初始化 UploadManager 对象并进行文件的上传。
                $qiniu=$sys->getSetting("qiniu");
                $qm=new Qiniu($qiniu); 

                // 要上传文件的本地路径
                $filePath = $_FILES['item']['tmp_name'];
                // 上传到七牛后保存的文件名
                $qiniukey = $month."/".md5_file($filePath).".".$ext;
               
                $ret=$qm->upload($qiniukey,$filePath); 
                
                if(isset($ret['hash'])&&isset($ret['key'])){
                    $filename=$ret['key'];
                }else{
                    return $ret;
                }
                
            } else{//传到本地服务器

                $store_path=ROOT_PATH . 'public' . DS . 'uploads'.DS.$month;
                mk_dir($store_path);
                $file = request()->file('item');
    
                // 移动到框架应用根目录/public/uploads/ 目录下
                if($file){
                    $info = $file->rule('unique_id')->move($store_path);
                    if($info){
                        // 成功上传后 获取上传信息
                        // 输出 jpg
                        // echo $info->getExtension();
                        // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
                        $filename=$month."/".$info->getSaveName();
                        // 输出 42a79759f284b767dfcb2a0197904287.jpg
                        // echo $info->getFilename(); 
                    }else{
                        return ['status'=>0,"error"=>$file->getError()];
                    }
                }
            }
           
            
            if(isset($filename)&&strlen($filename)>5){
                 $m=new SystemModel();
                $ret['status']=1;
                $data=[
                    "filename"=>$oldname,
                    "user_id"=>$uid,
                    "createtime"=>time(),
                    "group_id"=>0,
                    "path"=>$filename, 
                    "ext"=>$ext
                ];
                if(isset($upload['server_type'])&&intval($upload['server_type'])==2){
                    $data['server']=isset($qiniu['server'])?$qiniu['server']:"";
                    $data['server_type']=2;
                    $data['src']=((stripos($data['server'],"http")===false)?"http://".$data['server']:$data['server'])."/".$qiniukey;
                }else{
                    $data['server_type']=1;
                } 
                $imgs=["jpg","jpeg",'gif','png','wmp','bmp'];
                $audio=['mp3','wav'];
                $video=['mp4','mpg','rmvb','flv','avi','mkv'];
                $word=['doc','docx','xls',"xlsx",'pdf','wps','xmind','txt','html'];
                if(in_array(strtolower($ext),$imgs)){
                    $data['type']=1;
                }else if(in_array(strtolower($ext),$audio)){
                    $data['type']=2;
                }else if(in_array(strtolower($ext),$video)){
                    $data['type']=3;
                }else if(in_array(strtolower($ext),$word)){
                    $data['type']=4;
                }else {
                    $data['type']=5;
                }
                $res=$m->addUpload($data);
                if($res){
                    $ret['data']=$data;
                }
            }
            return $ret;
        }else if($this->post['act']=="del"){
            $ret=$this->checkPost([
                ['id',"require|number","缺少必要参数"],
                ['path',"require","缺少必要参数"],
                ['server_type',"require|number","缺少必要参数"]
            ]); 
            if($ret===true){
                $m=new SystemModel(); 
                 
                //先删数据库
                $ret=$m->delUpload(intval($this->post['id']),$uid); 
                $keys=[];
                $sy=new SystemModel();
                if(intval($this->post['server_type'])==2){
                    $keys[]=$v['path'];
                    $qm=new Qiniu($m->getSetting("qiniu"));
                    $ret= $qm->deleteMulti($keys);
                }else{
                    $file=ROOT_PATH . 'public' . DS . 'uploads'.DS.$this->post['path'];
                    if(file_exists($file)){
                        unlink($file);
                    }
                }
                
                return ['status'=>1,'msg'=>'删除成功','ret'=>$ret]; 
            }else{
                return ['status'=>0,"error"=>$ret];
            }
        }else if($this->post['act']=="delMulti"){
            $m=new SystemModel(); 
            if(isset($this->post['fids'])&&!empty($this->post['fids'])){
                $fids=$this->post['fids'];
            }else{
                $fids=[$m->getUpload($this->post["fid"])];
            }  
            $ids=[]; 
            foreach($fids as $k=>$v){
                $keys[]=$v['path'];
                $ids[]=intval($v['id']);
                // $res=$m->delUpload($v['id'],$uid );
                // if(isset($res->id)&&isset($res->stauts)&&$res->status==1){
                //     Storage::delete(public_path()."/".$res->path);
                // } 
            }
            //先删数据库
            $ret=$m->delUploads(implode(",", $ids),$uid); 
            $keys=[];
            $sy=new SystemModel();
            foreach ($ret as $k => $v) {
                if(intval($v['server_type'])==2)continue;
                $keys[]=$v['path'];
            }
            $qm=new Qiniu($sy->getSetting("qiniu"));
           $ret= $qm->deleteMulti($keys);
            return ['status'=>1,"datas"=>$keys,'ret'=>$ret]; 
        }else if($this->act=="page"){

            return view("/public/upload");
        } 
    }
    
    
}
