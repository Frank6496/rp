<?php

class utils extends baseclass{
	
	function addrow($args=array()){
		$tableName=$args[0];
		if(!security::hasPermissionOnTable($tableName,'i')){print 'Required permission for INSERT on table '.$tableName;return;}
		$table=$this->getTable($tableName);
		$this->beginTransaction();
		try{
		if(isset($args[1])&&($args[1]!='{}')&&($args[1]!='')){
			$beforeInsertValues = json_decode(str_replace('\"', '"', $args[1]), true);
			$table->copyFieldValuesFrom($beforeInsertValues);
		}
		$table->insert(false);
		$this->commitTransaction();
		} catch(Exception $e){
			print $e->getMessage();
			$this->rollbackTransaction();
		}
	}
	
	function deleterow($args=array()){
		$tableName=$args[0];
		if(!security::hasPermissionOnTable($tableName,'d')){print 'Required permission for DELETE on table '.$tableName;return;}
		$rowids = $args[1];
		$table=$this->getTable($tableName);
		$this->beginTransaction();
		try{
			foreach(explode(',', $rowids) as $id){
					$table->deleteById($id);
			}
			$this->commitTransaction();
		} catch (Exception $e){
			print $e->getMessage();
			$this->rollbackTransaction();
		}
	}
	
	function setUserPassword($args=[]){
		try{
			$user = $args[0];
			if(!security::isAdmin())
				{throw new Exception('You cannot change password for user '.$user);}
			$newpass = security::cryptpwd($args[1]);
			$users = $this->getTable('sys_users');
			$users->getById($user);
			$users->set_Password($newpass);
			$users->update(true);
			echo json_encode(array('Success'=>'success'));
		} catch(Exception $e){
			echo json_encode(array('Error'=>$e->getMessage()));
		}
		
		
	}
	
	function changeUserPassword($args=[]){
		try{
			$pass=security::cryptpwd($args[0]);
			$newpass=security::cryptpwd($args[1]);
			$users = $this->getTable('sys_users');
			$users->getById(security::userId());
			if($users->get_Password()!=$pass){throw new Exception('Invalid Password');}
			$users->set_Password($newpass);
			$users->update(true);
			echo json_encode(array('Success'=>'success'));
		} catch(Exception $e){
			echo json_encode(array('Error'=>$e->getMessage()));
		}
		
		
	}
	
	function refresh_tables(){
		try{
			$sys_tables = $this->getTable('sys_tables');
			$sys_tables->setCursorColumns(array('name'));
			$tab_arr = $sys_tables->getCursorAsArray();
			$tab_arr = array_column($tab_arr, 'name');
			$this->beginTransaction();
			$result = $this->db_link->query('SELECT DATABASE()'); 
			$database = $result->fetch_row();
			$query = 'SHOW TABLES FROM '.$database[0];
			$result = $this->db_link->query($query);
			while($row=$result->fetch_assoc()){
				$table_name=$row['Tables_in_'.$database[0]];
				if(!in_array($table_name,$tab_arr)){
					$sys_tables->set_Name($table_name);
					$sys_tables->insert(false);
				}	
			}
			$this->commitTransaction();
		}
		catch(Exception $e){
			$this->rollbackTransaction();
			json_encode(array('Error'=>$e->getMessage()));
		}
		
		
	}
	
	
	function getformcontent($args=array()){
		if(!security::isAdmin()||empty($args))return;
		$formName = $args[0];
		if($formName=='index') return;
		$fileName = $_SERVER['DOCUMENT_ROOT'].'/panel/forms/'.$formName.'.php';
		if(file_exists($fileName)){
			echo file_get_contents($fileName);
		}
		
		}
	
	
	function setformcontent($args=array()){
		if(!security::isAdmin()||empty($args))return;
		$formName = $args[0];
		if($formName=='index') return;
		$filecontent = $args[1];
		if($filecontent=='')return;
		$fileName = $_SERVER['DOCUMENT_ROOT'].'/panel/forms/'.$formName.'.php';
		file_put_contents($fileName, str_replace(["\\n","\\"], ["\r\n",""],$filecontent));
		chmod($fileName, 0755);
		
		}
	
	function updateformslist(){
		if(!security::isAdmin())return;
		try{
			$scanresult = scandir($_SERVER['DOCUMENT_ROOT'].'/panel/forms/');
			$formslist = array();
			foreach ($scanresult as $value){
				if($value == 'index.php') continue;
				if(substr($value,-4,4)=='.php'){
					
					$formslist[] = substr($value,0,strlen($value)-4);
					}
				
				}
			$sys_forms = $this->getTable('sys_forms');
			$sys_forms->setCursorColumns(array('name'));
			$sys_forms_in_table = array_column($sys_forms->fetchAll(),'name');
			$formslist_new = array_diff($formslist,$sys_forms_in_table);
			$formslist_delete = array_diff($sys_forms_in_table,$formslist);
			$formslist = array();
			foreach ($formslist_new as $value){
				$formslist[]['name']=$value;	
			}
			$sys_forms->bulkInsert($formslist);
			
			if(!empty($formslist_delete)){
				$sys_forms->setFilter('name','IN',$formslist_delete);
				$deleted_forms_ids = $sys_forms->getGroupConcat();
				
				$sys_forms->resetFilters();
				$forms_ids = explode(',',$sys_forms->getGroupConcat());
				$deleted_forms_ids = explode(',',$deleted_forms_ids);
				
				$sys_forms->skipOnUpdate(true);
				foreach($forms_ids as $id){
						$sys_forms->getById($id);
						$sys_forms->set_Notfound(in_array($id,$deleted_forms_ids)&&!empty($deleted_forms_ids)?1:0);
						if($sys_forms->getOld_Notfound()!=$sys_forms->get_Notfound()){
							$sys_forms->update(true);
						}
					};
			}	
			echo '{"Success":"Success"}';
				
			}
			catch(Exception $e){
				echo '{"Error":"'.$e->getMessage().'"}';
			}
		}
		
	function getclasscontent($args=array()){
		if(!security::isAdmin()||empty($args))return;
		$className = $args[0];
		if(in_array($className,array('baseclass','run','utils'))) return;
		$fileName = $_SERVER['DOCUMENT_ROOT'].'/panel/modules/classes/'.$className.'.php';
		if(file_exists($fileName)){
			echo file_get_contents($fileName);
		}
		
		}
	
	
	function setclasscontent($args=array()){
		if(!security::isAdmin()||empty($args))return;
		$className = $args[0];
		if(in_array($className,array('baseclass','run','utils'))) return;
		$filecontent = $args[1];
		if($filecontent=='')return;
		$fileName = $_SERVER['DOCUMENT_ROOT'].'/panel/modules/classes/'.$className.'.php';
		file_put_contents($fileName, str_replace(["\\n","\\"], ["\r\n",""],$filecontent));
		chmod($fileName, 0755);
		
		}
		
	function gettablecontent($args=array()){
		if(!security::isAdmin()||empty($args))return;
		$tableName = $args[0];
		if(substr( $tableName, 0, 4 ) === "sys_") return;
		$fileName = $_SERVER['DOCUMENT_ROOT'].'/panel/modules/data/tables/'.$tableName.'.php';
		if(file_exists($fileName)){
			echo file_get_contents($fileName);
		}
		
		}
	
	
	function settablecontent($args=array()){
		if(!security::isAdmin()||empty($args))return;
		$tableName = $args[0];
		if(substr( $tableName, 0, 4 ) === "sys_") return;
		$filecontent = $args[1];
		if($filecontent=='')return;
		$fileName = $_SERVER['DOCUMENT_ROOT'].'/panel/modules/data/tables/'.$tableName.'.php';
		file_put_contents($fileName, str_replace(["\\n","\\"], ["\r\n",""],$filecontent));
		chmod($fileName, 0755);
		
		}
		
	function getreportcontent($args=array()){
		if(!security::isAdmin()||empty($args))return;
		$reportName = $args[0];
		if(in_array(strtolower($reportName),array('report','run')))return;
		$fileName = $_SERVER['DOCUMENT_ROOT'].'/panel/modules/reports/'.$reportName.'.php';
		if(file_exists($fileName)){
			echo file_get_contents($fileName);
		}
		
		}
	
	
	function setreportcontent($args=array()){
		if(!security::isAdmin()||empty($args))return;
		$reportName = $args[0];
		if(in_array(strtolower($reportName),array('report','run')))return;
		$filecontent = $args[1];
		$fileName = $_SERVER['DOCUMENT_ROOT'].'/panel/modules/reports/'.$reportName.'.php';
		file_put_contents($fileName, str_replace(["\\n","\\"], ["\r\n",""],$filecontent));
		chmod($fileName, 0755);
		}
	
	function savereporttemplate($args=array()){
		if(!security::isAdmin()||empty($args))return;
		try{
			$this->beginTransaction();
			
			$filecontent = base64_decode(explode('base64,',$args[0])[1]);
			$templatename = $args[1];
			$reportid=$args[2];
			
			$reports = $this->getTable('sys_reports');
			$reports->getById($reportid);
			$reports->set_Template($templatename);
			$reports->update(false);
			$fileName = $_SERVER['DOCUMENT_ROOT'].'/panel/modules/reports/templates/'.$templatename;
			file_put_contents($fileName,$filecontent);
			chmod($fileName, 0755);
			
			$this->commitTransaction();
			echo '{"Success":"Report teplate has been uploaded"}';
		}catch(Exception $e){
			echo '{"Error":"'.$e->getMessage().'"}';
			$this->rollbackTransaction();
			}	
		}
		
	function downloadreporttemplate($args=array()){
		if(!security::isAdmin()||empty($args))return;
		try{
			$templatename = $args[0];
			$fileName = $_SERVER['DOCUMENT_ROOT'].'/panel/modules/reports/templates/'.$templatename;
			$filecontent = file_get_contents($fileName);
			if($filecontent!=''){
				echo $filecontent;
				}
		}catch(Exception $e){
			echo '{"Error":"'.$e->getMessage().'"}';
			}	
		}
		
	function saveformstate($args=array()){
			$formname = $args[0];
			$state = stripcslashes($args[1]);
			$sys_form_states = $this->getTable('sys_form_states');
			$sys_form_states->setFilter('user','=',security::userId());
			$sys_form_states->setFilter('form','=', $formname);
			if($sys_form_states->getFirstRow()){
				$sys_form_states->set_State($state);
				$sys_form_states->update();
			}else{
				$sys_form_states->init();
				$sys_form_states->set_User(security::userId());
				$sys_form_states->set_Form($formname);
				$sys_form_states->set_State($state);
				$sys_form_states->insert();
			}
			
		}
	function loadformstate($args=array()){
			$formname = $args[0];
			$sys_form_states = $this->getTable('sys_form_states');
			$sys_form_states->setFilter('user','=',security::userId());
			$sys_form_states->setFilter('form','=', $formname);
			if($sys_form_states->getFirstRow()){
				echo $sys_form_states->get_State();
			}else{
				echo '';
			}
		}
}	
