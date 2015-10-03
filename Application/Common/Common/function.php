<?php

function isLogin() {
    return isset($_SESSION['user_id']);
}

function hasRight($num) {
    return $_SESSION['user_right'][$num];
}

function comma() {
    $val = null;
    if(($n = func_num_args()) > 0)
        $val = func_get_arg($n - 1);
    return $val;
}

function msubstr($str, $length, $suffix='……') {
    $str = strip_tags($str);                                          
    $len = mb_strlen($str, 'utf-8');
    if($length >= $len) return $str;
    else return mb_substr($str, 0, $length-1, 'utf-8').$suffix;
}

function trans_oper_stat($num) {
    $str = '';
    switch($num) {
        case 0: $str = '离职';break;
        case 1: $str = '在职';break;
    }
    return $str;
}

function trans_dept_stat($num) {
    $str = '';
    switch($num) {
        case 0: $str = '撤销';break;
        case 1: $str = '存在';break;
    }
    return $str;
}

function trans_post_stat($num) {
    $str = '';
    switch($num) {
        case 0: $str = '撤销';break;
        case 1: $str = '存在';break;
    }
    return $str;
}

function trans_settle_flag($num) {
    $str = '';
    switch($num) {
        case 1: $str = '收';break;
        case -1: $str = '付';break;
    }
    return $str;
}

function trans_oper_no($num) {
    return str_pad($num,5,'0',STR_PAD_LEFT);
}

function find_public_func($num, $pre, $table, $no, $name) {
    if(S($pre.'/flag')) return S($pre.'/'.$num);
    else {
        $result = M($table)->field($no.','.$name)->select();
        foreach($result as $item) {
            S($pre.'/'.$item[$no], $item[$name]);
        }
        S($pre.'/flag', true);
        return find_public_func($num, $pre, $table, $no, $name);
    }
}

function find_oper_no($num) {
    return find_public_func(intval($num), 'oper', 'hr_opers', 'oper_no', 'oper_name');
}

function find_param_no($num) {
    return find_public_func($num, 'ord_stat', 'ord_para', 'para_id', 'para_name');
}

function find_subject_code($num) {
    return find_public_func($num, 'subject', 'fin_subject', 'subject_no', 'subject_name');
}

function percent($num) {
    return (round($num*10000)/100).'%';
}

function trans_oper_tree($num, $pre) {
    $str = '';
    $id = $num;
    while($id != 0) {
        $result = M("hr_{$pre}s")->field("{$pre}_name,higer_{$pre}")->where("{$pre}_stat=1")->find($id);
        $str = $result["{$pre}_name"].$str;
        $id = $result["higer_{$pre}"];
    }
    return $str;
}

function set_tree_info($arr, $pre, $id = 0, $str = '', $flag = false) {
    foreach($arr as $key => $value) {
        if($value['higer_'.$pre] == $id) {
            $result = $flag ? $value[$pre.'_name'] : $str.$value[$pre.'_name'];
            S($pre.'/'.$value[$pre.'_no'], $result);
            set_tree_info($arr, $pre, $value[$pre.'_no'], $result, $flag);
        }
    }
    if($id == 0) S($pre.'/flag', true);
}

function find_tree_info($arr, $pre) {
    $flag = false;
    foreach($arr as $key => $value) {
        $flag = true;
        S($pre.'/'.$value[$pre.'_no'], $value[$pre.'_name']);
    }
    if($flag) S($pre.'/flag', true);
}

function find_acct_name($num) {
    return find_public_func($num, 'acct', 'fin_acct', 'acct_id', 'acct_name');
}

function trans_dept_no($num) {
    if(S('dept/flag')) return S('dept/'.$num);
    else {
        find_tree_info(M('hr_depts')->select(), 'dept');
        return trans_dept_no($num);
    }
}

function trans_post_no($num) {
    if(S('post/flag')) return S('post/'.$num);
    else {
        find_tree_info(M('hr_posts')->select(), 'post');
        return trans_post_no($num);
    }
}

function trans_date($str) {
    return strtok($str, ' ');
}

function getOpers() {
    $oper = M('hr_opers')->field('oper_no, oper_name, oper_stat')->order('oper_stat desc')->select();
    foreach ($oper as &$item) {
        if($item['oper_stat'] == 0) $item['oper_name'] .= '-离职';
        else $item['oper_name'] .= '-在职';
    }
    return $oper;
}

function calc_diff($val1, $val2) {
    return sprintf('%.2f', $val1 - $val2);
}

function calc_percent($diff, $val) {
    return percent($diff/$val);
}