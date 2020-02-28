<?php 
$length=20;
if(isset($_GET['len'])){
	$length=intval($_GET['len']);
}
function randChar($length){
	$str="0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$tmp="";
	for($i=0;$i<$length;$i++){
	  $tmp.=$str[mt_rand(0,61)];
	}  
	return $tmp;
} 
echo randChar($length);?>

<!DOCTYPE html>
<html>
<head> 
	<meta charset="utf-8"/>
	<title></title>
	<script type="text/javascript" src="http://lib.sinaapp.com/js/jquery/1.9.1/jquery-1.9.1.min.js"></script>
</head>
<body>
<div style="width:100%;">
    <div id="touchArea" style="width:90%; height:200px; background-color:#CCC;font-size:100px">长按我</div>
    <div class="names" style="width:50%; height:100px; background-color:red;font-size:50px;display:none">长按显示</div>
 
</div>
<script>
var timeOutEvent=0;
$(function(){
	$("#touchArea").on({
		touchstart: function(e){
			timeOutEvent = setTimeout("longPress()",500);//500设置时间
		 	e.preventDefault();
		},
		touchmove: function(){
            		clearTimeout(timeOutEvent); 
		    	timeOutEvent = 0; 
		},
		touchend: function(){
	   		clearTimeout(timeOutEvent);
			if(timeOutEvent!=0){ 
			    alert("你这是点击，不是长按"); 
			} 
			return false; 
		}
	})
});
 
 
function longPress(){ 
    timeOutEvent = 0; 
    alert("长按事件触发发");
    $(".names").show()
 
} 
 
</script>
</body>
</html>
 