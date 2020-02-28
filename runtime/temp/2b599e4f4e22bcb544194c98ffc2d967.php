<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:61:"/www/wwwroot/admin/public/../app/core/view/user/userinfo.html";i:1581079663;s:49:"/www/wwwroot/admin/app/core/view/public/head.html";i:1581079660;}*/ ?>
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
    .am-icon-unlock{margin-right: 5px}
</style>
<script type="text/javascript" src="/static/js/md5.js"></script>
<script type="text/javascript" src="/static/js/jquery.qrcode.min.js"></script>
    <input type="hidden" name="act" value="save" readonly /> 
<div class="layui-tab layui-tab-brief" lay-filter="docDemoTabBrief" style="height: 100%;">
   
  <ul class="layui-tab-title">
    <li class="layui-this">基础信息</li>
    
    <li>更改密码</li> 
    <li>绑定设置</li>   
  </ul>
  <div class="layui-tab-content"  >
    <div class="layui-tab-item layui-show"> 
        <form class="layui-form" action="<?php echo wurl($route); ?>" method="post">
        <div class="layui-form-item">
            <label class="layui-form-label">用户名</label>
            <div class="layui-input-block">
              <input type="text"   value="<?php echo isset($user['username'])?$user['username']:'';?>" readonly disabled autocomplete="off" placeholder="请输入名称" class="layui-input layui-disabled">
            </div>
        </div>
          <div class="layui-form-item">
            <label class="layui-form-label">真实姓名</label>
            <div class="layui-input-block">
               <input type="text" name="truename" value="<?php echo isset($user['truename'])?$user['truename']:'';?>"  autocomplete="off" placeholder="请输入名称" class="layui-input">
            </div>
          </div> 
          <div class="layui-form-item">
            <label class="layui-form-label">手机号码</label>
            <div class="layui-input-block">
               <input type="text" name="mobile" value="<?php echo isset($user['mobile'])?$user['mobile']:'';?>"  lay-verify="required|phone"  autocomplete="off" placeholder="请输入名称" class="layui-input">
            </div>
          </div> 
          <div class="layui-form-item">
            <label class="layui-form-label">邮箱</label>
            <div class="layui-input-block">
               <input type="text" name="email" value="<?php echo isset($user['email'])?$user['email']:'';?>" lay-verify="email" autocomplete="off" placeholder="请输入名称" class="layui-input">
            </div>
          </div> 
          <div class="layui-form-item">
            <label class="layui-form-label">生日</label>
            <div class="layui-input-block">
               <input type="text" name="birthday"  value="<?php echo isset($user['birthday'])?$user['birthday']:'';?>"  autocomplete="off" placeholder="请输入名称" class="layui-input input-date">
            </div>
          </div> 
          <div class="layui-form-item">
            <label class="layui-form-label">性别</label>
            <div class="layui-input-block">
                <input type="radio" name="sex" value="1" title="男" <?php echo isset($user['sex'])&&intval($user['sex'])==1?"checked":'';?>>
                <input type="radio" name="sex" value="2" title="女" <?php echo isset($user['sex'])&&intval($user['sex'])==2?"checked":'';?>>
            </div>
          </div> 
          
          <div class="layui-form-item">
            <label class="layui-form-label">身份证</label>
            <div class="layui-input-block"  >
                  <input type="text" class="layui-input" name="idcard" value="<?php echo isset($user['idcard'])?$user['idcard']:'';?>"  placeholder="设置了身份证以后，生日和性别保存不生效">
            </div>
          </div> 
            <div style="width:90%;padding:20px 20px;text-align: center;">
              <button lay-submit lay-filter="*" class="layui-btn layui-btn-normal">保存</button>
          </div>
          <input type="hidden" name="act" value="save">
          </form>
    </div>
    <div class="layui-tab-item">   
        <form class="layui-form" method="post">
        <div class="layui-form-item">
            <label class="layui-form-label">原密码</label>
            <div class="layui-input-block"  >
                <input type="hidden" name="act" value="resetpwd">
                <input type="password" class="layui-input" name="password" value="" autocomplete="off">
            </div>
        </div>  
        <div class="layui-form-item">
            <label class="layui-form-label">新密码</label>
            <div class="layui-input-block"  >
                  <input type="password" class="layui-input" name="newpass" value="" autocomplete="off">
            </div>
        </div>  
        <div class="layui-form-item">
            <label class="layui-form-label">确认密码</label>
            <div class="layui-input-block"  >
                  <input type="password" class="layui-input" name="newpass1" autocomplete="off">
            </div>
        </div>  
        <div style="width:90%;padding:20px 20px;text-align: center;">
              <button lay-submit lay-filter="*" class="layui-btn layui-btn-normal">保存</button>
          </div>
        </form>
    </div>
     <div class="layui-tab-item"> 
           <div class="layui-form-item">
            <label class="layui-form-label">公众号OpenID</label>
            <div class="layui-input-block"  >
                <input type="text" class="layui-input" readonly value="<?php echo isset($user['openid_wx'])?$user['openid_wx']:'';?>" style="width: 300px;display: inline-block;" autocomplete="off">
                 <a href="" class="layui-btn  layui-btn-danger"><i class="am-icon-unlock"></i>解绑</a>
            </div>
          </div> 
          <div class="layui-form-item">
            <label class="layui-form-label">小程序OpenID</label>
            <div class="layui-input-block"  >
                <input type="text" class="layui-input" readonly value="<?php echo isset($user['openid_we'])?$user['openid_we']:'';?>" style="width: 300px;display: inline-block;"  autocomplete="off">
                <a href="" class="layui-btn  layui-btn-danger"><i class="am-icon-unlock"></i>解绑</a>
            </div>
          </div> 
          <div class="layui-form-item">
            <label class="layui-form-label"></label>
            <div class="layui-input-block"  >
                <?php 
        $url0=urlencode($siteurl."/redirect?from=wechat&act=binduser&uid=".$user['user_id']);
        $url='https://open.weixin.qq.com/connect/oauth2/authorize?appid='.(isset($set['wechat'])?$set['wechat']['appid']:'').'&redirect_uri='.$url0.'&response_type=code&scope=snsapi_userinfo&state=binduser#wechat_redirect';?>
                <?php echo $url; ?>
               <div id="qrcode"></div> 
            </div>
          </div> 
    </div>

    </div>
 
    </div>
  
<script type="text/javascript">
    $(function(){
        

        $("#qrcode").qrcode({
            render: "canvas", 
            width: 200, //宽度
            height:200, //高度
            text: "<?php echo $url; ?>" //任意内容
        }); 
        $.laydate.render({
            elem:".input-date",
            type:"date",
            value:"<?php echo isset($user['birthday'])?$user['birthday']:'';?>"
        })
        $.form.on('submit(*)', function(data){
            if(data.field.act=="resetpwd"){
                if(data.field.newpass!=data.field.newpass1){
                    $.toast("两次密码不一致");
                    return false;
                }
                data.field.newpass=$.md5(data.field.newpass);
                data.field.newpass1=$.md5(data.field.newpass1);
                data.field.password=$.md5(data.field.password);
                $.action({
                    url:"<?php echo wurl($route); ?>",
                    data:data.field,
                    success:function(ret){
                        if(ret.status>0){
                            $.toast(ret.msg);
                            setTimeout(function(){
                                window.location.reload();
                            })
                        }else{
                            $.alert(ret.error)
                        }
                    }
                })
            }else{
                if(data.field.idcard.length>0){ 
                    var ret=$.checkType(data.field.idcard,"idcard"); 
                    if(!ret){
                        $.toast("请输入正确的身份证号码");
                        return false;
                    }
                }
                return true;
            }

            return false;
        });
    })
</script> 
<script type="text/javascript" src="/static/js/md5.js"></script>
 
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