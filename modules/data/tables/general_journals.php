<?php
	class General_journals extends Table{
		function __construct($db_link, $tableFactory=null){
				parent::__construct('general_journals',$db_link, $tableFactory);
          		
		}	
		
		protected function onUpdate(){
          
          if($this->isChanged_Account_no()){ 
			switch($this->get_Account_type()){
              case 1: 
                $gl_accounts=$this->getTable('gl_accounts');
                if($gl_accounts->getById($this->get_Account_no())){
                	$this->set_Account_description($gl_accounts->get_Code().' '.$gl_accounts->get_Name());
                }; break;
            
            }
          }
          
          if($this->isChanged_Bal_account_no()){ //bal account changed
			switch($this->get_Bal_account_type()){
              case 1: 
                $gl_accounts=$this->getTable('gl_accounts');
                if($gl_accounts->getById($this->get_Bal_account_no())){
                	$this->set_Bal_account_description($gl_accounts->get_Code().' '.$gl_accounts->get_Name());
                }; break;
            
            }
          }
          
          if($this->isChanged_Account_type()){
          	$this->set_Account_no(null);
            $this->set_Account_description(null);
          }
          
          if($this->isChanged_Bal_account_type()){
          	$this->set_Bal_account_no(null);
            $this->set_Bal_account_description(null);
          }
          
          return;
		
		}
		
		protected function onInsert(){
			
			
			
		}
		
		protected function onDelete(){
			
		}
		
	} 
	
	
?>
