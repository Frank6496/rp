<?php
	class Employee extends Table{
		function __construct($db_link, $tableFactory=null){
				parent::__construct('employee',$db_link, $tableFactory);
          		
		}	
		
		protected function onUpdate(){
			
			$this->set_UpdateUser( security::userId() );
			$this->set_UpdateDateTime(date('Y-m-d H:i:s', time())); 
		
		}
		
		protected function onInsert(){
			
			$this->set_CreateUser(security::userId());
			$this->set_CreateDateTime(date('Y-m-d H:i:s', time())); 
			
		}
		
		protected function onDelete(){
			
		}
		
	} 
	
	
?>