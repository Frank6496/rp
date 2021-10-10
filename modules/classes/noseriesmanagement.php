<?php 

class NoSeriesManagement extends baseclass{
	function getNextNo($series_code,$commit=false){
      		$no_series = $this->getTable('no_series');
           	$no_series->setFilter('code','=',$series_code);
          	if(!$no_series->getFirstRow())return null;
          
			$last_no = null;
            $next_no = null;
            if(is_null($no_series->get_Last_no_used())||($no_series->get_Last_no_used()=='')){
            	$last_no=$no_series->get_Starting_no();
              	$next_no=$last_no;
            } else{
            	$last_no = $no_series->get_Last_no_used();
 	         	$next_no = $this->incstr($last_no,$no_series->get_Increment(),$no_series->get_Ending_no());
            }
      
            $no_series->set_Last_no_used($next_no);
            $no_series->update($commit);  
          	
          	return $next_no;
        
        }
		
  
  	function incstr($string,$increment,$max_no=null){
			if(is_null($string)||($string=='')) return null;
			
			$separated_arr = $this->separateBaseAndDigits($string);
			if(!is_null($max_no)){ // check if the limit has exceeded
            	$max_separated = $this->separateBaseAndDigits($max_no);
              	if(intval($separated_arr['digits'])>(intval( $max_separated['digits'])-$increment)){
                	throw new Exception('No. Series limit '.$max_no.' has been exceeded.');
                }
            }
			$digits_portion = intval( $separated_arr['digits'] + $increment);
			$add_zeros='';
			$count=$separated_arr['number_digits']-strlen($digits_portion);
			for($k=0;$k<$count;$k++){
				
				$add_zeros.='0';
				}
			
			return $separated_arr['base'].$add_zeros.$digits_portion;
		
		}
  	
  private function separateBaseAndDigits($string){
        	$tailing_number_digits =  0;
			$i = 0;
			$from_end = -1;
			
			while ( $i < strlen($string) ) {
				if ( is_numeric( substr( $string,$from_end - $i, 1 ) ) ) {
					$tailing_number_digits++;
				}
				else{
					// End our while if we don't find a number anymore
					break;
				}
				$i++;
			}		
			
			$base_portion = $string;
			$digits_portion = '';
			
			if ( $tailing_number_digits > 0 ) {
				$base_portion = substr( $string, 0, -$tailing_number_digits );
				$digits_portion = abs(substr( $string, -$tailing_number_digits ));	
			}
          
          	return array('base'=>$base_portion,'digits'=>$digits_portion,'number_digits'=>$tailing_number_digits);
        
        }
  
  
	} 

?>
