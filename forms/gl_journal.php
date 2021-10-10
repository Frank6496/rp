var maindata = {dataSourceTableName:'gl_journal_line',
				fields :[
                    { name: 'id', type: 'number', label: 'No.',width: 50 },
                 	 { name: 'account_type', type: 'number',label: 'Account Type', width:100, editable:true, hidden:true, showlabel:false, display:false},
                  	{ name: 'account_type__code', type: 'string',label: 'Account Type', width:100, editable:false,
                    			dropdowngrid:{datatable:'gl_account_types', needfield:'id', setfield:'account_type', fields:
														[
															{name:'id', type:'number', label:'No.', width:70, gridColumnProperties:{hidden:true}},
															{name:'code', type:'string', label:'Code', width:150}
														], gridProperties:{showfilterrow: false, pageable: false,showheader: false}
													}
                    
                    },
                    { name: 'account_no', type: 'number',label: 'Account No.', width:100, editable:true, hidden:true, showlabel:false, display:false },
                  	{ name: 'account_description', type: 'string',label: 'Account', width:200, editable:false,
                    										popupgrid:function(currentrow){
																	if(currentrow.account_type==1)//gl account													
																	return {datatable:'gl_accounts', needfield:'id', setfield:'account_no',fields:
																		[
                                                                          	{name: 'id', type: 'number', label: 'No.',width: 50,gridColumnProperties:{hidden:true} },
                                                                          	{name:'code', type:'number', label:'Account No.', width:70},
																			{name:'name', type:'string', label:'Account Descr.', width:250}
																			
																		]
																	};
                                                              		return undefined;
															}

                    },
                  	{ name: 'posting_date', type: 'date',label: 'Posting Date', width:100, editable:true, fieldformat:'MM/dd/yyyy', gridColumnProperties:{filtertype:'range'} },
					{ name: 'document_type', type: 'number',label: 'Document Type', width:100, editable:true, hidden:true, showlabel:false, display:false },
                  	{ name: 'document_type__code', type: 'string',label: 'Document Type', width:150, editable:false,
                    			dropdowngrid:{datatable:'gl_document_types', needfield:'id', setfield:'document_type', fields:
														[
															{name:'id', type:'number', label:'No.', width:70, gridColumnProperties:{hidden:true}},
															{name:'code', type:'string', label:'Code', width:150}
														], gridProperties:{showfilterrow: false, pageable: false,showheader: false}
													}
                    
                    },
                  	{ name: 'document_no', type: 'string',label: 'Document No.', width:100, editable:true },
					{ name: 'decription', type: 'string',label: 'Description', width:200, editable:true },
                   	{ name: 'amount', type: 'number',label: 'Amount', width:100, editable:true, input:{decimalDigits: 2} },
                  	{ name: 'bal_account_type', type: 'number',label: 'Bal. Account Type', width:100, editable:true, hidden:true, showlabel:false, display:false},
                  	{ name: 'bal_account_type__code', type: 'string',label: 'Bal. Account Type', width:100, editable:false,
                    	dropdowngrid:{datatable:'gl_account_types', needfield:'id', setfield:'bal_account_type', fields:
														[
															{name:'id', type:'number', label:'No.', width:70, gridColumnProperties:{hidden:true}},
															{name:'code', type:'string', label:'Code', width:150}
														], gridProperties:{showfilterrow: false, pageable: false,showheader: false}
													}
                    
                    },
                    { name: 'bal_account_no', type: 'string',label: 'Bal. Account No.', width:100, editable:true, hidden:true,showlabel:false,display:false },
                  	{ name: 'bal_account_description', type: 'string',label: 'Bal. Account', width:200, editable:false,
                    										popupgrid:function(currentrow){
                     												if(currentrow.account_type==1)//gl account													
																	return {datatable:'gl_accounts', needfield:'id', setfield:'bal_account_no',fields:
																		[
                                                                          	{name: 'id', type: 'number', label: 'No.',width: 50,hidden:true },
                                                                          	{name:'code', type:'number', label:'Account No.', width:70},
																			{name:'name', type:'string', label:'Account Descr.', width:250}
																			
																		]
																	};
                                                              		return undefined;
															}

                    }
                   
                ]};

var buttons = [ {name:'post',label:'Post Journal', 
                 onClick:function(){
                   if(!confirm("Post Gen. Journal Lines?")) return;
                   var result = rcf('post_gl_journal','post_gl_recs',[maindata.formState.getSelectedRowsIndexes()]);
                   try{
                   		result = JSON.parse(result);
                     	if(result.Error!=undefined){
                        	showError(result.Error);
                        }else{
                        	showSuccess('GL Jounal has been posted');
                        };
                   }catch(e){
                   		showError('Serverside application error');
                   }
                   updateData();

}}

];

runForm(maindata, buttons);
