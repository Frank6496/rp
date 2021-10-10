<?php 

class Importclass extends baseclass{
	function import($args=array()){
      	
      	if(empty($args))return;
    	$data = $args[0];
     	$data = $this->replaceLineEndInText($data);
     
      	$tableName = $args[1];
      	$truncate = $args[2];
      	
      	try{
          $table = $this->getTable($tableName);
          $data = json_decode($data,TRUE);
         
          if($truncate)$table->truncate();
          $table->bulkInsert($data);
        }catch(Exception $e){
        	echo $e->getMessage();
        }
      
    }
}
?>
