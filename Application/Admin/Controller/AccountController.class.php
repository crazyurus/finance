<?php
namespace Admin\Controller;

class AccountController extends AdminController {
    
    protected $acct_obj;
    
    function _initialize() {
        $this->acct_obj = M('fin_acct');
    }
    
    public function index() {
        if(!hasRight(3000)) exit;        
        $this->page($this->acct_obj);
        $this->display();
    }
    
    public function add() {
        if(!hasRight(3000)) exit;
        $data['acct_balance'] = '0.00';
        $this->assign('btnText','添加');
        $this->assign('data',$data);
        $this->display();
    }
    
    public function modify() {
        if(!hasRight(3000)) exit;
        $id = intval(I('get.id'));
        $result = $this->acct_obj->find($id);
        if($result == false) $this->timeout();
        $this->assign('btnText','修改');
        $this->assign('data',$result);
        $this->display('add');
    }
    
    public function delete() {
        if(!hasRight(3000)) exit;
        $id = intval(I('get.id'));
        $this->acct_obj->delete($id);
        $this->success('删除成功！');
    }
    
    public function change() {
        if(!hasRight(3000)) exit;
        if(empty($_POST['acct_id'])) {
            unset($_POST['acct_id']);
            $this->acct_obj->create($_POST);
            $this->acct_obj->add();
            $this->success('添加账户成功！',1);
        }
        else {
            $this->acct_obj->create($_POST);
            $this->acct_obj->save();
            $this->success('修改账户信息成功！',1);
        }
    }
    
    public function detail() {
        $this->assign('data', $this->acct_obj->find(intval(I('get.id'))));
        $this->assign('right', hasRight(3000));
        $this->display();
    }
}