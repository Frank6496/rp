<?php
	class Order_header extends Table{
		function __construct($db_link, $tableFactory=null){
				parent::__construct('order_header',$db_link, $tableFactory);
								
				$this->initVirtualField('order_sum',
					array(
						'table'=>'order_lines',
						'field'=>'item_line_sum',
						'function'=>'SUM',
						'groupField'=>'order_id',
						'join'=>array('id','order_id')
					)
				);
				
				$this->initVirtualField('order_item_quantity',
					array(
						'table'=>'order_lines',
						'field'=>'item_line_sum',
						'function'=>'COUNT',
						'groupField'=>'order_id',
						'join'=>array('id','order_id')
					)
				);
				
				
				
		}	
		
		protected function onUpdate(){
			   
		
		}
		protected function onInsert(){
			
		}
		protected function onAfterInsert(){
						
		}
		
		protected function onAfterUpdate(){
			
		}
		
		protected function onAfterDelete(){
			
		}
		
	} 
	
	
?>
