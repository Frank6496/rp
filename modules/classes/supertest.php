<?php 

class supertest extends baseclass{
	function message($args=array()){
    
    	$classes = $this->getTable('sys_classes');
      	echo $classes->getGroupConcat('name');
    }
}
?>
