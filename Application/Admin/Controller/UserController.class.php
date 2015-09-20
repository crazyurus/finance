<?php
namespace Admin\Controller;

class UserController extends AdminController {
    
    protected $oper_obj;
    
    function _initialize() {
        $this->oper_obj = M('hr_opers');
    }
    
    public function index() {
        if(!hasRight(2000)) exit;
        $this->page($this->oper_obj,array(),'oper_stat desc');
        $this->display();
    }
    
    public function add() {
        if(!hasRight(2000)) exit;
        $data['oper_stat'] = 1;
        $data['oper_no'] = '';
        $this->assign('data', $data);
        $this->assign('btnText', '添加');
        $this->assign('type', true);
        $this->display('modify');
    }
    
    private function query() {
        $id = isset($_GET['id']) ? $_GET['id'] : intval($_SESSION['user_id']);
        $result = $this->oper_obj->find($id);
        if($result == false) $this->timeout();
        $this->assign('type', isset($_GET['id']));
        $this->assign('data', $result);
    }
    
    public function info() {
        $this->query();
        $this->display();
    }
    
    public function modify() {
        $this->query();
        $this->assign('btnText', '修改');
        $this->display();
    }
    
    public function delete() {
        if(!hasRight(2000)) exit;
        $id = intval(I('get.id'));
        $this->oper_obj->delete($id);
        $this->success('删除成功！');
    }
    
    public function leave() {
        if(!hasRight(2000)) exit;
        $id = intval(I('get.id'));
        $result = $this->oper_obj->find($id);
        $data['oper_no'] = $id;
        $data['oper_stat'] = $result['oper_stat'] == 1 ? 0 : 1;
        $state = $result['oper_stat'] == 1 ? '离职' : '在职';
        $this->oper_obj->token(false)->create($data);
        $this->oper_obj->save();
        $this->success($result['oper_name'].'已修改为<b>'.$state.'</b>状态！');
    }
    
    public function setInfo() {
        $type = $_POST['type'];
        unset($_POST['type']);
        if(isset($_POST['oper_no'])) {
            if(!hasRight(2000)) $this->errors('你没有权限修改信息！');
            $temp = $this->oper_obj->where('`oper_no`='.$_POST['oper_no'])->find();
            if($temp != false && $temp['oper_no'] != $_POST['oper_no']) $this->errors('员工号已存在！');
            $_POST['oper_dept_no'] = $_POST['loop_dept_dept_no'];
            $_POST['oper_post_no'] = $_POST['loop_post_post_no'];
            unset($_POST['loop_dept_dept_no']);
            unset($_POST['loop_post_post_no']);
            unset($_POST['loop_dept_dept_name']);
            unset($_POST['loop_post_post_name']);
            if(!empty($_POST['oper_pass'])) $_POST['oper_pass'] = md5($_POST['oper_pass'].C('salt'));
            if($type == '添加') {
                if(empty($_POST['oper_pass'])) $_POST['oper_pass'] = md5('123456'.C('salt'));
                $this->oper_obj->create($_POST);
                $this->oper_obj->add();
            }
            else {
                if(empty($_POST['oper_pass'])) unset($_POST['oper_pass']);
                $this->oper_obj->create($_POST);
                $this->oper_obj->save();
            }
        }
        else $this->oper_obj->where('oper_no='.$_SESSION['user_id'])->data($_POST)->save();
        $this->success('操作成功！',1,200,'hr');
    }
    
    public function setPwd() {
        $pwd_c = I('post.pwd_c');
        $pwd_1 = I('post.pwd_1');
        $pwd_2 = I('post.pwd_2');
        if(empty($pwd_c)||empty($pwd_1)||empty($pwd_2)) {
            $this->errors('提交的信息不完整！');
        }
        if($pwd_1 !== $pwd_2) {
            $this->errors('两次输入的新密码不一致！');
        }
        $result = $this->oper_obj->find($_SESSION['user_id']);
        if($result == false) $this->timeout();
        if(md5($pwd_c.C('salt')) !== $result['oper_pass']) {
            $this->errors('原密码不正确！');        
        }
        $this->oper_obj->where('oper_no='.$_SESSION['user_id'])->setField('oper_pass', md5($pwd_1.C('salt')));
        $this->success('密码修改成功！',1);
    }
    
    public function password() {
        $this->display();
    }
    
    public function logout() {
        unset($_SESSION['user_id']);
        unset($_SESSION['user_name']);
        unset($_SESSION['user_dept']);
        unset($_SESSION['user_post']);
        unset($_SESSION['user_right']);
        $result = array();
        $result['statusCode'] = 301;
        $result['message'] = '注销成功！';
        $this->ajaxReturn($result);
    }
}