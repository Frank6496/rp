var dataSourceTableName = 'sys_forms';
var fields = [
               { name: 'id', type: 'number', label: 'No.',width: 70},
			   { name: 'name', type: 'string',label: 'Name', width: 250, editable:true},
			   { name: 'template', type: 'string',label: 'Template', width: 250, editable:true},
			   { name: 'notfound', type:'bool', label:'Not Found', width:70, editable:true, gridColumnProperties:{columntype:'checkbox'}}
			  
			  
			   
			  ];
var masterSettings = {
				dataSourceTableName:dataSourceTableName,
				fields:fields
			};
			
var formButtons = [
				{name:'update', label:'Update List',
					onClick:function(){
							var result ='';
							try{
								result = rcf('utils','updateformslist');
								
								result = JSON.parse(result);
								
								if(result.Error==undefined){
									updateData();
									showSuccess('Forms list has been updated successfully');
								} else { throw new Error(result.Error);}	
							}catch(e){
									showError(e.message)
							}	
						}
					},
				{name:'editsource', label:'Edit Source Code', onClick: function(){
								
								var result = rcf('utils','getformcontent',[masterSettings.formState.rowdata.name]);
								var editor;
								dialog({
								title:'Edit Source Code '+masterSettings.formState.rowdata.name,
								theme:theme,
								localization:userSettings.localization,
								message:'<div><button id="SaveSourceCodeButton" style="margin-left:1%">Save</button><div><div><textarea id="xeditor" style="margin-top:1%;visibility:hidden;width:100%;height:90%;white-space: pre;"></textarea></div>', 
								 beforeOpen: function(){
									 $('#xeditor').val(result);
									 $("#SaveSourceCodeButton").jqxButton({ template: "success", width:'5em' });
									 $("#SaveSourceCodeButton").on('click', function(){var result = rcf('utils','setformcontent',[masterSettings.formState.rowdata.name,editor.getValue()]);});
									 editor = CodeMirror.fromTextArea(document.getElementById("xeditor"), {
										lineNumbers: true,
										mode:  "javascript",
										styleActiveLine: true,
										matchBrackets: true,
										theme:'liquibyte'
										});
										editor.setSize(document.getElementById("xeditor").style.width, document.getElementById("xeditor").style.height);
									},
								success:function(){
									 
									 var result = rcf('utils','setformcontent',[masterSettings.formState.rowdata.name,editor.getValue()]); 
										
									},
								onClose:function(){
									//tinymce.remove();
									$('#xeditor').remove();
									$('#confirmDialog').remove();
								},	 
								width:1320,
								height:800,
								x:0,
								y:0
								
								});
	
				}
	
	}
];

		   
			
initForm(masterSettings, userSettings, formButtons);
