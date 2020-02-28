<?php 
namespace tool\email;  
use tool\email\PHPMailer;
class Email{
	private $conf;
	public function __construct($conf){
		
		$this->conf=$conf;
	}
	/** 
	 * 发送邮件 
	 * @param  touser 收件人
	 * @param  body   发送内容
	 * @param  config 配置，默认使用qq邮箱465端口
	 * @param  config 配置，默认"系统通知"
	 * @param  copys 抄送人 保证效率最多设置5个，["user2@qq.com","user3@qq.com"]
	 * 栗子：email("user1@qq.com","你的验证码是123",['username'=>'admin@zsk.com','password'=>"sdfjpewrg"])
	 */
	function send($touser,$body,$title="系统通知",$copys=[]){

		 
		$config=$this->conf;
	  if(empty($config['host'])){
	    $config['host']="smtp.qq.com";
	  }
	  if(empty($config['port'])){
	    $config['port']=465;
	  } 
	  $mail = new PHPMailer(); 
	  $mail->isSMTP();// 使用SMTP服务  
	  $mail->CharSet = "utf8";// 编码格式为utf8，不设置编码的话，中文会出现乱码  
	  $mail->Host = "smtp.qq.com";// 发送方的SMTP服务器地址  
	  $mail->SMTPAuth = true;// 是否使用身份验证  
	  $mail->Username =$config['username'];/// 发送方的163邮箱用户名，就是你申请163的SMTP服务使用的163邮箱 
	  $mail->Password =$config['password'];// 发送方的邮箱密码，注意用163邮箱这里填写的是“客户端授权密码”而不是邮箱的登录密码！  
	  $mail->SMTPSecure = "ssl";// 使用ssl协议方式 
	  $mail->Port = 465;// 163邮箱的ssl协议方式端口号是465/994  

	  $mail->setFrom($config['username'],"系统通知");// 设置发件人信息，如邮件格式说明中的发件人，这里会显示为Mailer(xxxx@163.com），Mailer是当做名字显示  
	  $mail->addAddress($touser,'用户');// 设置收件人信息，如邮件格式说明中的收件人，这里会显示为Liang(yyyy@163.com)  
	  $mail->addReplyTo($config['username'],"Reply");// 设置回复人信息，指的是收件人收到邮件后，如果要回复，回复邮件将发送到的邮箱地址  
	  if(count($copys)>0&&count($copys)<6){
	    foreach ($copys as $key => $val) {
	      $mail->addCC($val);
	    }
	  }
	  //$mail->addCC("xxx@163.com");// 设置邮件抄送人，可以只写地址，上述的设置也可以只写地址(这个人也能收到邮件)  
	  //$mail->addBCC("xxx@163.com");// 设置秘密抄送人(这个人也能收到邮件)  
	  //$mail->addAttachment("bug0.jpg");// 添加附件  

	  $mail->Subject = $title;// 邮件标题   

	  $mail->Body =$body;// 邮件正文  
	  //$mail->AltBody = "This is the plain text纯文本";// 这个是设置纯文本方式显示的正文内容，如果不支持Html方式，就会用到这个，基本无用  

	 if(!$mail->send()){// 发送邮件    
	    return ['status'=>false,"error"=>$mail->ErrorInfo];// 输出错误信息   
	  }else{  
	    return ["status"=>true];  //成功
	  }  
	}
}