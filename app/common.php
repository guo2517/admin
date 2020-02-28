<?php
// +--------------------------------- 
// | @ MrGuo 2019-12
// +------------------ ---------------  
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
function jsonReturn($data,$rep=true,$token=true){ 
//递归将数组json_encode; 
  header("Content-Type:application/json; charset=utf-8");
  if($token){
    $token = request()->token('_token', 'sha1');
    $data['_token']=$token;
  }
  $json= EACHARR($data,$rep,"urlencode"); 
  echo urldecode(json_encode($json,JSON_UNESCAPED_UNICOD)); 
  die();
}
//更新设置缓存 
function cacheSetting(){
  $db=db("core_setting"); 
  $sets=$db->select(); 
  $data=array();
  foreach ($sets as $key => $val) {
    $data[$val['key']]=json_decode($val['value'],true);
  }
  cache('setting',$data,3600);
  return $data;
}
//获取设置缓存
function getSetting($re=true){ 
  if($re){
    cacheSetting();
  }else{
    $data=cache("setting");
  } 
  if(empty($data)){ //缓存已过期
   $data=cacheSetting();
  } 
  $data['attachurl']=request()->domain()."/";
  return $data;
}
//日记文件
function file_log($data){
   $log_dir=ROOT_PATH."runtime/wlog/";
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }  
    $file=$log_dir.date("Y-m-d").".log";
    if(gettype($log)!="string"){
        $log=json_encode($log,JSON_UNESCAPED_UNICODE);
    }
    $f=fopen($file, "a+");
    $time=date("Y-m-d H:i:s",time()); 
    fwrite($f,"\r\n");
    fwrite($f, "[".$time."]  ".$log);
    fwrite($f,"\r\n");
    fclose($f); 
}
//sql日志记录，插入core_log
function sql_log($log,$action="系统日志",$type=1,$user_id=0,$table=null){ 
  $conf=config("database");
  if($conf['log']){
    $log=['createtime'=>time(),'log'=>$log];
    if(!empty($action))$log['action']=$action;
    if(!empty($type))$log['type']=$type; 
    if(!empty($table))$log['table_aff']=$table; 
    if(!empty($user_id))$log['user_id']=$user_id;
    db("core_log")->insert($log);
  } 
}
//生成兼容模式下的url 
function wurl($act = '', $vars = '')
{ 
     $r=request(); 
    if(stripos($r->url(),"index.php")===false){
      $url=$r->root(true)."/".$act;
    }else{
      $url=$r->root(true);
      $url.="?".config("var_pathinfo")."=".$act;
    } 

    if(gettype($vars)=="string"){
        if(strlen($vars)>0){
            $url.="?";
        }
        $url.="?".$vars;
    }else if(gettype($vars)=="array"){
      if(count($vars)>0){
            $url.="?";
            $url.=implode("&", $vars);
        }
        
    }  
    return $url;
}
//创建一个目录
function mk_dir($path0){
  if(strlen($path0)<3){return false;}
  $pps=explode(DS, $path0);
  $path=""; 
  foreach ($pps as $key => $val) {
    $path.=$val.DS;
    if(strlen($val)<1){
      continue;
    }
    if(!is_dir($path)){ 
      mkdir($path);
    }
  }
}
//保存流文件
function save_file_stream($stream,$type,$ext){
  $weapp=session("weapp");
  $date=date("Ym");
  $path=config("upload_path")."uploads".DS.$type.DS.intval($weapp['id']).DS.$date;
  mk_dir($path); 
  
  $file=md5($weapp['id']."_".microtime(true)).".".$ext;
  $h=fopen($path.DS.$file, "wbr");
  fwrite($h, $stream);
  fclose($h);
  return ["path"=>"uploads/".$type."/".intval($weapp['id'])."/".$date."/".$file,"file"=>$file];

}
/** 
 * 发送邮件 
 * @param  touser 收件人
 * @param  body   发送内容
 * @param  config 配置，默认使用qq邮箱465端口
 * @param  config 配置，默认"系统通知"
 * @param  copys 抄送人 保证效率最多设置5个，["user2@qq.com","user3@qq.com"]
 * 栗子：email("user1@qq.com","你的验证码是123",['username'=>'admin@zsk.com','password'=>"sdfjpewrg"])
 */
function email($touser,$body,$config=["username"=>"发件邮箱","password"=>"授权码"],$title="系统通知",$copys=[]){
  if(empty($config['host'])){
    $config['host']="smtp.qq.com";
  }
  if(empty($config['port'])){
    $config['port']=465;
  } 
  $mail = new \email\PHPMailer(); 
  $mail->isSMTP();// 使用SMTP服务  
  $mail->CharSet = "utf8";// 编码格式为utf8，不设置编码的话，中文会出现乱码  
  $mail->Host = "smtp.qq.com";// 发送方的SMTP服务器地址  
  $mail->SMTPAuth = true;// 是否使用身份验证  
  $mail->Username =$config['username'];/// 发送方的163邮箱用户名，就是你申请163的SMTP服务使用的163邮箱 
  $mail->Password =$config['password'];// 发送方的邮箱密码，注意用163邮箱这里填写的是“客户端授权密码”而不是邮箱的登录密码！  
  $mail->SMTPSecure = "ssl";// 使用ssl协议方式 
  $mail->Port = 465;// 163邮箱的ssl协议方式端口号是465/994  

  $mail->setFrom($config['username'],"系统通知");// 设置发件人信息，如邮件格式说明中的发件人，这里会显示为Mailer(xxxx@163.com），Mailer是当做名字显示  
  $mail->addAddress($touser,'用户');// 设置收件人信息，如邮件格式说明中的收件人，这里会显示为Liang(yyyy@163.com)  
  $mail->addReplyTo($config['username'],"Reply");// 设置回复人信息，指的是收件人收到邮件后，如果要回复，回复邮件将发送到的邮箱地址  
  if(count($copys)>0&&count($copys)<6){
    foreach ($copys as $key => $val) {
      $mail->addCC($val);
    }
  }
  //$mail->addCC("xxx@163.com");// 设置邮件抄送人，可以只写地址，上述的设置也可以只写地址(这个人也能收到邮件)  
  //$mail->addBCC("xxx@163.com");// 设置秘密抄送人(这个人也能收到邮件)  
  //$mail->addAttachment("bug0.jpg");// 添加附件  

  $mail->Subject = $title;// 邮件标题   

  $mail->Body =$body;// 邮件正文  
  //$mail->AltBody = "This is the plain text纯文本";// 这个是设置纯文本方式显示的正文内容，如果不支持Html方式，就会用到这个，基本无用  

 if(!$mail->send()){// 发送邮件    
    return ['status'=>false,"error"=>$mail->ErrorInfo];// 输出错误信息   
  }else{  
    return ["status"=>true];  //成功
  }  
}

function EACHARR($arr,$repSts=true,$act=false ){ 
//递归数组，方便其他函数应用
  switch (gettype($arr)) {
    case 'array': 
      foreach ($arr as $key => $val) {
        $arr[$key] = EACHARR($val,$act,$repSts);// 
      }
      break;  
    default: 
      if($repSts){
        $find = array("\r\n", "\n", "\r");  
        $replace = " "; 
        $arr=str_replace($find, $replace, $arr);  
        $find = array("\\");  
        $replace = "/"; 
        $arr=str_replace($find, $replace, $arr);  
      } 
      switch($act){
        case 'urlencode':
           $arr=urlencode($arr); 
          break;
        case 'urldecode':
           $arr=urldecode($arr); 
          break;
        default: 
          break;
      }   
    break;
  }
  return $arr;
}
function __Children($father,$son,$level=0){//找孩子函数
  //var_dump($father);
    //echo "<br/>找：".$val['text'];
  $father['children']=array();
  foreach ($son as $k => $v) {
    if(intval($v['pid'])==intval($father['id'])){ 
      //echo "<br/>找到了：".$v['text'];
      $sons=__Children($v,$son,$level+1);//递归，把一个系先全部找完。
      array_push($father['children'],$sons);
      unset($son[$k]);
    }
  }
  $father['slevel']=$level;
  return $father;
}
function childTree($son,$alone=false){//  
  $father=array();
  foreach ($son as $key => $val) {
    if(intval($val['pid'])==0){//获取第一代
      array_push($father, $val);
      unset($son[$key]); 
    }
  }
  foreach ($father as $key => $val) {
    $father[$key]=__Children($val,$son,0);
  }
  if($alone){//保留没有父级的孤儿
    foreach ($son as $key => $val) { 
      array_push($father, $val); 
    }
  }
  return $father;
}
function checkLogin(){
    $user=session("curruser");
  	if(intval($user['uid'])<1){
      header("Location:".wurl("login")); 
      exit();
    }
}
function passwordSalt($password,$salt){ 
	$password2=md5($salt.substr($password,0,3)."zh".md5($password.$salt));
	return $password2;
}
function matchSalt($pwd0,$pwd1,$salt){
	if(strlen($pwd0)<4||strlen($pwd1)<20||strlen($salt)<5){
		return false;
	} 
	$password2=md5($salt.substr($pwd0,0,3)."zh".md5($pwd0.$salt));
	 
	if($password2==$pwd1){
		return true;
	}
	return false;
}
//随机字符
function randChar($length){
	$str="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$tmp="";
	for($i=0;$i<$length;$i++){
		$tmp.=$str[mt_rand(0,51)];
	}  
	return $tmp;
}
//随机字符串
function randCharNumber($length){
	$str="0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$tmp="";
	for($i=0;$i<$length;$i++){
		$tmp.=$str[mt_rand(0,61)];
	}  
	return $tmp;
}
function currentUrl(){
	return 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
}
 
//发送curl请求
function curl($url,$data=null,$timeout=5){
    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//校验ssl
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);//校验ssl
    if(!empty($data)){
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    } 
    curl_setopt($ch, CURLOPT_REFERER,$_SERVER['HTTP_HOST']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); 
    curl_setopt($ch, CURLOPT_TIMEOUT,$timeout);
    $rs=curl_exec($ch);
    if(curl_errno($ch)){//出错则显示错误信息
         return array("errcode"=>60,"massage"=>"CURL：".curl_error($ch),"url"=>$url);
      }else{ 
      curl_close($ch);    
      return json_decode($rs,true); 
    }
}
 
function obj2arr($data){
  return json_decode(json_encode($data),true);
}

function pagenation($page=[]){
  if(empty($page))return ;
  $request=request();
  $currentUrl=$request->url();
  $postParam=$request->post();
  $page['pagecount'] =ceil($page['total']/$page['per_page']);
if(intval($page['total'])  >0){ 
$html='<style type="text/css">
  #form-pagenation .am-btn-primary{
    background-color:#3bb4f2!important;border:none!important;
  }
  #form-pagenation .page-desc{
    color:#23ABF0;line-height: 1.2;padding:.5em 1em;font-size:13px;
  }
  #form-pagenation .am-btn{
    border:none!important;
  }
  #form-pagenation #page-input{
    display:inline-block;padding:2.5px 6px;border:1px solid #c2cad8;text-align: center;
  }
</style>
   <form action="'.$currentUrl.'" method="post" id="form-pagenation">';
    foreach($postParam as $pindex=> $pvalue){
    $html.='<input type="hidden" name="'.$pindex.'"   value="'.$pvalue.'">';
    }
    $html.='<input type="hidden" name="page" value="">';
    $html.='<input type="hidden" name="limit" value="'.$page['per_page'].'" id="page-size">'; 
    $html.='<div class="am-fr">
              <ul class="am-pagination ">
         <li >
        <div class="page-desc" >共<span>&nbsp;'.$page["total"].'&nbsp;</span>条记录。共&nbsp;'.$page["pagecount"].'&nbsp;</span>页，当前第&nbsp;'.$page["current_page"].'&nbsp;</span>页&nbsp;&nbsp;</div>
      </li>';
    if( $page['pagecount']>1&&$page['current_page']>1){
    $html.='<li >
        <a class="am-btn am-btn-primary am-btn-xs" href="javascript:void(0);"  onclick="toPageUrl(1,'.$page['size'].')">首页</a>
      </li>';
    }else{ 
    $html.='<li>
      <a href="javascript:void(0);" class="am-btn am-btn-default am-disabled am-btn-xs" class="am-btn am-btn-default am-disabled am-btn-xs"   >首页</a>
    </li>';
    } 
  if( $page['current_page']>1){  
    $html.='<li>
      <a href="javascript:void(0);" class="am-btn am-btn-primary am-btn-xs" onclick="toPageUrl('.intval($page['current_page']-1).','.$page['size'].')">上一页</a>
    </li>';
  }else{ 
    $html.='<li>
      <a href="javascript:void(0);" class="am-btn am-btn-default am-disabled am-btn-xs"  >上一页</a>
    </li>'; 
  } 
  if( $page['current_page']<$page['pagecount']){ 
    $html.='<li>
      <a href="javascript:void(0);"  class="am-btn am-btn-primary am-btn-xs" onclick="toPageUrl('.intval($page['current_page']+1).','.$page['size'].')">下一页</a>
    </li>'; 
  }else { 
    $html.='<li>
      <a href="javascript:void(0);" class="am-btn am-btn-default am-disabled am-btn-xs"  >下一页</a>
    </li>'; 
  }
  
    $html.='<li>
      <a href="javascript:void(0);" class="am-btn am-btn-primary am-btn-xs" onclick="toPageUrl2()">转到</a>
    </li>
    <li>
      <input type="number" class="page-input" name="page" class="am-form-field " min="1" max="'.$page['pagecount'].'"  value="'.$page['current_page'].'"  id="page-page">
    </li>
    <li>
      <a href="javascript:void(0);" class="am-btn am-btn-primary am-btn-xs" onclick="toPageUrl2()">页</a>
    </li>';
  if( $page['pagecount']>1&&($page['current_page']<=$page['pagecount']-1)){
    $html.='<li>
      <a  href="javascript:void(0);" class="am-btn am-btn-primary am-btn-xs" onclick="toPageUrl('.$page['pagecount'].','.$page['size'].')">末页</a>
    </li>';
  }else{
    $html.='<li>
      <a href="javascript:void(0);" class="am-btn am-btn-default am-disabled am-btn-xs">末页</a>
    </li>';
  }
    $html.='</ul></div></form>
   <script type="text/javascript">
    function toPageUrl2(){
      if(parseInt($("#page-page").val())>0){
        $("#form-pagenation").submit();
      } 
    }
    function toPageUrl(page,size){
      $("#page-page").val(page);
      $("#page-size").val(size);
      $("#form-pagenation").submit();
    }
   </script>';
  }else{ 
    $html.='<div class="am-fr">
    <span style="font-size: 13px;">没有搜索到相关记录.</span>
  </div>';
  }; 
  return $html;
}
