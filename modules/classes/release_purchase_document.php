<?php

class release_purchase_document extends baseclass{
  
  function release($args){
  	$doc_id = $args[0];
    $purchase_header = $this->getTable('purchase_header');
    if($purchase_header->getById($doc_id)){
    	if($purchase_header->get_Released()){
          return;
        }else{
        	$purchase_header->set_Released(1);
        	$purchase_header->update(true);
        }
    
    
    }
  
  
  }
  
  function reopen($args){
  	$doc_id = $args[0];
    $purchase_header = $this->getTable('purchase_header');
    if($purchase_header->getById($doc_id)){
    	if(!$purchase_header->get_Released()){
          return;
        }else{
        	$purchase_header->set_Released(0);
        	$purchase_header->update(true);
        }
    
    
    }
  
  
  }
  
}

?>