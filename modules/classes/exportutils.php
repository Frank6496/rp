<?php 

class exportutils{
	function exportArrayAsCSV($a=[], $h=false){
		if(empty($a)) return;
		$out = fopen('php://output', 'w');
		if($h){
			fputcsv($out, array_keys(current($a)));
		}
		foreach($a as $v){
			fputcsv($out, $v);
		}
		fclose($out); 
	}
	
	function createHTMLTableFromArray($a=[], $h=false){
		if(empty($a))return '';
		$out='<table>';
		if (count($a) > 0){
			$out.='<thead><tr><th>'.implode('</th><th>', array_keys(current($a))).'</th></tr></thead>';
		}
		$out.='<tbody>';
		foreach($a as $v){
			array_map('htmlentities', $v);
			$out.='<tr><td>'.implode('</td><td>', $v).'</td></tr>';
		}
		$out.='</tbody></table>';
		return $out;
	}
}
?>
