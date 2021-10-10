<?php
session_start();
if(isset($_SESSION['userid'])) {
		header("location: menu.php");
}else {
      header("location: login.php");
}			
	
?>
