<?php 

class imageclass extends baseclass{
	function saveimage($args=array()){
		if(empty($args)){return;}
		
		$filename = $args[1];
		//по первому символу имени файла определяем подкаталог для хранения картинки
		$firstsymbol = substr($filename, 0, 1);
		$placeinfolder = substr(hash('md5', $firstsymbol),0,2);
		$imagesdirectory = $_SERVER['DOCUMENT_ROOT'].'/images/'.$placeinfolder;
		//если нет подкаталога, то создаем
		if(!file_exists($imagesdirectory)){mkdir($imagesdirectory,0775);};
		$imagesdirectory.='/';
		//ссылка для браузера
		$filelink = '../..'.'/images/'.$placeinfolder.'/';	
		$imagecontent = explode('base64,',$args[0]);
		
		try{	
			file_put_contents($imagesdirectory.$filename,base64_decode($imagecontent[1]));
		}catch(Exception $e){
			echo $e->getMessage();
			return;
		}	
		list($width, $height) = getimagesize($imagesdirectory.$filename);
		$imagetable = $this->getTable('images');
		$imagetable->setFilter('filename','=',$filelink.$filename);
		
		if($imagetable->getFirstRowByFilter()){
			//если есть запись о файле, то выходим
			echo 'exit';
			return;
		} else{
			//вставляем запись о новом файле
			try{
				$imagetable->init();
				$imagetable->set_Filename($filelink.$filename);
				$imagetable->set_Imageheight($height);
				$imagetable->set_Imagewidth($width);
				$imagetable->insert();
			}catch(Exception $e){
				echo $e->getMessage();
			}
		}	
		
	}
  	
  	function savemenuicon($args=array()){
    	if(empty($args)){return;}
      	if(!security::isAdmin())return;
      
      	$filename = $args[1];
      	$menutable_id = $args[2];
      	
      	$imagesdirectory = $_SERVER['DOCUMENT_ROOT'].'/images/icons/';
      	$imagecontent = explode('base64,',$args[0]);
      	
      	try{	
			file_put_contents($imagesdirectory.$filename,base64_decode($imagecontent[1]));
          	chmod($imagesdirectory.$filename, 0755);
		}catch(Exception $e){
			echo $e->getMessage();
			return;
		};
      	
      	$filename = explode('.',$filename)[0];
      	$menutable = $this->getTable('menu');
      	$menutable->getById($menutable_id);
      	$menutable->set_Icon($filename);
      	$menutable->update();
      	
      	
      	
      
    }
}

?>
  