<?php

class Sales_invoice extends Report{
	
	
	function build($args=array()){
		$company = $this->getTable('company_info');
		$company->getFirstRow();
		$customer = $this->getTable('vendor');
		$customer->getById(2);
		$arr = array(
				'company_name'=>$company->get_Name(),
				'company_address'=>$company->get_Address(),
				'company_phone'=>$company->get_Phone(),
				'company_email'=>$company->get_Email(),
				'customer_name'=>$customer->get_Name(),
				'invoice_number'=>'INV-0001983',
				'customer_address'=>$customer->get_Address(),
				'posting_date'=>'07/07/2018',
				'due_date'=>'07/14/2018'
		
		);
		echo $this->buildSection('invoice_header',$arr);
		echo $this->buildSection('invoice_lines');
		echo $this->buildSection('invoice_lines', array('description'=>'hard work','hours'=>'24/7','rate'=>1000, 'amount'=>'$1 000 000'));
		echo $this->buildSection('invoice_lines');
		echo $this->buildSection('invoice_footer');
		
		}
	}

?>
