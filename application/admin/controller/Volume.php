<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use app\common\model\Volume as VolumeModel;
use fast\Tree;

/**
 * 分类管理
 *
 * @icon fa fa-list
 * @remark 用于统一管理网站的所有分类,分类可进行无限级分类
 */
class Volume extends Backend
{

	protected $model = null;
	protected $categorylist = [];
	protected $noNeedRight = ['selectpage'];

	public function _initialize()
	{
		parent::_initialize();
		 

		$this->request->filter(['strip_tags']);
		$this->model = model('app\common\model\Volume');
		$this->con_list = collection( $this->model->order('id desc')->select() )->toArray();

		$this->view_url = 'http://'.TAOBAOKE_VOLUME_URL_HOST.'/index/volume/index/?id=%s';



		$colorList = [
			['color'=>'FCA5A5', 'name'=>'bg_01'],
			['color'=>'faf0ee', 'name'=>'bg_02'],
			['color'=>'fef2fd', 'name'=>'bg_03'],
			['color'=>'f2f7ff', 'name'=>'bg_04'],
			['color'=>'f0fdeb', 'name'=>'bg_05'],
			['color'=>'fefde9', 'name'=>'bg_06'],
		];
		$this->view->assign("colorList", $colorList);
		$this->view->assign("colorListName", '背影颜色');

	}

	/**
	 * 查看
	 */
	public function index()
	{
			 
		if ($this->request->isAjax())
		{
			$search = $this->request->request("search");
			$type = $this->request->request("type");

			//构造父类select列表选项数据
			$list = [];
			foreach ($this->con_list as $k => $v)
			{
				$v['url'] = sprintf( $this->view_url,$v['id'] );

				if ($search) {
					if ($v['type'] == $type && stripos($v['name'], $search) !== false || stripos($v['nickname'], $search) !== false)
					{
						$list[] = $v;
					}
				} else {
					$list[] = $v;
				}
			}

			$total = count($list);

			$result = array("total" => $total, "rows" => $list);

			return json($result);
		}
		return $this->view->fetch();
	}


	/**
	 * 编辑
	 */
	public function edit($ids = NULL)
	{

		$model_car = model('app\common\model\son\VolumeCard');

		$row = $this->model->get($ids);
			 
		if (!$row)
			$this->error(__('No Results were found'));
		$adminIds = $this->getDataLimitAdminIds();
		if (is_array($adminIds)) {
			if (!in_array($row[$this->dataLimitField], $adminIds)) {
				$this->error(__('You have no permission'));
			}
		}


		if ($this->request->isPost()) {
			$params = $this->request->post("row/a");
			try {
				$result = $row->allowField(true)->save($params);
				if ($result === false) {
					$this->error($row->getError());
				}
			} catch (\think\exception\PDOException $e) {
				$this->error($e->getMessage());
			}

			$params = $this->request->post( "car/a", [] );
	
			if( empty( $params ) ){
				$this->success();
			}
			$field = [];
			foreach($params as $k=>$v){
				if( $k != 'id' )$field[] = $k;
			}

			foreach( $params['id'] as $k=>$v ){

				$data = [];
				foreach( $field as $vv ){
					$data[ $vv ] = $params[ $vv ][ $k ];
				}

				if( $v == 0 ){                     
					try {
						$data['cat_id'] = $ids;
						$result = $model_car->allowField(true)->save($data);
						if ($result === false) {
							$this->error($model_car->getError());
						}
					} catch (\think\exception\PDOException $e) {
						$this->error($e->getMessage());
					}
					
				}else{

					$row = $model_car->get($v);
					if (!$row)
						$this->error(__('No Results were found'));

					try {
						$result = $row->allowField(true)->save($data);
						if ($result === false) {
							$this->error($row->getError());
						}
					} catch (\think\exception\PDOException $e) {
						$this->error($e->getMessage());
					}
				}
			}

			$this->success();
		}
		$this->view->assign("row", $row);

		$car_list = $model_car->where('cat_id', $ids)->order('order', 'asc')->select();
		$this->view->assign("car_list", $car_list);

		return $this->view->fetch();
	}




	/**
	 * 删除
	 */
	public function del_car($ids = "")
	{
		if ($ids) {
			$model_car = model('app\common\model\son\VolumeCard');

			$pk = $model_car->getPk();
			$adminIds = $this->getDataLimitAdminIds();
			if (is_array($adminIds)) {
				$count = $model_car->where($this->dataLimitField, 'in', $adminIds);
			}
			$list = $model_car->where($pk, 'in', $ids)->select();
			$count = 0;
			foreach ($list as $k => $v) {
				$count += $v->delete();
			}
			if ($count) {
				$this->success();
			} else {
				$this->error(__('No rows were deleted'));
			}
		}
		$this->error(__('Parameter %s can not be empty', 'ids'));
	}



	/**
	 * 删除
	 */
	public function del($ids = "")
	{

		var_dump( $ids );
		exit();
		
		if ($ids) {
			$pk = $this->model->getPk();
			$adminIds = $this->getDataLimitAdminIds();
			if (is_array($adminIds)) {
				$count = $this->model->where($this->dataLimitField, 'in', $adminIds);
			}
			$list = $this->model->where($pk, 'in', $ids)->select();
			$count = 0;
			foreach ($list as $k => $v) {
				$count += $v->delete();
			}
			if ($count) {
				$this->success();
			} else {
				$this->error(__('No rows were deleted'));
			}
		}
		$this->error(__('Parameter %s can not be empty', 'ids'));
	}





	/**
	 * Selectpage搜索
	 * 
	 * @internal
	 */
	public function selectpage()
	{
		return parent::selectpage();
	}

}
