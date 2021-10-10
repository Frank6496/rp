<?php
class purchase_line extends Table{
	function __construct($db_link, $tableFactory=null){
		parent::__construct('purchase_line',$db_link, $tableFactory);
          		
	}
  
    function onUpdate(){
      		//"no" changed 
      		if($this->isChanged_No()){ 
            	switch($this->get_Type()){
                  case 1:
                     $gl_account=$this->getTable('gl_accounts');
                      if($gl_account->getById($this->get_No())){
                      	$this->set_Description($gl_account->get_Code().' '.$gl_account->get_Name());
                      }
                    break;
                  case 2:
                    $item=$this->getTable('items');
                      if($item->getById($this->get_No())){
                      	$this->set_Description($item->get_Description());
                      }
                    break;
                  case 3:
                    $resources=$this->getTable('resources');
                      if($resources->getById($this->get_No())){
                      	$this->set_Description($resources->get_Description());
                      }
                    break;
                  case 4:
                    $fixed_asset=$this->getTable('fixed_assets');
                      if($fixed_asset->getById($this->get_No())){
                      	$this->set_Description($fixed_asset->get_Description());
                      }
                    break;
                  case 5:break;
                
                }
              
              	
            }
      		// "type" changed
    		if($this->isChanged_Type()){ 
            	$this->set_No(null);
              	$this->set_Description(null);
            }
      
      		//"quantity" changed
      		if($this->isChanged_Quantity()||$this->isChanged_Unit_price()){
              $this->validateLineAmount();
          }
      
    }
  
  	function validateLineAmount(){
    		$this->set_Line_amount($this->get_Quantity()*$this->get_Unit_price());
    }
  
}
?>