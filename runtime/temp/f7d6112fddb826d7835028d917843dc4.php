<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:66:"/www/wwwroot/admin/public/../app/core/view/admin/roleset-menu.html";i:1581079659;}*/ ?>
<style type="text/css">
	.role-menus{
		text-align: left;
	}
	 
	.role-menus table th{padding:10px 10px;}
	.role-menus table td{vertical-align: top;padding:10px 10px;}
	.role-menus .menu-td-1{border-left: 1px solid #f2f2f2;width: 200px;}
	.role-menus .menu-td-2{width:calc(100% - 200px);}
	.role-menus .td-3{width:150px;}
	.role-menus	input{
	    border-top: 0px;
	    border-left: 0px;
	    border-right: 0px;
	    outline: none;
	}
	.role-menus .form-inline-margin{margin-bottom: 7px;}
</style>
<form id="role-menu-form" class="layui-form" method="post">
	<input type="hidden" name="act" value="saveDetail">
	<input type="hidden" name="rid" value="<?php echo $post['rid']; ?>">
<div class="role-menus">
	
	<table border="0" style="width:90%;">
		<tr>
			<th class="menu-td-1">菜单</th>
			<th class="menu-td-2">二级菜单 \ 权限</th> 
		</tr>
	 
		<?php foreach($menus as $k=>$v): ?>
		<tr style="border:1px solid #f2f2f2">
			<td class="menu-td-1">
				<div class="form-inline-margin"> 
					<table class="layui-table">
						<tr>
							<td>
								<input type="checkbox" name="menu[<?php echo $v['id']; ?>]" lay-skin="primary" title="<?php echo $v['menu_name']; ?>" <?php echo $v['checked']; ?>>
							</td>
						</tr>
					</table>
					 
				 
				</div> 
			</td>
			<td class="menu-td-2">
				<?php if(count($v['children']) >0): foreach($v['children'] as $k2=>$v2): ?> 
				<table class="layui-table">
					<tr>
						<td class="td-3">
							 <input type="checkbox" name="menu[<?php echo $v2['id']; ?>]" class="check-all-<?php echo $v['id']; ?>" lay-skin="primary" title="<?php echo $v2['menu_name']; ?>"  id="check<?php echo $v2['id']; ?>" <?php echo $v2['checked']; ?>  name="menu[<?php echo $v2['id']; ?>]" value="1"> 
						</td>
						
							<?php if(count($v2['children']) >0): ?>
							<td>
							<?php foreach($v2['children'] as $k3=>$v3): ?> 
							<input type="checkbox" name="menu[<?php echo $v3['id']; ?>]" class="check-all-<?php echo $v['id']; ?> check-all-<?php echo $v2['id']; ?>" lay-skin="primary" title="<?php echo $v3['menu_name']; ?>"  id="check<?php echo $v2['id']; ?>" <?php echo $v3['checked']; ?>  name="menu[<?php echo $v3['id']; ?>]" value="1"> 
							<?php endforeach; ?>
							</td>
							<?php endif; ?>
						
					</tr>
				</table>
			 
				
				<?php endforeach; endif; ?>
			</td>
		</tr> 
		
		<?php endforeach; ?>
	 
	</table>
</div>

</form>
<script type="text/javascript"> 
		$.form.render(); 
	$.each($(".role-menus .checkall"),function(){
		$.checkAll(this,".role-menus .check-all-"+$(this).data("id"));
	}) 
</script>