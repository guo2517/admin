<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:59:"/www/wwwroot/admin/public/../app/core/view/user/forget.html";i:1581500067;}*/ ?>
<form class="layui-form" id="forget-form" action="<?php echo wurl($route); ?>" method="post" style="width: 370px;padding-top:30px;">
	<input type="hidden" name="act" value="reset" readonly="">
	<div class="layui-form-item">
	    <label class="layui-form-label">用户名</label>
	    <div class="layui-input-block">
	      <input type="text" name="username"  value=""  autocomplete="off" placeholder="请输入名称" class="layui-input">
	    </div>
	</div>
	<div class="layui-form-item">
	    <label class="layui-form-label">邮箱</label>
	    <div class="layui-input-block">
	       <input type="text" name="email" id="forget-email"  value=""  autocomplete="off" placeholder="请输入名称" class="layui-input">
	    </div>
	</div> 
	<div class="layui-form-item">
	    <label class="layui-form-label">验证码</label>
	    <div class="layui-input-block">
	       <input type="text" name="verify" value="" style="width:150px;display: inline-block;vertical-align: top" autocomplete="off" placeholder="请输入名称" class="layui-input">
	       <button class="layui-btn" lay-submit lay-filter="code" id="sendverifybtn">发送验证码</button>
	    </div>
	</div> 
	<div class="layui-form-item">
	    <label class="layui-form-label">新密码</label>
	    <div class="layui-input-block">
	       <input type="password" id="newpass-forget" value=""  autocomplete="off" placeholder="请输入名称" class="layui-input">
	    </div>
	</div> 
</form>
<script type="text/javascript">
	$.form.render();
	$.form.on('submit(code)', function(data){
		console.log(data.field); 
		if($("#sendverifybtn").hasClass("layui-disabled"))return false;
         var email=data.field.email;
         var tr=$.checkType(email,"email");
         console.log(tr);
    	if(tr){
    		data.field.act="sendverify";
    		$.action({
    			url:"<?php echo wurl('forgetpwd'); ?>",
    			data:data.field,
    			loading:true,
    			success:function(ret){
    				if(ret.status>0){
    					$("#sendverifybtn").addClass("layui-disabled");
    					$("#sendverifybtn").text("已发送");
    				}else{
    					$.alert(ret.error);
    				}
    			}
    		})
    	}else{
    		$.toast("邮箱格式不正确");
    	}
    	return false;
    });
</script>