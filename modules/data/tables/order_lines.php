<?php
	class Order_lines extends Table{
		function __construct($db_link, $tableFactory=null){
				parent::__construct('order_lines',$db_link, $tableFactory);
		}	
		
		protected function onUpdate(){
			  if($this->get_Item_quantity()!=$this->getOld_Item_quantity()){
				$this->set_Item_line_sum($this->get_Item_quantity()*$this->get_Item_price());
			}  
		
		}
		protected function onInsert(){
			
		}
		protected function onAfterInsert(){
						
		}
		
		protected function onAfterUpdate(){
			 if($this->get_Item_line_sum()!=$this->getOld_Item_line_sum()){
				$this->updateOrderSum($this->get_Order_id());
			}	 
			
		}
		
		protected function onAfterDelete(){
			$this->updateOrderSum($this->get_Order_id());
		}
		
		private function updateOrderSum($order_id){
			$order_header = $this->getTable('order_header');
			$order_lines = $this->getTable('order_lines');
			$order_lines->setFilter('order_id','=', $order_id);
			$totalAmount=$order_lines->getSumColumn('item_line_sum');
			if($order_header->getById($order_id)){
				$order_header->set_Total_amount($totalAmount);
				$order_header->update();
			}  
			
		}
		
	} 
	
	
?>