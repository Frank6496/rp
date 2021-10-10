var maindata = {dataSourceTableName:'vendor_posting_group',
				fields :[
                    { name: 'id', type: 'number', label: 'No.',width: 50 },
                    
					{ name: 'code', type: 'string',label: 'Code', width:150, editable:true },
                  
					{ name: 'payables_account', type: 'number',label: '', width:150, hidden:true, display:false,showlabel:false},
                	{ name: 'payables_account__code', type: 'string',label: 'Payables Account Code', width:70},
                  	{ name: 'payables_account__name', type: 'string',label: 'Payables Account', width:150,
                    							popupgrid:{datatable:'gl_accounts', needfield:'id', setfield:'payables_account', fields:
														[
															{name:'id', type:'number', label:'No.', width:70, gridColumnProperties:{hidden:true}},
															{name:'code', type:'string', label:'Code', width:70},
                                                          	{name:'name', type:'string', label:'Name', width:250}
														] 
														
													}
                    },
                  	{ name: 'service_charge_account', type: 'number',label: 'Service Charge Acc.', width:150, hidden:true, display:false,showlabel:false},
                  	{ name: 'service_charge_account__code', type: 'string',label: 'Service Charge Acc. Code', width:70},
                  	{ name: 'service_charge_account__name', type: 'string',label: 'Service Charge Acc.', width:150,
                    							popupgrid:{datatable:'gl_accounts', needfield:'id', setfield:'service_charge_account', fields:
														[
															{name:'id', type:'number', label:'No.', width:70, gridColumnProperties:{hidden:true}},
															{name:'code', type:'string', label:'Code', width:70},
                                                          	{name:'name', type:'string', label:'Name', width:250}
														] 
														
													}
                    },
                   
                ]};
runForm(maindata);
