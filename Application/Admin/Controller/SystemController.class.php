<?php
namespace Admin\Controller;

class SystemController extends AdminController {
    
    protected $state_obj;
    
    function _initialize() {
        if(!hasRight(5000)) exit;
        $this->state_obj = M('sys_state');
    }
    
    public function index() {
        $result = $this->state_obj->find();
        if($result == false) {
            $this->alert('未找到系统设置数据，请重新安装此产品。');
        }
        $this->assign('data', $result);
        $this->display();
    }
    
    public function save() {
        if($this->state_obj->where('1=1')->save($_POST) === false) {
            $this->errors('操作失败，请检查产品完整性。');
        };
        $this->success('系统参数保存成功！');
    }
    
    public function runtime() {
        $info = array(
            '操作系统'=>PHP_OS,
            '运行环境'=>$_SERVER["SERVER_SOFTWARE"],
            'PHP版本'=>PHP_VERSION,
            'MySQL版本'=>$this->mysql_version(),
            'ThinkPHP版本'=>THINK_VERSION,
            'PHP运行方式'=>php_sapi_name(),
            '上传附件限制'=>ini_get('upload_max_filesize'),
            '执行时间限制'=>ini_get('max_execution_time').'秒',
            '服务器时间'=>date("Y年n月j日 H:i:s"),
            '服务器端口'=>getenv('SERVER_PORT'),
            '服务器语言'=>$_SERVER['HTTP_ACCEPT_LANGUAGE'],
            '传输协议'=>getenv('SERVER_PROTOCOL'),
            '北京时间'=>gmdate("Y年n月j日 H:i:s",time()+8*3600),
            '服务器域名/IP'=>$_SERVER['SERVER_NAME'].' [ '.gethostbyname($_SERVER['SERVER_NAME']).' ]',
            '剩余空间'=>round((@disk_free_space(".")/(1024*1024)),2).'M',
            '当前用户'=>get_current_user()
            );
        $this->assign('info',$info);
        $this->display();
    }
    
    public function logs() {
        $this->page(M('hr_log'), array(), '`log_date` desc');
        $this->display();
    }

    public function clean() {
        M()->execute('TRUNCATE TABLE `'.C('DB_PREFIX').'hr_log`');
        $this->success('清空记录成功！');
    }
    
    public function info() {
        phpinfo();
    }
    
    private function mysql_version() {
        $version = M()->query('SELECT version() AS ver');
        return $version[0]['ver'];
    }
}