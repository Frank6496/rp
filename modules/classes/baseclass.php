<?php

class baseclass{
		
	protected $db_link;
	protected $tableFactory;
	function __construct($mysqli, $tableFactory){
		$this->db_link = $mysqli;
		$this->tableFactory =$tableFactory;
	}
		
	protected function beginTransaction(){
		$this->db_link->autocommit(FALSE);
	}
	
	protected function commitTransaction(){
		$this->db_link->commit();
		$this->db_link->autocommit(TRUE);
	}
	protected function rollbackTransaction(){
		$this->db_link->rollback();
	}
	
	protected function getTable($tableName){
		try{
		 return $this->tableFactory->buildTable($tableName, $this->db_link, $this->tableFactory);
		} catch (Exception $e){
			throw new Exception('Error getting table '.$tableName.' in function getTable in class '.get_class($this).' : '.$e->getMessage());
		} 
	}
	
	protected function getClass($className){
		include_once($className.'.php');
		return new $className($this->db_link,$this->tableFactory);
		}
	
	protected function setExecutionTime($time){
		if (function_exists("set_time_limit") == TRUE AND @ini_get("safe_mode") == 0)
			{
				@set_time_limit($time);
			}
		}
		
	
	protected function exportArrayAsCSV($a=[], $h=false){
			if(empty($a)) return;
			$out = fopen('php://output', 'w');
			if($h){
				fputcsv($out, array_keys(current($a)));
			}
			foreach($a as $v){
				fputcsv($out, $v);
			}
			fclose($out); 
		}
	
	protected function createHTMLTableFromArray($a=[], $h=false){
			if(empty($a))return '';
			$out='<table>';
			if (count($a) > 0){
				$out.='<thead><tr><th>'.implode('</th><th>', array_keys(current($a))).'</th></tr></thead>';
			}
			$out.='<tbody>';
			foreach($a as $v){
				array_map('htmlentities', $v);
				$out.='<tr><td>'.implode('</td><td>', $v).'</td></tr>';
			}
			$out.='</tbody></table>';
			return $out;
		}
	protected function replaceLineEndInText($text){
			return str_replace(["\\n","\\"], ["\r\n",""],$text);
		
		}
	

}

?>
