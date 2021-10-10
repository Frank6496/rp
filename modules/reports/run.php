<?php

	
	include_once $_SERVER['DOCUMENT_ROOT'].'/panel/modules/security/security.php';
	include_once $_SERVER['DOCUMENT_ROOT'].'/panel/modules/security/log.php';
	
	if(!security::allowRequest()){die(json_encode(array('Error'=>'User not found. Please log in.')));}
	
	if(!(	isset($_POST['reportName'])&&!is_null($_POST['reportName'])))		
		{
			die('{"Error":"reportName is not set."}');
		};
			
	$reportName = $_POST['reportName'];
	
	
	if(!file_exists($reportName.'.php')){
				die('Error: report '.$reportName.' not found in '.dirname(__FILE__));
		};
		
	include_once $_SERVER['DOCUMENT_ROOT'].'/panel/modules/data/connect.php';
	include_once $_SERVER['DOCUMENT_ROOT'].'/panel/modules/data/tableFactory.php';
	include_once $_SERVER['DOCUMENT_ROOT'].'/panel/modules/reports/report.php';
	
	include($reportName.'.php');
	$mysqli = new mysqli($hostname, $username, $password, $database);
	if ($mysqli->connect_error)
	{
		die("Connect failed: %s\n".$mysqli->connect_error);
	}
	
	$tableFactory = new TableFactory();
	$report = new $reportName($mysqli, $tableFactory);
	
	$args = array();
	
	if(isset($_POST['argcount'])&&!is_null($_POST['argcount'])){
		if($_POST['argcount']>0){
			for($i=0; $i<$_POST['argcount']; $i++){
				$args[] = is_null($_POST['arg'.$i])?null:$mysqli->real_escape_string($_POST['arg'.$i]);
			};
		};
	};
	
	try{
		$report->print($args);
	}catch(Exception $e){
		echo $e->getMessage();
	}	
	

?>
