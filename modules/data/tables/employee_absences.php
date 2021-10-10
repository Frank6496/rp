<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/panel/modules/data/table.php');
	class employee_absences extends Table{
		function __construct($db_link, $tableFactory=null){
				parent::__construct('employee_absences',$db_link, $tableFactory);
		}	
		
		protected function onUpdate(){
			 
			 if((!is_null($this->get_From_date()))&&(!is_null($this->get_To_date()))){
				if($this->get_From_date()!=$this->get_To_date()){
						$date_from = date_create($this->get_From_date());
						$date_to = date_create($this->get_To_date());
						$interval = date_diff($date_to,$date_from); 
						$this->set_Qty($interval->format('%a'));
						$this->set_Unitofmeasure(3);
					}
				else{
						$this->set_Unitofmeasure(5);
					}
			 }
		 
		}
		
		protected function onInsert(){
			
		}
		
		protected function onDelete(){
			
		}
		
	} 
	
	
?>