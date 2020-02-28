<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:68:"/www/wwwroot/admin/public/../app/core/view/admin/develop-design.html";i:1581143042;s:49:"/www/wwwroot/admin/app/core/view/public/head.html";i:1581079660;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin</title>
    <meta name="description" content="">
    <meta name="keywords" content="index">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="renderer" content="webkit">
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <link rel="icon" type="image/png" href="_ASSETSP/i/favicon.png">
    <link rel="apple-touch-icon-precomposed" href="_ASSETSP/i/app-icon72x72@2x.png">
    <meta name="apple-mobile-web-app-title" content="" /> 
    <link rel="stylesheet" href="/static/layui/css/layui.css" /> 
    <link rel="stylesheet" type="text/css" href="/static/font-awesome/css/font-awesome.css">
    <link rel="stylesheet" href="/static/css/main.css" />
    <script src="/static/js/jquery.min.js" type="text/javascript"></script>
    <script src="/static/layui/layui.all.js" type="text/javascript"></script>
    <script src="/static/js/jquery.plus.js" type="text/javascript"></script>
    <script src="/static/js/vue.min.js" type="text/javascript"></script>
    <style> 
        html,body{width: 100%;height: 100%;background-color: #fff;}
    </style>
</head>
<body>
    <div class="layui-row  ">
        <div class="layui-col-md15">
        <div class="layui-card">
    
<style type="text/css">
	.layui-form-label{width: 120px!important;}
	.layui-input-block{margin-left: 150px!important;}
</style>

<div class="layui-tab layui-tab-brief" lay-filter="docDemoTabBrief" style="height: 100%;width:1280px;">
   
  <ul class="layui-tab-title">
    <li class="layui-this">搜索条件</li>
    <li>JSON</li> 
    <li>Serialize</li> 
    <li>Bejson</li> 
     <!-- <li class="hide">baidu</li> -->
  </ul>
  <div class="layui-tab-content"  >
     
      <div class="layui-tab-item layui-show"> 
        <form class="layui-form" action="<?php echo wurl($route); ?>" method="post">
        <input type="hidden" name="act" value="save" readonly />
        <div class="layui-form-item">
            <label class="layui-form-label">搜索名</label>
            <div class="layui-input-block">
              	<input type="text" lay-verify="required"  name="search_name" class="layui-input" /> 
            </div>
        </div> 
    	  <div class="layui-form-item">
            <label class="layui-form-label">字段名</label>
            <div class="layui-input-block">
              	<input type="text" lay-verify="required"  name="search_field" class="layui-input" /> 
            </div>
        </div> 
        <div class="layui-form-item">
            <label class="layui-form-label">选择菜单</label>
            <div class="layui-input-block">
               <select name="menu_id">
               		<?php foreach($menus as $k=>$v): ?>
               		<option value="<?php echo $v['id']; ?>"><?php echo $v['menu_name']; ?></option>
               		<?php foreach($v['children'] as $k2=>$v2): ?>
	               		<option value="<?php echo $v2['id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $v2['menu_name']; ?></option>  
               		<?php endforeach; endforeach; ?>
               </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">输入类型</label>
            <div class="layui-input-block">
             <select name="type" >
             	<option value="1">普通输入框</option>
             	<option value="0">隐藏输入框</option>
             	<option value="2">下拉选择框</option>
             	<option value="3">日期 Y-m-d</option>
             	<option value="4">日期 Y-m-d H:i:s</option>
             	
             	<option value="5">日期 Y-m</option>
             	<option value="6">时间 H:i:s</option>
             	<option value="7">年份 Y</option>
             </select>
            </div>
        </div> 
        <div class="layui-form-item">
            <label class="layui-form-label">默认值</label>
            <div class="layui-input-block">
              <input type="text" name="default_val" value="" class="layui-input"> 
            </div>
        </div> 
        <div class="layui-form-item">
            <label class="layui-form-label">可选值</label>
            <div class="layui-input-block">
             
             	 <textarea class="layui-input" name="extend_val" rows="5" style="padding:10px 10px;width: 100%;resize: none;height: auto;min-height: 60px;" placeholder='例如json字符串：{"1":"启用","0":"禁用"}'></textarea>
              
            </div>
        </div> 
        
        <div class="layui-form-item">
            <label class="layui-form-label">可选值序列化</label>
            <div class="layui-input-block">
             
             	 <input type="checkbox"  value="1" name="serialize" lay-skin="switch" lay-filter="switchQrlogin" lay-text="开启|关闭" <?php echo (isset($set['login']['qrcode'])&&intval($set['login']['qrcode'])==1)?"checked":'';?> > 
            </div>
        </div> 
        <div class="layui-form-item">
            <label class="layui-form-label">添加方式</label>
            <div class="layui-input-block">
             
               <input type="radio"  value="1" checked="" name="addtype" title="新增" > 
               <input type="radio"  value="2"   name="addtype" title="覆盖" > 
            </div>
        </div> 
    </div>
    <div class="layui-tab-item">
       <form class="layui-form" action="<?php echo wurl($route); ?>" method="post">
        <input type="hidden" name="act" value="json" readonly />
        <div class="layui-form-item">
          <label class="layui-form-label">JSON</label>
          <div class="layui-input-block">  
               <textarea name="content" class="layui-input" style="resize: none;height: 100px;padding:10px 10px;"></textarea> 
          </div>
        </div>
      </form>
    </div> 
    <div class="layui-tab-item">
          <form class="layui-form" action="<?php echo wurl($route); ?>" method="post">
        <input type="hidden" name="act" value="serialize" readonly />
        <div class="layui-form-item">
            <label class="layui-form-label">字符串</label>
            <div class="layui-input-block">
               <textarea name="content" class="layui-input" style="resize: none;height: 100px;padding:10px 10px;"></textarea> 
            </div>
        </div> 
         </form>

    </div> 
    <div class="layui-tab-item">
         <iframe src="http://bejson.com" style="width: 100%;height: 500px;"></iframe>
    </div>
  
    <div class="layui-tab-item">

       
    </div> 
  </div>
  <div style="width: 50%;padding:20px 20px;text-align: center;margin:0 auto;">
      <button onclick="subcheck()" class="layui-btn layui-btn-normal">保存</button>
  </div>
</div>  
  
<script type="text/javascript">
	function subcheck(){
		if($.validEasy(".valid")){
			$("#form").submit();
		}
	} 
</script>

        </div>
        </div>
    </div>
</body> 
    <script type="text/javascript">
        $(function(){
            $.searchRender(); 
        });
    </script>
</html>