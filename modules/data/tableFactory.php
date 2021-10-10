<?php
include_once('table.php');
class TableFactory{
	protected $tables = array();
	function buildTable($tableName, $mysqli){
		if(isset($this->tables[$tableName]))
			return clone $this->tables[$tableName];
		
		if(file_exists(dirname(__FILE__).'/tables/'.$tableName.'.php')){
			include_once dirname(__FILE__).'/tables/'.$tableName.'.php';
			$this->tables[$tableName] = new $tableName($mysqli, $this);
			return clone $this->tables[$tableName];
		} else {
			$this->tables[$tableName] = new Table($tableName, $mysqli, $this);
			return clone $this->tables[$tableName];
		}
	}
} 

?>