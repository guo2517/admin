<?php 
namespace app\core\model;  
use think\Request;
use think\Cache;
use app\core\model\BaseModel;
use app\build\model\SectionModel; 
class SearchModel extends BaseModel
{   
    public static function interface($type){
        $model=new BaseModel();
        switch($type){
            case "sturole"://学生类型的角色
                $sm=new SectionModel();
                $role=$sm->getExamRoles(); 
                $ret=['0'=>"不限"];
                foreach($role as $k=>$v){
                    $ret[$v['role_id']]=$v['text'];
                }
                return $ret;
                break;
            case "subject_id":
                $sm=new SectionModel();
                $role=$sm->getSubject(); 
                $ret=['0'=>"不限"];
                foreach($role as $k=>$v){
                    $ret[$v['id']]=$v['name'];
                }
                return $ret;
                break;
            default:
                break;
        }
        return false;
    }
    public static $search_type=[
        "0"=>"hidden",
        "1"=>"text",
        "2"=>"select",
        "3"=>"date",
        "4"=>"datetime",
        "5"=>"month",
        "6"=>"time",
        "7"=>"year",
        "8"=>"number"
    ];
	/**
    *  
    * @param    string $key    
    * @return   array
    */
    public static function getSearch($route=""){
    	$search=Cache::get('search_'.$route);
    	if(empty($search)){
    		$search=self::cacheSearch($route);
    	} 
        return $search;
    }
    //更新搜索条件换成
    public static function cacheSearch($route=""){
        $sm = new BaseModel();
        $search=$sm->table("core_search")->where("route='".($route)."'")->getall(); 
        Cache::set("search_".($route),$search);
        return $search;
    }
    //设置html片段
    public static function setSearch($route,$options=[]){

    	$search=self::getSearch($route); 
        $searchStr=""; 
    	if(!empty($search)){
    		foreach ($search as $key => $val) {
                $search_type=isset(self::$search_type[$val['search_type']])?self::$search_type[$val['search_type']]:"text";
                if(!empty($val['extend_val'])){
                    $val['extend_value']=unserialize($val['extend_val']);
                }else{
                    $val['extend_value']=[];
                }
                if(!empty($val['default_val'])){
                    $val['default_value']=explode(",",$val['default_val']);
                }else{
                    $val['default_value']=[];
                }
    			switch ($search_type) {
                    case "hidden":
                        $searchStr.=' 
                    <input type="hidden" name="'.$val['search_field'].'" value="'.$val['default_val'].'"> ';
                        break;
    				case 'number'://
                        $searchStr.='<div class="search-item">
                <div class="search-label">'.$val['search_name'].'</div>
                <div class="search-content">
                    <input type="number" class="layui-input search-input" name="'.$val['search_field'].'" placeholder="'.$val['placeholder'].'" value="'.$val['default_val'].'">
                </div>
            </div> ';
                        break;
                    case 'text'://
    					$searchStr.='<div class="search-item">
                <div class="search-label">'.$val['search_name'].'</div>
                <div class="search-content">
                    <input type="text" class="layui-input search-input" name="'.$val['search_field'].'" placeholder="'.$val['placeholder'].'" value="'.$val['default_val'].'">
                </div>
            </div> ';
    					break;
    				case 'select':// 
    					$searchStr.='<div class="search-item">
                <div class="search-label">'.$val['search_name'].'</div>
                <div class="search-content">
                    <select name="'.$val['search_field'].'" >'; 
                    if(!empty($val['interface'])){
                        $val['extend_value']=self::interface($val['interface']);
                    } 
                    if(isset($val['extend_value'])&&!empty($val['extend_value'])){ 
                        foreach($val['extend_value'] as $k=>$v){
                            $select="";
                            if(isset($val['default_value'][0])&&$k==$val['default_value'][0]){
                                $select="selected=selected";
                            }
                            $searchStr.='<option value="'.$k.'" '.$select.'>'.$v.'</option>';
                        }
                    }
                    $searchStr.='</select>
                </div>
            </div> ';
    					break;
    				case 'date':// 
                    case 'datetime'://
                    case 'time'://
                    case 'year'://
                    case 'month'://
                         
                        $searchStr.='<div class="search-item">
                <div class="search-label">'.$val['search_name'].'</div>
                <div class="search-content">
                    <input type="text" id="search-date-'.$val['id'].'" class="layui-input search-input search-date" data-type="'.$search_type.'" date-value="'.$val['default_val'].'" name="'.$val['search_field'].'" value="'.$val['default_val'].'">
                </div>
            </div> '; 
    					break; 
    					break;
    				
    				default:
    					# code...
    					break;
    			}
    		}
    	}
        if(isset($options['btn'])){

        } 

        return $searchStr;
    }
    //设计搜索条件
    public function design($data,$id=0){ 
        if(intval($id)>0){
            $ret=$this->table("core_search")->where("id=".$id)->limit(1)->save($data);
        } else{
            $ret=$this->table("core_search")->insert($data);
        }
        self::cacheSearch($data['route']);
        return $ret;
    }
    public function getOneSearch($where){
        return $this->table("core_search")->where($where)->get();
    }
}