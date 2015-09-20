<?php
namespace Admin\Controller;

class DeptController extends AdminController {
    
    protected $dept_obj;
    
    function _initialize() {
        $this->dept_obj = M('hr_depts');
    }
    
    public function index() {
        $this->page($this->dept_obj);
        $this->display();
    }
    
    public function add() {
        $this->assign('btnText','添加');
        $this->display();
    }
    
    public function modify() {
        $id = intval(I('get.id'));
        $result = $this->dept_obj->find($id);
        if($result == false) $this->timeout();
        $this->assign('btnText','修改');
        $this->assign('data',$result);
        $this->display('add');
    }
    
    public function delete() {
        $id = intval(I('get.id'));
        $this->dept_obj->delete($id);
        $this->success('删除成功！');
    }
    
    public function change() {
        $temp = $this->dept_obj->where('`dept_no`='.$_POST['dept_no'])->find();
        if($temp != false && $temp['dept_no'] != $_POST['dept_no']) $this->errors('部门号已存在！');
        $type = $_POST['type'];
        $_POST['higer_dept'] = empty($_POST['loop_dept_dept_name']) ? 0 :$_POST['loop_dept_dept_no'];
        unset($_POST['loop_dept_dept_no']);
        unset($_POST['loop_dept_dept_name']);
        unset($_POST['type']);
        unset($_SESSION['dept']['flag']);
        $this->dept_obj->create($_POST);
        if($type == '添加') {
            $this->dept_obj->add();
            $this->success('添加部门成功！',1,200,'hr');
        }
        else {
            $this->dept_obj->save();
            $this->success('修改部门信息成功！',1,200,'hr');
        }
    }
    
    public function detail() {
        $this->assign('tree',$this->tree($this->dept_obj->where("dept_stat=1")->select(), 'dept'));
        $this->display('Public/tree');
    }
}