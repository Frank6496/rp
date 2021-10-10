<?php

class stdObject {
    public function __call($method, $arguments) {
        $arguments = array_merge(array("stdObject" => $this), $arguments); 
        if (isset($this->{$method}) && is_callable($this->{$method})) {
            return call_user_func_array($this->{$method}, $arguments);
        } else {
            throw new Exception("Fatal error: Call to undefined method {$this}::{$method}()");
        }
    }
}

class Table extends stdObject{
	protected $name;
	protected $fields;
	protected $fieldsView;
	protected $db_link;
	protected $id=null; 
	protected $field_count;
	protected $filters;
	protected $order;
	protected $cursorColumns;
	protected $limit;
	protected $onInsert=true;
	protected $onUpdate=true;
	protected $onDelete=true;
	protected $tF;
	protected $secureInput=true;
	protected $removeTags=true;
	protected $virtualFields=array();
	protected $selectFromView=null;
	protected $filterFields=array();
	protected $agregates=array();
	protected $initLinkedObjects=false;
	
	function __construct($name, $db_link, $tableFactory=null){
		
		$this->db_link = $db_link;
		$this->name = $this->secureValue($name);
		$this->field_count=0;
		$this->filters = array();
		$this->order = null;
		$this->cursorColumns = null;
		$this->limit = array();
		$this->tF = $tableFactory;
		
		$query = "SHOW COLUMNS FROM ".$this->name;
		$result = $this->db_link->prepare($query);
		if(!$result){
			throw new Exception("Error: Can't get info of table ".$this->name);
		};
		$result->execute();
		if($this->db_link->error){
			throw new Exception($this->db_link->error);
		};
		$result->bind_result($Name, $Type, $Null, $Key, $Default, $Extra);
		$this->fields = array();
		while ($result->fetch())
		{
			
			if(strpos($Type,"(")>0){
				$ttype=explode("(",$Type);
				$Type = strtoupper($ttype[0]);
				$Length = str_replace(")","",$ttype[1]);
			}
			
			$fieldQuotes="";
			if(strpos($Name,' ')>0){
				$fieldQuotes = "`";
			}
			$this->fields[$Name] = array(
											'Name' => $Name,
											'Type' => $Type,
											'Length'=> $Length==''?0:$Length,
											'Null' => $Null,
											'Key' => $Key,
											'Default' => $Default,
											'Extra' => $Extra,
											'Value' =>isset($Default)?$Default:null,
											'oldValue' =>isset($Default)?$Default:null,
											'Quotes'=>$this->getQuotes($Type),
											'fieldQuotes'=>$fieldQuotes,
											'ref_table'=>null,
											'ref_column'=>null
									);
					
			if(($Key=="PRI")){
				$this->id = $Name;
			}
			$this->field_count++;
			
			$this->{"set_" . ucfirst($Name)} = function($stdObject, $value) use ($Name){
				 $stdObject->setFieldValue($Name, $value);
			};
			$this->{"get_" . ucfirst($Name)} = function($stdObject) use ($Name){
				 return $stdObject->getFieldValue($Name);
			};
			$this->{"getOld_" . ucfirst($Name)} = function($stdObject) use ($Name){
				 return $stdObject->getFieldOldValue($Name);
			};
			$this->{"isChanged_".ucfirst($Name)} = function($stdObject) use ($Name){
				return $stdObject->isChangedFieldValue($Name);
			};
			
		}

		$result->free_result();
	}
	
	protected function onInsert(){}
	protected function onUpdate(){}
	protected function onDelete(){}
	
	protected function onAfterInsert(){}
	protected function onAfterUpdate(){}
	protected function onAfterDelete(){}
	
	
	function skipOnInsert($param=true){$this->onInsert=!$param;}
	function skipOnUpdate($param=true){$this->onUpdate=!$param;}
	function skipOnDelete($param=true){$this->onDelete=!$param;}  
	
	public function get_dblink(){
	    return $this->db_link;
	}
	
	public function beginTransaction(){
		$this->db_link->autocommit(FALSE);
	}
	
	public function commitTransaction(){
		$this->db_link->commit();
		$this->db_link->autocommit(TRUE);
	}
	public function rollbackTransaction(){
		$this->db_link->rollback();
	}
	
	
	function getTableName(){
		return $this->name;
	}
	
	function init(){
		 foreach($this->fields as $field){
			$field['Value'] = isset($field['Default'])?$field['Default']:null;
			$field['oldValue'] = isset($field['Default'])?$field['Default']:null;
		} 
	}
	
	function initLinkedObjects(){
		$query = "select column_name,referenced_table_name,referenced_column_name from information_schema.key_column_usage where referenced_table_name is not null and table_name = '$this->name'";
		$result = $this->db_link->prepare($query);
		if(!$result){
			throw new Exception("Error: Can't get info of table ".$this->name);
		};
		$result->execute();
		if($this->db_link->error){
			throw new Exception($this->db_link->error);
		};
		$result->bind_result($column_name, $referenced_table_name, $referenced_column);
		while ($result->fetch())
		{
			$this->fields[$column_name]['ref_table'] = $referenced_table_name;
			$this->fields[$column_name]['ref_column'] = $referenced_column;
			$this->{"obj_".ucfirst($column_name)} = function($stdObject) use ($column_name) {
					if(is_null($stdObject->fields[$column_name]['ref_table'])) {throw new Exception("Error: table ".$this->name." field:".$column_name." has no foreign_key to another table.");}
					if(is_null($stdObject->getFieldValue($column_name))){return null;}
					$obj = $stdObject->getTable($this->fields[$column_name]['ref_table']);
					if($obj->getById($stdObject->getFieldValue($column_name))){
							return $obj;
					}else{return null;}
				};
		}		
		$this->initLinkedObjects=true;
		$result->free_result();
	}
	
	function insert($commit=true){
		$this->db_link->autocommit(FALSE);
		$insertId = null;
		try{
			if($this->onInsert){$this->onInsert();}
	
			$fieldnames=array();
			$fieldvalues=array();
			foreach($this->fields as $field){
					$fieldnames[]=$field['Name'];
					if(isset($field['Value'])&&($field['Name']!=$this->id)){
						$fieldvalues[]=$field['Quotes'].$this->secureValue($field['Value']).$field['Quotes'];
					} else{
							$fieldvalues[]='NULL';
					}
			};
		
			$query = "INSERT INTO ".$this->name." (".implode(",", $fieldnames).") VALUES (".implode(",", $fieldvalues).")";
			if(!$this->db_link->query($query)){
				throw new Exception('MySQL Error:'.$this->db_link->error);
			};
			$insertId = $this->db_link->insert_id;
			if($commit){$this->db_link->commit();}
			$this->set_Id($insertId);
			if($this->onInsert){$this->onAfterInsert();}
			return $insertId;
			
		} catch (Exception $e){
			$this->db_link->rollback();
			throw $e;
		} finally {
			$this->db_link->autocommit($commit);
		}
		return false;	
	}
	
	
	
	function bulkInsert($records=array(), $commit=true){
		
		if(empty($records)){return;}
		$this->db_link->autocommit(FALSE);
		$this_saved_state = $this->getRecordAsArray();
		$values_str=array();
		$fieldnames=array();
		foreach($this->fields as $field){
					$fieldnames[]=$field['Name'];
		}		
		try{
			foreach($records as $rec){
				$this->copyAllFieldValuesFrom($rec);
				if($this->fields[$this->id]['Extra']=='auto_increment'){$this->set_Id(null);}
				if($this->onInsert){$this->onInsert();}
				$values_str[]='('.implode(",",$this->suitArrayForQuery($this->getRecordAsArray())).')';
			}
			$query = "INSERT INTO ".$this->name." (".implode(",", $fieldnames).") VALUES ".implode(",",$values_str);
			
			
			
			if(!$this->db_link->query($query)){
				throw new Exception('MySQL Error:'.$this->db_link->error);
			};
			if($commit){$this->db_link->commit();}
		} catch (Exception $e){
			$this->db_link->rollback();
			throw $e;
		} finally {
			$this->copyFieldValuesFrom($this_saved_state);
			$this->db_link->autocommit($commit);
		}
		
	}	
	
	function deleteById($id_input, $commit=true){
		$id = $this->cleanNumber($id_input);
		$this->db_link->autocommit(FALSE);
		try{
			if($this->getById($id)){
				if($this->onDelete){$this->onDelete();}
			
				$query = "DELETE FROM ".$this->name." WHERE ".$this->id."=".$this->secureValue($id);
				if(!$this->db_link->query($query)){
					throw new Exception('MySQL Error:'.$this->db_link->error);
				};
				if($commit){$this->db_link->commit();}
				if($this->onDelete){$this->onAfterDelete();}
			}
		} catch (Exception $e){
			$this->db_link->rollback();
			throw $e;
		} finally {
			$this->db_link->autocommit($commit);		
		}
	}
	
	protected function secureValue($value){
		return $this->secureInput?$this->db_link->real_escape_string($value):$value;
	}
	
	protected function cleanNumber($value){
		return preg_replace('/[^0-9.]+/', '', $value.'.');
	}
	function setSecureInput($param=true){
		$this->secureInput=$param;
	}
	function setRemoveTags($param=true){
		$this->removeTags=$param;
	}
	function getById($id_input){
		$id = $this->cleanNumber($id_input);
		$query = "SELECT * FROM ".$this->getNameForSelect()." WHERE ".$this->id."=".$this->secureValue($id)." LIMIT 1";
		$result = $this->db_link->query($query);
		$row = $result->fetch_assoc();
		if(is_null($row)){return false;};
		foreach($this->fields as $field){		
			$this->setFieldValue($field['Name'],$row[$field['Name']]);
			$this->fields[$field['Name']]['oldValue']=$row[$field['Name']];			
		};
		$result->free();
		return true;
	}
	
	function update($commit=true){
		$this->db_link->autocommit(FALSE);
		try{
			if($this->onUpdate){$this->onUpdate();}
			
			$fieldsforupdate=array();
			foreach($this->fields as $field){
				if(($field['Name']!=$this->id)){
					if(!is_null($field['Value'])){
						$fieldsforupdate[]=$this->getQuotedFieldName($field['Name'])."=".$field['Quotes'].$this->secureValue($field['Value']).$field['Quotes'];
					} else{
						$fieldsforupdate[]=$this->getQuotedFieldName($field['Name'])."=NULL";
					}
				}
				
				
			};
			$query = "UPDATE ".$this->name." SET ".implode(",", $fieldsforupdate)." WHERE ".$this->id."=".$this->secureValue($this->fields[$this->id]['Value']);
			
			if(!$this->db_link->query($query)){
				throw new Exception('MySQL Error:'.$this->db_link->error);
			};
			if($commit){$this->db_link->commit();}
			if($this->onUpdate){$this->onAfterUpdate();}
		} catch (Exception $e){
			$this->db_link->rollback();
			throw $e;
		} finally {
			$this->db_link->autocommit($commit);		
		}
		
	}	
	
	function setFieldValue($fieldName,$fieldValue){
		if(!isset($this->fields[$fieldName])){
				throw new Exception("Column ".$fieldName." not found in table ".$this->name);
		};
		$this->fields[$fieldName]['oldValue'] = $this->fields[$fieldName]['Value'];
		if($this->removeTags){
			$this->fields[$fieldName]['Value'] = $this->isStringType($fieldName)?strip_tags($fieldValue):$fieldValue;
			return;
		} 
		
		$this->fields[$fieldName]['Value'] = $fieldValue;
			
	}
	
	function getFieldValue($fieldName){
		if(!isset($this->fields[$fieldName])){
				if(isset($this->fieldsView[$fieldName])){ return $this->fieldsView[$fieldName]['Value'];}
				else {throw new Exception("Column ".$fieldName." not found in table or view ".$this->name);}
		};
		return $this->fields[$fieldName]['Value'];
	}
	
	
	function getFieldOldValue($fieldName){
		if(!isset($this->fields[$fieldName])){
				throw new Exception("Column ".$fieldName." not found in table ".$this->name);
		};
		return $this->fields[$fieldName]['oldValue'];
	}
	
	function isChangedFieldValue($fieldName){
			if(is_null($this->getFieldOldValue($fieldName)) xor is_null($this->getFieldValue($fieldName))) return true;
			else
			return ($this->getFieldValue($fieldName)!=$this->getFieldOldValue($fieldName));
		}
	
	protected function getNameForSelect(){
		return (is_null($this->selectFromView)?$this->name:$this->selectFromView);
		}
	
	function getSum($column){
		
		$query = 'SELECT SUM('.$column.') as s FROM '
				.$this->getNameForSelect()
				.$this->buildWhere();
		$result = $this->db_link->query($query);
		$row = $result->fetch_assoc();
		$result->free();
		return is_null($row['s'])?0:$row['s'];
		
	}
	function getAvg($column){
		
		$query = 'SELECT AVG('.$column.') as a FROM '
				.$this->getNameForSelect()
				.$this->buildWhere();
		$result = $this->db_link->query($query);
		$row = $result->fetch_assoc();
		$result->free();
		return is_null($row['a'])?0:$row['a'];
		
	}
	function getMin($column){
				
		$query = 'SELECT MIN('.$column.') as m FROM '
				.$this->getNameForSelect()
				.$this->buildWhere();
		$result = $this->db_link->query($query);
		$row = $result->fetch_assoc();
		$result->free();
		return is_null($row['m'])?null:$row['m'];
		
	}
	function getMax($column){
				
		$query = 'SELECT MAX('.$column.') as m FROM '
				.$this->getNameForSelect()
				.$this->buildWhere();
		$result = $this->db_link->query($query);
		$row = $result->fetch_assoc();
		$result->free();
		return is_null($row['m'])?null:$row['m'];
		
	}
	protected function isNumericType($fieldName){
		$numericTypes = array('DECIMAL','INT','BIGINT','DOUBLE','FLOAT','MEDIUMINT','REAL','SMALLINT','TINYINT');
		return in_array($this->fields[$fieldName]['Type'], $numericTypes);
	}
	protected function isStringType($fieldName){
		$stringTypes = array('CHAR','JSON','VARCHAR','NVARCHAR','TEXT','LONGTEXT','MEDIUMTEXT','TINYTEXT');
		return in_array($this->fields[$fieldName]['Type'], $stringTypes);
	}
	protected function getQuotes($type){
		switch(strtoupper($type)){
			case 'CHAR':
			case 'NVARCHAR':
			case 'VARCHAR':
			case 'DATETIME':
			case 'DATE':
			case 'TIME':
			case 'TIMESTAMP':
			case 'TEXT':
			case 'LONGTEXT':
			case 'MEDIUMTEXT':
			case 'TINYTEXT':
			case 'YEAR':
					return "'";	
					break;
			default: 
					return "";
		}
	}
	
	function fields(){
		return $this->fields;
	}
	
	function id(){
		return $this->id;
	}
	
	function resetFilters(){
		$this->filters=array();
		$this->order=null;
		$this->cursorColumns = null;
		$this->limit=array();
	}
	function reset(){
		$this->filters=array();
		$this->order=null;
		$this->cursorColumns = null;
		$this->limit=array();
	}
	
	function setFilter($column, $filterType, $filterValue1=null, $filterValue2=''){
		$quotes='';
        if(!isset($this->fields[$column])){
				if(!isset($this->selectFromView))
				return;
				if(!isset($this->fieldsView[$column]))
				return;
                $quotes = $this->getQuotes($this->fieldsView[$column]['Type']);
		}else{
                $quotes = $this->getQuotes($this->fields[$column]['Type']);
        };
		$columnQuoted = $this->getQuotedFieldName($column);				
		switch(strtoupper($filterType)){
			case 'IS NULL':
			case 'IS NOT NULL':
				$this->filters[]=' '.$columnQuoted.' '.strtoupper($filterType);
				return;
		}
		
		if(is_null($filterValue1)){return;};
		
		switch(strtoupper($filterType)){
			case '=':
						$this->filters[]= ' '.$columnQuoted.'='.$quotes.$this->secureValue($filterValue1).$quotes.' ';
						break;
			case '>':
						$this->filters[]= ' '.$columnQuoted.'>'.$quotes.$this->secureValue($filterValue1).$quotes.' ';
						break;
			case '>=':
						$this->filters[]= ' '.$columnQuoted.'>='.$quotes.$this->secureValue($filterValue1).$quotes.' ';
						break;
			case '<':
						$this->filters[]= ' '.$columnQuoted.'<'.$quotes.$this->secureValue($filterValue1).$quotes.' ';
						break;
			case '<=':
						$this->filters[]= ' '.$columnQuoted.'<='.$quotes.$this->secureValue($filterValue1).$quotes.' ';
						break;
			case '<>':
						$this->filters[]= ' '.$columnQuoted.'<>'.$quotes.$this->secureValue($filterValue1).$quotes.' ';
						break;
			case 'BETWEEN':
					if($filterValue2!=''){
						$this->filters[]= ' ('.$columnQuoted.'>='.$quotes.$this->secureValue($filterValue1).$quotes.' AND '
						.$columnQuoted.'<='.$quotes.$this->secureValue($filterValue2).$quotes.') ';
					};
						break;
			case 'NOT BETWEEN':
					if($filterValue2!=''){
						$this->filters[]= ' NOT ('.$columnQuoted.'>='.$quotes.$this->secureValue($filterValue1).$quotes.' AND '
						.$columnQuoted.'<='.$quotes.$this->secureValue($filterValue2).$quotes.') ';
					};
						break;			
			case 'IN':
			case 'NOT IN':
					if(!is_array($filterValue1)){
						if(($filterValue1=='')||is_null($filterValue1)){return;}
						$filterValue1 = explode(',',$filterValue1);
						}
					
						if(empty($filterValue1)){return;}
						$tmp = array();
						
						foreach($filterValue1 as $value){
							$tmp[] = $quotes.$this->secureValue($value).$quotes;
						};
					$this->filters[]=' '.$columnQuoted.' '.strtoupper($filterType).' ('.implode(",",$tmp).') ';					
					
					break;
			case 'LIKE':
			case 'NOT LIKE':
					$this->filters[]=' '.$columnQuoted.' '.strtoupper($filterType).' \'%'.$this->secureValue($filterValue1).'%\' ';
					break;
			case 'LIKE %_':		
					$this->filters[]=' '.$columnQuoted.' LIKE \'%'.$this->secureValue($filterValue1).'\' ';
					break;
			case 'LIKE _%':		
					$this->filters[]=' '.$columnQuoted.' LIKE \''.$this->secureValue($filterValue1).'%\' ';
					break;
			default: throw new Exception("Error: operator ".$filterType." undefined for filtering.");		
		}		
		return;
	}
	
	protected function getQuotedFieldName($fieldName){
		return $this->fields[$fieldName]['fieldQuotes'].$fieldName.$this->fields[$fieldName]['fieldQuotes'];
		}
	
	function setOrder($c,$orderType='ASC'){
		$columns = explode(',',$c);
		$orders = explode(',',$orderType);
		$column = array();
		foreach($columns as $key=>$value){
				if(isset($this->fields[$value])||isset($this->fieldsView[$value])){
					$of = $this->getQuotedFieldName($value);
					$oforder = 'ASC';
					if(isset($orders[$key])){
						if(strtoupper($orders[$key])=='DESC')$oforder='DESC';
					}
					$column[]=$of.' '.$oforder;
					}
			};
		if(empty($column))return;	
		$this->order = ' ORDER BY '.implode(',',$column);
		return;
	}
	
	function setCursorColumns($columns=null){
		if(is_null($columns)){return;}
		
		if(is_array($columns)){
				$tmp = array();
				foreach($columns as $column){
					if($column==$this->id())continue;
					if(isset($this->fields[$column])){
						$tmp[]=$this->fields[$column]['fieldQuotes'].$column.$this->fields[$column]['fieldQuotes'];
					}
					if(!is_null($this->selectFromView)&&isset($this->fieldsView[$column])){
						$tmp[]=$this->fieldsView[$column]['fieldQuotes'].$column.$this->fieldsView[$column]['fieldQuotes'];
					}
					
				}
				
				if(empty($tmp)){return;}
				
				$this->cursorColumns = $this->id.(!is_null($columns)?','.implode(",",array_unique($tmp)):'');
		}
	}
	
	function getCursor(){
		//error_log($this->buildQuery()."\r\n", 3, "/var/tmp/my-errors.log");
		$result = $this->db_link->query($this->buildQuery());		
		if($this->db_link->error){
			throw new Exception('MySQL Error:'.$this->db_link->error.' Query:'.$this->buildQuery());
		}
		return $result;
	}
	
	function setLimit($limit=array()){
		$this->limit = $limit;
	}
	
	protected function buildQuery(){
		return 'SELECT '
					.(is_null($this->cursorColumns)?'*':$this->cursorColumns)
					.' FROM '
					.$this->getNameForSelect()
					.$this->buildWhere()
					.(is_null($this->order)?'':' '.$this->order)
					.(empty($this->limit)?'':' LIMIT '.implode(",",$this->limit));
					
	}
		
	protected function buildWhere(){
		return (empty($this->filters)?'':' WHERE '.implode(" AND ",$this->filters));
	}
	function getFirstRowByFilter(){
		$this->init();
		$this->setLimit(array(1));
		$result = $this->db_link->query($this->buildQuery());
		$row = $result->fetch_assoc();
		if(is_null($row)){return false;};
		foreach($this->fields as $field){
			$this->setFieldValue($field['Name'],$row[$field['Name']]);
			$this->fields[$field['Name']]['oldValue']=$row[$field['Name']];
		};
		$result->free();
		return true;
	}
	function getFirstRow(){
		return $this->getFirstRowByFilter();
	}
	function getCount(){
		$query = 'SELECT COUNT(*) as total FROM '
				.$this->getNameForSelect()
				.$this->buildWhere();
		$result = $this->db_link->query($query);
		$row = $result->fetch_assoc();
		$result->free();
		return is_null($row['total'])?0:$row['total'];
	}
	
	function getMinId(){
		$query = 'SELECT MIN('.$this->id().') as minid FROM '
				.$this->getNameForSelect()
				.$this->buildWhere();
		$result = $this->db_link->query($query);
		$row = $result->fetch_assoc();
		$result->free();
		return is_null($row['minid'])?0:$row['minid'];
	}
	
	function getMaxId(){
		$query = 'SELECT MAX('.$this->id().') as maxid FROM '
				.$this->getNameForSelect()
				.$this->buildWhere();
		$result = $this->db_link->query($query);
		$row = $result->fetch_assoc();
		$result->free();
		return is_null($row['maxid'])?0:$row['maxid'];
	}
	
	function copyFieldValuesFrom($copyFrom){
		$className = 'Table';
		if($copyFrom instanceof $className){
			$arrayFieldsCopyFrom = $copyFrom->fields();
			foreach($this->fields as $field){
				if(isset($arrayFieldsCopyFrom[$field['Name']])&&$field['Name']!=$this->id){
					$this->setFieldValue($field['Name'],$arrayFieldsCopyFrom[$field['Name']]['Value']);
				}
			}
		}elseif(is_array($copyFrom)){
			foreach($this->fields as $field){
				if(isset($copyFrom[$field['Name']])&&$field['Name']!=$this->id){
					$this->setFieldValue($field['Name'],$copyFrom[$field['Name']]);
				}
			}
		}
	}
	
	function copyAllFieldValuesFrom($copyFrom){
		$className = 'Table';
		if($copyFrom instanceof $className){
			$arrayFieldsCopyFrom = $copyFrom->fields();
			foreach($this->fields as $field){
				if(isset($arrayFieldsCopyFrom[$field['Name']])){
					$this->setFieldValue($field['Name'],$arrayFieldsCopyFrom[$field['Name']]['Value']);
				}
			}
		}elseif(is_array($copyFrom)){
			foreach($this->fields as $field){
				if(isset($copyFrom[$field['Name']])){
					$this->setFieldValue($field['Name'],$copyFrom[$field['Name']]);
				}
			}
		}
	}
	
	 function getGroupConcat($fieldName=null){
		$fieldName = is_null($fieldName)?$this->id():$this->secureValue($fieldName);
		$query = 'SELECT DISTINCT '.$fieldName.' FROM '
				.$this->getNameForSelect()
				.$this->buildWhere();
		$result = $this->db_link->query($query);
		if($result->num_rows==0){$result->free();return null;}
		$row = $result->fetch_all();
		$result->free();
		return implode(',',array_filter(array_column($row,0)));
	} 
	
	function getCursorAsArray(){
		$result = $this->getCursor();
		if($result->num_rows==0){$result->free();return null;}
		$records = array();
		while($row=$result->fetch_assoc()){
			$records[]=$row;
		}
		
		if(!empty($this->virtualFields)){
			foreach($this->virtualFields as $key=>$value){
				$this->calcVirtualField($records, $key);
			 }
		}
		
		$result->free();
		return $records;
	}
	
	protected function suitArrayForQuery($arr){
		$inputArray = $arr;
		
		foreach($this->fields as $field){
			if(isset($inputArray[$field['Name']])){
				$inputArray[$field['Name']]=$field['Quotes'].$this->secureValue($inputArray[$field['Name']]).$field['Quotes'];
			}
			if(is_null($inputArray[$field['Name']])){$inputArray[$field['Name']]='NULL';}
		}
		return $inputArray;
	}
	
	function getRecordAsArray(){
		$record = array();
		foreach($this->fields as $field){
			$record[$field['Name']]=$field['Value'];
		}
		
		if(!empty($this->virtualFields)){
			foreach($this->virtualFields as $key=>$value){
				$this->calcVirtualField($record, $key);
			 }
		}
		
		return $record;
	}
	
	function fetchRecord(){
		return $this->getRecordAsArray();
	}
	
	function getOldRecordAsArray(){
		$record = array();
		foreach($this->fields as $field){
			$record[$field['Name']]=$field['OldValue'];
		}
		return $record;
	}
	function fetchOldRecord(){
		return $this->getOldRecordAsArray();
	}
	
	function fetchAll(){
		return $this->getCursorAsArray();
	}
	
	function getCursorAsHashMap($key_field=null){
			$key=$this->id();
			if(!is_null($key_field)&&(isset($this->fields[$key_field]))){
				$key = $key_field;
			}
			$result = $this->getCursor();
			if($result->num_rows==0){$result->free();return null;}
			$records = array();
			while($row=$result->fetch_assoc()){
				$records[$row[$key]]=$row;
			}
			
			$result->free();
			
			if(!empty($this->virtualFields)){
			foreach($this->virtualFields as $key=>$value){
				$this->calcVirtualField($records, $key);
			 }
			}
			
			return $records;
	}
	
	function fetchHashMap($key_field=null){
		return $this->getCursorAsHashMap($key_field);
	}
	
	protected function getTable($tableName){
		try{
			if(!is_null($this->tF)){
				$table = $this->tF->buildTable($tableName, $this->db_link, $this->tF);
				if($this->initLinkedObjects){$table->initLinkedObjects();}
				return $table;
			}else{return null;}	
		} catch (Exception $e){
			throw new Exception('Error getting table '.$tableName.' in function getTable in table class '.get_class($this).' : '.$e->getMessage());
		} 
	}
	/*
	protected function getClass($className){
		if(is_null($className)||($className=='')) return null;
		try{
			include_once($_SERVER['DOCUMENT_ROOT'].'/panel/modules/classes/'.$className.'.php');
			return new $className($this->db_link,$this->tF);
		}catch(Exception $e){
			throw new Exception('Error getting class '.$className.' in function getClass in table class '.get_class($this).' : '.$e->getMessage());
		}
		
	}
	*/
	function initVirtualField($virtualField,$vFieldSettings){
		
		$this->virtualFields[$virtualField]=$vFieldSettings;
		/*									array(	'datatable'=>$vTableName,
													'field'=>$vFieldName,
													'agregate'=>$vFieldFunction,
													'group'=>$vGroupFields,
													'join'=>$vJoinField,
													'jon_to'=>$vJoinToField
													'filter'=>array(filters)
		);*/
	}
	
	function getVirtualFields(){
		return $this->virtualFields;
		}
	
	function copyVirtualFields($t){
		$className = 'Table';
		if(!($t instanceof $className))return;
		$this->virtualFields = array_merge($this->virtualFields, $t->getVirtualFields());
		}
		
	function getGroupCursor($groupByFields){
		$group_by = $this->secureValue($groupByFields);
		$columns='';
		$agr_arr = array();
		foreach($this->agregates as $agr_cols){
			$agr_arr[]=$agr_cols['function'].'('.$agr_cols['field'].') as '.$agr_cols['field'];
			}
		if(!empty($agr_arr)){
			$columns=$group_by.','.implode(',',$agr_arr);
		}else{
			$columns=$group_by;
			}
		
		
		$query = 'SELECT '.$columns.' FROM '.$this->getNameForSelect()
					.$this->buildWhere()
					.' GROUP BY '.$group_by;
		
		$result=$this->db_link->query($query);
		
		if($this->db_link->error){
			throw new Exception('MySQL Error:'.$this->db_link->error.' Query:'.$query);
		}
		
		return $result;
		
		}
	
	function setAgregate($field, $func){
		if(!in_array(strtoupper($func),array('SUM','MIN','MAX','AVG','COUNT'))) return;
			$this->agregates[] = array('field'=>$this->secureValue($field),'function'=> $this->secureValue($func));
		}	
	
	protected function calcVirtualField(&$records, $virtualField){
		if(!isset($this->virtualFields[$virtualField]))return;
		
		$vFieldSettings = $this->virtualFields[$virtualField];
		$datatable = $this->getTable($vFieldSettings['datatable']);
		$vFieldFilters = $vFieldSettings['filter'];
		foreach($vFieldFilters as $filter){
				$filter_field = $filter[0];
				$filter_cond = $filter[1];
				$filter_value1 = isset($filter[2])?$filter[2]:null;
				$filter_value2 = isset($filter[3])?$filter[3]:null;
				$datatable->setFilter($filter_field,$filter_cond,$filter_value1,$filter_value2); 
		}
		
		$datatable->setFilter($vFieldSettings['join'],'IN',array_column($records, $vFieldSettings['join_to']));
		$datatable->setAgregate($vFieldSettings['field'],$vFieldSettings['agregate']);		
		
		$result = $datatable->getGroupCursor($vFieldSettings['group']);
		$vrecords=array();
		while($row=$result->fetch_assoc()){
				$vrecords[$row[$vFieldSettings['join']]]=$row;
			}
		$result->free();
		foreach($records as &$record){
				$record[$virtualField]=$vrecords[$record[$vFieldSettings['join_to']]][$vFieldSettings['field']];
		}
		
	}
	
	function setSelectionView($viewName){
		$this->selectFromView = $this->secureValue($viewName);
		if(is_null($this->selectFromView)) return;
		$query = "SHOW COLUMNS FROM ".$this->selectFromView;
		$result = $this->db_link->prepare($query);
		if(!$result){
			throw new Exception("Error: Can't get info of view ".$this->selectFromView);
		};
		$result->execute();
		if($this->db_link->error){
			throw new Exception($this->db_link->error);
		};
		$result->bind_result($Name, $Type, $Null, $Key, $Default, $Extra);
		$this->fieldsView = array();
		while ($result->fetch())
		{
			
			if(isset($this->fields[$Name]))continue;
			
			if(strpos($Type,"(")>0){
				$ttype=explode("(",$Type);
				$Type = strtoupper($ttype[0]);
				$Length = str_replace(")","",$ttype[1]);
			}
			
			$fieldQuotes="";
			if(strpos($Name,' ')>0){
				$fieldQuotes = "`";
			}
			
			$this->fieldsView[$Name] = array(
											'Name' => $Name,
											'Type' => $Type,
											'Length'=> $Length==''?0:$Length,
											'Null' => $Null,
											'Key' => $Key,
											'Default' => $Default,
											'Extra' => $Extra,
											'Value' =>isset($Default)?$Default:null,
											'oldValue' =>isset($Default)?$Default:null,
											'Quotes'=>$this->getQuotes($Type),
											'fieldQuotes'=>$fieldQuotes
									);
									
			$this->{"get_" . ucfirst($Name)} = function($stdObject) use ($Name){
				 return $stdObject->getFieldValue($Name);
			};		

		}
		
	}
	
	function truncate(){
		$query = 'TRUNCATE TABLE '.$this->name;
		if(!$this->db_link->query($query)){
				throw new Exception('MySQL Error:'.$this->db_link->error);
			};		
		}

	
}

?>
