<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:60:"/www/wwwroot/admin/public/../app/core/view/setting/site.html";i:1581079661;s:49:"/www/wwwroot/admin/app/core/view/public/head.html";i:1581079660;}*/ ?>
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
    <li class="layui-this">网站设置</li>
    <li>登录设置</li> 
    <li>上传设置</li>
    <li>WebSocket</li>
    <li>邮件设置</li>
     <!-- <li class="hide">baidu</li> -->
  </ul>
  <div class="layui-tab-content"  >
    
    <div class="layui-tab-item layui-show"> 
          <div class="layui-form-item">
            <label class="layui-form-label">站点名称</label>
            <div class="layui-input-block">
              <input type="text" name="site[title]" value="<?php echo isset($set['site']['title'])?$set['site']['title']:'';?>"  autocomplete="off" placeholder="请输入名称" class="layui-input">
            </div>
          </div>
           <div class="layui-form-item">
            <label class="layui-form-label">底部版权</label>
            <div class="layui-input-block">
              <input type="text" name="site[copyright]" value="<?php echo isset($set['site']['copyright'])?$set['site']['copyright']:'';?>" autocomplete="off" placeholder="请输入名称" class="layui-input">
            </div>
          </div> 
    </div>
    <div class="layui-tab-item">
      <div class="layui-form-item">
        <label class="layui-form-label">二维码登录</label>
        <div class="layui-input-block">
          <input type="checkbox"  value="1" name="login[qrcode]" lay-skin="switch" lay-filter="switchQrlogin" lay-text="开启|关闭" <?php echo (isset($set['login']['qrcode'])&&intval($set['login']['qrcode'])==1)?"checked":'';?> >
        </div>
      </div>
      <div class="layui-form-item">
        <label class="layui-form-label">登录方式</label>
        <div class="layui-input-block">  
              <input type="radio" name="login[type]" value="wechat" title="公众号" <?php echo (isset($set['login']['type'])&&$set['login']['type']=="wechat")?"checked":'';?>  >
              <input type="radio" name="login[type]" value="weapp" title="小程序" <?php echo (isset($set['login']['type'])&&$set['login']['type']=="weapp")?"checked":'';?> > 
            
        </div>
      </div>
      <div class="layui-form-item">
        <label class="layui-form-label">小程序路径</label>
        <div class="layui-input-block">  
            <input type="text" name="login[weapp_path]" class="layui-input"  value="<?php echo isset($set['login']['weapp_path'])?$set['login']['weapp_path']:'';?>" placeholder="启用小程序登录时需要设置" autocomplete="off">  
            
        </div>
      </div>
    </div> 
    <div class="layui-tab-item">
        <div class="layui-form-item">
            <label class="layui-form-label">服务器</label>
            <div class="layui-input-block">
              <input type="radio" name="upload[server_type]" value="1" title="本地服务器" checked="">
              <input type="radio" name="upload[server_type]" value="2" title="七牛云" checked="">
            </div>
        </div>
          <div class="layui-form-item">
            <label class="layui-form-label">七牛云域名</label>
            <div class="layui-input-block">
                 <input type="text" name="qiniu[server]" class="layui-input" placeholder="" value="<?php echo isset($set['qiniu']['server'])?$set['qiniu']['server']:'';?>" >
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">七牛云BucketID</label>
            <div class="layui-input-block">
                <input type="text" name="qiniu[bucket]" class="layui-input" placeholder="" value="<?php echo isset($set['qiniu']['bucket'])?$set['qiniu']['bucket']:'';?>" >
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">七牛云AK</label>
            <div class="layui-input-block">
                <input type="text" name="qiniu[ak]" class="layui-input" placeholder="" value="<?php echo isset($set['qiniu']['ak'])?$set['qiniu']['ak']:'';?>" >
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">七牛云SK</label>
            <div class="layui-input-block">
                <input type="text" name="qiniu[sk]" class="layui-input" placeholder="" value="<?php echo isset($set['qiniu']['sk'])?$set['qiniu']['sk']:'';?>" >
            </div>
        </div>
      
         
    </div> 
    <div class="layui-tab-item">
        <div class="layui-form-item">
            <label class="layui-form-label">URL</label>
            <div class="layui-input-block">
                  <input type="text" name="socket[url]" class="layui-input" placeholder="" value="<?php echo isset($set['socket']['url'])?$set['socket']['url']:'';?>" >
            </div>
        </div> 
    </div>
  
    <div class="layui-tab-item">

        <div class="layui-form-item">
            <label class="layui-form-label">邮件服务器</label>
            <div class="layui-input-block">
                <input type="text" name="email[host]" class="layui-input" placeholder="" value="<?php echo isset($set['email']['host'])?$set['email']['host']:'smtp.qq.com';?>" >
            </div>
        </div> 
        <div class="layui-form-item">
            <label class="layui-form-label">服务器端口</label>
            <div class="layui-input-block">
                <input type="text" placeholder="发送邮件使用的服务器端口，默认465" name="email[username]" class="layui-input" placeholder="" value="<?php echo isset($set['email']['port'])?$set['email']['port']:'465';?>" >
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">发件邮箱</label>
            <div class="layui-input-block">
                <input type="text" name="email[username]" class="layui-input" placeholder="" value="<?php echo isset($set['email']['username'])?$set['email']['username']:'';?>" >
            </div>
        </div>
         <div class="layui-form-item">
            <label class="layui-form-label">授权码</label>
            <div class="layui-input-block">
                <input type="text" name="email[password]" class="layui-input" placeholder="" value="<?php echo isset($set['email']['password'])?$set['email']['password']:'';?>" >
            </div>
        </div>  
    </div>


      <div class="layui-tab-item hide">
        <div class="layui-form-item">
            <label class="layui-form-label">百度云APPID</label>
            <div class="layui-input-block">
                 <input type="text" name="baidu[appid]" class="layui-input" placeholder="" value="<?php echo isset($set['baidu']['appid'])?$set['baidu']['appid']:'';?>" >
            </div>
        </div> 
        <div class="layui-form-item">
            <label class="layui-form-label">百度云secret</label>
            <div class="layui-input-block">
                  <input type="text" name="baidu[secret]" class="layui-input" placeholder="" value="<?php echo isset($set['baidu']['secret'])?$set['baidu']['secret']:'';?>" >
            </div>
        </div> 
        <div class="layui-form-item">
            <label class="layui-form-label">百度云apikey</label>
            <div class="layui-input-block">
                  <input type="text" name="baidu[apikey]" class="layui-input" placeholder="" value="<?php echo isset($set['baidu']['apikey'])?$set['baidu']['apikey']:'';?>" >
            </div>
        </div> 
        <div class="layui-form-item">
            <label class="layui-form-label">百度云APPID</label>
            <div class="layui-input-block">
                 
            </div>
        </div> 
        <div class="layui-form-item">
            <label class="layui-form-label">百度云APPID</label>
            <div class="layui-input-block">
                 
            </div>
        </div> 
    </div>
  </div>
  <div style="width: 50%;padding:20px 20px;text-align: center;margin:0 auto;">
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