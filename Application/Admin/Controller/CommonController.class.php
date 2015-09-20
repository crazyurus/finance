<?php
namespace Admin\Controller;
use Think\Controller;

class CommonController extends Controller {
    
    function __call($method, $args) {
        header('HTTP/1.1 404 Not Found');
        header("status: 404 Not Found");
    }
    
    protected function success($msg = '', $type = 0, $code = 200, $tabid = '') {
        $result = array();
        $result['message'] = $msg;
        $result['statusCode'] = $code;
        $result['navTabId'] = $tabid;
        $result['rel'] = '';
        $result['callbackType'] = ($type==1)?'closeCurrent':'';
        $result['forwardUrl'] = '';
        $this->ajaxReturn($result);
        exit;
    }
    
    protected function errors($msg = '', $type = 0) {
        $this->success($msg, $type, 300);
    }
    
    protected function timeout() {
        $this->success('', 0, 301);
    }
}