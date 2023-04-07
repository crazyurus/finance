<?php
return array(
    // 网站设置
    'salt' => 'D2jpM2wQ4HvQXynV',
    'title' => '财务管理系统',
    'author' => 'Crazy Urus',
    'var_page' => 20,

    // 表单令牌
    'TOKEN_ON'      =>    false,  // 是否开启令牌验证 默认关闭
    'TOKEN_NAME'    =>    '__TOKEN__',    // 令牌验证的表单隐藏字段名称
    'TOKEN_TYPE'    =>    'md5',  //令牌哈希验证规则 默认为MD5
    'TOKEN_RESET'   =>    true,  //令牌验证出错后是否重置令牌 默认为true

    // 数据库
    'DB_TYPE'               =>  'mysql',     // 数据库类型
    'DB_HOST'               =>  $_ENV['MYSQLHOST'], // 服务器地址
    'DB_NAME'               =>  $_ENV['MYSQLDATABASE'],          // 数据库名
    'DB_USER'               =>  $_ENV['MYSQLUSER'],      // 用户名
    'DB_PWD'                =>  $_ENV['MYSQLPASSWORD'],          // 密码
    'DB_PORT'               =>  $_ENV['MYSQLPORT'],        // 端口
    'DB_PREFIX'             =>  'ut_',    // 数据库表前缀
);