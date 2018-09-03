<?php


class ThreadRun {
	public $run_sh = '';
	public $log_dir = '';
	public $finished_sleep = 2;
	public $ops;
	public $R = [];
	public $params = []; // 本次获取到的参数
	public $params_next = []; // 下次要使用的参数数组
	public $params_name_string = 'et_'; // 参数的识别格式
	public $run_num = 0; // 要执行的次数


	function init ( $ops, &$thia=null, $method=null ) {

		$this->R['STARTIME']    = microtime(true);
		$this->ops = $ops;
		$this->log_dir     	  = $ops['log_dir'];
		if( !empty( $ops['finished_sleep'] ) )$this->finished_sleep = $ops['finished_sleep'];
		if( !empty( $ops['run_sh'] ) )$this->run_sh         = $ops['run_sh'];
		if( !empty( $ops['run_num'] ) )$this->run_num        = $ops['run_num'];
		if( !empty( $ops['R'] ) ){
			$this->R = array_merge( $this->R, $ops['R'] );
		}
		if( !isset( $ops['show_msg'] ) ){
			$this->ops['show_msg'] = 1;
		}


		$this->thread_recode();
		$this->thread_params();

		try {				
			$res = $this->thread_is_run( $this->run_sh, 3, 2 );
			if( $res ){
				throw new \Exception('，运行错误！(上一个进程没有结束)',500);
			}
			if( is_object( $thia ) )$thia->$method();
		} catch (\Exception $e) {
			$this->R['ERROR_MSG'] .= $e->getMessage().'->'.
				$e->getFile().'::R'.$e->getLine();
		}
		if( is_object( $thia ) ){
			$this->R['IS_FINISHED'] = 1;
		}

	}



	function thread_params( ){
		if( empty( $_SERVER['argv'] ) || count( $_SERVER['argv'] ) < 2 ){
			return;
		}

		foreach( $_SERVER['argv'] as $v ){
			if( substr( $v, 0, 3 ) === $this->params_name_string && strpos( $v, '=' ) !== false ){
				$res = explode( '=', $v );
				$this->params[ $res[0] ] = $res[1];
			}
		}
	}



	function thread_is_run($str_sh, $num, $time ){
		if( empty( $str_sh ) )return 0;
		$is_file_run = function ( $str_sh ){
			exec('ps -ef | grep \''.$str_sh.'\' | wc -l',$output);
			return $output['0'] > 3 ? 1 : 0 ;
		};
		for($i=0;$i<$num;++$i){
			if( !$is_file_run( $str_sh ) )return 0;
			sleep( $time );
		}
		return !$is_file_run( $str_sh ) ? 0 : 1;
	}



	function thread_handler($errno, $errstr, $errfile, $errline) {	
		$error = " ,[$errno] $errstr {$errfile}::R{$errline} PHP版本: " . PHP_VERSION . "(" . PHP_OS . ") ";
		foreach ( debug_backtrace() as $t){
			if(isset($t['file'])){
				$error.= $t['file']. '::R' . $t['line'].' ';
			}else{
				$error.= $t['class'] . $t['type'] . $t['function'] . '() ';
			}
		}
		$this->R['ERROR_MSG'] .= $error;
	}



	// 此处是致命错误，直接停止脚本
	function thread_exception_handle ($e) {
		$this->R['ERROR_MSG'] .= get_class($e).": ".$e->getMessage().' '.
			$e->getFile(). '::R'.$e->getLine().' '.str_replace("\n", '', $e->getTraceAsString());
		exit();
	}



	function thread_CallbackFunction(){	
		date_default_timezone_set('PRC'); // PRC
		$this->R['LOG_FILE'] .= 'run_'.date('Y-m-d').'.log';

		# ----- ----- [ 运行类到退出的时间，但不包含执行sleep、写入日志的时间 ]
		$run_time = number_format(microtime(true)-$this->R['STARTIME'],3,'.','');

		if( empty( $this->params[ $this->params_name_string.'ru_time'] ) )
				$this->params[ $this->params_name_string.'ru_time' ] = 0;
		if( empty( $this->params[ $this->params_name_string.'ru_num'] ) )
				$this->params[ $this->params_name_string.'ru_num' ] = 0;


		if( $this->R['IS_FINISHED'] !==1 )$this->R['LOG_MSGAGE'].=', 未执行完成';
		if( $this->R['ERROR_MSG'] !== ''){
			$this->R['LOG_MSGAGE'] .= '，运行错误！('.$this->R['ERROR_MSG'].')';
		}
		foreach( $this->R as $k=>$v ){
			if( substr( $k, 0, 2 ) === 'N_' )
				$this->R['LOG_MSGAGE'] .= '，'.$v['v'].':'.$v['n'];
		}


		$params = array_merge( $this->params, $this->params_next );
		$params[ $this->params_name_string.'ru_time' ] += $run_time;
		$params[ $this->params_name_string.'ru_num' ] += 1;


		if( $params[ $this->params_name_string.'ru_num' ] > 100000000 ){
			$params[ $this->params_name_string.'ru_time' ] = 0;
			$params[ $this->params_name_string.'ru_num' ] = 0;
		}


		if( $this->run_num > 0 && $this->run_num <= $params[ $this->params_name_string.'ru_num' ] ){
			$this->R['LOG_MSGAGE'] .= '，已经执行('.$this->run_num.')完成';

		}else if( $this->R['IS_FINISHED'] === 1 && !empty( $this->run_sh ) ){

			foreach( $params as $k=>$v ){
				$this->run_sh .= ' '.$k.'='.$v;
			}
			$this->run_sh .= ' &';

			sleep( $this->finished_sleep );
			pclose( popen( $this->run_sh, 'r' ) );
			$this->R['LOG_MSGAGE'] .= ',is_run_next,sleep->'.$this->finished_sleep.'';
		}

		$this->R['LOG_MSG_END'] = '，执行时间:'.$run_time.
			', 总执行次数/时间:'.$params[ $this->params_name_string.'ru_num' ].'/'.$params[ $this->params_name_string.'ru_time' ]
				."\n".$this->R['LOG_MSG_END'];

		$this->R['LOG_MSGAGE'] = date('c').$this->R['LOG_MSGAGE'].$this->R['LOG_MSG_END'];

		if( !is_file( $this->R['LOG_FILE'] ) && !is_dir( dirname( $this->R['LOG_FILE'] ) ) ){
			mkdir( dirname( $this->R['LOG_FILE'] ), 0777, true );
		}
		file_put_contents( $this->R['LOG_FILE'], $this->R['LOG_MSGAGE'], FILE_APPEND );
		if( $this->ops['show_msg'] === 1 )echo( $this->R['LOG_MSGAGE'] );

	}
	function thread_recode(){               
		$this->R['LOG_FILE']    = empty($this->log_dir)?__DIR__.'/redis_log/':$this->log_dir;
		$this->R['IS_FINISHED'] = 0;
		$this->R['LOG_MSGAGE']  = '';
		$this->R['ERROR_MSG']   = '';
		$this->R['LOG_MSG_END'] = '';
		set_error_handler([&$this,'thread_handler']);
		set_exception_handler([&$this,'thread_exception_handle']);
		register_shutdown_function(array(&$this, 'thread_CallbackFunction')); 
	}
}




