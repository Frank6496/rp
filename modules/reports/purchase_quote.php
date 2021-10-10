<?php

class Purchase_quote extends Report{
	
	
	function build($args=array()){
      	
      	$purch_h = $args[0];
      	$purchase_header=$this->getTable('purchase_header');
      	$purchase_header->getById($purch_h);
      	
		$company = $this->getTable('company_info');
		$company->getFirstRow();
		$vendor = $this->getTable('vendor');
      	$arr=array('quote_number'=>$purchase_header->get_Document_no());
		if($vendor->getById($purchase_header->get_Buy_from_vendor_no())){
        	$arr = array_merge($arr, array('buy_vendor_name'=>$vendor->get_Name(),
                                          	'buy_vendor_address'=>$vendor->get_Address()));
          	
        
        };
      	if($vendor->getById($purchase_header->get_Pay_to_vendor_no())){
        	$arr = array_merge($arr, array('pay_vendor_name'=>$vendor->get_Name(),
                                          	'pay_vendor_address'=>$vendor->get_Address()));
        
        };
		$arr = array_merge($arr,array(
				'company_name'=>$company->get_Name(),
				'company_address'=>$company->get_Address(),
				'company_phone'=>$company->get_Phone(),
				'company_email'=>$company->get_Email(),
		));
		echo $this->buildSection('purchase_quote_header',$arr);
      
      	$purchase_line = $this->getTable('purchase_line');
      	$purchase_line->setFilter('header','=',$purch_h);
      	$purchase_line->setFilter('line_amount','>',0);
		$purch_lines=$purchase_line->fetchAll();
      	$order_line_types=$this->getTable('order_line_types');
      	$order_line_types = $order_line_types->fetchHashMap();
      	$uom = $this->getTable('unitofmeasure');
      	$uom = $uom->fetchHashMap();
      	$total_amount = 0;
      	foreach($purch_lines as $rec){
        	$arr_line = array(
            			'type'=>$order_line_types[$rec['type']]['code'],
              			'Description'=>$rec['description'],
              			'Quantity'=>$rec['quantity'],
              			'uom'=>$uom[$rec['unit_of_measure']]['code'],
              			'price'=>$rec['unit_price'],
            			'amount'=>$rec['line_amount']  
            
            );
          	$total_amount+=$rec['line_amount'];
          	//print line
        	echo $this->buildSection('purchase_quote_line',$arr_line);
        }
      	
      	$shipment_methods=$this->getTable('wm_shipment_method');
      	$location = $this->getTable('location');
      	if($shipment_methods->getById($purchase_header->get_Shipment_method())){};
      	if($location->getById($purchase_header->get_Location())){$ship_to_address=$location->get_Address().' '.$location->get_Address2();};
      	
      	$purch_footer= array('total_amount'=>$total_amount,
        			'shipment_method'=>$shipment_methods->get_Description(),
          			'ship_to_address'=>$ship_to_address
        );
      
      	//print footer
      	echo $this->buildSection('purchase_quote_footer',$purch_footer);
      
		
		
		}
	}

?>
