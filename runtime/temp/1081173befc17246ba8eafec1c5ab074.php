<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:57:"/www/wwwroot/admin/public/../app/core/view/admin/log.html";i:1581135186;s:49:"/www/wwwroot/admin/app/core/view/public/head.html";i:1581079660;}*/ ?>
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
     
<div class="layui-card-header">角色管理</div>
<div class="layui-card-body"> 
    <div class="search-plugin">
        <form class="search-form layui-form">
        <div class="search-body">
            <?php echo isset($search)?$search:''?>
            <div class="search-btn">
                <button class="layui-btn layui-btn-sm layui-btn-normal"><i class="am-icon-search"></i>搜索</button>  
            </div>
        </div>
        </form>
    </div>
    <table class="layui-table" id="table-log"> 
         <thead> 
            <tr> 
                <th>No</th>
                <th  class="sort-field" data-field="rid">时间</th>
                <th  class="sort-field" data-field="nickname">摘要</th>
                <th  class="sort-field" data-field="nickname">用户</th> 
                <th  class="sort-field" data-field="username">详细</th>  
                <th>操作</th> 
            </tr>
        </thead>
        <tbody id="vubody">
             <tr  v-for="(item,index) in logs" class="gradeX" > 
                <td v-html="index+1"></td> 
                <td v-html="item.time"></td>
                <td v-html="item.action"></td>
                <td >
                	<span v-html="item.username"></span>
                	<span v-html="item.code" class="layui-badge layui-bg-black" v-if="item.code!=null"></span>
                </td> 
                 
                <td v-html="item.log"></td>  
                <td>
                    <div class="tpl-table-black-operation">
                        <a href="javascript:void(0);" @click="detail(index)"   >
                            <i class="am-icon-pencil"></i> 详细
                        </a> 
                    </div>
                </td>
            </tr>  
        </tbody>
        <tfoot>
            <tr>
                <td id="page-log" colspan="10">
                    
                </td>
            </tr>
        </tfoot>
    </table>
</div>
<script type="text/javascript">
    var vu=new Vue({
         el:"#vubody",
        data:{
            logs:[], 
        },
         methods:{  
            detail:function(index){
                var id=vu.logs[index].id;
                var text=vu.logs[index].name;
                $.action({
                    url:"<?php echo wurl($route); ?>",
                    type:"post",
                    dataType:"html", 
                    data:{rid:id,act:"getDetails"},
                    success:function(res){
                        $.poppage(res,function(){
                             
                        },{title:"日志详情",width:"800px",showOk:true});
                        $.form.render();
                    },
                    error:function(res){
                        $.poppage(res.responseText);

                    }
                }) 

            } 
             
        }
    })
    $(function(){ 
        $.searchRender();
         
        var table=new TableModel({
            url:"<?php echo wurl($route); ?>",
            id:"#table-log",
            page:"#page-log",
            sort:".sort-field",
            form:".search-form",
            extra:{
                act:"data"
            }
        }); 
        table.search=function(data){
            if(data.status>0){
                vu.logs=data.data
                console.log(data.data)
            }else{
                $.alert(data.error)
            } 
        };
        table.doSearch();
    })  
     
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