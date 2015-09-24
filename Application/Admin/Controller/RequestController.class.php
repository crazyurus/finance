<?php
namespace Admin\Controller;

class RequestController extends AdminController {
    
    protected $fin_obj;
    protected $fin_obj2;
    
    const UNCHECK = 0;
    const UNPAY = 3;
    const PASS = 1;
    const BAN = 2;
    
    function _initialize() {
        $this->fin_obj = M('fin_seqtemp');
        $this->fin_obj2 = M('fin_seqcheck');
    }
    
    public function index() {
        if(S('request_srh')) S('request_srh', null);
        $type = I('path.2')=='' ? 'home' : I('path.2');
        $this->method($type);
        $this->display('index');
    }
    
    public function table() {
        $type = I('path.2')=='' ? 'home' : I('path.2');
        $this->method($type);
        $this->display('table');
    }
    
    public function check($id = 0) {
       
        $acc_obj = M('fin_acct');
        $ord_obj = M('ord_info');
        $type = I('path.2')=='' ? 'home' : I('path.2');
        if($type =='no') {
            $data['id'] = $id;
            $data['check_stat'] = self::BAN;
            $this->fin_obj->token(false)->create($data);
            $this->fin_obj->save();
            $this->success('操作成功，该请求未通过！', 0, 200, 'fin');
        }
        elseif($type == 'yes'|| $type == 'pay') {
            $this->assign('type', $type);
            $this->assign('data', $this->fin_obj->find($id));
            $this->assign('select', $acc_obj->select());
            $this->display();
        }
        elseif($type == 'check') {
            if($_POST['type'] == 'pay') {
                $now = time();
                $_POST['check_stat'] = self::PASS;
                $_POST['seq_no'] = $this->seq($now);
                unset($_POST['type']);
                $this->fin_obj->create($_POST);
                $this->fin_obj->save();
                $seq = $this->fin_obj->find($_POST['id']);
                unset($seq['id']);
                $seq['check_date'] = date('Y-m-d H:i:s', $now);
                $seq['check_no'] = session('user_id');
                $this->update($seq);
                $this->success('该请求支付成功！', 1, 200, 'fin');
            }
            elseif($_POST['type'] == 'yes') {
                $data['id'] = $_POST['id'];
                $data['check_stat'] = self::UNPAY;
                $this->fin_obj->create($data);
                $this->fin_obj->save();
                $this->success('操作成功，该请求审核通过，等待支付！', 1, 200, 'fin');
            }
        }
    }
    
    public function add() {
        $type = I('path.2');
        $flag = $type=='out'? -1 : 1;
        $this->assign('type', $type);
        $this->assign('flag', $flag);
        $this->assign('tree', $this->subject($type));
        if($flag==1) $this->assign('select', M('fin_acct')->select());
        $this->display();
    }
    
    public function change() {
        $now = time();
        $flag = isset($_POST['order_id']);
        if(!$flag) $_POST['seq_no'] = $this->seq($now);
        $_POST['action_date'] = date('Y-m-d H:i:s',$now);
        $_POST['oper_no'] = session('user_id');
        $_POST['check_stat'] = $flag ? self::UNCHECK : self::PASS;
		$_POST['check_date'] = $_POST['action_date'];
        if($flag) {
            $_POST['check_no'] = $_POST['oper_no'];
            $this->fin_obj->create($_POST);
            $this->fin_obj->add();
        }
        else $this->update($_POST);
        $this->success('请求提交成功！', 1, 200, 'fin');
    }
    
    public function delete($id) {
        $result = $this->fin_obj->field('check_stat')->find($id);
        if($result['check_stat'] == self::PASS) $this->errors('该请求已经过审核，无法删除！');
        $this->fin_obj->delete($id);
        $this->success('删除成功！', 0, 200, 'fin');
    }
    
    public function detail($id) {
        $type = I('path.2');
        $obj = $type=='temp' ? $this->fin_obj : $this->fin_obj2;
        $result = $obj->find($id);
        $this->assign('data', $result);
        $this->display();
    }
    
    public function subject($type) {
        $str = '';
        if($type=='in') $str = "`settle_flag`=1 AND ";
        elseif($type=='out') $str = "`settle_flag`=-1 AND ";
        return $this->tree(M('fin_subject')->where($str."`subject_stat`=1")->select(), 'subject', 0, false);
    }
    
    private function update(&$seq) {
        
        $acc_obj = M('fin_acct');
        $ord_obj = M('ord_info');
        //更新账户金额
        $data = array();
        $acc = $acc_obj->find($seq['acct_id']); 
        $data['acct_id'] = $seq['acct_id'];
        $data['acct_balance'] = $acc['acct_balance'] + $seq['settle_flag'] * $seq['action_amount'];
        $acc_obj->create($data);
        $acc_obj->save();
        //计入流水
        $seq['current_amount'] = $data['acct_balance'];
        $this->fin_obj2->create($seq);
        $this->fin_obj2->add();
        //更新订单金额
        if(!empty($seq['order_id'])) {
            $data = array();
            $ord = $ord_obj->field('sell_total,selled_total,sell_difference,pay_total,payed_total,pay_difference')->find($seq['order_id']);
            $data['ord_id'] = $seq['order_id'];
            if($seq['settle_flag']==1) {
                $data['selled_total'] = $ord['selled_total'] + $seq['action_amount'];
                $data['sell_difference'] = $ord['sell_total'] - $data['selled_total'];
            }
            elseif($seq['settle_flag']==-1) {
                $data['payed_total'] = $ord['payed_total'] + $seq['action_amount'];
                $data['pay_difference'] = $ord['pay_total'] - $data['payed_total'];
            }
            $ord_obj->create($data);
            $ord_obj->save();
        }
    }
    
    private function method($type) {
        $map = array();
        $order = 'action_date';
        switch($type) {
            case 'home':
                $map['oper_no'] = session('user_id');
            case 'read':
                $map['order_id'] = I('get.id');
                $obj = $this->fin_obj;
                break;
            case 'fin':
                $this->query($map);
                $map['check_stat'] = self::PASS;
                $obj = $this->fin_obj2;
                $order = 'check_date';
                $this->assign('account', M('fin_acct')->select());
                $this->assign('oper', getOpers());
                $this->assign('subject', M('fin_seqcheck')->distinct(true)->field('ut_fin_seqcheck.subject_code, ut_fin_subject.subject_name')->join('ut_fin_subject ON ut_fin_seqcheck.subject_code = ut_fin_subject.subject_no AND ut_fin_subject.subject_stat=1')->order('ut_fin_subject.subject_no')->select());
                break;
            case 'pay':
                $map['check_stat'] = self::UNPAY;
                $obj = $this->fin_obj;
                break;
            case 'list':
                if(session('user_post')==10020) $map['subject_code'] = array('notlike', array('6001%', '6401%'), 'AND');
                elseif(session('user_post')==20000) {
                    $oper = M('hr_opers')->field('oper_no')->where('oper_dept_no='.session('user_dept'))->select();
                    $target = array();
                    foreach($oper as $key => $value) {
                        $target[] = $value['oper_no'];
                    }
                    $map['oper_no'] = array('in', $target);
                    $map['subject_code'] = array('like', array('6001%', '6401%'), 'OR');
                } 
                $map['check_stat'] = self::UNCHECK;
                $obj = $this->fin_obj;
                break;
            case 'account':
                $map = array('acct_id'=>I('get.id'));
                $obj = $this->fin_obj2;
                $order = 'check_date';
                break;
            default:
                exit;
        }
        $this->page($obj, $map, '`check_stat` asc, `'.$order.'` desc');
        $this->assign('type', $type);
    }
    
    private function query(&$map) {
        if(!isset($_POST['srh'])&&S('request_srh')) $map = S('request_srh');
        elseif(isset($_POST['srh'])) {
            // 操作时间
            if($_POST['srh']['date_start']!==''&&$_POST['srh']['date_end']!=='') $map['action_date'] = $this->between($_POST['srh']['date_start'], $_POST['srh']['date_end'].' 23:59:59');
            // 订单ID
            if(!empty($_POST['srh']['id'])) $map['order_id'] = array('like', '%'.$_POST['srh']['id'].'%');
            // 员工
            if(isset($_POST['srh']['oper'])&&$_POST['srh']['oper']!=='') $map['oper_no'] = $_POST['srh']['oper'];
            // 收付账户
            if(isset($_POST['srh']['acct'])&&!empty($_POST['srh']['acct'][0])) $map['acct_id'] = array('in', $_POST['srh']['acct']);
            // 收付标志
            if(isset($_POST['srh']['flag'])) $map['settle_flag'] = array('in', $_POST['srh']['flag']);
            // 收付金额
            if($_POST['srh']['amount_start']!==''&&$_POST['srh']['amount_end']!=='') $map['action_amount'] = $this->between($_POST['srh']['amount_start'], $_POST['srh']['amount_end']);
            // 科目
            if(isset($_POST['srh']['subject'])&&!empty($_POST['srh']['subject'][0])) $map['subject_code'] = array('in', $_POST['srh']['subject']);
            S('request_srh', $map);
        }
    }
    
    private function seq($now) {
        $sys_obj = M('sys_index');
        $sys_info = $sys_obj->where("`sys_table_name`='ut_fin_seqtemp'")->find();
        if($sys_info['sys_date']!=date('Y-m-d',$now)) $sys_obj->where("`sys_table_name`='ut_fin_seqtemp'")->data(array('sys_count'=>1,'sys_date'=>date('Y-m-d',$now)))->save();
        else $sys_obj->where("`sys_table_name`='ut_fin_seqtemp'")->setInc('sys_count',1);
        $index = $sys_obj->where("`sys_table_name`='ut_fin_seqtemp'")->find();
        return trans_oper_no(session('user_id')).date('Ymd',$now).trans_oper_no($index['sys_count']);
    }
}