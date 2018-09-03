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
class NginxLogs extends Backend
{
	public function _initialize()
	{
		parent::_initialize();
	}

	/**
	 * 查看
	 */
	public function indexs()
	{

		$sort = $this->request->request("sort",'count');
		$order = $this->request->request("order",'desc');
		$dates = $this->request->request("dates",date('Y-m-d'));

		if ( $this->request->isAjax() )
		{
			$dir =  ROOT_PATH.'db/sqlite/day/';
			$arr = scandir( $dir );
			$host_ar = [];
			foreach( $arr as $v ){
				if( $v === '.' || $v==='..' || $v==='.DS_Store' ){
					continue;
				}
				$count = 0;
				$host = $v;
				$dir_son = $dir.$v.'/'.$dates.'/';
				if( is_dir( $dir_son ) ){
					$arr_son = scandir( $dir_son );
					foreach( $arr_son as $s_v ){
						if( substr( $s_v,-3 ) != '.db' ){
							continue;
						}

						$sqlite_db = new \mySqLite3(  $dir_son.$s_v );
						$sql = 'SELECT name FROM sqlite_master  where type="table" ';
						$res = $sqlite_db->queryall( $sql );
						foreach( $res as $s_vv ){
							if( $s_vv['name'] == 'sqlite_sequence' )continue;
							$sql = 'SELECT sum(num) co FROM '.$s_vv['name'];
							$ret = $sqlite_db->find( $sql );
							$count += empty( $ret )?0:$ret['co'];
						}
					}
				}
				$row = [
					'count' =>$count,
					'host' => $host,
				];
				$row['url'] = url('nginxlogs/index').'?hostf='.$host.'&dates='.$dates;
				$host_ar[] = $row;
			}

			usort($host_ar,function ($a, $b) use( $order, $sort  ) {
				$ar = ['count'];
				if( $order == 'asc' ){
					if( in_array( $sort, $ar ) ){
						return ($a[$sort] < $b[$sort]) ? -1 : 1;
					}else{
						return strcmp($a[$sort], $b[$sort]);
					}
				}else{
					if( in_array( $sort, $ar ) ){
						return ($b[$sort] < $a[$sort]) ? -1 : 1;
					}else{
						return strcmp($b[$sort], $a[$sort]);
					}
				}
			});

			foreach($host_ar as &$v){
				$v['count'] = number_format( $v['count'],0,'.',',');
			}


			$total = count($host_ar);
			$result = array("total" => $total, "rows" => $host_ar);
			return json($result);
		}

		$this->view->assign( 'dates' ,$dates );
		return $this->view->fetch();
	}





	/**
	 * 查看
	 */
	public function index()
	{
		$dates = $this->request->request("dates",date('Y-m-d'));
		$sort  = $this->request->request("sort",'count');
		$order = $this->request->request("order",'desc');
		$hostf = $this->request->request("hostf");
		if( empty( $hostf  ) ){
			$this->error( '参数不正确！' );
		}


		if ( $this->request->isAjax() )
		{
			$dir =  ROOT_PATH.'db/sqlite/day/'.$hostf.'/'.$dates.'/';

			$arr = scandir( $dir );
			$host_ar = [];
			foreach( $arr as $v ){
				if( substr( $v,-3 ) != '.db' ){
					continue;
				}
				$host = substr( $v, 0, -3 );
				$sqlite_db = new \mySqLite3(  $dir.$v );
				$sql = 'SELECT name FROM sqlite_master  where type="table" ';
				$res = $sqlite_db->queryall( $sql );
				$count = 0;
				foreach( $res as $vv ){
					if( $vv['name'] == 'sqlite_sequence' )continue;
					$sql = 'SELECT sum(num) co FROM '.$vv['name'];
					$ret = $sqlite_db->find( $sql );
					$count += empty( $ret )?0:$ret['co'];
				}

				$row = [
					'count' =>$count,
					'host' => $host,
				];
				$row['url'] = url('nginxlogs/index_des').'?host='.$host.'&dates='.$dates.'&hostf='.$hostf;
				$host_ar[] = $row;
			}

			usort($host_ar,function ($a, $b) use( $order, $sort  ) {
				$ar = ['count'];
				if( $order == 'asc' ){
					if( in_array( $sort, $ar ) ){
						return ($a[$sort] < $b[$sort]) ? -1 : 1;
					}else{
						return strcmp($a[$sort], $b[$sort]);
					}
				}else{
					if( in_array( $sort, $ar ) ){
						return ($b[$sort] < $a[$sort]) ? -1 : 1;
					}else{
						return strcmp($b[$sort], $a[$sort]);
					}
				}
			});

			foreach($host_ar as &$v){
				$v['count'] = number_format( $v['count'],0,'.',',');
			}

			$total = count($host_ar);
			$result = array("total" => $total, "rows" => $host_ar);
			return json($result);
		}

		$this->view->assign( 'dates' ,$dates );
		$this->view->assign( 'hostf' ,$hostf );
		return $this->view->fetch();
	}


	/**
	 * 查看
	 */
	public function index_des()
	{
		$host = $this->request->request("host",'');
		$dates = $this->request->request("dates",date('Y-m-d'));
		$sort = $this->request->request("sort",'count');
		$order = $this->request->request("order",'desc');
		$hostf = $this->request->request("hostf");
		if( empty( $hostf  ) ){
			$this->error( '参数不正确！' );
		}

		if ( $this->request->isAjax() )
		{
			$file =  ROOT_PATH.'db/sqlite/day/'.$hostf.'/'.$dates.'/'.$host.'.db';

			$host_ar = [];
			$sqlite_db = new \mySqLite3(  $file );
			$sql = 'SELECT name FROM sqlite_master  where type="table" ';
			$res = $sqlite_db->queryall( $sql );

			foreach( $res as $vv ){

				if( $vv['name'] == 'sqlite_sequence' )continue;
				$sql = 'SELECT sum(num) co FROM '.$vv['name'];
				$ret = $sqlite_db->find( $sql );
				$row = [
					'count' => empty( $ret )?0:$ret['co'],
					'host' => $vv['name'],
					'table' => $vv['name'],
				];
				$row['url'] = url('nginxlogs/descs').'?host='.$host.'&dates='.$dates.'&table='.$vv['name'].'&hostf='.$hostf;
				$host_ar[] = $row;

			}

			usort($host_ar,function ($a, $b) use( $order, $sort  ) {
				$ar = ['count'];
				if( $order == 'asc' ){
					if( in_array( $sort, $ar ) ){
						return ($a[$sort] < $b[$sort]) ? -1 : 1;
					}else{
						return strcmp($a[$sort], $b[$sort]);
					}
				}else{
					if( in_array( $sort, $ar ) ){
						return ($b[$sort] < $a[$sort]) ? -1 : 1;
					}else{
						return strcmp($b[$sort], $a[$sort]);
					}
				}
			});


			foreach($host_ar as &$v){
				$v['count'] = number_format( $v['count'],0,'.',',');
			}


			$total = count($host_ar);
			$result = array("total" => $total, "rows" => $host_ar);
			return json($result);
		}

		$this->view->assign( 'dates' ,$dates );
		$this->view->assign( 'host' ,$host );
		$this->view->assign( 'hostf' ,$hostf );
		return $this->view->fetch();
	}



	/**
	 * 查看
	 */
	public function descs()
	{

		$host = $this->request->request("host");
		$dates = $this->request->request("dates");
		$table = $this->request->request("table");
		$sort = $this->request->request("sort",'count');
		$order = $this->request->request("order",'desc');
		$hostf = $this->request->request("hostf");
		if( empty( $hostf  ) ){
			$this->error( '参数不正确！' );
		}


		if( empty( $host ) || empty( $dates ) || empty( $table ) ){
			$this->error( '参数不正确！' );
		}

		if ( $this->request->isAjax() )
		{

			$file =  ROOT_PATH.'db/sqlite/day/'.$hostf.'/'.$dates.'/'.$host.'.db';

			$sqlite_db = new \mySqLite3(  $file );
		
			$db_table_name = $table;
		
			$sql = 'SELECT * FROM '.$db_table_name.' GROUP BY app,port LIMIT 2000 ';
			$ret = $sqlite_db->queryall( $sql );

			$host_ar = [];
			foreach( $ret as $v ){
				$sql = 'SELECT sum( num ) co FROM '.$db_table_name.' WHERE app="'.$v['app'].'" AND port="'.$v['port'].'" ';
				$ret = $sqlite_db->find( $sql );
				$v['count'] = $ret['co'];
				$v['url'] = url('nginxlogs/descs_des').'?host='.$host.'&dates='.
						$dates.'&app='.urldecode( $v['app'] ).'&port='.$v['port'].'&table='.$table.'&hostf='.$hostf;
				$host_ar[] = $v;
			}

			usort($host_ar,function ($a, $b) use( $order, $sort  ) {
				$ar = ['count'];
				if( $order == 'asc' ){
					if( in_array( $sort, $ar ) ){
						return ($a[$sort] < $b[$sort]) ? -1 : 1;
					}else{
						return strcmp($a[$sort], $b[$sort]);
					}
				}else{
					if( in_array( $sort, $ar ) ){
						return ($b[$sort] < $a[$sort]) ? -1 : 1;
					}else{
						return strcmp($b[$sort], $a[$sort]);
					}
				}
			});

			foreach($host_ar as &$v){
				$v['count'] = number_format( $v['count'],0,'.',',');
			}

			$total = count($host_ar);
			$result = array("total" => $total, "rows" => $host_ar);
			return json($result);
		}

		$this->view->assign( 'host' ,$host );
		$this->view->assign( 'dates' ,$dates );
		$this->view->assign( 'table' ,$table );
		$this->view->assign( 'hostf' ,$hostf );
		return $this->view->fetch();
	}


	/**
	 * 查看
	 */
	public function descs_des()
	{
		$host = $this->request->request("host");
		$dates = $this->request->request("dates");
		$table = $this->request->request("table");
		$hostf = $this->request->request("hostf");
		$type_t = $this->request->request("type_t", 2);

		if( empty( $hostf  ) ){
			$this->error( '参数不正确！' );
		}
 
		$app = urldecode( $this->request->request("app") );
		$port = $this->request->request("port");
		if( empty( $host ) || empty( $dates ) || empty( $port ) || empty( $table ) ){
			$this->error( '参数不正确！' );
		}


		$list = [];
		if( $type_t ==2 ){
			// 5 分钟单位
			for( $i=0; $i < 24; $i++ ){
				for( $y=0; $y < 60; $y+=5 ){
					$list[ ( $i < 10 ? '0'.$i : $i ).':'.( $y < 10 ? '0'.$y : $y ) ] = 0;
				}
			}

		}else if( $type_t == 3 ){
			// 1 小时钟单位

			for( $i=0; $i < 24; $i++ ){
				$list[ ( $i < 10 ? '0'.$i : $i ).':00' ] = 0;
			}
		}else{
			// 1 分钟单位
			for( $i=0; $i < 24; $i++ ){
				for( $y=0; $y < 60; $y++ ){
					$list[ ( $i < 10 ? '0'.$i : $i ).':'.( $y < 10 ? '0'.$y : $y ) ] = 0;
				}
			}
		}


		$file =  ROOT_PATH.'db/sqlite/day/'.$hostf.'/'.$dates.'/'.$host.'.db';
		$sqlite_db = new \mySqLite3(  $file );
		$sql = 'SELECT * FROM '.$table.' WHERE app="'.$app.'" AND port="'.$port.'" ';
		$ret = $sqlite_db->queryall( $sql );

		$type_h_fn = function( $h, $m ){
			if( $m > 0 ){
				++$h;
				$m = '00';
				if( $h < 10 )$h = '0'.$h;
			}
			return [ $h, $m ];
		};

		$type_m_fn = function( $h, $m ){
			if( $m%5 > 0 ){
				$int = intval( $m/5 );
				if( $int === 11 ){
					++$h;
					$m = '00';
					if( $h < 10 )$h = '0'.$h;
				}else{
					$m = ( $int + 1 ) * 5;
					if( $m < 10 )$m = '0'.$m;
				}
			}
			return [ $h, $m ];
		};

		$table_list_num_all  = $list;
		$table_list_time_all = $list;
		$table_list_time     = $list;
		$table_list_count = 0;
		foreach( $ret as $vv ){

			$h = substr( $vv['ymdhi'], 8,2);
			$m = substr( $vv['ymdhi'], 10);
			if( $type_t ==2 ){
				list( $h, $m ) = $type_m_fn( $h, $m );
			}else if( $type_t == 3 ){
				list( $h, $m ) = $type_h_fn( $h, $m );
			}
			$keys = $h.':'.$m;
			if( !empty( $table_list_num_all[ $keys ] ) ){
				$table_list_count += $vv['num'];
				$table_list_num_all[ $keys ] += $vv['num'];
				$table_list_time_all[ $keys ] += $vv['time'];
				$table_list_time[ $keys ] = round( 
							($table_list_num_all[ $keys ] > 0?$table_list_time_all[ $keys ] / $table_list_num_all[ $keys ]:0) /1000, 3 );

			}else{
				$table_list_count += $vv['num'];
				$table_list_num_all[ $keys ] = $vv['num'];
				$table_list_time_all[ $keys ] = $vv['time'];
				$table_list_time[ $keys ] = round( 
							($table_list_num_all[ $keys ] > 0?$table_list_time_all[ $keys ] / $table_list_num_all[ $keys ]:0) /1000, 3 );
			}
		}
		$len = array_search( $keys, array_keys($list)); // $key = 2;
		if( empty( $len ) )$len = count( $table_list_time );
		unset( $table_list_time_all );

		$this->view->assign( 'table_list_count' ,$table_list_count );
		$this->view->assign( 'table_list' , array_slice( $table_list_num_all, 0, $len, true ) );
		$this->view->assign( 'table_list_time' , array_slice( $table_list_time, 0, $len, true ) );
		


			 
		$dates_yesterday =  date( 'Y-m-d', strtotime( ' -1 day '.$dates ) );
		$file =  ROOT_PATH.'db/sqlite/day/'.$hostf.'/'.$dates_yesterday.'/'.$host.'.db';
		$sqlite_db = new \mySqLite3(  $file );
		$sql = 'SELECT * FROM '.$table.' WHERE app="'.$app.'" AND port="'.$port.'" ';
		$ret = $sqlite_db->queryall( $sql );

		$table_list_num_all  = $list;
		$table_list_time_all = $list;
		$table_list_time     = $list;
		$table_list_count = 0;
		foreach( $ret as $vv ){

			$h = substr( $vv['ymdhi'], 8,2);
			$m = substr( $vv['ymdhi'], 10);
			if( $type_t ==2 ){
				list( $h, $m ) = $type_m_fn( $h, $m );
			}else if( $type_t == 3 ){
				list( $h, $m ) = $type_h_fn( $h, $m );
			}
			$keys = $h.':'.$m;
			if( !empty( $table_list_num_all[ $keys ] ) ){
				$table_list_count += $vv['num'];
				$table_list_num_all[ $keys ] += $vv['num'];
				$table_list_time_all[ $keys ] += $vv['time'];
				$table_list_time[ $keys ] = round( 
							($table_list_num_all[ $keys ] > 0?$table_list_time_all[ $keys ] / $table_list_num_all[ $keys ]:0) /1000, 3 );

			}else{
				$table_list_count += $vv['num'];
				$table_list_num_all[ $keys ] = $vv['num'];
				$table_list_time_all[ $keys ] = $vv['time'];
				$table_list_time[ $keys ] = round( 
							($table_list_num_all[ $keys ] > 0?$table_list_time_all[ $keys ] / $table_list_num_all[ $keys ]:0) /1000, 3 );
			}
		}
		unset( $table_list_time_all );

		$this->view->assign( 'dates_yesterday' ,$dates_yesterday );
		$this->view->assign( 'table_list_count_yesterday' ,$table_list_count );
		$this->view->assign( 'table_list_yesterday' , array_slice( $table_list_num_all, 0, $len, true ) );
		$this->view->assign( 'table_list_time_yesterday' , array_slice( $table_list_time, 0, $len, true ) );



		$this->view->assign( 'dates' ,$dates );
		$this->view->assign( 'hostf' ,$hostf );
		$this->view->assign( 'type_t' ,$type_t );
		$this->view->assign( 'host' ,$host );
		$this->view->assign( 'table' ,$table );
		$this->view->assign( 'app' ,$app );
		$this->view->assign( 'port' ,$port );


		return $this->view->fetch();

	}

}
