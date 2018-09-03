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
class Volume extends FrontendPublic
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



	public function index( $id )
	{
		$model_car = model('app\common\model\son\VolumeCard');
		$row = model('app\common\model\Volume')->get($id);

		if (!$row)
			$this->error(__('No Results were found'));

		$this->view->assign("row", $row);

		$car_list = $model_car->where('cat_id', $id)->order('order', 'asc')->select();
		$this->view->assign("car_list", $car_list);

		return $this->view->fetch();
	}

	/**
	 * 注册会员
	 */
	public function register()
	{
		$url = $this->request->request('url');
		if ($this->auth->id)
			$this->success(__('You\'ve logged in, do not login again'), $url);
		if ($this->request->isPost()) {
			$username = $this->request->post('username');
			$password = $this->request->post('password');
			$email = $this->request->post('email');
			$mobile = $this->request->post('mobile', '');
			$captcha = $this->request->post('captcha');
			$token = $this->request->post('__token__');
			$rule = [
				'username'  => 'require|length:3,30',
				'password'  => 'require|length:6,30',
				'email'     => 'require|email',
				'mobile'    => 'regex:/^1\d{10}$/',
				'captcha'   => 'require|captcha',
				'__token__' => 'token',
			];

			$msg = [
				'username.require' => 'Username can not be empty',
				'username.length'  => 'Username must be 3 to 30 characters',
				'password.require' => 'Password can not be empty',
				'password.length'  => 'Password must be 6 to 30 characters',
				'captcha.require'  => 'Captcha can not be empty',
				'captcha.captcha'  => 'Captcha is incorrect',
				'email'            => 'Email is incorrect',
				'mobile'           => 'Mobile is incorrect',
			];
			$data = [
				'username'  => $username,
				'password'  => $password,
				'email'     => $email,
				'mobile'    => $mobile,
				'captcha'   => $captcha,
				'__token__' => $token,
			];
			$validate = new Validate($rule, $msg);
			$result = $validate->check($data);
			if (!$result) {
				$this->error(__($validate->getError()), null, ['token' => $this->request->token()]);
			}
			if ($this->auth->register($username, $password, $email, $mobile)) {
				$synchtml = '';
				////////////////同步到Ucenter////////////////
				if (defined('UC_STATUS') && UC_STATUS) {
					$uc = new \addons\ucenter\library\client\Client();
					$synchtml = $uc->uc_user_synregister($this->auth->id, $password);
				}
				$this->success(__('Sign up successful') . $synchtml, $url ? $url : url('user/index'));
			} else {
				$this->error($this->auth->getError(), null, ['token' => $this->request->token()]);
			}
		}
		//判断来源
		$referer = $this->request->server('HTTP_REFERER');
		if (!$url && (strtolower(parse_url($referer, PHP_URL_HOST)) == strtolower($this->request->host()))
			&& !preg_match("/(user\/login|user\/register)/i", $referer)) {
			$url = $referer;
		}
		$this->view->assign('url', $url);
		$this->view->assign('title', __('Register'));
		return $this->view->fetch();
	}

	/**
	 * 会员登录
	 */
	public function login()
	{
		$url = $this->request->request('url');
		if ($this->auth->id)
			$this->success(__('You\'ve logged in, do not login again'), $url);
		if ($this->request->isPost()) {
			$account = $this->request->post('account');
			$password = $this->request->post('password');
			$keeplogin = (int)$this->request->post('keeplogin');
			$token = $this->request->post('__token__');
			$rule = [
				'account'   => 'require|length:3,50',
				'password'  => 'require|length:6,30',
				'__token__' => 'token',
			];

			$msg = [
				'account.require'  => 'Account can not be empty',
				'account.length'   => 'Account must be 3 to 50 characters',
				'password.require' => 'Password can not be empty',
				'password.length'  => 'Password must be 6 to 30 characters',
			];
			$data = [
				'account'   => $account,
				'password'  => $password,
				'__token__' => $token,
			];
			$validate = new Validate($rule, $msg);
			$result = $validate->check($data);
			if (!$result) {
				$this->error(__($validate->getError()), null, ['token' => $this->request->token()]);
				return FALSE;
			}
			if ($this->auth->login($account, $password)) {
				$synchtml = '';
				////////////////同步到Ucenter////////////////
				if (defined('UC_STATUS') && UC_STATUS) {
					$uc = new \addons\ucenter\library\client\Client();
					$synchtml = $uc->uc_user_synlogin($this->auth->id);
				}
				$this->success(__('Logged in successful') . $synchtml, $url ? $url : url('user/index'));
			} else {
				$this->error($this->auth->getError(), null, ['token' => $this->request->token()]);
			}
		}
		//判断来源
		$referer = $this->request->server('HTTP_REFERER');
		if (!$url && (strtolower(parse_url($referer, PHP_URL_HOST)) == strtolower($this->request->host()))
			&& !preg_match("/(user\/login|user\/register)/i", $referer)) {
			$url = $referer;
		}
		$this->view->assign('url', $url);
		$this->view->assign('title', __('Login'));
		return $this->view->fetch();
	}

	/**
	 * 注销登录
	 */
	function logout()
	{
		//注销本站
		$this->auth->logout();
		$synchtml = '';
		////////////////同步到Ucenter////////////////
		if (defined('UC_STATUS') && UC_STATUS) {
			$uc = new \addons\ucenter\library\client\Client();
			$synchtml = $uc->uc_user_synlogout();
		}
		$this->success(__('Logout successful') . $synchtml, url('user/index'));
	}

	/**
	 * 个人信息
	 */
	public function profile()
	{
		$this->view->assign('title', __('Profile'));
		return $this->view->fetch();
	}

	/**
	 * 修改密码
	 */
	public function changepwd()
	{
		if ($this->request->isPost()) {
			$oldpassword = $this->request->post("oldpassword");
			$newpassword = $this->request->post("newpassword");
			$renewpassword = $this->request->post("renewpassword");
			$token = $this->request->post('__token__');
			$rule = [
				'oldpassword'   => 'require|length:6,30',
				'newpassword'   => 'require|length:6,30',
				'renewpassword' => 'require|length:6,30|confirm:newpassword',
				'__token__'     => 'token',
			];

			$msg = [
			];
			$data = [
				'oldpassword'   => $oldpassword,
				'newpassword'   => $newpassword,
				'renewpassword' => $renewpassword,
				'__token__'     => $token,
			];
			$field = [
				'oldpassword'   => __('Old password'),
				'newpassword'   => __('New password'),
				'renewpassword' => __('Renew password')
			];
			$validate = new Validate($rule, $msg, $field);
			$result = $validate->check($data);
			if (!$result) {
				$this->error(__($validate->getError()), null, ['token' => $this->request->token()]);
				return FALSE;
			}

			$ret = $this->auth->changepwd($newpassword, $oldpassword);
			if ($ret) {
				$synchtml = '';
				////////////////同步到Ucenter////////////////
				if (defined('UC_STATUS') && UC_STATUS) {
					$uc = new \addons\ucenter\library\client\Client();
					$synchtml = $uc->uc_user_synlogout();
				}
				$this->success(__('Reset password successful') . $synchtml, url('user/login'));
			} else {
				$this->error($this->auth->getError(), null, ['token' => $this->request->token()]);
			}
		}
		$this->view->assign('title', __('Change password'));
		return $this->view->fetch();
	}

}
