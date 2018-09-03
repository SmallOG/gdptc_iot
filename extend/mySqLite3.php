<?php

class mySqLite3 extends SQLite3
{
	public function __construct( $file )
	{        
		if ( !is_writable( dirname( $file ) )) {
			echo 'The file is not writable';
			exit();
		}
		$this->open($file);

		if(!$this){
			echo $this->lastErrorMsg();
		}

	}

	public function my_exec($sql)
	{
		// echo( $sql."\n" );
		// ++$GLOBALS['recode_sqlite_exec'];
		return  $this->exec($sql);
	}
	public function my_query($sql)
	{
		// echo( $sql."\n" );
		// ++$GLOBALS['recode_sqlite_select'];
		return $this->query( $sql );
	}




	public function delete($table, $where)
	{
		if (empty($where)) {
			return false;
		}

		$sql = 'DELETE FROM ' . $table . ' WHERE ' . $where . ' ;';
		$ret = $this->my_exec($sql);

		if (!$ret) {
			return 0;
		} else {
			return 1;
		}
	}

	public function update($table, $data, $where)
	{
		if (!is_array($data) || empty($where)) {
			return false;
		}

		$sql = 'UPDATE ' . $table . ' set ';
		foreach ($data as $k => $v) {
			$sql .= '`' . $k . '`="' . $v . '",';
		}
		$sql = rtrim( $sql, ',' );
		$sql .= ' WHERE ' . $where . ' ;';

		$ret = $this->my_exec($sql);
		if (!$ret) {
			return 0;
		} else {
			return 1;
		}
	}

	public function insert($table, $data)
	{
		$sql = 'INSERT INTO ' . $table . ' ("' . implode('","', array_keys($data)) . '") ' .
		'VALUES ("' . implode('","', $data) . '"); ';
		$ret = $this->my_exec($sql);
		if (!$ret) {
			return 0;
		} else {
			return 1;
		}
	}





	public function queryall($sql)
	{
		$ret = $this->my_query($sql);
		$ar  = array();
		while ($row = $ret->fetchArray(SQLITE3_ASSOC)) {
			$ar[] = $row;
		}
		return $ar;
	}

	public function find($sql)
	{
		$ret = $this->my_query($sql);
		$ar  = $ret->fetchArray(SQLITE3_ASSOC);
		if (!is_array($ar)) {
			$ar = array();
		}

		return $ar;
	}

}
