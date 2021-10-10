<?php
	
	class resources extends Table{
		function __construct($db_link, $tableFactory=null){
				parent::__construct('resources',$db_link, $tableFactory);
		}	
		
		protected function onUpdate(){
			 
			 if(($this->get_Type()==2)&&($this->getOld_Type()!=2)){
				 $this->set_Employeeid(NULL);
				 $this->set_Description(NULL);
			 }
			 
			 
			 if(($this->get_Type()==1)&&($this->getOld_Type()!=1)){
				 if($this->get_Employeeid()!=$this->getOld_Employeeid()){
						$employee = $this->getTable('employee');
						if($employee->getById($this->get_Employeeid())){
							$this->set_Description($employee->get_First_name().' '.$employee->get_Last_name());
						} 
					} 
			 }
			
		}
		
		protected function onInsert(){
			
		}
		
		protected function onDelete(){
			
		}
		
	} 
	
	
?>