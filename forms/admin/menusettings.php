var imagerenderer = function (row, datafield, value) {
				var iconfilename = (value=='')?'folder':value;
               return '<div style="display: inline;"><img style="margin-left: 5px;" height="16" src="../../images/icons/' + iconfilename + '.png"/></div>';
				
};
var maindata = {dataSourceTableName : 'menu',
				fields : [
                    { name: 'id', type: 'number', label: 'No.',width: 70 },
                    { name: 'name', type: 'string',label: 'Name', width: 200, editable:true },
                    { name: 'showname', type: 'string',label: 'Menu item', width:200, editable:true },
					{ name: 'link', type: 'string',label: 'Form', width:150,editable:true,
													popupgrid:{datatable:'sys_forms', needfield:'name', x:100,y:100, fields:
														[
															{name:'id', type:'number', label:'No.', width:70},
															{ name: 'name', type: 'string',label: 'Name', width: 200 }
															
														] 
														
													}
						 },
					{ name: 'parent', type: 'number', label: 'Parent',width: 150,editable:true,
													popupgrid:{datatable:'menu', needfield:'id', x:100,y:100, fields:
														[
															{name:'id', type:'number', label:'No.', width:70},
															{ name: 'name', type: 'string',label: 'Name', width: 200 },
															{ name: 'showname', type: 'string',label: 'Menu item', width:200 },
															{ name: 'link', type: 'string',label: 'Form', width:150 }
														] 
														
													}
						 },
					{ name: 'admin_only', type: 'bool', label: 'Admin only',width: 130, columntype:'checkbox',editable:true },
					{ name: 'orderitems', type: 'number', label: 'Order',width:50, editable:true  },
					{ name: 'icon', type: 'string',label: 'Icon', width:200, editable:true, gridColumnProperties:{cellsrenderer: imagerenderer} }
               ], gridProperties:{pagesizeoptions: ['100', '200', '300'], pagesize: 200}
			   };
			   
 var formButtons = [
						
						{name:'loadimages', label:'Upload Icon', 
							onClick: function(){
											
											var fileinfo = [];
								
											 function handleFileSelect(evt) {
												var files = evt.target.files; // FileList object
					
												for (var i = 0, f; f = files[i]; i++) {
											  
												  if (!f.type.match('image.*')) {
													continue;
												  }
													
												  var reader = new FileReader();
											 
												  reader.onloadend = (function(theFile) {
													return function(e) {
													  
													  var span = document.createElement('span');
													  span.innerHTML = ['<img class="thumb" src="', e.target.result,
																		'" title="', escape(theFile.name), '"/>'].join('');
													  document.getElementById('filelist').insertBefore(span, null);
													  
													  fileinfo[theFile.name] = {filename:theFile.name,filecontent:e.target.result};
													  
													};
												  })(f);
											
												  reader.readAsDataURL(f);
												}
											  }
											  
											 dialog({
												title:'Select files for upload',
												theme:theme,
												localization:userSettings.localization,
												message:'<style>.thumb {height: 16px;border: 1px solid #000;margin: 10px 5px 0 0;}</style><input type="file" accept=".png" id="files" name="files[]" /><br><output id="filelist"></output>', 
												beforeOpen: function(){
													document.getElementById('files').addEventListener('change', handleFileSelect, false);
													},
												success:function(){
													
													for (var key in fileinfo) {
														
														var result = rcf('imageclass','savemenuicon',[fileinfo[key].filecontent,fileinfo[key].filename, maindata.formState.rowdata.id]);
														updateData();
														
													}
													
												},
												width:320,
												height:240,
												x:200,
												y:200
												
											});	
										 
						}
						}]; 

initForm(maindata, userSettings, formButtons);
