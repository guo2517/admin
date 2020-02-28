<?php 
/**
* [文章模型]
* @author   MrGuo <[gj2517@qq.com]>
* @date     2019-07-02
*/
namespace app\weapi\model;  
use think\Request;
use app\core\model\BaseModel;
class TrendModel extends BaseModel
{     
    public function getTrendPage($post){
    	$now=time();
    	$where="t.createtime<".$now." and t.status=1";
    	if(isset($post['keyword'])&&strlen(trim($post['keyword']))>0){
    		$where.=" AND (content like '%".trim($post['keyword'])."%' or tags like '%".trim($post['keyword'])."%')";
    	}
    	if(isset($post['type'])&&strlen(trim($post['type']))>0){
    		$where.=" AND type='".trim($post['type'])."'";
    	}
    	$trend=$this->table("fit_trend t")->join("LEFT JOIN core_user u ON t.user_id=u.user_id")->field("t.id,t.type,t.item_id,t.content,t.picture,t.tags,t.top_time,t.good_time,t.hot_time,t.user_id,t.createtime,u.nickname,u.avatar")->where($where)->order("if(top_time>{$now},0,1),createtime DESC")->page();
    	$trend['sql']=$this->lastSql();
        return $trend;
    } 
}
