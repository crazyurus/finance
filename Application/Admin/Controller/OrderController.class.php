<?php
namespace Admin\Controller;

class OrderController extends AdminController {
    
    protected $order_obj;
    
    function _initialize() {
        $this->order_obj = M('ord_info');
    }
    
    public function index() {
        if(S('order_srh')) S('order_srh', null);
        $type = I('path.2')=='' ? 'home' : I('path.2');
        $this->method($type);
        $this->display('index');
    }
    
    public function table() {
        $type = I('path.2')=='' ? 'home' : I('path.2');
        $this->method($type);
        $this->display('table');
    }
    
    public function add() {
        $this->assign('btnText','添加');
        $this->select();
        $this->display();
    }
    
    public function modify($id) {
        $this->assign('btnText','修改');
        $this->assign('data', $this->order_obj->find($id));
        $this->select();
        $this->display('add');
    }
    
    public function delete($id) {
        $this->order_obj->delete($id);
        $this->success('删除订单成功！', 0, 200, 'fin');
    }
    
    public function change() {
        $_POST['sell_total'] = $_POST['person_no']*$_POST['sell_unit']+$_POST['sell_other'];
        $_POST['pay_total'] = $_POST['person_no']*($_POST['pay_unit']+$_POST['insurance_pay'])+$_POST['pay_other'];
        if(isset($_POST['ord_id'])) {
            $result = $this->order_obj->find($_POST['ord_id']);
            if($result == false) $this->errors('无效的操作，请联系管理员！');
            $_POST['sell_difference'] = $_POST['sell_total'] - $result['selled_total'];
            $_POST['pay_difference'] = $_POST['pay_total'] - $result['payed_total'];
            $this->order_obj->create($_POST);
            $this->order_obj->save();
            $this->success('修改订单成功！', 1, 200, 'order');
        }
        else {
            $now = time();
            $sys_obj = M('sys_index');
            $sys_info = $sys_obj->where("`sys_table_name`='ut_ord_info'")->find();
            if($sys_info['sys_date']!=date('Y-m-d',$now)) $sys_obj->where("`sys_table_name`='ut_ord_info'")->data(array('sys_count'=>1,'sys_date'=>date('Y-m-d',$now)))->save();
            else $sys_obj->where("`sys_table_name`='ut_ord_info'")->setInc('sys_count',1);
            $index = $sys_obj->where("`sys_table_name`='ut_ord_info'")->find();
            $_POST['ord_id'] = trans_oper_no(session('user_dept')).trans_oper_no(session('user_id')).date('Ymd',time()).trans_oper_no($index['sys_count']);
            $_POST['ord_date'] = date('Y-m-d H:i:s',time());
            $_POST['oper_no'] = session('user_id');
            $_POST['dept_no'] = session('user_dept');
            $_POST['selled_total'] = 0;
            $_POST['sell_difference'] = $_POST['sell_total'];
            $_POST['payed_total'] = 0;
            $_POST['pay_difference'] = $_POST['pay_total'];
            $this->order_obj->create($_POST);
            $this->order_obj->add();
            $this->success('添加订单成功！<br/>订单ID为：'.$_POST['ord_id'], 1);
        }
    }
    
    public function detail($id) {
        $result = $this->order_obj->find($id);
        $this->assign('data', $result);
        $this->display();
    }
    
    private function param($col) {
        return M('ord_para')->where("`para_column`='{$col}'")->select();
    }
    
    private function method($type) {
        $map = array();
        $this->query($map);
        switch($type) {
            case 'home':
                $map['oper_no'] = intval(session('user_id'));
                break;
            case 'list':
                break;
            default:
                exit;
        }
        $this->page($this->order_obj, $map, '`ord_date` desc');
        $this->assign('oper', getOpers());
        $this->assign('type', $type);
        $this->select();
    }
    
    private function select() {
        $this->assign('area', $this->param('order_area'));
        $this->assign('way', $this->param('insurance_way'));
        $this->assign('stat', $this->param('insurance_stat'));
        $this->assign('settle', $this->param('settle_stat'));
    }
    
    private function query(&$map) {
        if(isset($_POST['loop_dept_dept_no'])) {
            $_POST['srh']['dept'] = $_POST['loop_dept_dept_no'];
            unset($_POST['loop_dept_dept_no']);
            unset($_POST['loop_dept_dept_name']);
        }
        if(!isset($_POST['srh'])&&S('order_srh')) $map = S('order_srh');
        elseif(isset($_POST['srh'])) {
            // 订单创建日期
            if($_POST['srh']['date_start']!==''&&$_POST['srh']['date_end']!=='') $map['ord_date'] = $this->between($_POST['srh']['date_start'], $_POST['srh']['date_end'].' 23:59:59');
            // 所属部门
            if(!empty($_POST['srh']['dept'])) {
				$dept_no = $_POST['srh']['dept'];
				$vector = array($dept_no);
				$data = M('hr_depts')->where('`dept_stat`=1')->select();
				foreach($data as $key=>$value) {
					if(in_array($value['higer_dept'], $vector)) $vector[] = $value['dept_no'];
				}
				$map['dept_no'] = array('in', $vector);
			}
            // 员工
            if(isset($_POST['srh']['oper'])&&$_POST['srh']['oper']!=='') $map['oper_no'] = $_POST['srh']['oper'];
            // 订单号
            if(!empty($_POST['srh']['order_id'])) $map['ord_id'] = array('like', '%'.$_POST['srh']['order_id'].'%');
            // 供应商
            if(!empty($_POST['srh']['provider'])) $map['provider_name'] = array('like', '%'.$_POST['srh']['provider'].'%');
            // 订单线路出境
            if(isset($_POST['srh']['area'])&&!empty($_POST['srh']['area'][0])) $map['order_area'] = array('in', $_POST['srh']['area']);
            // 客户出行时间
            if($_POST['srh']['custom_start']!==''&&$_POST['srh']['custom_end']!=='') $map['depart_date'] = $this->between($_POST['srh']['custom_start'], $_POST['srh']['custom_end']);
            // 客户代表名称
            if(!empty($_POST['srh']['standard'])) $map['custom_name'] = array('like', '%'.$_POST['srh']['standard'].'%');
            // 保险购买状态
            if(isset($_POST['srh']['stat'])&&!empty($_POST['srh']['stat'][0])) $map['insurance_stat'] = array('in', $_POST['srh']['stat']);
            // 订单结算状态
            if(isset($_POST['srh']['settle'])&&!empty($_POST['srh']['settle'][0])) $map['settle_stat'] = array('in', $_POST['srh']['settle']);
            S('order_srh', $map);
        }
    }
}