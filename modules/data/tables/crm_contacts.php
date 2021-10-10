<?php
	class Crm_contacts extends Table{
		function __construct($db_link, $tableFactory=null){
				parent::__construct('crm_contacts',$db_link, $tableFactory);
          		$this->setSelectionView('crm_contacts_view');
		}	
		
		protected function onUpdate(){
			
		
		}
		
		protected function onInsert(){
			
			
		}
		
		protected function onDelete(){
			
		}
		
	} 
	
	
?>