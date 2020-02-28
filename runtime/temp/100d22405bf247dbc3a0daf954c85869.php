<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:61:"/www/wwwroot/admin/public/../app/core/view/setting/weapp.html";i:1581085410;s:49:"/www/wwwroot/admin/app/core/view/public/head.html";i:1581079660;}*/ ?>
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
    
<form class="layui-form" action="<?php echo wurl($route); ?>" method="post">
    <input type="hidden" name="act" value="save" readonly /> 
<div class="layui-tab layui-tab-brief" lay-filter="docDemoTabBrief" style="height: 100%;">
   
  <ul class="layui-tab-title">
    <li class="layui-this">公众号</li>
    <li>小程序</li>  
     <!-- <li class="hide">baidu</li> -->
  </ul>
  <div class="layui-tab-content"  >
     <div class="layui-tab-item layui-show"> 
          <div class="layui-form-item">
            <label class="layui-form-label">名称</label>
            <div class="layui-input-block">
              <input type="text" name="wechat[name]" value="<?php echo isset($set['wechat']['name'])?$set['wechat']['name']:'';?>"  autocomplete="off" placeholder="请输入名称" class="layui-input">
            </div>
          </div>
          <div class="layui-form-item">
            <label class="layui-form-label">APPID</label>
            <div class="layui-input-block">
               <input type="text" name="wechat[appid]" value="<?php echo isset($set['wechat']['appid'])?$set['wechat']['appid']:'';?>"  autocomplete="off" placeholder="请输入名称" class="layui-input">
            </div>
          </div> 
          <div class="layui-form-item">
            <label class="layui-form-label">SECRET</label>
            <div class="layui-input-block">
               <input type="text" name="wechat[secret]" value="<?php echo isset($set['wechat']['secret'])?$set['wechat']['secret']:'';?>"  autocomplete="off" placeholder="请输入名称" class="layui-input">
            </div>
          </div> 
          <div class="layui-form-item">
            <label class="layui-form-label">ACCESS_TOKEN</label>
            <div class="layui-input-block"  >
                  <input type="text" class="layui-input" name="wechat[access_token]" value="<?php echo isset($set['wechat']['access_token'])?$set['wechat']['access_token']:'';?>" readonly="">
            </div>
          </div> 
    </div>
     <div class="layui-tab-item"> 
         <div class="layui-form-item">
            <label class="layui-form-label">名称</label>
            <div class="layui-input-block">
              <input type="text" name="weapp[name]" value="<?php echo isset($set['weapp']['name'])?$set['weapp']['name']:'';?>"  autocomplete="off" placeholder="请输入名称" class="layui-input">
            </div>
          </div>
          <div class="layui-form-item">
            <label class="layui-form-label">APPID</label>
            <div class="layui-input-block">
               <input type="text" name="weapp[appid]" value="<?php echo isset($set['weapp']['appid'])?$set['weapp']['appid']:'';?>"  autocomplete="off" placeholder="请输入名称" class="layui-input">
            </div>
          </div> 
          <div class="layui-form-item">
            <label class="layui-form-label">SECRET</label>
            <div class="layui-input-block">
               <input type="text" name="weapp[secret]" value="<?php echo isset($set['weapp']['secret'])?$set['weapp']['secret']:'';?>"  autocomplete="off" placeholder="请输入名称" class="layui-input">
            </div>
          </div> 
          <div class="layui-form-item">
            <label class="layui-form-label">ACCESS_TOKEN</label>
            <div class="layui-input-block"  >
                <input type="text" class="layui-input" name="weapp[access_token]" value="<?php echo isset($set['weapp']['access_token'])?$set['weapp']['access_token']:'';?>" readonly="">
                
            </div>
          </div> 
    </div>
    </div>
      <div style="width:90%;padding:20px 20px;text-align: center;">
      <button onclick="subcheck()" class="layui-btn layui-btn-normal">保存</button>
  </div>
    </div>
</form>  
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