<?php 

class crypt extends baseclass{
	function encrypt($args=array()){
      $NoSeriesManagement = $this->getClass('noseriesmanagement');
      echo $NoSeriesManagement->getNextNo('SALES_INVOICE', true);
      	
    }
  
  	function xor_string($string, $key) {
    	for($i = 0; $i < strlen($string); $i++) 
   	     $string[$i] = ($string[$i] ^ $key[$i % strlen($key)]);
    	return $string;
	}
  
  	
  
}
?>
