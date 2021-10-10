<?php
	class Gl_accounts extends Table{
		function __construct($db_link, $tableFactory=null){
				parent::__construct('gl_accounts',$db_link, $tableFactory);
          		
		}
  
  		function getCursorAsArray(){
        	$cursor = parent::getCursorAsArray();
          	$gl_accounts = $this->getTable('gl_accounts');
          	$gl_accounts->copyVirtualFields($this);
          	$hashmap = $gl_accounts->getCursorAsHashMap('code');
          	
          	foreach($cursor as $key=>$value){
            	if(!is_null($value['totaling'])&&($value['totaling']!='')){
                  	$cursor[$key]['start_balance'] = $this->getTotaling($value['totaling'], $hashmap, 'start_balance');
                	$cursor[$key]['end_balance'] = $this->getTotaling($value['totaling'], $hashmap, 'end_balance');
                	$cursor[$key]['debit'] = $this->getTotaling($value['totaling'], $hashmap, 'debit');
                  	$cursor[$key]['credit'] = $this->getTotaling($value['totaling'], $hashmap, 'credit');
                }
            }
          	
          	return $cursor;
        }
  		
  		protected function getTotaling($range, $hashmap, $type){
          $total=0;	
          $range = explode('&',$range);
          
          foreach($range as $value){
           	$tmp_range = explode('..',$value);
             	
             if(count($tmp_range)>1)
             {
               foreach($hashmap as $key=>$hashvalue){
               	if($key<$tmp_range[0]){continue;}
                if($key>$tmp_range[1]){break;}
                 $total+=$hashvalue[$type];
                 
               }
              
             }
             
            if(count($tmp_range)==1){$total+=$hashmap[$tmp_range[0]][$type];};
             
            
          }
          return $total;
        }

}
?>