<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:60:"/www/wwwroot/admin/public/../app/core/view/setting/base.html";i:1580827078;s:49:"/www/wwwroot/admin/app/core/view/public/head.html";i:1580960944;}*/ ?>
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
    <link rel="icon" type="image/png" href="./static/assets/i/favicon.png">
    <link rel="apple-touch-icon-precomposed" href="./static/assets/i/app-icon72x72@2x.png">
    <meta name="apple-mobile-web-app-title" content="" /> 
    <link rel="stylesheet" href="./static/layui/css/layui.css" /> 
    <link rel="stylesheet" type="text/css" href="./static/font-awesome/css/font-awesome.css">
    <link rel="stylesheet" href="./static/css/main.css" />
    <script src="./static/js/jquery.min.js" type="text/javascript"></script>
    <script src="./static/layui/layui.all.js" type="text/javascript"></script>
    <script src="./static/js/jquery.plus.js" type="text/javascript"></script>
    <script src="./static/js/vue.min.js" type="text/javascript"></script>
    <style> 
    </style>
</head>
<body>
    <div class="layui-row  ">
        <div class="layui-col-md15">
        <div class="layui-card">
    
<style type="text/css"> 
</style>

<div class="widget am-cf vueEl" >
	<div class="widget-head am-cf">
        <div class="widget-title am-fl">通用配置</div>
        <div class="widget-function am-fr">
            <a href="javascript:history.go(0);" class="am-icon-refresh"></a>
        </div>
    </div>
    <div class="widget-body am-fr">
 	<form class="am-form tpl-form-line-form" action="<?php echo wurl($route); ?>?&act=save" method="post" id="form"> 
 		<div class="am-form-group">
            <label for="user-email" class="am-u-sm-3 am-form-label">WebSocket <span class="tpl-form-line-small-title">WebSocket</span></label>
            <div class="am-u-sm-9">
                <input type="text" name="socket[url]" class="am-form-field tpl-form-no-bg" placeholder="" value="<?php echo isset($set['socket']['url'])?$set['socket']['url']:'';?>" >
                <small></small>
            </div>
        </div> 
        <div class="am-form-group">
            <label for="user-email" class="am-u-sm-3 am-form-label">启用七牛云 <span class="tpl-form-line-small-title"></span></label>
            <div class="am-u-sm-9">
                <input type="text" name="qiniu[enable]" class="am-form-field tpl-form-no-bg" placeholder="" value="<?php echo isset($set['qiniu']['enable'])?$set['qiniu']['enable']:'0';?>" >
                <small></small>
            </div>
        </div> 
        <div class="am-form-group">
            <label for="user-email" class="am-u-sm-3 am-form-label">七牛云域名 <span class="tpl-form-line-small-title">CNAME</span></label>
            <div class="am-u-sm-9">
                <input type="text" name="qiniu[server]" class="am-form-field tpl-form-no-bg" placeholder="" value="<?php echo isset($set['qiniu']['server'])?$set['qiniu']['server']:'';?>" >
                <small></small>
            </div>
        </div> 
         <div class="am-form-group">
            <label for="user-email" class="am-u-sm-3 am-form-label">七牛云AK <span class="tpl-form-line-small-title">AccessKey</span></label>
            <div class="am-u-sm-9">
                <input type="text" name="qiniu[ak]" class="am-form-field tpl-form-no-bg" placeholder="" value="<?php echo isset($set['qiniu']['ak'])?$set['qiniu']['ak']:'';?>" >
                <small></small>
            </div>
        </div> 
         <div class="am-form-group">
            <label for="user-email" class="am-u-sm-3 am-form-label">七牛云SK <span class="tpl-form-line-small-title">SecretKey</span></label>
            <div class="am-u-sm-9">
                <input type="text" name="qiniu[sk]" class="am-form-field tpl-form-no-bg" placeholder="" value="<?php echo isset($set['qiniu']['sk'])?$set['qiniu']['sk']:'';?>" >
                <small></small>
            </div>
        </div> 
         <div class="am-form-group">
            <label for="user-email" class="am-u-sm-3 am-form-label">七牛云BucketID <span class="tpl-form-line-small-title">空间名称</span></label>
            <div class="am-u-sm-9">
                <input type="text" name="qiniu[bucket]" class="am-form-field tpl-form-no-bg" placeholder="" value="<?php echo isset($set['qiniu']['bucket'])?$set['qiniu']['bucket']:'';?>" >
                <small></small>
            </div>
        </div> 
         <div class="am-form-group">
            <label for="user-email" class="am-u-sm-3 am-form-label">百度云APPID <span class="tpl-form-line-small-title">appid</span></label>
            <div class="am-u-sm-9">
                <input type="text" name="baidu[appid]" class="am-form-field tpl-form-no-bg" placeholder="" value="<?php echo isset($set['baidu']['appid'])?$set['baidu']['appid']:'';?>" >
                <small></small>
            </div>
        </div> 
        <div class="am-form-group">
            <label for="user-email" class="am-u-sm-3 am-form-label">百度云APIKey <span class="tpl-form-line-small-title">apikey</span></label>
            <div class="am-u-sm-9">
                <input type="text" name="baidu[apikey]" class="am-form-field tpl-form-no-bg" placeholder="" value="<?php echo isset($set['baidu']['apikey'])?$set['baidu']['apikey']:'';?>" >
                <small></small>
            </div>
        </div> 
        <div class="am-form-group">
            <label for="user-email" class="am-u-sm-3 am-form-label">百度云SecretKey <span class="tpl-form-line-small-title">secret</span></label>
            <div class="am-u-sm-9">
                <input type="text" name="baidu[secret]" class="am-form-field tpl-form-no-bg" placeholder="" value="<?php echo isset($set['baidu']['secret'])?$set['baidu']['secret']:'';?>" >
                <small></small>
            </div>
        </div> 
        

        

        <div class="am-form-group">
            <div class="am-u-sm-9 am-u-sm-push-3">
                <button type="button" onclick="subcheck()" class="am-btn am-btn-primary tpl-btn-bg-color-success ">保存</button> 
            </div>
        </div>
    </form>
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