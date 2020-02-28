<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>跳转提示</title>
    <meta name="description" content="">
    <meta name="keywords" content="index">
    <meta name="viewport" content="width=device-width,height=device-height,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="renderer" content="webkit">
    <meta http-equiv="Cache-Control" content="no-siteapp" /> 
    <meta name="apple-mobile-web-app-title" content="" /> 
    <link rel="stylesheet" type="text/css" href="_STATICP/font-awesome/css/font-awesome.css">
    <style type="text/css">
        html,body{width: 100%;height: 100%;background-color:#f2f2f2;margin:0 0;} 
        .bg{
           background-color: #fff;position: fixed; text-align: center;
        }
        .warning{
            color:#DD514C;
        }
        .info{
            color:#3BB4F2;
        }
        .success{
            color:#22B14C;
        } 
        @media screen and (max-width: 600px) {
            .bi{font-size:45px; display: block;}
            .bg{ 
                width:100%; top:calc(50% - 140px);
                border-radius: 0px;padding:40px 0px;
            }
             .description{font-size: 20px;padding:0px 5px;}
            .content{
                display: block;width:calc(100% - 20px); 
                padding:10px 10px;}
            .jumpdesc{margin-top: 10px;}
        }
        @media screen and (min-width: 601px) {
            .bi{font-size: 80px;margin-top:50px;display: inline-block;}
           .bg{
                border-radius: 4px;
                width:400px;height: 300px;left:calc(50% - 200px);top:calc(50% - 150px);
            }
            .content{display: block;width:calc(100% - 20px); height:calc(50% - 20px);padding:10px 10px;position: absolute;bottom:0px;}
             .description{font-size: 20px;padding:10px 5px;}
        }
        
        .title{
            font-size:22px;font-weight: 600;
        }
        .jumpdesc{font-size: 14px;}
        .content i{margin:0px 5px;}
        a{text-decoration-line: none;}
    </style>
</head>
<body>
    <div class="bg">   

        <i class="<?php echo $code == 1?'am-icon-check-square-o info':'am-icon-warning warning'?> bi"></i>
        
        <div class="content ">
            <?php if(intval($wait)<0) $url="#"; ?>
            <a href="<?php echo strlen($url)>0?$url:'javascript:history.go(-1);';?>" id="jump" class="jump"><div class="title "><?php echo isset($title)?$title:"";?></div>
                <?php if(isset($msg)&&strlen($msg)>0){?>
                <div class="description warning"><?php echo $msg;?> </div>
                <?php }?>
                <?php if(isset($wait)&&intval($wait)>0){?>
                <div class="description warning jumpdesc">等待跳转<span class="wait">{$wait}</span>秒</div>
                <?php }?>
            </a> 
        </div> 
        
    </div>
    <script src="_STATICP/js/jquery.min.js"></script> 
</body>
<script type="text/javascript">
    <?php if(isset($wait)&&intval($wait)>0){?>
        var time={$wait*1};
    $(function(){
        var ttt=setInterval(function(){ 
            time=time-1;
            if(parseInt(time)<0){ 
                $("#jump")[0].click();
                clearInterval(ttt);
                return;
            }

            $(".wait").text(time);
        },1000); 
    })
    <?php }?>
</script>
</html>