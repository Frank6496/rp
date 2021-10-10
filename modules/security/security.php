<?php

class security{
	
	public static function setSessionTablesPermissions($userid, $username, $mysqli){
			if($username=='admin'){return;}
			
			$roles_arr = self::getUserRoles($userid,$mysqli);
			if(empty($roles_arr)) {return;}
			$roles = implode(',',array_filter(array_column($roles_arr,0)));
			$query = "SELECT b.name, max(a.allow_select) as allow_select, max(a.allow_insert) as allow_insert, max(a.allow_update) as allow_update, max(a.allow_delete) as allow_delete  FROM sys_roles_tables a,sys_tables b where a.tableid=b.id and a.roleid in ($roles) group by name"; 
			$result = $mysqli->query($query);
			$rights=array();
			while ($row = $result->fetch_assoc()){
					$rights[$row['name']]= array(	's'=>($row['allow_select']==1),
													'i'=>($row['allow_insert']==1),
													'u'=>($row['allow_update']==1),
													'd'=>($row['allow_delete']==1)
					);
				}
			$_SESSION['tr'] = $rights;	
	}
	
	public static function setSessionMenuItemPermissions($userid, $username, $mysqli){
			if(self::isAdmin())return;
			$roles_arr = self::getUserRoles($userid,$mysqli);
			if(empty($roles_arr)) {return;}
			$roles = implode(',',array_filter(array_column($roles_arr,0)));
			$query = "SELECT menuitemid FROM sys_role_menuitems where roleid in ($roles) group by menuitemid"; 
			$result = $mysqli->query($query);
			$menuitems = array();
			while ($row = $result->fetch_assoc()){
				$menuitems[]=$row['menuitemid'];
			}
			$_SESSION['menuitems'] = $menuitems;
	}
	
	public static function hasPermissionOnMenuItem($menuitemid){
			if(self::isAdmin())return true;
			return in_array($menuitemid, $_SESSION['menuitems']);
	}
	
	public static function isAdmin(){
		return ($_SESSION['username']=='admin');
		}
	
	public static function getUserRoles($userid, $mysqli){
			$query = "SELECT role FROM sys_user_roles WHERE user = $userid";
			$result = $mysqli->query($query);
			return $result->fetch_all();
	}
	public static function hasPermissionOnTable($tablename, $righttype='s'){
		if(self::isAdmin())return true;
		return isset($_SESSION['tr'])&&isset($_SESSION['tr'][$tablename])&&($_SESSION['tr'][$tablename][$righttype]);
	}
	
	public static function allowRequest(){
		if(!($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'))
			{
				return false;
			}  
		// defend crossite requests
		if ("POST" == $_SERVER["REQUEST_METHOD"]) {
			if (isset($_SERVER["HTTP_ORIGIN"])) {
				$address = "http://".$_SERVER["SERVER_NAME"];
				if (strpos($address, $_SERVER["HTTP_ORIGIN"]) !== 0) {
					return false;
				}
			}else{return false;} 
		}
		if(!isset($_SESSION))session_start();
		if(!isset($_SESSION['userid'])){return false;}
		return true;
	}
	
	public static function userId(){
		if(!isset($_SESSION))session_start();
		return (isset($_SESSION['userid']))?$_SESSION['userid']:null;
		}
	public static function userName(){
		if(!isset($_SESSION))session_start();
		return (isset($_SESSION['username']))?$_SESSION['username']:null;
		}
	
	public static function cryptpwd($pass){
			return md5(''.$pass); //add salt here
		}	
		
	public static function login(){
		session_start();

		include $_SERVER['DOCUMENT_ROOT'].'/panel/modules/data/connect.php';
		$mysqli = new mysqli($hostname, $username, $password, $database);
		/* check connection */
		if (mysqli_connect_errno())
			{
			printf("Connection to database is lost.");
			die();
		}
		
		if($_SERVER["REQUEST_METHOD"] == "POST") {
		
			$myusername = $mysqli->real_escape_string($_POST['username']);
			$mypassword = self::cryptpwd($mysqli->real_escape_string($_POST['password'])); 
		  
			$sql = "SELECT id FROM sys_users WHERE username = '$myusername' and password = '$mypassword'";
			$result = $mysqli->query($sql);
			$row = $result->fetch_assoc();
			
		  if(!is_null($row)) {
			 $user = $row['id'];
			 $_SESSION['userid'] = $user;
			 $_SESSION['username'] = $myusername;
			 self::setSessionTablesPermissions($user, $myusername, $mysqli);
			 self::setSessionMenuItemPermissions($user, $myusername, $mysqli);
			 header("location: menu.php");
		  }else {
			 header("location: login.php?error=1");
		  }
		}
	}
}
