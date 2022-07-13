<?php
namespace Admin\Controller;

class LoginController extends CommonController {
    
    public function index() {
        if (isLogin()) redirect(__APP__);
        else $this->display();
    }
    
    public function verify() {
        $img = new \Org\Util\Image();
        $img->buildImageVerify(4,1,'gif');
    }
    
    public function check() {
        $no = I('post.oper_no');
        $pwd = I('post.oper_pass');
        $verify = md5(I('post.oper_verify'));
        if (empty($no)||empty($pwd)) {
            $this->errors('提交的信息不完整');
        }
        if ($verify != $_SESSION['verify']) {
           $this->errors('验证码不正确');
        }
        $result = M('hr_opers')->find($no);
        if ($result == false) {
            $this->errors('提交的员工工号不存在');
        }
        if ($result['oper_pass'] != md5($pwd.C('salt'))) {
            $this->logs($no, '【失败】密码错误');
            $this->errors('密码不正确');
        }
        if ($result['oper_stat'] == 0) {
            $this->logs($no, '【失败】已离职');
            $this->errors('你已离职，无法登录系统');
        }
        $state = M('sys_state')->find();
        if ($state['sys_status'] != 1 && $no != 0) {
            $this->logs($no, '【失败】系统关闭');
            $this->errors('系统关闭，请稍后再登录');
        }
        $this->logs($no, '【成功】登录成功');
        $right = M('hr_posts')->field('post_right')->find($result['oper_post_no']);
        $right = json_decode($right['post_right'], true);
        for($i=1000; $i<=6000; $i+=1000) {
            $_SESSION['user_right'][$i] = in_array($i, $right);
        }
        $_SESSION['user_id'] = intval($result['oper_no']);
        $_SESSION['user_name'] = $result['oper_name'];
        $_SESSION['user_dept'] = $result['oper_dept_no'];
        $_SESSION['user_post'] = $result['oper_post_no'];
        $this->success("登录成功");
    }
    
    private function logs($no, $r) {
        $obj_log = M('hr_log');
        $obj_log->create();
        $obj_log->oper_no = $no;
        $obj_log->log_date = date('Y-m-d H:i:s', time());
        $obj_log->log_ip = get_client_ip();
        $obj_log->log_ua = $_SERVER['HTTP_USER_AGENT'];
        $obj_log->log_method = 'Web';
        $obj_log->log_target = 'Admin';
        $obj_log->log_result = $r;
        $obj_log->add();
    }
}