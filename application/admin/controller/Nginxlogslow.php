<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use think\Config;

/**
 * 控制台
 *
 * @icon fa fa-dashboard
 * @remark 用于展示当前系统中的统计数据、统计报表及重要实时数据
 */
class NginxLogslow extends Backend
{
	public function _initialize()
	{
		parent::_initialize();
	}


	/**
	 * 查看
	 */
	public function index()
	{
		$sort = $this->request->request("sort",'count');
		$order = $this->request->request("order",'desc');
		$dates = $this->request->request("dates",date('Y-m-d'));
		$db_name = $this->request->request("db_name",'slow_1');

		$id = $this->request->request("id",0);

		$dir =  ROOT_PATH.'db/sqlite/slow/'.$dates.'/';
		$db_index_ar = [];
		if( is_dir( $dir  ) ){
			$arr = scandir( $dir );
			foreach( $arr as $v){
				if( substr( $v, 0, 5 ) == 'slow_' ){
					$db_index_ar[] = substr( $v, 0, -3 );
				}
			}
		}else{
			$db_index_ar[] = '不存在数据！';
		}


		if ( $this->request->isAjax() )
		{

			if( $db_index_ar[0] != 'slow_1' ){
				$result = array("total" => 0, "rows" => []);
				return json($result);
			}

			if( !in_array( $db_name, $db_index_ar ) ){
				$db_name = substr( array_pop( $db_index_ar ), 0, -3 );
			}
	

			list($where, $sort, $order, $offset, $limit) = $this->buildparams();

			$sqlite_db = new \mySqLite3(  $dir.$db_name.'.db' );
			if( $id > 0 ){
				$sql = 'SELECT * FROM s_slow where id < '.$id.' ORDER BY id DESC LIMIT '.$offset.','.$limit;
			}else{
				$sql = 'SELECT * FROM s_slow ORDER BY id DESC LIMIT '.$offset.','.$limit;
			}
	
			$list = $sqlite_db->queryall( $sql );
			foreach($list as &$v){
				$v['times'] = round( ($v['num'] > 0 ? $v['time'] / $v['num']:0) /1000, 3 );
			}

			$sql = 'SELECT count(*) co FROM s_slow LIMIT 1';
			$re = $sqlite_db->find( $sql );

			$total = $re['co'];
			$result = array("total" => $total,  "rows" => $list);
			return json($result);
		}

		$this->view->assign( 'dates' ,$dates );
		$this->view->assign( 'db_name' , $db_name );
		$this->view->assign( 'db_index_ar' ,$db_index_ar );
		return $this->view->fetch();
	}



}
