<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:61:"/www/wwwroot/admin/public/../app/core/view/admin/roleset.html";i:1581500991;s:49:"/www/wwwroot/admin/app/core/view/public/head.html";i:1581079660;}*/ ?>
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
    .goodspic{width:60px;height: 60px;border-radius: 3px;}
</style>
<div class="layui-card-header">角色管理</div>
<div class="layui-card-body"> 
	<div class="search-plugin">
		<form class="search-form layui-form">
		<div class="search-body">
			<?php echo $search; ?> 
			<div class="search-btn">
				<button class="layui-btn layui-btn-sm layui-btn-normal"><i class="am-icon-search"></i>搜索</button>
				<button class="layui-btn layui-btn-sm layui-btn-normal">新增</button>
				<button class="layui-btn layui-btn-sm layui-btn-normal">导入</button>
			</div>
		</div>
		</form>
	</div>
	<table class="layui-table" id="table-user"> 
        <thead>
            <tr> 
                <th>No</th>
                <th>名称</th>
                <th>状态</th> 
                <th>操作</th> 
            </tr>
        </thead>
        <tbody id="vubody">
        	 
            <tr v-for="(item,index) in roles"  > 
                <td v-html="index*1+1"></td> 
                <td v-html="item.name"></td> 
                <td> 
                	<span class="am-badge am-badge-secondary am-radius">启用</span> 
                	<span class="am-badge am-radius">禁用</span> 
                </td> 
                <td> 
                     	<a class="layui-btn layui-btn-xs" href="javascript:void(0);" @click="getDetail(index)" >
                            <i class="am-icon-lock"></i> 权限
                        </a>
                        <a class="layui-btn layui-btn-xs" href="javascript:void(0);" @click="eidtrole(index)"  >
                            <i class="am-icon-pencil"></i> 编辑
                        </a>
                        <a class="layui-btn layui-btn-xs layui-btn-danger" href="javascript:void(0);" @click="delrole(index)" >
                            <i class="am-icon-trash"></i> 删除
                        </a> 
                </td>
            </tr>  
        </tbody>
        <tfoot>
        	<tr>
        		<td id="page-user" colspan="4">
        			
        		</td>
        	</tr>
        </tfoot>
    </table> 
    </div>
</div>

 
<script type="text/javascript">
	var vu=new Vue({
		 el:"#vubody",
        data:{
            roles:[], 
        },
         methods:{  
           	getDetail(index){
				var id=vu.roles[index].id;
				var text=vu.roles[index].name;
				$.action({
					url:"<?php echo wurl($route); ?>",
					type:"post",
					dataType:"html", 
					data:{rid:id,act:"getDetails"},
					success:function(res){
						$.poppage(res,function(){
							var datas=$("#role-menu-form").serializeArray();
							$.action({
								url:"<?php echo wurl($route); ?>",
								type:"post",
								data:datas, 
								dataType:"json",
								success:function(ret){
									if(ret.status==1){
										$.toast("保存成功");
										setTimeout(function(){
											$.layer.closeAll();
										},1500)	
										
									}else{
										$.alert(ret.error);
									} 

								}
							})
						},{title:text+" 的权限",width:"800px",showOk:true});
						$.plusRender(); 
					},
					error:function(res){
						$.poppage(res.responseText);

					}
				}) 

			},
			delrole:function(index){
				var id=vu.roles[index].id; 
				var name=vu.roles[index].name; 
		        $.confirm("是否删除 "+ name +" ?",function(){
		            $.action({
						url:"<?php echo wurl('admin/main/delrole'); ?>mid="+id , 
						type:"get",
						dataType:"json",
						success:function(res){
							$.alert("删除成功");
							setTimeout(function(){
								history.go(0)
							},2000)
						}
					});
		        },{title:"删除确认",danger:1});
			},
			eidtrole:function(index){ 
				var r=vu.roles[index];
				var options=[
					{name:'id',type:'hidden',value:r.id},
					{name:'act',type:"hidden",value:"saveRole"}, 
					{name:'name',placeholder:"",type:'text',label:"角色名称",value:r.name}, 
					{name:"status",type:"select",label:"状态",value:r.status,options:[
						{'text':"启用",value:"1"},{'text':"禁用",value:"0"}
					]} 
				]; 
				$.prompts(options,function(data){ 
					console.log(data);
					$.action({
						url:"<?php echo wurl($route); ?>", 
						data:data,
						type:"post",
						dataType:"json",
						success:function(res){
							if(ret.status==1){
								$.alert("修改成功");
								setTimeout(function(){
									history.go(0)
								},2000)
							}else{
								$.alert(res.error);
							}
							
						}
					})
				},{title:"编辑角色",height:"200px"})
			}
        }
	})
	$(function(){ 
		$.searchRender();
		var table=new TableModel({
			url:"<?php echo wurl($route); ?>",
			id:"#table-user",
			page:"#page-user",
			form:".search-form",
			extra:{
				act:"data"
			}
		}); 
		table.search=function(data){
			if(data.status>0){
				vu.roles=data.data
				console.log(data.data)
			}else{
				$.alert(data.error)
			} 
		};
		table.doSearch();
	})  
	function addrole(a){
		var pid=$(a).attr("data-pid"),pname=$(a).attr("data-pname");
		console.log(pid);
		$.prompts("新增角色",[
			{name:'id',type:'hidden',value:0},
			{name:'act',type:"hidden",value:"add"}, 
			{name:'name',placeholder:"",type:'text',label:"角色名称",value:""}, 
			{name:"status",type:"select",label:"状态",options:[
				{'text':"启用",value:"1"},{'text':"禁用",value:"0"}
			]} 
		],function(data){
			console.log(data);
			$.action({
				url:"<?php echo wurl($route); ?>",
				data:data,
				type:"post", 
				dataType:"json",
				success:function(res){
					if(ret.status==1){
						$.alert("修改成功");
						setTimeout(function(){
							history.go(0)
						},2000)
					}else{
						$.alert(res.error);
					}
				}
			})
		})
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