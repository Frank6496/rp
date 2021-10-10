var maindata = {dataSourceTableName:'sys_roles',
				fields : [
                    { name: 'id', type: 'number', label: 'No.',width: 50 },
                    { name: 'description', type: 'string',label: 'Name', width: 250 , editable:true}
                ]
			   };
var slaveData = [
				{
					name:'tables',
					label:'Tables',
					dataSourceTableName:'sys_roles_tables',
					fields : [
                    { name: 'id', type: 'number', label: 'No.',width: 50 },
					{ name: 'tableid', type: 'number',label: 'TableId', width: 70, editable:true, hidden:true, display:false, showlabel:false},
                    { name: 'tableid__name', type: 'string',label: 'Name', width: 250 , editable:false,
													popupgrid:{datatable:'sys_tables', needfield:'id', setfield:'tableid', x:100,y:100, fields:
														[
															{name:'id', type:'number', label:'No.', width:70},
															{name:'name', type:'string', label:'Table', width:250}
														] 
														
													}
																		},
					{ name: 'allow_select', type:'bool', label:'Select', width:70, editable:true, gridColumnProperties:{columntype:'checkbox'}},
					{ name: 'allow_insert', type:'bool', label:'Insert', width:70, editable:true, gridColumnProperties:{columntype:'checkbox'}},
					{ name: 'allow_update', type:'bool', label:'Update', width:70, editable:true, gridColumnProperties:{columntype:'checkbox'}},
					{ name: 'allow_delete', type:'bool', label:'Delete', width:70, editable:true, gridColumnProperties:{columntype:'checkbox'}}
                ],
				addFilters: function(){
					return [['roleid', 'EQUAL', maindata.formState.rowdata.id]];
				},
				formTriggers:{
					beforeInsert: function(row={}){
						row.roleid=maindata.formState.rowdata.id;
						return row;
					}
				}
				},
				{name:'menuitems',
				label:'Menu Items',
				dataSourceTableName:'sys_role_menuitems',
				fields : [
                    { name: 'id', type: 'number', label: 'No.',width: 50 },
					{ name: 'menuitemid', type: 'number',label: 'Menu Item', width: 70, editable:true, hidden:true, display:false, showlabel:false},
					{ name: 'menuitemid__name', type: 'string',label: 'Name', width: 250 , editable:false,
													popupgrid:{datatable:'menu', needfield:'id', setfield:'menuitemid', x:100,y:100, fields:
														[
															{name:'id', type:'number', label:'No.', width:70},
															{name:'name', type:'string', label:'Table', width:250},
															{name: 'showname', type: 'string',label: 'Menu item', width:250 }
														] 
														
													}
				},
					{ name: 'menuitemid__showname', type: 'string',label: 'Menu item', width:250 }
				],
				addFilters: function(){
					return [['roleid', 'EQUAL', maindata.formState.rowdata.id]];
				},
				formTriggers:{
					beforeInsert: function(row={}){
						row.roleid=maindata.formState.rowdata.id;
						return row;
					}
				}
				}
];			   

var buttons = [
				{name:'button1', label:'Refresh tables', 
							onClick: function(){
												var result='';
												result = rcf('utils', 'refresh_tables');
												if(result!=''){
														showError(result);
													}else{
														updateData();
														showSuccess('Refresh complete');
													} 
						}}
];
initForm(maindata,userSettings,buttons,slaveData);
