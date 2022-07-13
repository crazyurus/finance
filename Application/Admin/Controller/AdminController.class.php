<?php
namespace Admin\Controller;

class AdminController extends CommonController {
   
    function __construct() {
        parent::__construct();
        if (!isLogin()) redirect(U('admin/login/index'));
    }
    
    protected function alert($msg = '', $type = 'warn', $close = 1) {
        header('Content-type: text/html; charset=utf-8');
        $str = '';
        switch($close) {
            case 1: $str = 'navTab.closeCurrentTab();';break;
        }
        echo "<script>alertMsg.{$type}('{$msg}');{$str}</script>";
        exit;
    }
    
    protected function page($model, $map = array(), $sort = '') {
        if (!empty($_REQUEST['orderField'])) {
            if (!empty($sort)) $sort .= ',';
            $sort .= $_REQUEST['orderField'].($_REQUEST['orderDirection'] == 'asc' ? ' asc' : ' desc');
        }
        $count = $model->where($map)->count($model->getPk());
        $page = new \Org\Util\Page($count);
        $per = $page->getPer();
        $current = $page->getCurrent();
        if ($count > 0) {
            if (!$page->isCorrect()) $this->alert('无效的页码范围','warn',0);
            $voList = $model->where($map)->order($sort)->limit($per)->page($current)->select();
            $this->assign('data', $voList);
        }
        $this->assign('totalCount', $count);
        $this->assign('numPerPage', $per);
        $this->assign('currentPage', $current);
        $this->assign('orderField', I('request.orderField'));
        $this->assign('orderDirection', I('request.orderDirection'));
    }
    
    protected function tree($arr, $pre, $id = 0, $chk = true, $str = '') {
        $result = '';
        $flag = false;
        foreach($arr as $key => $value) {
            if ($value['higer_'.$pre] == $id) {
                if ($flag == false&&$id != 0) $result .= '<ul>';
                $extend = '';
                $func = $chk ? '$.bringBack' : 'subjectBack';
                $next = $this->tree($arr, $pre, $value[$pre.'_no'], $chk, $extend);
                $jsstr = $chk||empty($next) ? "onclick=\"".$func."({ ".$pre."_no: '".$value[$pre.'_no']."', ".$pre."_name: '".$str.$value[$pre.'_name']."' })\"" : "";
                $result .= "<li><a href=\"javascript:\" ".$jsstr.">".$value[$pre.'_name']."</a>";
                $result .= $next.'</li>';
                $flag = true;
            }
        }
        if ($flag == true&&$id != 0) $result .= '</ul>';
        return $result;
    }
    
    protected function between($min, $max) {
        if ($min!==''&&$max!=='') {
            if ($min==='') return array('elt', $max);
            elseif ($max==='') return array('egt', $min);
            else return array('between', array($min, $max));
        }
    }
}