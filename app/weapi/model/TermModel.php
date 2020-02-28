<?php 
/**
* [营养元素模型]
* @author   MrGuo <[gj2517@qq.com]>
* @date     2019-07-02
*/
namespace app\weapi\model;
use app\core\model\BaseModel;
class TermModel extends BaseModel
{    
    public function getTermPage($post){
    	$where="t.`status`=1";
    	if(isset($post['type_id'])&&intval($post['type_id'])){
    		$where.=" AND t.type_id=".intval($post['type_id']);
    	}
    	if(isset($post['keyword'])&&trim($post['keyword'])!=''){
    		$keyword=trim($post['keyword']);
    		$where.=" AND (t.name like '%".$keyword."%' or t.alias like '%".$keyword."%' or t.note like '%".$keyword."%')";
    	}
    	$terms=$this->table("fit_term t")->join("LEFT JOIN fit_term_type ty ON t.type_id=ty.id")->where($where)->field("t.id,t.type_id,t.name,t.alias,t.note,t.image,ty.icon")->order("t.sort desc,t.id desc")->page();
    	return $terms;
    }
    public function getTermType(){
        return $this->table("fit_term_type")->where("status=1")->order("sort desc,id asc")->getall();
    }
    public function getDetail($id){
        $term=$this->table("fit_term t")->join("LEFT JOIN fit_term_type g ON t.type_id=g.id")->where("t.id=".$id)->field("t.id,t.name,t.type_id,t.sort,t.note,FROM_UNIXTIME(t.lastchange,'%Y-%m-%d %H:%i:%s') as lastchange,g.name as type_name")->get();
        if(!empty($term)){
            $term['content']=$this->table("fit_term_content")->where("id=".$id)->get();
        }
        return $term;
    }
}
