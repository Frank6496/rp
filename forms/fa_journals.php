var maindata = {dataSourceTableName:'fa_journal_line',
				fields :[
                    { name: 'id', type: 'number', label: 'No.',width: 50 },
                 	
                  	{ name: 'posting_date', type: 'date',label: 'Posting Date', width:100, editable:true, fieldformat:'MM/dd/yyyy', gridColumnProperties:{filtertype:'range'} },
					{ name: 'document_type', type: 'number',label: 'Document Type', width:100, editable:true, hidden:true, showlabel:false, display:false },
                  	{ name: 'document_type__code', type: 'string',label: 'Document Type', width:150, editable:false,
                    			dropdowngrid:{datatable:'gl_document_types', needfield:'id', setfield:'document_type', fields:
														[
															{name:'id', type:'number', label:'No.', width:70, gridColumnProperties:{hidden:true}},
															{name:'code', type:'string', label:'Code', width:150}
														], 
                                              		gridProperties:{showfilterrow: false, pageable: false,showheader: false},
                                              		filterBefore:function(row){return [['id','IN','1,3,4']];}
													}
                    
                    },
                  	{ name: 'document_no', type: 'string',label: 'Document No.', width:100, editable:true },
					{ name: 'decription', type: 'string',label: 'Description', width:200, editable:true },
                  	{ name: 'depreciation_book', type: 'number',label: 'Depr. Book', width:100, editable:true, hidden:true, showlabel:false, display:false },
                  	{ name: 'depreciation_book__code', type: 'string',label: 'Depr. Book', width:150, editable:false,
                    			dropdowngrid:{datatable:'depreciation_book', needfield:'id', setfield:'depreciation_book', fields:
														[
															{name:'id', type:'number', label:'No.', width:70, gridColumnProperties:{hidden:true}},
															{name:'code', type:'string', label:'Code', width:150}
														], gridProperties:{showfilterrow: false, pageable: false,showheader: false}
													}
                    
                    },
                  	{ name: 'fa_posting_type', type: 'number',label: 'FA Posting Type', width:100, editable:true, hidden:true, showlabel:false, display:false },
                  	{ name: 'fa_posting_type__code', type: 'string',label: 'FA Posting Type', width:150, editable:false,
                    			dropdowngrid:{datatable:'fa_posting_types', needfield:'id', setfield:'fa_posting_type', fields:
														[
															{name:'id', type:'number', label:'No.', width:70, gridColumnProperties:{hidden:true}},
															{name:'code', type:'string', label:'Code', width:150}
														], gridProperties:{showfilterrow: false, pageable: false,showheader: false}
													}
                    
                    },
                  	{ name: 'fa_posting_date', type: 'date',label: 'FA Posting Date', width:100, editable:true, fieldformat:'MM/dd/yyyy', gridColumnProperties:{filtertype:'range'} },
                   	{ name: 'amount', type: 'number',label: 'Amount', width:100, editable:true, input:{decimalDigits: 2} },
                  	{ name: 'salvage_value', type: 'number',label: 'Salvage Value', width:100, editable:true, input:{decimalDigits: 2} },
                  	{ name: 'no_of_depreciation_days', type: 'number',label: 'Num. Of Depr. Days', width:100, editable:true },
                  	{ name: 'depr_until_fa_posting_date', type:'bool', label:'Depr. Until Posting Date', width:70, editable:true, gridColumnProperties:{columntype:'checkbox'}},
                  	{ name: 'depr_acquisition_cost', type:'bool', label:'Depr. Acquisition Cost', width:70, editable:true, gridColumnProperties:{columntype:'checkbox'}},
                  	
                  	{ name: 'maintenance', type: 'number',label: 'Maintenance', width:100, editable:true, hidden:true, showlabel:false, display:false },
                  	{ name: 'maintenance__code', type: 'string',label: 'Maintenance Code', width:150, editable:false,
                    			dropdowngrid:{datatable:'fa_maintenance', needfield:'id', setfield:'maintenance', fields:
														[
															{name:'id', type:'number', label:'No.', width:70, gridColumnProperties:{hidden:true}},
															{name:'code', type:'string', label:'Code', width:150}
														], gridProperties:{showfilterrow: false, pageable: false,showheader: false}
													}
                    
                    },
                  	{ name: 'insurance', type: 'number',label: 'Insurance', width:100, editable:true, hidden:true, showlabel:false, display:false },
                  	{ name: 'insurance__policy_no', type: 'string',label: 'Insurance Policy', width:150, editable:false,
                    			popupgrid:{datatable:'fa_insurance', needfield:'id', setfield:'insurance', fields:
														[
															{name:'id', type:'number', label:'No.', width:70, gridColumnProperties:{hidden:true}},
															{name:'fa__description', type:'string', label:'Fixed Asset', width:150},
                                                          	{name:'policy_no', type:'string', label:'Policy No.', width:150}
														]
													}
                    
                    }
                   
                ]};

var buttons = [ {name:'post',label:'Post Journal', 
                 onClick:function(){
                   if(!confirm("Post FA Gen. Journal Lines?")) return;
                   var result = rcf('post_gl_journal','post_gl_recs',[maindata.formState.getSelectedRowsIndexes()]);
                   try{
                   		result = JSON.parse(result);
                     	if(result.Error!=undefined){
                        	showError(result.Error);
                        }else{
                        	showSuccess('FA GL Jounal has been posted');
                        };
                   }catch(e){
                   		showError('Serverside application error');
                   }
                   updateData();

}}

];

runForm(maindata, buttons);
