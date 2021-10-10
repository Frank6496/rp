<?php
	class purchase_header extends Table{
		function __construct($db_link, $tableFactory=null){
				parent::__construct('purchase_header',$db_link, $tableFactory);
          		
		}	
		
		protected function onUpdate(){
			if($this->isChanged_Buy_from_vendor_no()){
            	$this->set_Pay_to_vendor_no($this->get_Buy_from_vendor_no());
            }
          
          	if($this->isChanged_Pay_to_vendor_no()){
            	$vendor=$this->getTable('vendor');
              	if($vendor->getById($this->get_Pay_to_vendor_no())){
                	$payment_terms = $this->getTable('payment_terms');
                  if(!is_null($vendor->get_Payment_terms())&&$payment_terms->getById($vendor->get_Payment_terms())){
                    $dateformulas = $this->getClass('dateformulas');
                  	$this->set_Due_date($dateformulas->calcdate($payment_terms->get_Due_date_calculation()));
                  }else{
                  	$this->set_Due_date(null);
                  }
                
                }
              	
            }
			
		
		}
		
		protected function onInsert(){
			$purch_no_series_settings = $this->getTable('purch_series_settings');
          	$purch_no_series_settings->getFirstRow();
          	$noseriesmanagement = $this->getClass('noseriesmanagement');
          	switch($this->get_Document_type()){
              case 1:$this->set_Document_no($noseriesmanagement->getNextNo($purch_no_series_settings->get_Quotes(),false));break;//quote
              case 2:$this->set_Document_no($noseriesmanagement->getNextNo($purch_no_series_settings->get_Orders(),false));break;//order
              case 3:$this->set_Document_no($noseriesmanagement->getNextNo($purch_no_series_settings->get_Invoices(),false));break;//invoice
              case 4:$this->set_Document_no($noseriesmanagement->getNextNo($purch_no_series_settings->get_Cr_memos(),false));break;//Credit Memo
              case 5:$this->set_Document_no($noseriesmanagement->getNextNo($purch_no_series_settings->get_Blanket_orders(),false));break;//Blanket Order
              case 6:$this->set_Document_no($noseriesmanagement->getNextNo($purch_no_series_settings->get_Return_orders(),false));break;//Return Order
            
            }
          
			$this->set_Order_date(date('Y-m-d H:i:s', time()));
          	$this->set_Posting_date(date('Y-m-d H:i:s', time()));
			
		}
		
		protected function onDelete(){
			
		}
		
	} 
	
	
?>