<?php

namespace app\common\controller;

use app\common\library\Auth;
use think\Config;
use think\Controller;
use think\Hook;
use think\Lang;

/**
 * 前台控制器基类
 */
class FrontendPublic extends Controller
{

	/**
	 * 布局模板
	 * @var string
	 */
	protected $layout = '';

	/**
	 * 无需登录的方法,同时也就不需要鉴权了
	 * @var array
	 */
	protected $noNeedLogin = ['volume'];

	/**
	 * 无需鉴权的方法,但需要登录
	 * @var array
	 */
	protected $noNeedRight = [];

	/**
	 * 权限Auth
	 * @var Auth
	 */
	protected $auth = null;

	public function _initialize()
	{
		
	}

	/**
	 * 加载语言文件
	 * @param string $name
	 */
	protected function loadlang($name)
	{
		Lang::load(APP_PATH . $this->request->module() . '/lang/' . $this->request->langset() . '/' . str_replace('.', '/', $name) . '.php');
	}

	/**
	 * 渲染配置信息
	 * @param mixed $name 键名或数组
	 * @param mixed $value 值
	 */
	protected function assignconfig($name, $value = '')
	{
		$this->view->config = array_merge($this->view->config ? $this->view->config : [], is_array($name) ? $name : [$name => $value]);
	}

}
