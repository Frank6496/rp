<?php

class loger {
	
	public static function log($message){
			error_log($message."\r\n", 3, "/var/tmp/my-errors.log");
	
		}
	
	
	}

?>
