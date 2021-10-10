<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/panel/modules/security/security.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/panel/modules/security/log.php');
if(!security::allowRequest()){die();}
if(!(security::hasPermissionOnTable($_POST['table'],'s')||security::hasPermissionOnTable($_POST['table'],'u'))){die(json_encode(array('Error'=>'Required permissions on table '.$_POST['table'])));}

include ('connect.php');
include ('tableFactory.php');
include ('utils.php');


$mysqli = new mysqli($hostname, $username, $password, $database);
if ($mysqli->connect_errno)
	{
	printf("Connect failed: %s\n", $mysqli->connect_errno);
	die();
	}
	
$tF = new TableFactory();
$table = $tF->buildTable($_POST['table'],$mysqli);
error_reporting(0);	
$needReturnData=true;
if (function_exists("set_time_limit") == TRUE AND @ini_get("safe_mode") == 0)
	{
		@set_time_limit(300);
	}

if (isset($_POST['update']))
	{//UPDATE COMMAND
		
		if(isset($_POST[$table->id()])&&($table->getByID($_POST[$table->id()])))
		{	
			$isRecordChanged=false;
			try{
				if(!security::hasPermissionOnTable($_POST['table'],'u')){throw new Exception('Required permission for UPDATE on table '.$_POST['table']);}
				$table->setRemoveTags(false);
				foreach($table->fields() as $field){
						if($field['Name']==$table->id())continue;
						if(isset($_POST[$field['Name']])){
							$fieldVal=($_POST[$field['Name']]==''?$field['Default']:$_POST[$field['Name']]);
							if(($table->getFieldValue($field['Name'])!=$fieldVal)){
								$table->setFieldValue($field['Name'],$fieldVal);
								$isRecordChanged=true;
							}
					} 
				}
				
				if($isRecordChanged){$table->update();}
				$table->setFilter($table->id(),'=',$_POST[$table->id()]);
				$needReturnData=true;
			} catch (Exception $e){
				die(json_encode(array('Error'=>$e->getMessage())));
			}
		}
	}
 	
  if($needReturnData)
	{// SELECT COMMAND
	if(!security::hasPermissionOnTable($_POST['table'],'s')){throw new Exception('Required permission for SELECT on table '.$_POST['table']);}
	$refLinks=array();
	
	if(isset($_POST['fieldset'])){
		
		$refFieldNames=array();
		$fieldset=$_POST['fieldset'];
		$requestWithRefs=(strpos($fieldset,'__')>0);
		$fieldset_arr=array();
		 if($requestWithRefs){
			foreach(explode(",",$fieldset) as $f){
				if(strpos($f,'__')>0){
					$tmp_arr = explode('__',$f);
					if(!isset($refLinks[$tmp_arr[0]])){
						$refLinks[$tmp_arr[0]]= array(	'fieldName'=>$tmp_arr[0],
														'needFieldNames'=>array($tmp_arr[1]),
														'tableName'=>null,
														'refFieldName'=>null,
														'hasFilter'=>false,
														'tableObj'=>null,
														'queryResult'=>array()
												);
					} else {
						$refLinks[$tmp_arr[0]]['needFieldNames'][]=$tmp_arr[1];
					}							
					$refFieldNames[]="'".$tmp_arr[0]."'";							
				}else{
					$fieldset_arr[]=$f;
				}
			}
			
			 $result=$mysqli->query("select column_name,referenced_table_name,referenced_column_name from information_schema.key_column_usage "
									."where referenced_table_name is not null and table_schema = '".$database."' and table_name = '".$table->getTableName()."' and column_name in (".implode(",",$refFieldNames).")");	
			
			while($row = $result->fetch_assoc()){
				$refLinks[$row['column_name']]['tableName']=$row['referenced_table_name'];
				$refLinks[$row['column_name']]['refFieldName']=$row['referenced_column_name'];
				$fieldset_arr[]=$row['column_name'];
				$refLinks[$row['column_name']]['tableObj'] = $tF->buildTable($refLinks[$row['column_name']]['tableName'],$mysqli);
				$tmp_arr=array();
				$tmp_arr=$refLinks[$row['column_name']]['needFieldNames'];
				$tmp_arr[]=$refLinks[$row['column_name']]['refFieldName'];
				$refLinks[$row['column_name']]['tableObj']->setCursorColumns($tmp_arr);
			} 
			$fieldset_arr=array_unique ($fieldset_arr); 
			$fieldset=implode(",",$fieldset_arr);
		} 
		$table->setCursorColumns(explode(",",$fieldset));
	} 
	
	if(isset($_POST['calcfieldscount'])){
		$calcfieldscount = $_POST['calcfieldscount'];
		if($calcfieldscount>0){
			
			for($i=0;$i<$calcfieldscount;$i++){
				$calcfieldname=$_POST['calcfieldname'.$i];
				$calcfieldproperties=json_decode($_POST['calcfieldproperties'.$i],true);
				$table->initVirtualField($calcfieldname,$calcfieldproperties);
				
				}
			
			}
		
		
		}
	
	
	$hasIdFilter=false;
	if(isset($_POST['filterscount'])){
		 $filterscount = $_POST['filterscount'];
		
		if($filterscount>0){
			
			for ($i = 0; $i < $filterscount; $i++)
			{
				$filtervalue = rawurldecode($_POST["filtervalue" . $i]);
				
				$filtercondition = $_POST["filtercondition" . $i];
				$filterdatafield = $_POST["filterdatafield" . $i];
				$filteroperator = $_POST["filteroperator" . $i];
				if((translateFilterCondition($filtercondition)!="UNKNOWN")&&(!strpos($filterdatafield,"__"))){
					$table->setFilter($filterdatafield, translateFilterCondition($filtercondition), $filtervalue);
				}
				$hasIdFilter = $hasIdFilter||($filterdatafield==$table->id());
				
				if(strpos($filterdatafield,"__")>0){
					$tmp_arr=array();
					$tmp_arr=explode("__",$filterdatafield);
					$refLinks[$tmp_arr[0]]['tableObj']->setFilter($tmp_arr[1],translateFilterCondition($filtercondition),$filtervalue);
					$refLinks[$tmp_arr[0]]['hasFilter']=true;
				}
				
			}
		} 
	}
	
	if(isset($_POST['addfiltercount'])){
		 $addfiltercount = $_POST['addfiltercount'];
		if($addfiltercount>0){
			
			for ($i = 0; $i < $addfiltercount; $i++)
			{
				$filtervalue = $_POST["addfiltervalue" . $i];
				$filtercondition = $_POST["addfiltercondition" . $i];
				$filterdatafield = $_POST["addfilterdatafield" . $i];
				 if(translateFilterCondition($filtercondition)!="UNKNOWN"){
					$table->setFilter($filterdatafield, translateFilterCondition($filtercondition), $filtervalue);
				}
				$hasIdFilter = $hasIdFilter||($filterdatafield==$table->id());
			}
		} 
	}

	 foreach($refLinks as $refLink){
		 
		if($refLink['hasFilter']){
			
			
			 $referedFields=$refLink['tableObj']->getGroupConcat($refLink['refFieldName']);
			 
			if(!is_null($referedFields)){
				
				$table->setFilter($refLink['fieldName'],'IN',explode(',',$referedFields));
			}else{die('{"TotalRows":"0","Rows":[]}');}  
		}
	} 

	if(isset($_POST['pagenum'])){
		$pagenum = $_POST['pagenum'];
		$pagesize = $_POST['pagesize'];
		$table->setLimit(array($pagenum*$pagesize, $pagesize));
	}
	
	if (isset($_POST['sortdatafield'])&&(isset($_POST['sortorder'])&&($_POST['sortorder']!='')))
	{
		$sortfield = $_POST['sortdatafield'];
		if(strpos($sortfield,'__')>0){$sortfield=explode('__',$sortfield);$sortfield=$sortfield[0];};
		$sortorder = $_POST['sortorder'];
		if ($sortorder != ''){
			$table->setOrder($sortfield, $sortorder);	
		} else{
			$table->setOrder($sortfield);
		}
		
	}else{
		
		if(isset($_POST['defaultSortingFields'])){
			$table->setOrder($_POST['defaultSortingFields'], $_POST['defaultSortingOrder']);
			}else{
				$table->setOrder($table->id()); }
		}	
		
	$tmp_cursor = $table->getCursorAsArray();
	foreach($refLinks as $refLink){
		$referedFields=array_filter(array_column($tmp_cursor,$refLink['fieldName'])); 
			if(!is_null($referedFields)){
				$refLink['tableObj']->setFilter($refLink['refFieldName'],'IN',$referedFields);
				$refLinks[$refLink['fieldName']]['queryResult']=$refLink['tableObj']->getCursorAsHashMap();
			}
	}
		
	
	
	$records = array();
	foreach($tmp_cursor as $row){
		
		foreach($refLinks as $refLink){
				 foreach($refLink['needFieldNames'] as $needName){
					$row[$refLink['fieldName'].'__'.$needName]=is_null($refLink['queryResult'])?null:$refLink['queryResult'][$row[$refLink['fieldName']]][$needName];
				} 
			}
		$records[] = $row;
	}
	
	$tableCount = $table->getCount();   
	    

	$records = array(
					'TotalRows' => $tableCount,
					'Rows' => $records
	);
	
	echo json_encode($records);    
	}
	
$mysqli->close();
?>
