<?php 
/**
* [营养元素模型]
* @author   MrGuo <[gj2517@qq.com]>
* @date     2019-07-02
*/
namespace app\weapi\model;
use app\core\model\BaseModel;
class FoodModel extends BaseModel
{    
    
	/**
	* [元素分页] 
	* @param 	$post
	* @return 	array
	*/
    public function getFoodPage($post){
    	   $this->post=$post; 
       $where="f.status=1"; 
        if(isset($post['keyword'])&&trim($post['keyword']!="")){
            $where.=" AND (f.name like '%".($post['keyword'])."%' or f.alias like '%".($post['keyword'])."')";
        }else if(isset($post['group_id'])){//小程序或
            if(intval($post['group_id'])==9999){
                $where.=" AND f.sort>9999";
            } 
        }   
        $field="id,name,code,alias,image_thumb as picture,sort";
        $order="sort"; 
        $desc="desc";
        if(isset($post['desc'])&&$post['desc']=="desc")$desc="desc";
        if(!isset($post['show_field'])||empty($post['show_field'])){
            $field.=",calory,protein,fat,carbohydrate";
        }else{
            $field.=",".trim($post['show_field']);
        }
        if(isset($post['desc_code'])&&!empty($post['desc_code'])){
            $order=$post['desc_code'];
        }
        $data=$this->table("fit_food f")->field($field)->order($order." ".$desc)->where($where)->page();  
        $data['sql']=$this->lastSql();
        return $data;
    }
    /**
    * [获取一个食材的含量等所有信息] 
    * @param    string $id  
    * @param    string $name   
    * @return   array
    */
    public function getFoodInfo($food_id){ 
        $food=$this->table("fit_food")->where("id=".intval($food_id) )->get();  
        $detail=$this->table("fit_food_detail")->where("food_id=".intval($food_id))->get();

        if(empty($food)){
            return null;
        }
        
        $food['detail']=$detail;
        return $food;
    }
    /**
	* [获取一个元素] 
	* @param 	string $id  
	* @param 	string $name   
	* @return 	array
	*/
    public function getOne($id=null,$name=null){
    	$where="1=1";
    	if($name){
    		$where.=" AND name='".$name."' ";
    	}
    	if($id){
    		$where.=" AND id='".$id."' ";
    	} 
    	return $this->table("fit_food")->where($where)->get();
    }
    

    /**
	* [更新菜单信息] 
	* @param 	array $data 
	* @param 	int $nid    
	* @return 	int
	*/
    public function updateFood($data,$nid){
        $data['lastchange']=time();
    	return $this->table("fit_food")->where("id=".intval($nid))->save($data);
    }

     /**
	* [删除菜单] 
	* @param 	int $nid    
	* @return 	int
	*/
    public function removeFood($nid){
    	return $this->table("fit_food")->delete("id=".intval($nid));
    }
    public function getAllElements(){
        return $this->table("fit_element t")->order("t.sort asc,t.id asc")->getall();
    }
    public function getElement($food_id,$element_id,$status=false){
        $where="food_id=".$food_id;
        if($status!==false){
            $where.=" AND f.stauts=".intval($status);
        }
        if($element_id!==false){
            $where.=" AND f.element_id=".intval($element_id);
        }
        $ret=$this->table("fit_food_element f")->join("left join fit_element e on f.element_id=e.id")->field("f.id as fn_id,f.food_id,f.element_id,f.content,f.content_value,f.content_unit,f.level,e.name as element")->where($where)->getall();
        if($element_id!==false&&isset($ret[0])){
            return $ret[0];
        }
        return $ret;
    }
    public function getGroups(){
        return $this->table("fit_food_group")->where("status=1")->order("sort asc,id asc")->getall();
    }
    public function getFoodByNames($names=[]){
        if(empty($names))return []; 
        $where=' id>0 ';
        foreach($names as $k=>$v){ 
            $where.=" OR alias like '%".$v."%' or name like '%".$v."%'";
        }
        return $this->table("fit_food")->field("id,name, concat(',',alias,',') as alias_name ,alias ,image_thumb as picture,calory")->where($where)->getall();
    }
}
