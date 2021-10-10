<?php
	
	include_once $_SERVER['DOCUMENT_ROOT'].'/panel/modules/security/security.php';
	include_once $_SERVER['DOCUMENT_ROOT'].'/panel/modules/security/log.php';
	include_once('baseclass.php');
	if(!security::allowRequest()){die(json_encode(array('Error'=>'User not found. Please log in.')));}
	
	if(!(	isset($_POST['className'])&&!is_null($_POST['className'])&&
		isset($_POST['funcName'])&&!is_null($_POST['funcName']))){
			die('Error: className or funcName not set.');
		};
			
	$className = $_POST['className'];
	$funcName = $_POST['funcName'];
	
	if(!file_exists($className.'.php')){
				die('Error: class '.$className.' not found in '.dirname(__FILE__));
		};
		
	include_once $_SERVER['DOCUMENT_ROOT'].'/panel/modules/data/connect.php';
	include_once $_SERVER['DOCUMENT_ROOT'].'/panel/modules/data/tableFactory.php';
	
	include($className.'.php');
	$mysqli = new mysqli($hostname, $username, $password, $database);
	if ($mysqli->connect_error)
	{
		die("Connect failed: %s\n".$mysqli->connect_error);
	}
	
	$tableFactory = new TableFactory();
	$class = new $className($mysqli, $tableFactory);
	
	if(!method_exists($class, $funcName)){
		die('Error: class '.$className.' has not method '.$funcName);
	};
	$args = array();
	
	if(isset($_POST['argcount'])&&!is_null($_POST['argcount'])){
		if($_POST['argcount']>0){
			for($i=0; $i<$_POST['argcount']; $i++){
				$args[] = is_null($_POST['arg'.$i])?null:$mysqli->real_escape_string($_POST['arg'.$i]);
			};
		};
	};
	
	try{
		$class->$funcName($args);
	}catch(Exception $e){
		echo '{"Error":"'.$e->getMessage().'"}';
	}	


?>
