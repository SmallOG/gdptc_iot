<?php

namespace app\index\controller;

use app\common\controller\FrontendPublic;
use think\Config;
use think\Cookie;
use think\Hook;
use think\Session;
use think\Validate;

/**
 * 会员中心
 */
class Index extends FrontendPublic
{

	protected $layout = 'default';
	protected $noNeedLogin = ['login', 'register', 'third'];
	protected $noNeedRight = ['*'];

	public function _initialize()
	{            
		parent::_initialize();
	 
		if (!Config::get('fastadmin.usercenter')) {
			$this->error(__('User center already closed'));
		}

		$ucenter = get_addon_info('ucenter');
		if ($ucenter && $ucenter['state']) {
			include ADDON_PATH . 'ucenter' . DS . 'uc.php';
		}

	}

	public function index(){

		return $this->view->fetch();
	}


}
