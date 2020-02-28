<?php 
namespace cloud;
require VENDOR_PATH.'qiniu/autoload.php';
use app\core\model\SystemModel;
use Qiniu\Auth as QiniuAuth;
use Qiniu\Storage as QiqiuStorage;
use Qiniu\Http as QiniuHttp; 
use Qiniu\Storage\UploadManager;
use Qiniu\Config;
use Qiniu\Regin;
class Qiniu  {
	public $ak="";
	public $sk="";
	public $server="";
	public $bucket="";
	public $token="";
	public function __construct($conf=[]){
		$this->ak=$conf['ak'];
		$this->sk=$conf['sk'];
		$this->server=$conf['server'];
		$this->bucket=$conf['bucket'];
		$this->token=$this->getToken();
	}
	public function upload($key,$filePath){   
		if(!file_exists($filePath)){
			$ret=['status'=>0,"error"=>"文件不存在"];
			//CacheModel::log("qiniu",$ret);
			return $ret;
		}
		// 初始化 UploadManager 对象并进行文件的上传。
		$uploadMgr = new \Qiniu\Storage\UploadManager();
		// 调用 UploadManager 的 putFile 方法进行文件的上传。
		list($ret, $err) = $uploadMgr->putFile($this->token, $key, $filePath); 

		if($err!=null||!empty($err)){
			//CacheModel::log("qiniu",$err);
		}
		$ret['status']=1;
		$ret['error']=$err;
		return $ret;
	}
	public function getToken(){
		$sm=new SystemModel();
		$qiniu=$sm->getSetting("qiniu");
		if(isset($qiniu['token'])&&isset($qiniu['expire'])&&intval($qiniu['expire'])>(time()+60)){
			return $qiniu['token'];
		}else{
			$auth = new \Qiniu\Auth($this->ak,$this->sk);
			$expires = 7200;
			$policy = null;
			$token = $auth->uploadToken($this->bucket, null, $expires, $policy, true);
		 
            // 生成上传 Token 
            $qiniu['token']=$token;
            $qiniu['expire']=time()+7200; 
			$qiniu=$sm->saveSetting("qiniu",$qiniu);
           // CacheModel::saveSetting("qiniu",$qiniu);
            if($token){
            	$this->token=$token;
            	return $token;
            }
           

		} 
	}
	public function delete($key){
		$auth = new \Qiniu\Auth($this->ak, $this->sk);
		$config = new \Qiniu\Config();
		$bucketManager = new \Qiniu\Storage\BucketManager($auth, $config);
		$err = $bucketManager->delete($bucket, $key);
		if($err){
			return ['status'=>0,"err"=>$err,"error"=>"删除失败"];
		}
	}
	public function deleteMulti($keys){
		$auth = new \Qiniu\Auth($this->ak, $this->sk);
		$config = new \Qiniu\Config();
		$bucketManager = new \Qiniu\Storage\BucketManager($auth, $config);
		$ops = $bucketManager->buildBatchDelete($this->bucket, $keys);
		list($ret, $err) = $bucketManager->batch($ops);
		if ($err) {
		   // CacheModel::log("qiniu",$err);
		    return ['status'=>0,"err"=>$err,"error"=>"操作失败"];
		} else {
		    return ['status'=>1,"data"=>$ret];
		}
	} 
}