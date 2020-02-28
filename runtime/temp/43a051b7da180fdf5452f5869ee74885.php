<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:58:"/www/wwwroot/admin/public/../app/core/view/admin/main.html";i:1581141206;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php if(isset($site)){echo isset($site['title'])?$site['title']:"管理系统";}?></title>
    <meta name="description" content="">
    <meta name="keywords" content="index">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="renderer" content="webkit">
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <link rel="icon" type="image/png" href="_ASSETSP/i/favicon.png">
    <link rel="apple-touch-icon-precomposed" href="_ASSETSP/i/app-icon72x72@2x.png">
    <meta name="apple-mobile-web-app-title" content="Amaze UI" /> 
    <link rel="stylesheet" href="/static/layui/css/layui.css" /> 
    <link rel="stylesheet" type="text/css" href="/static/font-awesome/css/font-awesome.css">
    <style type="text/css">
        @media screen and (max-width: 750px){
            html,body{width: 1280px!important;height: 100%!important;} 
        }
        
        .layui-nav-tree i{padding-right: 10px;}
        .layui-nav-child dd a{text-align: center;font-size: 13.7px;}
        .layui-layout-left .layui-nav-bar{display: none;}
        .tpl-target{padding:0px 10px;background-color:#4A4F5F;height: 42px;margin-top:8px;line-height: 42px!important;margin-right: 5px; cursor: pointer;}
        .tpl-target .tpl-target-title ,.tpl-target i{display: inline-block;vertical-align: top;}
        .tpl-target-active{background-color:#009688;}
        .tpl-webpage{
            width: 100%;height: 100%;display: none;background-color: #f2f2f2;
        }
        .tpl-webpage-active{
            display: block!important;
        }
        .webpage-frame{
            border:none!important;background-color: #f2f2f2;padding:10px 10px 0px 10px;
            width: calc(100% - 20px);height: calc(100% - 10px);border-radius: 4px;
            overflow-x: hiddel;
        }
        .webpage-frame html{
            background-color: #fff;
        }
        .name .layui-badge{
          position: relative;top:0;border-radius:6px;
        }
        #mainbody{overflow-y: hidden;overflow-x: hidden;}
    </style>
    <script src="/static/js/jquery.min.js"></script>
    <script src="/static/layui/layui.js" type="text/javascript"></script>
    <script src="/static/js/jquery.plus.js"></script>
    <script type="text/javascript"> 
      $(function(){
        $(".layui-nav-item").unbind("mouseover");
        $(".sidebar-nav-target").bind("click",function(){
          var urll=$(this).data("url"),target=$(this).data("target"),code=$(this).data("code");
          var html=' <li class="layui-nav-item tpl-target" data-url="'+urll+'" data-code="'+code+'" data-target="'+target+'"  ><div  class="tpl-target-title inline" >'+$(this).text()+'</div> <i class="am-icon-close inline"></i> </li>';
          var item=$(".tpl-header-target").find(".tpl-target[data-code='"+code+"']");
          if(item.length>0){
            item.eq(0).click();
          }else{
            if($('.tpl-header-target .tpl-target').length>9){
              $.toast("开启页面太多，请关闭后重试");return false;
            }
            $(".layui-layout-left").append(html); 
          }
          $(".tpl-target").unbind("click").bind("click",function(){
              $(this).parent().find(".tpl-target-active").removeClass("tpl-target-active");
              $(this).addClass("tpl-target-active");
              var urll=$(this).data("url"),target=$(this).data("target"),code=$(this).data("code");
              var page=$(".tpl-webpage[data-code='"+code+"']"); 
              console.log(urll);
              if(page.length>0){
                if(page.eq(0).hasClass(".tpl-webpage-active")){return true;}
                $('.tpl-webpage-active').removeClass("tpl-webpage-active");
                page.eq(0).addClass("tpl-webpage-active");
              }else{ 
                if(urll==undefined||urll==""){
                    urll=$.wurl(code);
                }
                 $('.tpl-webpage-active').removeClass("tpl-webpage-active"); 
                var html='<div class="tpl-webpage tpl-webpage-active webpage-'+code+'" data-code="'+code+'" data-url="'+urll+'"><iframe name="'+code+'" src="'+urll+'"   class="webpage-frame"></iframe></div>';
                 $("#mainbody").append(html);
            
                
              }
              
          });
          $(".tpl-target .am-icon-close").unbind("click").bind("click",function(){ 
              var code=$(this).parent().data("code");
              $(this).parent().remove();
              $(".webpage-"+code).remove();
              $(".tpl-target:last-child")[0].click();
          });
          $(".tpl-header-target").find(".tpl-target[data-code='"+code+"']").eq(0).click();
        })
      })
    </script>
</head>
<body class="layui-layout-body">
<div class="layui-layout layui-layout-admin">
  <div class="layui-header">
    <div class="layui-logo"><?php if(isset($site)){echo isset($site['title'])?$site['title']:"管理系统";}?></div>
    <!-- 头部区域（可配合layui已有的水平导航） -->
    <ul class="layui-nav layui-layout-left tpl-header-target">
      <!-- <li class="layui-nav-item tpl-target" data-url="" data-code="" data-target=""  >
        <div  class="tpl-target-title inline" >公告</div> <i class="am-icon-close inline"></i> 
     </li> -->
    </ul>
    <ul class="layui-nav layui-layout-right">
      <li class="layui-nav-item">
        <a href="javascript:;">
          <img src="<?php echo $user['avatar']; ?>" class="layui-nav-img">
          <span class="name">
          <?php if(!empty($user['truename'])){echo $user['truename'];}else if(!empty($user['nickname'])){echo $user['nickname'];}?> 
         
             <!-- <span class="layui-badge layui-bg-orange">1</span> -->
          </span>
         
        </a>
        <dl class="layui-nav-child">
          <dd><a href="javascript:void(0);"  data-url="" data-code="userinfo" class="sidebar-nav-target">基本资料</a></dd>  
        </dl>
      </li> 
      <li class="layui-nav-item"><a href="/login">退出</a></li>
    </ul>
  </div>
  
  <div class="layui-side layui-bg-black">
    <div class="layui-side-scroll">
      <!-- 左侧导航区域（可配合layui已有的垂直导航） -->
      <ul class="layui-nav layui-nav-tree"  lay-filter="test">
        <?php if(is_array($menus) || $menus instanceof \think\Collection || $menus instanceof \think\Paginator): if( count($menus)==0 ) : echo "" ;else: foreach($menus as $key=>$item): if(count($item['children'])<1): ?>
            <li class="layui-nav-item"><a href="javascript:void(0);"  data-code="<?php echo $item['route']; ?>" data-url="<?php echo $item['url']; ?>" data-target="<?php echo $item['target']; ?>"  class="sidebar-nav-target"><i class="<?php echo (isset($item['icon'])?$item['icon']:'am-icon-cubes');?> sidebar-nav-link-logo"></i><?php echo $item['menu_name']; ?></a></li>
            <?php else: ?>
             <li class="layui-nav-item ">
              <a class="" href="javascript:void(0);" ><i class="<?php echo (isset($item['icon'])?$item['icon']:'am-icon-cubes');?> sidebar-nav-link-logo"></i><?php echo $item['menu_name']; ?></a>
              <dl class="layui-nav-child">
                 <?php if(is_array($item['children']) || $item['children'] instanceof \think\Collection || $item['children'] instanceof \think\Paginator): if( count($item['children'])==0 ) : echo "" ;else: foreach($item['children'] as $key=>$m): ?>
                <dd><a href="javascript:void(0);"  class="sidebar-nav-target" data-code="<?php echo $m['route']; ?>" data-url="<?php echo $m['url']; ?>" data-target="<?php echo $m['target']; ?>"><?php echo $m['menu_name']; ?></a></dd>
                 <?php endforeach; endif; else: echo "" ;endif; ?>
              </dl>
            </li>
            <?php endif; endforeach; endif; else: echo "" ;endif; ?> 
        
      </ul>
    </div>
  </div>
  
  <div class="layui-body" id="mainbody">
    <!-- 内容主体区域 -->
     
  </div>
  
  <div class="layui-footer">
    <!-- 底部固定区域 -->
    <?php if(isset($site)){echo isset($site['copyright'])?$site['copyright']:"";}?>
  </div>
</div> 
<script>
//JavaScript代码区域
layui.use('element', function(){
  var element = layui.element;
  
});
</script>
</body>

</html>