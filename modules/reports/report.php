<?php
class Report{
	protected $db_link;
	protected $tableFactory;
	protected $sections=array();
	
	function __construct($mysqli, $tableFactory){
		$this->db_link = $mysqli;
		$this->tableFactory =$tableFactory;
	}
			
	protected function getTable($tableName){
		try{
		 return $this->tableFactory->buildTable($tableName, $this->db_link, $this->tableFactory);
		} catch (Exception $e){
			throw new Exception('Error getting table '.$tableName.' in function getTable in report class '.get_class($this).' : '.$e->getMessage());
		} 
	}
	
	function init($reportName){
			$sys_reports = $this->getTable('sys_reports');
			$sys_reports->setFilter('name','=',$reportName);
			if($sys_reports->getFirstRow()){
				$content = file_get_contents('templates/'.$sys_reports->get_Template());
				$sectionsArray = $this->getSectionsList($content);
				$this->sections['begin']=explode('[[',$content)[0];
				$last=explode(']]',$content);
				$this->sections['end']=$last[count($last)-1];
				foreach($sectionsArray as $value){
					$this->sections[$value]=$this->getSectionContent($value,$content);
				}
			}
		}	
	
	private function getSectionContent($sectionName, $content){
		$tmp= explode('[['.$sectionName.'_begin]]',$content);
		$tmp=$tmp[1];
		$tmp= explode('[['.$sectionName.'_end]]',$tmp);
		$tmp=$tmp[0];
		return $tmp;
		}
	
	function print($args=array()){
			$this->init(strtolower(get_class($this)));
			print($this->sections['begin']);
			$this->build($args);
			print($this->sections['end']);
		}
	
	function build($args=array()){
		
	}
	
	function buildSection($sectionName, $data=array()){
		if(!isset($this->sections[$sectionName])){return '';}
		$template = $this->sections[$sectionName];
		foreach ($data as $key => $value){
			$template = str_replace('{{'.$key.'}}',$value,$template);
		}
		$template = preg_replace('/{{.*?}}/is', '', $template);
		return $template;
	}
	
	private function getSectionsList($content){
		preg_match_all('#\[\[(.*?)\]\]#is',$content,$ar);
		$arr=array();
		foreach($ar[1] as $value){
			$str = str_replace(array('_begin','_end'),'',$value);
			if(!in_array($str,$arr)){
				$arr[]=$str;
				}
			}
		return $arr;	
	}
	}
?>
