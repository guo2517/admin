<?php 
/**
* [文章模型]
* @author   MrGuo <[gj2517@qq.com]>
* @date     2019-07-02
*/
namespace app\weapi\model;  
use think\Request;
use app\core\model\BaseModel;
class ArticleModel extends BaseModel
{     
    public function addArticle($data,$content){
        $id=$this->table("fit_article")->insert($data);
    	$ret=['status'=>0];
    	if(intval($id)){
    		$content['id']=$id; 
    		$this->table("fit_article_content")->insert($content);
    	}
    	return $id;
    }
    public function getArticlePage($post){
        $where="a.status=1";
        if(isset($post['keyword'])&&trim($post['keyword'])!=""){
            $where.=" AND a.title like '%".trim($post['keyword'])."%'";
        }
        $res=$this->table("fit_article a")->where($where)->order("lastchange desc")->page(); 
        return $res;
    }
    /**
	* [获取一片文章] 
	* @param 	string $id  
	* @param 	string $name   
	* @return 	array
	*/
    public function getArticleInfo($arr,$getContent=true){ 
    	$ret=[ ];
        if(!isset($arr['id'])){
            return ['content'=>'',"article"=>null];
        }
    	$ret['article']=$this->table("fit_article")->where(["id"=>intval($arr['id'])])->field("id,author,title,ontime,FROM_UNIX_TIME('%y-%m-%d %h:%i',lastchange) as lastchange,type_id,user_id,picture,is_original,original_url")->get();
        if($getContent){
            $ret['content']=$this->table("fit_article_content")->where("id=".intval($arr['id']))->get();
        } 
        return $ret;
    } 
     /**
    * [删除文章] 
    * @param    int $articleid    
    * @return   int
    */
    public function removeArticle($nid,$uid){
        $this->table("fit_article_content")->limit(1)->delete("id=".intval($nid)." AND user_id=".intval($uid));
        return $this->table("fit_article")->limit(1)->delete("id=".intval($nid)." AND user_id=".intval($uid));
    }
    public function updateArticle($art,$content){
        $ret=0;
        $content['lastchange']=$art['lastchange']=time(); 
        if(isset($art['id'])){
             $ret=$this->table("fit_article")->where("id=".$art['id']." and user_id=".$art['user_id'])->limit(1)->save($art);
             $content=$this->table("fit_article_content")->where("id=".$art['id']." and user_id=".$art['user_id'])->limit(1)->save($content);
        }else{//新增
            $id=$this->table("fit_article")->insert($art);
            if($id>0){
                $ret=1;
                $content['id']=$id;
                $this->table("fit_article_content")->insert($content);
            }
        }
        return $ret;
    }
       public function updateArticleType($type,$id=0){
        if($id>0){
            return $this->table("fit_article_type")->where("id=".intval($id))->limit(1)->save($type);
        }else{
            return $this->table("fit_article_type")->insert($type);
        }
    }
    //添加名词分类
    public function addArticleType($data){
        return $this->table("fit_article_type")->insert($data);
    }
     
     /**
    * [删除菜单] 
    * @param    int $nid    
    * @return   int
    */
    public function removeArticleType($nid){
        return $this->table("fit_article_type")->delete("id=".intval($nid));
    }
    public function getArticleTypePage($post){
         $where="1=1";
        if(isset($post['keyword'])){
            $where.=" AND t.name like '%".$post['keyword']."%'  ";
        }
        if(isset($post['status'])&&intval($post['status'])>=0){
            $where.=" AND t.status=".intval($post['status']);
        } 
        $res=$this->table("fit_article_type t")->where($where)->order("lastchange desc")->page(); 
        return $res;
    }
    public function getArticleType($where){
        return $this->table("fit_article_type")->where($where)->find();
    }
    public function getArticleTypes($where=""){
        return $this->table("fit_article_type")->order("sort desc")->where($where)->getall();
    }
}
