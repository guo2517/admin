<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:61:"/www/wwwroot/admin/public/../app/core/view/admin/menuset.html";i:1581141565;s:49:"/www/wwwroot/admin/app/core/view/public/head.html";i:1581079660;}*/ ?>
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
    .layui-table{width: 100%;}
    .menu-icon{margin-right: 5px;}
    .menu-type{font-size:13px;color:#1E9FFF;}
    .menu-type-api{color:#5eb95e;}
    .menu-type-permit{color:#F37B1D;}
</style> 
<div class="layui-card-header">菜单管理</div>
<div class="layui-card-body"> 
	<div class="search-plugin"> 
		<div class="search-btn" style="text-align: center;"> 
			<button onclick="addmenu0()" class="layui-btn layui-btn-sm layui-btn-normal">新增</button> 
		</div>
	</div>
	<table class="layui-table" id="table-user">
        <thead>
            <tr> 
                <th>code</th>
                <th>路由</th>
                <th>名称</th>
                <th>类型</th>
                <th>排序</th>
                <th>窗口</th>
                <th>自定义URL</th> 
                
                <th>状态</th>
                <th>操作</th> 
            </tr>
        </thead>
        <tbody>
        	<?php foreach($menus as $k => $v): ?>
            <tr  > 
                <td><?php echo $v['menu_code']; ?></td> 
                 <td><?php echo $v['route']; ?></td> 
                <td><i class="<?php echo $v['icon']; ?> menu-icon"></i><?php echo $v['menu_name']; ?></td>
                <td>
                	<?php if(intval($v['type']) == 1): ?>
                	<span class="menu-type  ">菜单</span>
                	<?php elseif(intval($v['type']) == 2): ?>
                	<span class="menu-type menu-type-permit">权限</span>
                	<?php elseif(intval($v['type']) == 3): ?>
                	<span class="menu-type menu-type-api">api接口</span>
                	<?php endif; ?>
                </td>
                <td><?php echo $v['sort']; ?></td>
                <td><?php echo $v['url']; ?></td>
                <td><?php echo $v['target']; ?></td>
                <td>
                	<?php if(intval($v['status']) == 1): ?>
                	<span class="menu-type menu-type-api">启用</span> 
                	<?php else: ?>
                	<span class="menu-type menu-type-permit">禁用</span>
                	<?php endif; ?>
		                </td> 
                <td>  

                        	<a  href="javascript:void(0);" onclick="addmenu1(this)" data-pid="<?php echo $v['id']; ?>" class="layui-btn layui-btn-xs" data-pname="<?php echo $v['menu_name']; ?>">
                                <i class="am-icon-pencil"></i> 添加菜单/权限
                            </a> 
                        <a href="javascript:void(0);" onclick="eidtmenu(this)" data-id="<?php echo $v['id']; ?>" data-pid="0" data-pname="无" data-name="<?php echo $v['menu_name']; ?>"  data-url="<?php echo $v['url']; ?>" data-target="<?php echo $v['target']; ?>" data-icon="<?php echo $v['icon']; ?>" data-menu_code="<?php echo $v['menu_code']; ?>" data-action="<?php echo $v['action']; ?>" data-check_perm="<?php echo $v['check_perm']; ?>" data-sort="<?php echo $v['sort']; ?>" data-type="<?php echo $v['type']; ?>" data-route="<?php echo $v['route']; ?>" class="layui-btn layui-btn-xs">
                            <i class="am-icon-pencil" ></i> 编辑
                        </a>
                        <?php if($v['module_id'] != 0): ?>
                        <a href="javascript:void(0);" data-id="<?php echo $v['id']; ?>" data-name="<?php echo $v['menu_name']; ?>"   class="delbtn layui-btn layui-btn-xs layui-btn-danger" onclick="delmenu(this)" >
                            <i class="am-icon-trash"></i> 删除
                        </a>
                        <?php endif; ?> 
                </td>
            </tr>
            <?php if(count($v['children'])>0){foreach($v['children'] as $k2 => $v2): ?>
                    <tr  > 
                    	<td><?php echo $v2['menu_code']; ?></td> 
                    	 <td><?php echo $v2['route']; ?></td> 
                        <td><span style="padding:0px 25px;"></span><i class="<?php echo $v2['icon']; ?> menu-icon"></i><?php echo $v2['menu_name']; ?></td> 
                        <td>
                        	<?php if(intval($v2['type']) == 1): ?>
		                	<span class="menu-type  ">菜单</span>
		                	<?php elseif(intval($v2['type']) == 2): ?>
		                	<span class="menu-type menu-type-permit">权限</span>
		                	<?php elseif(intval($v2['type']) == 3): ?>
		                	<span class="menu-type menu-type-api">api接口</span>
		                	<?php endif; ?>
                        </td>
                        <td><?php echo $v2['sort']; ?></td>
                        <td><?php echo $v2['url']; ?></td>
                        <td><?php echo $v2['target']; ?></td>
                        <td>
                        	<?php if(intval($v2['status']) == 1): ?>
		                	<span class="menu-type menu-type-api">启用</span> 
		                	<?php else: ?>
		                	<span class="menu-type menu-type-permit">禁用</span>
		                	<?php endif; ?>
                        </td> 
                        <td> 
                            	 <a  href="javascript:void(0);" onclick="addmenu1(this)" data-pid="<?php echo $v2['id']; ?>" class="layui-btn layui-btn-xs" data-pname="<?php echo $v2['menu_name']; ?>">
                                <i class="am-icon-pencil"></i> 添加菜单/权限
                            </a> 
                                <a href="javascript:void(0);" onclick="eidtmenu(this)" data-id="<?php echo $v2['id']; ?>" data-pid="<?php echo $v2['pid']; ?>" data-name="<?php echo $v2['menu_name']; ?>" data-pname="<?php echo $v['menu_name']; ?>"  data-url="<?php echo $v2['url']; ?>" data-target="<?php echo $v2['target']; ?>" data-icon="<?php echo $v2['icon']; ?>"  data-menu_code="<?php echo $v2['menu_code']; ?>" data-action="<?php echo $v2['action']; ?>" data-check_perm="<?php echo $v2['check_perm']; ?>"   data-type="<?php echo $v2['type']; ?>" data-route="<?php echo $v2['route']; ?>"  data-sort="<?php echo $v2['sort']; ?>" class="layui-btn layui-btn-xs">
                                    <i class="am-icon-pencil"></i> 编辑
                                </a>
                                 <?php if($v2['module_id'] != 0): ?>
                                <a href="javascript:void(0);" data-id="<?php echo $v2['id']; ?>" data-name="<?php echo $v2['menu_name']; ?>"   class="delbtn layui-btn layui-btn-xs layui-btn-danger" onclick="delmenu(this)" >
                                    <i class="am-icon-trash"></i> 删除
                                </a>
                                <?php endif; ?> 
                        </td>
                    </tr>
                      <?php if(count($v2['children'])>0){foreach($v2['children'] as $k3 => $v3): ?>
                    <tr  > 
                    	<td><?php echo $v3['menu_code']; ?></td> 
                    	 <td><?php echo $v3['route']; ?></td> 
                        <td><span style="padding:0px 50px;"></span><i class="<?php echo $v3['icon']; ?> menu-icon"></i><?php echo $v3['menu_name']; ?></td> 
                        <td>
                        	<?php if(intval($v3['type']) == 1): ?>
		                	<span class="menu-type">菜单</span>
		                	<?php elseif(intval($v3['type']) == 2): ?>
		                	<span class="menu-type menu-type-permit">权限</span>
		                	<?php elseif(intval($v3['type']) == 3): ?>
		                	<span class="menu-type menu-type-api">api接口</span>
		                	<?php endif; ?>
                        </td>
                        <td><?php echo $v3['sort']; ?></td>
                        <td><?php echo $v3['url']; ?></td>
                        <td><?php echo $v3['target']; ?></td>
                        <td>
                        	<?php if(intval($v3['status']) == 1): ?>
		                	<span class="menu-type menu-type-api">启用</span> 
		                	<?php else: ?>
		                	<span class="menu-type menu-type-permit">禁用</span>
		                	<?php endif; ?>
		                </td> 
                        <td> 
                            	 
                                <a href="javascript:void(0);" onclick="eidtmenu(this)" data-id="<?php echo $v3['id']; ?>" data-pid="<?php echo $v3['pid']; ?>" data-name="<?php echo $v3['menu_name']; ?>" data-pname="<?php echo $v2['menu_name']; ?>"  data-url="<?php echo $v3['url']; ?>" data-target="<?php echo $v3['target']; ?>" data-icon="<?php echo $v3['icon']; ?>"  data-menu_code="<?php echo $v3['menu_code']; ?>" data-action="<?php echo $v3['action']; ?>" data-check_perm="<?php echo $v3['check_perm']; ?>"   data-type="<?php echo $v3['type']; ?>" data-route="<?php echo $v3['route']; ?>"  data-sort="<?php echo $v3['sort']; ?>" class="layui-btn layui-btn-xs">
                                    <i class="am-icon-pencil"></i> 编辑
                                </a>
                                 <?php if($v3['module_id'] != 0): ?>
                                <a href="javascript:void(0);" data-id="<?php echo $v3['id']; ?>" data-name="<?php echo $v3['menu_name']; ?>"   class="delbtn layui-btn layui-btn-xs layui-btn-danger" onclick="delmenu(this)" >
                                    <i class="am-icon-trash"></i> 删除
                                </a>
                                <?php endif; ?> 
                        </td>
                    </tr>
                <?php endforeach; } endforeach; } endforeach; ?>
           
             
            <!-- more data -->
        </tbody>
    </table>
</div>  
 
<script type="text/javascript">
	function addmenu0(pid,pname){
		var options=[
			{name:'menu_name',placeholder:"请输入名称",type:'text',label:"名称"},
			{name:'route',placeholder:"路由",type:'text',label:"路由"},

			{name:'action',placeholder:"执行模块/控制器/方法",type:'text',label:"动作"},
			{name:'menu_code',placeholder:"唯一权限编码 code",type:'text',label:"编码"},
			{name:'url',placeholder:"打开外部链接",type:'text',label:"外部链接"},
			{name:'check_perm',placeholder:"",type:'checkbox',label:"启用权限",options:[
				{label:"",value:"1",checked:0}
			]}, 
			{name:'icon',placeholder:"css样式类",type:'text',label:"样式类"},
			{name:'type',placeholder:"菜单类型",type:'select',label:"类型",options:[
				{text:"菜单",value:"1"},{text:"权限",value:"2"},{text:"api",value:"3"}
			]},
			{name:'sort',placeholder:"整数，越大越靠前",type:'number',label:"排序"},
			{name:'target',placeholder:"0表示当前，1表示新窗口",type:'number',label:"窗口"}
		];
		if(typeof(pid)!="undefined"){
			options.unshift({name:'p_id',placeholder:"",type:'hidden',value:pid});
			options.unshift({name:'pname',disabled:"disabled",placeholder:"父级菜单",type:'text',value:pname,label:"父级菜单"});
		}else{
			options.unshift({name:'p_id',placeholder:"",type:'hidden',value:0});
		}
		$.prompts(options,function(data){
			 
			data.act="addmenu0";
			$.action({
				url:"<?php echo wurl($route); ?>",
				data:data,
				type:"post", 
				dataType:"json",
				success:function(res){
					if(parseInt(res.status)==1){
						$.alert("添加成功");
						setTimeout(function(){
							history.go(0)
						},2000)
					}else{
						setTimeout(function(){
							$.alert(res.error);
						},500)
						
					}
					
				}
			})
		},{title:"新增菜单",height:"65%"})
	}
	function addmenu1(a){

		var pid=$(a).attr("data-pid"),pname=$(a).attr("data-pname");
		addmenu0(pid,pname); 
	}

	function eidtmenu(a){
		 
		var options=[
			{name:'id',type:'hidden',value:$(a).attr("data-id")}, 
			{name:'pname',placeholder:"",type:'text',label:"父级菜单",disabled:"disabled",value:$(a).attr("data-pname")}, 
			{name:'menu_name',placeholder:"请输入名称",type:'text',label:"名称",value:$(a).attr("data-name")},
			{name:'route',placeholder:"路由",type:'text',label:"路由",value:$(a).attr("data-route")},  
			{name:'action',placeholder:"执行模块/控制器/方法",type:'text',label:"执行动作",value:$(a).attr("data-action")}, 
			{name:'menu_code',placeholder:"唯一权限验证码",type:'text',label:"编码",value:$(a).attr("data-menu_code")},

			
			{name:'p_id',type:'hidden',value:$(a).attr("data-pid")},
			
			{name:'url',placeholder:"链接，不含http时按照tp路由处理",type:'text',label:"外部链接",value:$(a).attr("data-url")}, 
			{name:'check_perm',placeholder:"",type:'checkbox',value:0,label:"启用权限",options:[
				{label:"",value:1,checked:$(a).attr("data-check_perm")}
			]}, 
			{name:'type',placeholder:"菜单类型",type:'select',label:"类型",value:$(a).attr("data-type"),options:[
				{text:"菜单",value:"1"},{text:"权限",value:"2"},{text:"api",value:"3"}
			],value:$(a).attr("data-type")},
			{name:'icon',placeholder:"请输入名称",type:'text',label:"样式类名",value:$(a).attr("data-icon")},
			{name:'sort',placeholder:"整数，越大越靠前",type:'number',label:"排序",value:$(a).attr("data-sort")},
			{name:'target',placeholder:"0表示当前，1表示新窗口",type:'number',label:"窗口",value:$(a).attr("data-target")}
		];

		$.prompts(options,function(data){

			data.act="savemenu";
			$.action({
				url:"<?php echo wurl($route); ?>",
				data:data,
				type:"post",
				dataType:"json", 
				success:function(res){
					$.alert("修改成功");
					setTimeout(function(){
						history.go(0)
					},2000)
				}
			})
		},{title:"新增菜单",height:"65%"})
	}
	function delmenu(a){
		var id=$(a).attr("data-id"),name=$(a).attr("data-name"); 
		 
        $.confirm("是否删除 "+name +" ?",function(){

            $.action({
				url:"<?php echo wurl($route); ?>", 
				type:"post", 
				data:{id:id,act:"del"},
				dataType:"json",
				success:function(res){
					if(res.status==1){
						$.toast("删除成功");
						setTimeout(function(){
							history.go(0)
						},2000)
					}
					
				}
			}) 
        },{title:"提示"});
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