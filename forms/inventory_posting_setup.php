var maindata = {dataSourceTableName:'inventory_posting_setup',
				fields :[
                    { name: 'id', type: 'number', label: 'No.',width: 50, hidden:true, showlabel:false, display:false },
                    
					{ name: 'location', type: 'number',label: 'Location ID', width:150, hidden:true, display:false,showlabel:false},
                	{ name: 'location__code', type: 'string',label: 'Location', width:70,
                    								popupgrid:{datatable:'location', needfield:'id', setfield:'location', fields:
														[
															{name:'id', type:'number', label:'No.', width:70, gridColumnProperties:{hidden:true}},
															{name:'code', type:'string', label:'Code', width:70},
                                                          	{name:'description', type:'string', label:'Description', width:250}
														] 
														
													}
                    },
                  	{ name: 'inventory_posting_group', type: 'number',label: 'Inventory Posting Group ID', width:150, hidden:true, display:false,showlabel:false},
                	{ name: 'inventory_posting_group__code', type: 'string',label: 'Inventory Posting Group', width:70,
                    								popupgrid:{datatable:'inventory_posting_group', needfield:'id', setfield:'inventory_posting_group', fields:
														[
															{name:'id', type:'number', label:'No.', width:70, gridColumnProperties:{hidden:true}},
															{name:'code', type:'string', label:'Code', width:70},
                                                          	{name:'description', type:'string', label:'Description', width:250}
														] 
														
													}
                    },
					{ name: 'inventory_account', type: 'number',label: '', width:150, hidden:true, display:false,showlabel:false},
                	{ name: 'inventory_account__code', type: 'string',label: 'Inventory Account', width:70,
                    								popupgrid:{datatable:'gl_accounts', needfield:'id', setfield:'inventory_account', fields:
														[
															{name:'id', type:'number', label:'No.', width:70, gridColumnProperties:{hidden:true}},
															{name:'code', type:'string', label:'Code', width:70},
                                                          	{name:'name', type:'string', label:'Name', width:250}
														] 
														
													}
                    },
                  	{ name: 'inventory_account__name', type: 'string',label: 'Inventory Account', width:150, showlabel:false},
                  	{ name: 'inventory_account_interim', type: 'number',label: 'Inventory Account Interim ID', width:150, hidden:true, display:false,showlabel:false},
                  	{ name: 'inventory_account_interim__code', type: 'string',label: 'Inventory Account Interim', width:70,
                    							popupgrid:{datatable:'gl_accounts', needfield:'id', setfield:'inventory_account_interim', fields:
														[
															{name:'id', type:'number', label:'No.', width:70, gridColumnProperties:{hidden:true}},
															{name:'code', type:'string', label:'Code', width:70},
                                                          	{name:'name', type:'string', label:'Name', width:250}
														] 
														
													}
                    },
                  	{ name: 'inventory_account_interim__name', type: 'string',label: 'Inventory Account Interim', width:150,showlabel:false},
                  	{ name: 'wip_account', type: 'number',label: 'WIP account ID', width:150, hidden:true, display:false,showlabel:false},
                	{ name: 'wip_account__code', type: 'string',label: 'WIP account', width:70,
                    								popupgrid:{datatable:'gl_accounts', needfield:'id', setfield:'wip_account', fields:
														[
															{name:'id', type:'number', label:'No.', width:70, gridColumnProperties:{hidden:true}},
															{name:'code', type:'string', label:'Code', width:70},
                                                          	{name:'name', type:'string', label:'Name', width:250}
														] 
														
													}
                    },
                  	{ name: 'wip_account__name', type: 'string',label: 'WIP account', width:150,showlabel:false},
                  	{ name: 'material_variance_account', type: 'number',label: 'Material Variance Account ID', width:150, hidden:true, display:false,showlabel:false},
                  	{ name: 'material_variance_account__code', type: 'string',label: 'Material Variance Account', width:70,
                    								popupgrid:{datatable:'gl_accounts', needfield:'id', setfield:'material_variance_account', fields:
														[
															{name:'id', type:'number', label:'No.', width:70, gridColumnProperties:{hidden:true}},
															{name:'code', type:'string', label:'Code', width:70},
                                                          	{name:'name', type:'string', label:'Name', width:250}
														] 
														
													}
                    },
                  	{ name: 'material_variance_account__name', type: 'string',label: 'Material Variance Account', width:150,showlabel:false},
                 	{ name: 'capacity_variance_account', type: 'number',label: 'Capacity Variance Account ID', width:150, hidden:true, display:false,showlabel:false},
                	{ name: 'capacity_variance_account__code', type: 'string',label: 'Capacity Variance Account', width:70,
                    								popupgrid:{datatable:'gl_accounts', needfield:'id', setfield:'capacity_variance_account', fields:
														[
															{name:'id', type:'number', label:'No.', width:70, gridColumnProperties:{hidden:true}},
															{name:'code', type:'string', label:'Code', width:70},
                                                          	{name:'name', type:'string', label:'Name', width:250}
														] 
														
													}
                    },
                  	{ name: 'capacity_variance_account__name', type: 'string',label: 'Capacity Variance Account Name', width:150,showlabel:false},
                  	{ name: 'mfg_overhead_variance_account', type: 'number',label: 'Mfg. Overhead Variance Account ID', width:150, hidden:true, display:false,showlabel:false},
                  	{ name: 'mfg_overhead_variance_account__code', type: 'string',label: 'Mfg. Overhead Variance Account', width:70,
                    								popupgrid:{datatable:'gl_accounts', needfield:'id', setfield:'mfg_overhead_variance_account', fields:
														[
															{name:'id', type:'number', label:'No.', width:70, gridColumnProperties:{hidden:true}},
															{name:'code', type:'string', label:'Code', width:70},
                                                          	{name:'name', type:'string', label:'Name', width:250}
														] 
														
													}
                    },
                  	{ name: 'mfg_overhead_variance_account__name', type: 'string',label: 'Mfg. Overhead Variance Account Name', width:150,showlabel:false},
                 	{ name: 'cap_overhead_variance_account', type: 'number',label: 'Cap. Overhead Variance Account ID', width:150, hidden:true, display:false,showlabel:false},
                	{ name: 'cap_overhead_variance_account__code', type: 'string',label: 'Cap. Overhead Variance Account', width:70,
                    								popupgrid:{datatable:'gl_accounts', needfield:'id', setfield:'cap_overhead_variance_account', fields:
														[
															{name:'id', type:'number', label:'No.', width:70, gridColumnProperties:{hidden:true}},
															{name:'code', type:'string', label:'Code', width:70},
                                                          	{name:'name', type:'string', label:'Name', width:250}
														] 
														
													}
                    },
                  	{ name: 'cap_overhead_variance_account__name', type: 'string',label: 'Cap. Overhead Variance Account Name', width:150,showlabel:false},
                  	{ name: 'subcontracted_variance_account', type: 'number',label: 'Subcontracted Variance Account ID', width:150, hidden:true, display:false,showlabel:false},
                  	{ name: 'subcontracted_variance_account__code', type: 'string',label: 'Subcontracted Variance Account', width:70,
                    								popupgrid:{datatable:'gl_accounts', needfield:'id', setfield:'subcontracted_variance_account', fields:
														[
															{name:'id', type:'number', label:'No.', width:70, gridColumnProperties:{hidden:true}},
															{name:'code', type:'string', label:'Code', width:70},
                                                          	{name:'name', type:'string', label:'Name', width:250}
														] 
														
													}
                    },
                  	{ name: 'subcontracted_variance_account__name', type: 'string',label: 'Subcontracted Variance Account Name', width:150,showlabel:false}
                   
                ]};
runForm(maindata);
