var maindata = {dataSourceTableName : 'users',
				fields :[
                    { name: 'id', type: 'number', label: 'No.',width: 50 },
					{ name: 'username', type: 'string',label: 'Login', width: 250, editable:true },
                ]};
var slaveData = [
				{name:'roles',
				label:'Roles',
				dataSourceTableName:'sys_user_roles',
				fields :[
                    { name: 'id', type: 'number', label: 'No.',width: 50 },
					{ name: 'role', type: 'number',label: 'TableId', width: 70, editable:true, hidden:true, display:false, showlabel:false},
                    { name: 'role__description', type: 'string',label: 'Role', width: 250 , editable:false,
													popupgrid:{datatable:'sys_roles', needfield:'id', setfield:'role', x:100,y:100, fields:
														[
															{name:'id', type:'number', label:'No.', width:70},
															{name:'description', type:'string', label:'Role', width:250}
														] 
														, gridProperties:{showfilterrow: false}
													}
													}
					],
				addFilters: function(){
					return [['user', 'EQUAL', maindata.formState.rowdata.id]];
				},
				formTriggers:{
					beforeInsert: function(row={}){
						row.user=maindata.formState.rowdata.id;
						return row;
					}
				}
				}
];
var buttons = [
					{name:'button1', label:'Set password', onClick: function(){
					
						dialog({
								title:'Password for '+maindata.formState.rowdata.username,
								theme:theme,
								localization:userSettings.localization,
								message:'Type new password<br>'+
								'<table>'+
								'<tr><td><input id="passInput" type="password" /></td></tr>'+
								
								'</table>', 
								beforeOpen: function(){
									$('#passInput').jqxPasswordInput({  width: '150px', height: '20px'});
									
									},
								success:function(){
									var newpass = $('#passInput').val();
									setPassword(maindata.formState.rowdata.id, newpass);
								},
								width:200,
								height:150,
								x:200,
								y:200
								
								});	
						
						
						function setPassword(userid,newpassword){
							var obj = rcf('utils', 'setUserPassword', [userid, newpassword]);
							var result= '';
							try{
								result = JSON.parse(obj);
							} catch(e){
								showError(obj);
							}
							
							if(result.Error!=undefined){showError(result.Error);}
							else{showSuccess('Password has been changed');}
							
						
					}}}
];
				
initForm(maindata, userSettings, buttons, slaveData);
