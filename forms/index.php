<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/panel/modules/security/security.php');
if(!isset($_SESSION)){session_start();};
if(!isset($_SESSION['userid'])){die('Error: Disconnected. Please log in.');};

if(!isset($_GET['id'])){die('Requested form id not found');};
$is_admin = (isset($_GET['admin'])&&($_SESSION['userid']==1))?($_GET['admin']==1):false;
$uri = dirname(__FILE__).($is_admin?'/admin/':'/').$_GET['id'].'.php';
if(!file_exists($uri)){die('Requested form file '.$_GET['id'].' not found');};

if(!isset($_GET['template'])){
	include('templates/default.php');}
else{
	include('templates/'.$_GET['template'].'.php');
}
run($uri); 

?>
