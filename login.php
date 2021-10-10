<?php

session_start();
include 'modules/security/security.php';
security::login();

?>
<html>
   
   <head>
	  <meta charset="UTF-8">
      <title>Login Page</title>
      <link rel="stylesheet" href="css/style.css">
    
   </head>
   
   <body bgcolor = "#FFFFFF">
	
	<div class="background-wrap">
	<div class="background"></div>
	</div>
	
	<form id="accesspanel" action="" method="post">
	  <h1 id="litheader">YOUR COMPANY</h1>
	  <div class="inset">
		<p>
		  <input type="text" name="username" id="email" placeholder="Login">
		</p>
		<p>
		  <input type="password" name="password" id="password" placeholder="Password">
		</p>
		<div style = "font-size:11px; color:#cc0000; margin-top:10px"><?php if(isset($_GET['error'])){echo 'Invalid Login or Password.';}; ?></div>
	  </div>
	  <p class="p-container">
		<input type="submit" name="Login" id="go" value="Sign in">
	  </p>
	</form>
   </body>
</html>
