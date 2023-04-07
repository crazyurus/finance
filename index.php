<?php

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

// SESSION开启
if(!isset($_SESSION)) session_start();

// 时区设置
ini_set('date.timezone','Asia/Shanghai');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG', true);
define('RUNTIME_PATH', '/tmp/');

// 定义应用目录
define('APP_PATH','./Application/');

// 引入ThinkPHP入口文件
require './ThinkPHP/ThinkPHP.php';

// 注入过滤
if(ini_get('magic_quotes_gpc')) {
	function stripslashesRecursive(array $array){
		foreach($array as $k => $v) {
			if(is_string($v)) {
				$array[$k] = stripslashes($v);
			} elseif(is_array($v)) {
				$array[$k] = stripslashesRecursive($v);
			}
		}
		return $array;
	}
	$_GET = stripslashesRecursive($_GET);
	$_POST = stripslashesRecursive($_POST);
}