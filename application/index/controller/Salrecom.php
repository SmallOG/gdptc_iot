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
class Salrecom extends FrontendPublic
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
		$row = model('app\admin\model\Salrecom')->get($id);
		
		$img_ar = [
			'1_image',
			'2_image',
			'3_image',
			'4_image',
			'5_image',
			'6_image',
			'7_image',
			'8_image',
		];


		$img = [];
		foreach( $img_ar as $v ){
			if( !empty( $row[ $v ] ) ){
				$img[] = $row[ $v ];
			}
		}


		if (!$row)
			$this->error(__('No Results were found'));

		if( $row['status'] === 'hidden' ){
			echo( '访问内容不存在！' );
			exit();
		}


		model('app\admin\model\Salrecom')->where( 'id', $id )->setInc( 'views', 1 );


		$this->view->assign("row", $row);
		$this->view->assign("img", $img);

		return $this->view->fetch();
	}

}
