<?php
namespace Org\Util;

class Page {

    protected $count;
    protected $current;
    protected $per;
    protected $num;
    
    public function __construct($count, $current = 1) {
        $this->count = $count;
        $this->current = isset($_REQUEST['pageNum']) ? intval($_REQUEST['pageNum']) : $current;
        $this->per = isset($_REQUEST['numPerPage']) ? intval($_REQUEST['numPerPage']) : (isset($_COOKIE['numPerPage']) ? $_COOKIE['numPerPage'] : C('var_page'));
        $this->num = ceil($count/$this->per);
        setcookie('numPerPage', $this->per, time()+3600, '/');
    }

    public function getPer() {
        return $this->per;
    }
    
    public function getRow() {
        return ($this->current-1) * $this->per;
    }
    
    public function getCurrent() {
        return $this->current;
    }
    
    public function isCorrect() {
        return $this->current>0 && $this->current<=$this->num;
    }

}