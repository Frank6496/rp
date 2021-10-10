var dataSourceTableName = 'pages';
var fields = [
               { name: 'id', type: 'number', label: 'No.',width: 70},
			   { name: 'urlname', type: 'string',label: 'Адрес', width: 250, editable:true},
			   { name: 'title', type: 'string',label: 'Заголовок', width: 250, editable:true},
			   { name: 'keywords', type: 'string',label: 'Ключевые слова', width: 250, editable:true},
			   { name: 'description', type: 'string',label: 'Описание', width: 250, editable:true}
			  // { name: 'page_content', type: 'string',label: 'Содержимое', width: 250, editable:true, textarea:{width:800, height:400}, hidden:true, editor:false}
			   
			  ];
var masterSettings = {
				dataSourceTableName:dataSourceTableName,
				fields:fields,
				panelButtons:{addButton:{disabled:false}, deleteButton:{disabled:false}},
			};
	
var formButtons = [{name:'button41', label:'Содержимое', 
							onClick: function(){
								var result = rcf('utils','getpagecontent',[masterSettings.formState.rowdata.id]);
								
								dialog({
								title:'Изменить',
								theme:theme,
								localization:userSettings.localization,
								message:'<textarea id="xeditor" style="width:100%;height:95%"></textarea>', 
								 beforeOpen: function(){
									 $('#xeditor').html(result);
									 $('#xeditor').jqxEditor();
									// $('#xeditor').jqxEditor('val',result);
									},
								success:function(){
									 
									 var result = rcf('utils','updatePageContent',[masterSettings.formState.rowdata.id,$('#xeditor').jqxEditor('val')]); 
									
									},
								onClose:function(){
									//tinymce.remove();
									$('#xeditor').remove();
									$('#confirmDialog').remove();
								},	 
								width:1100,
								height:800,
								x:50,
								y:50
								
								});	 			
										
							}
						}];

			
		initForm(masterSettings, userSettings, formButtons);
			  
