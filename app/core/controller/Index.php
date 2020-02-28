<?php
namespace app\core\controller; 
use think\View;
use app\core\controller\Base;
use app\core\model\UserModel;
use app\core\model\SystemModel;
use baidu\BaiduImage; 
use think\Config;
use tool\Excel;
class Index extends Base
{
    public function test(){
       
        $excel=new Excel();
        $um=new UserModel();
        $page=$um->getUsersPage();
        $excel->exportExcel($page['data'],"用户列表",['username','nickname','idcard'],1);
    }
    //此控制器不需要permit权限验证
    public function index()
    { 
        $datas['user']=session('core_user');
        if(intval($datas['user']['role_id'])!=1){
            //普通用户跳转微信端
            header("location:/wechat");
        }else{
            //管理端
            $sys=new SystemModel();
            $menus=$sys->getUserMenus(); 
           
            $datas['menus']=childTree($menus);  
            $datas['site']=$sys->getSetting("site");
            return view("/admin/main",$datas);
        }
        
    }
    public function getToken(){
        $token = $this->request->token('_token','sha1'); 
        $this->jsonReturn(['status'=>1,"_token"=>$token]);
    }
    public function notReadMsg(){
        $sys=new SystemModel();
        $msg=$sys->getNotReadMsg($this->user_id);
        return $msg;
    }
    public function install(){
        if(file_exists(ROOT_PATH."extend/updater/install.lock")){
            $this->success("系统安装已锁定，请删除install.lock文件","/login",3);
            exit("");
        };
        if($this->act=="install"){
           $conf=$this->post;
           if(!isset($conf['db']['port'])||empty($conf['db']['port'])){
            $conf['db']['port']=3306;
           }
            $constr="<?php
return [
    // 日志记录
    'log'            => false,
    // 数据库类型
    'type'            => 'mysql',
    // 服务器地址
    'hostname'        => '".$conf['db']['hostname']."',
    // 数据库名
    'database'        => '".$conf['db']['database']."',
    // 用户名
    'username'        => '".$conf['db']['username']."',
    // 密码
    'password'        => '".$conf['db']['password']."',
    // 端口
    'hostport'        => '".$conf['db']['port']."',
    // 连接dsn
    'dsn'             => '',
    // 数据库连接参数
    'params'          => [],
    // 数据库编码默认采用utf8
    'charset'         => 'utf8',
    // 数据库表前缀
    'prefix'          => '',
    // 数据库调试模式
    'debug'           => true,
    // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
    'deploy'          => 0,
    // 数据库读写是否分离 主从式有效
    'rw_separate'     => false,
    // 读写分离后 主服务器数量
    'master_num'      => 1,
    // 指定从服务器序号
    'slave_no'        => '',
    // 自动读取主库数据
    'read_master'     => false,
    // 是否严格检查字段是否存在
    'fields_strict'   => true,
    // 数据集返回类型
    'resultset_type'  => 'array',
    // 自动写入时间戳字段
    'auto_timestamp'  => false,
    // 时间字段取出后的默认时间格式
    'datetime_format' => 'Y-m-d H:i:s',
    // 是否需要进行SQL性能分析
    'sql_explain'     => false,
];
";  
$sql="CREATE EVENT  IF NOT EXISTS `event_core_cache_expire_delete` ON SCHEDULE EVERY 10 MINUTE STARTS '2020-02-07 14:50:08' ON COMPLETION NOT PRESERVE ENABLE DO DELETE FROM core_cache WHERE expire_time>0 and expire_time<unix_timestamp()";
            $f=fopen(APP_PATH."database.php","w+");
            $ret=fwrite($f,$constr);
            fclose($f);
            if($ret){
               $r= file_put_contents(ROOT_PATH."extend/updater/install.lock","1");
               
               $this->success("安装完成！","/login");
            }else{
                $this->error("写入配置文件失败","index/install");
            }

        }else{
            $config['db']=config("database"); 
            $this->assign($config);
            return view("/public/install");
        }
    }
}
