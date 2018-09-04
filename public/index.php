<?php

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// [ 应用入口文件 ]
// 定义应用目录


var_dump( '2018-09-04T15:43:31+0800 ppp' );
exit();
	 


define('APP_PATH', __DIR__ . '/../application/');


// 判断是否安装FastAdmin
if (!is_file(APP_PATH . 'admin/command/Install/install.lock'))
{
var_dump( APP_PATH . 'admin/command/Install/install.lock' );
exit();

	var_dump( 34343 );
	exit();
		 
	header("location:./install.php");
	exit;
}



// 加载框架引导文件
require __DIR__ . '/../thinkphp/start.php';
