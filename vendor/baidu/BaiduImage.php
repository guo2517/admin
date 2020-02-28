<?php 
namespace baidu; 

use baidu\lib\AipHttpClient;
use baidu\lib\AipHttpUtil;
use baidu\lib\AipImageUtil;
use baidu\lib\AipBCEUtil;
use baidu\lib\AipBase;
use baidu\cls\AipImageClassify;

class BaiduImage{
	public $conf=null;
	public function __construct($conf=null){
		$this->conf=$conf;
	}
	public function foodMatch($img){//食材识别统一入口
		$ret1=$this->ingredient($img);
		foreach ($ret1 as $key => $val) {
			if(floatval($val['score'])>0.6){
				return $ret1;
			}
		}
		$ret2=$this->dishDetect($img);
		$list=array_merge($ret1,$ret2);
		if(empty($list))return $list;
		return $this->array_field_sort($list,"score","desc",true); 
	}
	public function dishDetect($img,$options=[]){//菜品识别 
		$options["top_num"] = 5;
		$options["filter_threshold"] = "0.7";
		$options["baike_num"] = 5;
		$image = file_get_contents($img);
		$client=new AipImageClassify($this->conf['appid'],$this->conf['apikey'],$this->conf['secret']);
		$ret=$client->dishDetect($image);
		$ret=$ret['result'];
		foreach($ret as $k=>$v){
			if($v['name']=="非菜"){
				unset($ret[$k]);
			}else if(floatval($v['probability'])<0.1){
				unset($ret[$k]);
			}else{ 
				$ret[$k]['score']=round($v['probability'],2);
			} 
		} 
		return $ret;
	}
	public function ingredient($img,$options=[]){//食材识别
		$options["top_num"] = 5;
		$options["filter_threshold"] = "0.7"; 
		$image = file_get_contents($img);
		$client=new AipImageClassify($this->conf['appid'],$this->conf['apikey'],$this->conf['secret']);
		$ret=$client->ingredient($image);
		$ret=$ret['result'];
		foreach($ret as $k=>$v){
			if($v['name']=="非果蔬食材"){
				unset($ret[$k]);
			}else if(floatval($v['score'])<0.1){
				unset($ret[$k]);
			}else{
				$ret[$k]['score']=round($v['score'],2);
			}
			
		} 
		return $ret;
	}
	function array_field_sort($arr,$field,$desc="desc",$number=false){
	  $arr2=[];
	  foreach($arr as $k=>$v){
	    if(isset($v[$field])){
	      $arr2[$v[$field]]=$v;
	    }else{
	      $arr2[$k]=$v;
	    }
	  }
	  if(strtoupper($desc)=="DESC"){
	    krsort($arr2);
	  }else{
	    ksort($arr2);
	  } 
	  if($number){
	    $arr3=[];
	    foreach ($arr2 as $k => $v) {
	      array_push($arr3,$v);
	    }
	  }
	  return $arr2;
	}
}
 

