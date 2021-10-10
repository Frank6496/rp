<?php

class test extends baseclass{
	
	function ftest($args=array()){
		$id=$args[0];
		try{
			$employee = $this->getTable('employee');
			if($employee->getByID($id)){
				$this->beginTransaction();
				$this->setExecutionTime(300);
				for($i=0;$i<5;$i++){
					$employee->insert(false);
				}	
				$this->commitTransaction();
			};
		} catch (Exception $e){
			$this->rollbackTransaction();
			print $e->getMessage();
		}
	}
	
	function itemsForPrint(){
		try{
			$items=$this->getTable('items');
			$itemcategory=$this->getTable('itemcategory');
			$tmp = $items->getGroupConcat('category');
			$itemcategory->setFilter('id','IN',$tmp);
			
			$records = array('items'=>$items->getCursorAsArray(), 'itemcategory'=>$itemcategory->getCursorAsHashMap());
			print json_encode($employee->getCursorAsArray());
			$out = fopen('php://output', 'w');
			foreach($employee->getCursorAsArray() as $value){
				fputcsv($out, $value);
			}
			fclose($out);
			
		} catch(Exception $e) {
			print json_encode(array('Error'=>$e->getMessage()));
		}
		
	}
	
	function exportCSV(){
		$employee = $this->getTable('employee');
		$this->exportArrayAsCSV($employee->getCursorAsArray(), true);
	}

	function exportXLS(){
			header ( "Content-type: application/vnd.ms-excel" );
			header ( "Content-Disposition: attachment; filename=foo_bar.xls" );
			$employee = $this->getTable('employee');
			$employee->setCursorColumns(array('id', 'birth_date', 'first_name', 'last_name', 'employeephone', 'email'));
			print $this->createHTMLTableFromArray($employee->getCursorAsArray(),true);
		
	}

	function fftest($args=array()){
		$id=$args[0];
		try{
			$employee = $this->getTable('employee');
			if($employee->getByID($id)){
				$this->beginTransaction();
				$records=[];
				for($i=1;$i<=100000;$i++){
					if(($i % 10000)==0){
						$employee->bulkInsert($records,false);
						$records=[];
					} else {
						$records[]= $employee->getRecordAsArray();
					}
				}	
				$this->commitTransaction();
			};
		} catch (Exception $e){
			$this->rollbackTransaction();
			print $e->getMessage();
		}
	}
	
	
	function documentRelease($args=array()){
		$doSleep=$args[0];
		try{
			$this->beginTransaction();
			
			$tS=$this->getTable('transactionsettings');
			$tJ=$this->getTable('transactionjournal');
			$tS->getFirstRowByFilter();
			
			if($doSleep){sleep(10);}
			
			$tJ->set_Transno($tS->get_Transcounter()+1);
			$tJ->insert(false);
			$tS->set_Transcounter($tS->get_Transcounter()+1);
			$tS->update(false);
			
			$this->commitTransaction();
			print json_encode(array('Success'=>''));
			
		}catch(Exception $e){
			$this->rollbackTransaction();
			print json_encode(array('Error'=>$e->getMessage()));
		}
	}
	
	function testDistinct(){
		try{
			$empl = $this->getTable('employee');
			$h=$empl->getGroupConcat('department');
			
			throw new Exception($h);
		} catch(Exception $e){
			print $e->getMessage();
		}		
	}
	
	function calcfield($arg){
		$id=$arg[0];
		
		$orders = $this->getTable('order_header');
		//$orders->setFilter('id','=',$id);
		$order_sum_settings = array(
			'table'=>'order_lines',
			'field'=>'item_line_sum',
			'function'=>'COUNT',
			'groupField'=>'order_id',
			'join'=>array('id','order_id')
		);
		$orders->initVirtualField('order_sum',$order_sum_settings);
		$records=$orders->fetchAll();
		$orders->calcVirtualField($records,'order_sum');
		var_dump($records);
		
	}
  	function fields(){
    	$employee = $this->getTable('employee');
      	var_dump($employee->fields());
      
    }
}

?>
