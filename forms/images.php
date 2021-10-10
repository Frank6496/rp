var imagerenderer = function (row, datafield, value) {
               return '<div style="display: inline;"><img style="margin-left: 5px;" height="60" src="' + value + '"/></div><div style="margin-left: 15px;display: inline;">'+ value +'</div>';
				
}
var maindata = {dataSourceTableName:'images',
            fields : [
                    { name: 'id', type: 'number', label: '№ пп',width: 70 },
                    { name: 'filename', type: 'string',label: 'Имя файла', width: 400,gridColumnProperties:{cellsrenderer: imagerenderer}, editable:true},
					{ name: 'description', type: 'string',label: 'Описание', width: 400, editable:true },
					{ name: 'imagewidth', type: 'number', label: 'Ширина',width: 50, editable:true},
					{ name: 'imageheight', type: 'number', label: 'Высота',width: 50, editable:true},
					
					
					
], gridProperties:{rowsheight: 65},
panelButtons:{addButton:{disabled:false}}
};

 var formButtons = [
						
						{name:'loadimages', label:'Загрузить изображения', 
							onClick: function(){
											
											var fileinfo = [];
								
											 function handleFileSelect(evt) {
												var files = evt.target.files; // FileList object

												// Цикл по списку файлов и рендер предпросмотра.
												for (var i = 0, f; f = files[i]; i++) {

												  // Только файлы с картинками.
												  
												  if (!f.type.match('image.*')) {
													continue;
												  }
													
												  var reader = new FileReader();

												  // Замыкание. Забираем информацию о файле и содержимое файла
												  reader.onloadend = (function(theFile) {
													return function(e) {
													  // Делаем предпоказ.
													  var span = document.createElement('span');
													  span.innerHTML = ['<img class="thumb" src="', e.target.result,
																		'" title="', escape(theFile.name), '"/>'].join('');
													  document.getElementById('filelist').insertBefore(span, null);
													  // забираем содержимое файла в массив
													  fileinfo[theFile.name] = {filename:theFile.name,filecontent:e.target.result};
													  
													};
												  })(f);
												  
												  

												  // Читаем файл.
												  reader.readAsDataURL(f);
												}
											  }
											  
											  
											 //выводим всплывающее окно 
											 dialog({
												title:'Выберите файлы для загрузки',
												theme:theme,
												message:'<style>.thumb {height: 75px;border: 1px solid #000;margin: 10px 5px 0 0;}</style><input type="file" id="files" name="files[]" multiple /><br><output id="filelist"></output>', 
												beforeOpen: function(){
													document.getElementById('files').addEventListener('change', handleFileSelect, false);
													},
												success:function(){
													
													for (var key in fileinfo) {
														
														var result = rcf('imageclass','saveimage',[fileinfo[key].filecontent,fileinfo[key].filename]);
														updateData();
													}
													
												},
												width:900,
												height:630,
												x:200,
												y:200
												
											});	
											  
											  
											 
						},
						buttonProperties:{textPosition: "left",height: 40}
						}]; 


initForm(maindata, userSettings ,formButtons/*,slaveData */);
