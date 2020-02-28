<?php 
namespace app\core\model; 
use think\Db;
use think\Request;
use think\Cache;
class BaseModel
{   
    public $logDriver="sql";//file:文件，sql:数据库
    public $logEnable=false;//
	public $prefix = ""; 
    public $table;//表全名
    public $tab;//传进来的table参数  
    public $sql;//最后一个sql  
    public $debugOn=true;//是否显示sql错误   
    public $preQuerySts=false;//不执行语句返回
    public $wherestr="";
    public $orderstr="";
    public $limitstr=""; 
    public $groupstr=""; 
    public $post=[];
    public $params=[];
    public $joinstr="";
    public $isReset=true;//下一次时候执行reset
    public $field="*"; 
    public $redis=null;
    public $redisConf=null;
    public function __construct($table=""){
        $this->user=session("core_user");
        $this->reset(); 
        $this->redisConf=config("redis");
        $conf=config("database");
        $this->logEnable=$conf['log'];
        $this->table($table);
    }
    public function setPost($post=[]){
        $this->post=$post;
    }
    public function tablename($name){
        $this->reset();
        $this->table=" ".($name)." "; 
        $this->tab=$name; 
        return $this;
    }
    public function table($name){
        $this->reset();
        $this->table=" ".($this->prefix.$name)." "; 
        $this->tab=$name; 
        return $this;
    }
    public function field($field){ 
        $this->field=$field; 
        return $this;
    }
    public function reset(){
        if(!$this->isReset){
            //抵消一次reset
            $this->isReset=true;
            return true;
        }
        $this->wherestr="";
        $this->orderstr="";
        $this->limitstr="";
        $this->groupstr="";
        $this->joinstr="";
        $this->field="*";
    }
    public function whereOr($str=""){
        return $this->where($str,"OR");
    }
    //避免字段与关键字冲突记得字段写成这样： `where`
    public function where($str="",$and="AND"){
        if(gettype($str)=="array"){
            $where="";
            if(count($str)<=0){

            }else{
                 foreach($str as $k=>$v){
                    $where.=" ".$k.' = "'.$v.'" '.$and;
                }
                $where=substr($where,0,strlen($where)-3);
            }
            $str=$where; 
        }
        if(strlen($str)>0){
           $this->wherestr.=(strlen(trim($this->wherestr))>0)?" AND (".$str.")":$str;
        } 
        return $this;
    } 
    //避免字段与关键字冲突记得字段写成这样： `limit`
    public function limit($str=""){
        $this->limitstr=$str; 
        return $this;
    }
    //避免字段与关键字冲突记得字段写成这样： `order`
    public function order($str=""){
        $this->orderstr=$str; 
        return $this;
    }
    //避免字段与关键字冲突记得字段写成这样： `group`
    public function group($str=""){
        $this->groupstr=$str; 
        return $this;
    } 
    //echo $model->lastSql();//返回最后一次执行的语句
    public function lastSql(){ 
        return $this->sql;
    }  
    //echo $model->preQuery()->save($data);//返回未执行的语句
    public function preQuery($stop=true){ 
        $this->preQuerySts=$stop;
        return $this;   
    }
    public function join($str){
        $this->joinstr.=" ".$str." ";
        return $this;
    }
    
    public function existOr($ors){
        $where="";
        foreach ($ors as $k => $v) {
            $where.=$k." = '".$v."' OR";
        }
        if($where!=""){
            $where=" (".substr($where, 0,strlen($where)-2).")";
        }
        return $this->exist($where);
    }
    //是否存在
    public function exist($wheres){
        $where="";
        if(is_array($wheres)){
            foreach ($wheres as $k => $v) {
                $where.=$k." = '".$v."' OR ";
            }
            if($where!=""){
                $this->where=" (".substr($where, 0,strlen($where)-2).")";
            } 
        }else{
            if(strlen($wheres)>=3){
                $this->wherestr=$wheres; 
            }
        }
        $sql= $this->__replace("SELECT * FROM ".$this->table." WHERE 1=1 limit 1");
        $res=$this->fetch($sql);
        if(!empty($res)){
            return true;
        }  
        return false;
    }   
    //多host，独立部署时这里进行空操作即可
    public function host($id=""){

        if(stripos($id, "=")!==false){
            return $this->where($id);
        }else{
            return $this->where("host_id=".intval($this->host_id));
        }
    }
    //获取数量
    public function count(){
        $tb=$this->table;
        $sql = $this->__replace("SELECT count(*) as `total` FROM $tb"); 
        return intval($this->fetchColumn($sql,"total"));
    }

    //执行普通sql语句，0返回结果集或者影响行数,1返回一条结果及，2放回
    public function execute($sql){ 
        $this->sql=$sql;
        $this->reset(); 
        if($this->preQuerySts==true){
            return $this->sql;
        }  
        if(stripos($sql,"INSERT")!==false){//此时返回的是true
            
            return DB::execute($sql);
        }else if(stripos($sql, "UPDATE")!==false){
             
            return DB::execute($sql);
        }else if(stripos($sql,"DELETE")!==false){
            
            return DB::execute($sql);
        }else if(stripos($sql,"SELECT")!==false){ 
            return DB::query($sql);
        }else{
            return DB::execute($sql);
        }
        
    }
    public function startTrans(){
        DB::startTrans();
    }
    public function commit(){
        DB::commit();    
    }
    public function rollback(){
        DB::rollback();
    }
    //获取所有数据
    public function getall($field=false){
        $tb=$this->table; 
        if(!$field){
            $field=$this->field; 
        }

        $sql =  $this->__replace("SELECT $field FROM $tb ");
        $res = $this->execute($sql);
        return $res;
         
    }
    //获取单个数据对象
    public function get($field=false){
        $tb=$this->table;
        $this->limitstr="1";
         if(!$field){
            $field=$this->field;
        }
        $sql =  $this->__replace("SELECT $field FROM $tb");  
        return $this->fetch($sql);
    }
    
    //保存，返回影响行数 
    public function save($data){
        $tb=$this->table;
        $set ="";
        foreach ($data as $key => $val) {
            $set.="`$key`='".addslashes($val)."',";
        }
        $set=substr($set,0,strlen($set)-1);
        $sql=$this->__replace("UPDATE $tb SET $set ");
        $res=$this->execute($sql);
        return $res;
    }
    //返回插入行id
    public function insert($data){ 
          $fields="";$values="";

        foreach ($data as $key => $val) {
            if($val==null){ 
                continue;
            } 
            $fields.=$key.",";
            $values.='"'.addslashes($val).'",';
        } 
        $fields=substr($fields,0,strlen($fields)-1); 
        $values=substr($values,0,strlen($values)-1); 
         $this->sql="INSERT INTO ".$this->table." (".$fields.") VALUES ( ".$values.")";  
        return DB::table($this->tab)->insertGetId($data);
        
    } 
    //批量插入
    public function insertAll($datas){
        $tb=$this->table;
        $fields="";
        $values="";
        $buff=true;
        if(count($datas)<1)return false;
        foreach ($datas as $key => $val) {
            if(empty($val)||$val==null)continue;
            $values.="(";
            foreach ($val as $k=> $v) {
                if($buff){
                    $fields.="`".$k."`,";
                    
                } 
                $values.='\''.$v.'\',';
            }
            $buff=false;
            $values=substr($values,0,strlen($values)-1); 
            $values.="),"; 
        }
        $fields=substr($fields,0,strlen($fields)-1); 
        $values=substr($values,0,strlen($values)-1); 
        $sql =  "INSERT INTO $tb ($fields) VALUES $values;" ;
        $res=$this->execute($sql); 
        return $res;  
    }
    //返回boolean，没有where条件时不执行
    //直接传值覆盖where()传值，如：$m->where("id=2")->delete("id=1");删除id=1
    public function delete($where=""){
        $tb=$this->table;   
        $sql="DELETE FROM $tb ";
        if(strlen(trim($where))>2 || strlen($this->wherestr)>0){
            if(strlen(trim($where))>2){
                $this->wherestr=$where;
            }
            $sql =$this->__replace("DELETE FROM $tb ");
            return $this->execute($sql);
        }else { 
            return 0;
        }
    } 
    public function fetch($sql){ 
        $res=$this->execute($sql); 
        return isset($res[0])?$res[0]:null;
    }
    public function fetchColumn($sql,$field="count(*)"){ 
        $res=$this->execute($sql);  
        if(isset($res[0])&&isset($res[0][$field])){
            return $res[0][$field];
        } 
        return null;
    }
    //分页，返回array，包括page，pagecount，total，data，size
    //例：page("U_ID,U_Name","U_Status=1","U_ID desc")
    //参数全部可缺省，其中page和size使用get或post传上来
    public function page($fields=false,$page0=0,$size0=0){
        $post=$this->post;
        $tb=$this->table;  
        $page=1;
        $size=(isset($post['from'])&&$post['f']=="app")?20:10;
        if($fields===false){
            $fields=$this->field;
        }
        if(isset($post['page'])&&intval($post['page'])>0){ 
            $page=intval($post['page']);
        }    
        if(!isset($post['limit'])){
            if(isset($post['size'])){
                $post['limit']=$post['size'];
            }else{
                $post['limit']=10;
            }
        }else{
             $post['limit']=intval($post['limit']);
        }
        if(isset($post['pagenation'])){
            $page=intval($post['pagenation']['page']);
            $post['page']=$page;
            $post['limit']=intval($post['pagenation']['limit']);
        }
        if(isset($post['page'])&&intval($post['limit'])>0&&intval($post['limit'])<1001){
            $size=intval($post['limit']);
        } 
        if(intval($page0)>0){
            $page=$page0;
        }
        if(intval($size0)>0){
            $size=$size0;
        }
        $data=array( 
            "page"=>$page,
            "pagecount"=>0,
            "count"=>0,
            "limit"=>$size,
            "url"=>""//指明下一跳去哪里
        ); 
       // var_dump($size);
        $sql0= $this->__replace("SELECT COUNT(*) as total FROM ".$this->table." ",true,false,false);
        $this->isReset=false;
        $data['count']=intval($this->fetchColumn($sql0,"total")); 
        //var_dump($post['pagenation']);
        $data['pagecount']=ceil($data['count']/$size);
        if($page>$data['pagecount']){//当前页大于页数
            $page=$data['pagecount'];
            $data['page']=$page;
            return ['page'=>$data,"data"=>[], "status"=>1];
        }
        $start=intval(($page-1)*$size);//开始从第几行查
        $this->limitstr=intval(($page-1)*$size).",$size";
        if($start>$data['count']||$start<0){//条数不符，提前返回
            return ['page'=>$data,"data"=>[]];
        }   
        $sql= $this->__replace("SELECT $fields FROM ".$this->table." ",true,true,true);
        $datas=$this->execute($sql);
        return ['page'=>$data,"data"=>$datas,"status"=>1]; 
        
    }

    //自定义sql分页查询，适用于链表查询，自定义查询。返回array，包括page，pagecount，total，data，size
    //pagenation("select a.* from a left join b on a.id=b.aid where a.id=1 ");
    //参数全部可缺省，其中page和size使用get或post传上来,$sql语句格式的 语句中只能出现一次select和from
    public function pagenation($sql,$page0=0,$size0=0){
        global $post,$_W;
        if(intval($post['page'])>0){ 
            $page=intval($post['page']);
        }else{
            $page=1;
        }

        if(intval($post['size'])>0&&intval($post['size'])<1001){
            $size=intval($post['size']);
        }else{
            $size=10;
        } 
        if(intval($page0)>0){
            $page=$page0;
        }
        if(intval($size0)>0){
            $size=$size0;
        } 
        $data=array(
            "data"=>array(),
            "page"=>$page,
            "pagecount"=>0,
            "count"=>0,
            "size"=>$size,
            "url"=>""//指明下一跳去哪里
        );
        $strPos2=stripos($sql, "from");
        $sql0="SELECT count(*) as total FROM ".substr($sql,$strPos2+4,strlen($sql)-($strPos2+4));
        $this->sql=$sql0; 
        if($this->preQuerySts==true){
            return $this->sql0;
        }else{ 
            $pos=stripos($sql0,"group by");
            if($pos!==false){
                $sql0=substr($sql0,0,$pos);
            }
            $data['count']=intval($this->fetchColumn($sql0,"total"));
        }
        
        $data['pagecount']=ceil($data['count']/$size);
        if($page>$data['pagecount']){
            $page=$data['pagecount'];
            $data['page']=$page;
            return $data;
        }

        $start=intval(($page-1)*$size);//开始从第几行查
        $this->limitstr=intval(($page-1)*$size).",$size";
        if($start>$data['count']||$start<0){//条数不符，提前返回
            return $data;
        }   
        $sql= $this->__replace($sql); 
        $data["data"]=$this->execute($sql);
        return $data;
         
    } 
    public function pagelimit($sql){
        if(intval($post['page'])>0){ 
            $page=intval($post['page']);
        }else{
            $page=1;
        }

        if(intval($post['size'])>0&&intval($post['size'])<1001){
            $size=intval($post['size']);
        }else{
            $size=10;
        } 
        if(intval($page0)>0){
            $page=$page0;
        }
        if(intval($size0)>0){
            $size=$size0;
        }
        $strPos2=stripos($sql, "from");
     
        $data=array(
            "data"=>array(),
            "page"=>$page,
            "pagecount"=>0,
            "count"=>0,
            "limitstr"=>"1",
            "size"=>$size,
            "url"=>""//指明下一跳去哪里
        );
        
        $sql0="SELECT count(*) as total FROM ".substr($sql,$strPos2+4,strlen($sql)-($strPos2+4));
        $this->sql=$sql0;
        $data['count']=intval($this->fetchColumn($sql0,"total"));
        $data['pagecount']=ceil($data['count']/$size);
        if($page>$data['pagecount']){
            $page=$data['pagecount'];
            $data['page']=$page;
            return $data;
        }

        $start=intval(($page-1)*$size);//开始从第几行查
        $data['limitstr']=intval(($page-1)*$size).",$size";
        return $data;
    } 
    public function __replace($sql,$catGroup=true,$catOrder=true,$catLimit=true){//执行一些替换 
        $sql=str_replace(":table", $this->table, $sql);//替换sql语句中的:table  
        if(strlen(trim($this->joinstr))>0){ 
            $start=strlen($sql);
           
            if(stripos($sql," WHERE ")!==false){
                $start=stripos($sql," WHERE ");
            }else if(stripos($sql," GROUP ")!==false){
                $start=stripos($sql," GROUP ");
            }else if(stripos($sql," ORDER ")!==false){
                $start=stripos($sql," ORDER ");
            }

             $sql=substr($sql, 0,$start)." ".$this->joinstr." ".substr($sql,$start,strlen($sql)-$start);  
        } 
        $isW=stripos($sql," where ");
        if($isW===false){//不匹配where
            if(strlen(trim($this->wherestr))>2){//替换where
                $sql=$sql." WHERE ".$this->wherestr." ";
            } 
        }else{
            if(strlen(trim($this->wherestr))>2){//替换where
                $sql=str_ireplace("WHERE"," WHERE ".$this->wherestr." AND ", $sql);
            } 
        }
        $isG=stripos($sql," group by ");
        if($catGroup){ 
            if($isG===false){//不匹配group
                if(strlen(trim($this->groupstr))>0){ 
                    $sql=$sql."GROUP BY ".$this->groupstr." "; 
                }  
            }else{
                if(strlen(trim($this->groupstr))>0){ 
                    $sql=str_ireplace("GROUP BY", " GROUP BY ".$this->groupstr.", ", $sql); 
                }  
            } 
        }
         $isO=stripos($sql," order by ");
        if($catOrder){  
            if($isO===false){//不匹配order
                if(strlen(trim($this->orderstr))>3){ 
                    $sql=$sql."ORDER BY ".$this->orderstr." "; 
                }  
            }else{
                if(strlen(trim($this->orderstr))>3){ 
                    $sql=str_ireplace("ORDER BY", " ORDER BY ".$this->orderstr.", ", $sql); 
                }  
            } 
            $isL=stripos($sql," limit "); 
            if($isL===false){//不匹配limit 
                if(strlen(trim($this->limitstr))>0){ 
                    $sql=$sql." LIMIT ".$this->limitstr." "; 
                }  
            }
        }

      
        return trim($sql);
    } 
    public function log($log,$action="系统日志",$user_id=0,$type=1,$table=null){ 
        if($this->logEnable){
            $log=['createtime'=>time(),'log'=>$log];
            if(!empty($action))$log['action']=$action;
            if(!empty($type))$log['type']=$type;
            if(!empty($user_id))$log['user_id']=$user_id;
            if(!empty($table))$log['table_aff']=$table;
            $this->table("core_log")->insert($log);
        }
    }
    //type=file/redis/mysql
    public function setCache($type="file"){
        $this->cacheType=$type;
    }
    public function cacheFile($k,$v){

    }
    public function cacheSQL($k,$v){

    }
    public function cacheRedis($k,$v=false){
        if($v!==false){
            if(gettype($v)!='string'){
                $v=json_encode($v);
            }

        }else{

        }
        if($this->redis==null)$this->redisInit();
    } 
    public function redis(){ 
        Cache::store('redis')->set('name','value');
        $this->redis=new Redis();
        $this->redis->connect($this->redisConf['HOST'],$this->redisConf['PORT']);
    }
}
