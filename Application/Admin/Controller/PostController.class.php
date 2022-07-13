<?php
namespace Admin\Controller;

class PostController extends AdminController {
    
    protected $post_obj;
    
    function _initialize() {
        $this->post_obj = M('hr_posts');
    }
    
    public function index() {
        $this->page($this->post_obj);
        $this->display();
    }
    
    public function add() {
        $this->assign('btnText','添加');
        $this->display();
    }
    
    public function modify() {
        $id = intval(I('get.id'));
        $result = $this->post_obj->find($id);
        if ($result == false) $this->timeout();
        $result['post_right'] = json_decode($result['post_right']);
        $this->assign('btnText','修改');
        $this->assign('data',$result);
        $this->display('add');
    }
    
    public function delete() {
        $id = intval(I('get.id'));
        $this->post_obj->delete($id);
        $this->success('删除成功');
    }
    
    public function change() {
        $temp = $this->post_obj->where('`post_no`='.$_POST['post_no'])->find();
        if ($temp != false && $temp['post_no'] != $_POST['post_no']) $this->errors('岗位号已存在');
        $type = $_POST['type'];
        $_POST['higer_post'] = empty($_POST['loop_post_post_name']) ? 0 :$_POST['loop_post_post_no'];
        unset($_POST['loop_post_post_no']);
        unset($_POST['loop_post_post_name']);
        unset($_POST['type']);
        unset($_SESSION['post']['flag']);
        $_POST['post_right'] = json_encode($_POST['post_right']);
        $this->post_obj->create($_POST);
        if ($type == '添加') {
            $this->post_obj->add();
            $this->success('添加岗位成功',1,200,'hr');
        }
        else {
            $this->post_obj->save();
            $this->success('修改岗位信息成功',1,200,'hr');
        }
    }
    
    public function detail() {
        $this->assign('tree',$this->tree($this->post_obj->where("post_stat=1")->select(), 'post'));
        $this->display('Public/tree');
    }
}