var maindata = {dataSourceTableName:'fixed_assets',
				fields :[
                    { name: 'id', type: 'number', label: 'No.',width: 50 },
                    
					{ name: 'description', type: 'string',label: 'Description', width:150, editable:true },
                  	{ name: 'fa_class', type: 'number', label: 'FA Class',width: 50, hidden:true, display:false, showlabel:false },
					{ name: 'fa_class__code', type: 'string', label: 'FA Class',width: 150 ,
                     							dropdowngrid:{datatable:'fa_class', needfield:'id', setfield:'fa_class', fields:
														[
															{name:'id', type:'number', label:'No.', width:70, gridColumnProperties:{hidden:true}},
															{name:'code', type:'string', label:'Code', width:150}
														] 
														, gridProperties:{showfilterrow: false, pageable: false,showheader: false}
													}
                     },
                  { name: 'fa_subclass', type: 'number', label: 'FA Subclass',width: 50, hidden:true, display:false, showlabel:false },
                  { name: 'fa_subclass__code', type: 'string', label: 'FA Subclass',width: 150 ,
                     							popupgrid:{datatable:'fa_subclass', needfield:'id', setfield:'fa_subclass', fields:
														[
															{name:'id', type:'number', label:'No.', width:70, gridColumnProperties:{hidden:true}},
															{name:'code', type:'string', label:'Code', width:150},
                                                          	{name:'description', type:'string', label:'Description', width:150}
														] 
														
													}
                     },
                  { name: 'fa_location', type: 'number', label: 'FA Location',width: 50, hidden:true, display:false, showlabel:false },
                  { name: 'fa_location__code', type: 'string', label: 'FA Location',width: 150 ,
                     							popupgrid:{datatable:'fa_locations', needfield:'id', setfield:'fa_location', fields:
														[
															{name:'id', type:'number', label:'No.', width:70, gridColumnProperties:{hidden:true}},
															{name:'code', type:'string', label:'Code', width:150},
                                                          	{name:'description', type:'string', label:'Description', width:150}
														] 
														
													}
                     },
                  { name: 'fa_type', type: 'number', label: 'FA type',width: 50, hidden:true, display:false, showlabel:false },
                  { name: 'fa_type__code', type: 'string', label: 'FA Type',width: 150 ,
                     							dropdowngrid:{datatable:'fa_type', needfield:'id', setfield:'fa_type', fields:
														[
															{name:'id', type:'number', label:'No.', width:70, gridColumnProperties:{hidden:true}},
															{name:'code', type:'string', label:'Code', width:150}
                                                          	
														] 
                                                        , gridProperties:{showfilterrow: false, pageable: false,showheader: false}      
														
													}
                     },
                  { name: 'component_of', type: 'number', label: 'Component of ',width: 50, hidden:true, display:false, showlabel:false },
                  { name: 'component_of__description', type: 'string', label: 'Component of ',width: 150 ,
                     							popupgrid:{datatable:'fixed_assets', needfield:'id', setfield:'component_of', fields:
														[
															{name:'id', type:'number', label:'No.', width:70, gridColumnProperties:{hidden:true}},
															{name:'description', type:'string', label:'Description', width:150}
														],
                                                        filterBefore:function(rowdata){return [['fa_type','EQUAL',2]]}
														
													}
                     },
                  { name: 'vendor', type: 'number', label: 'Vendor',width: 50, hidden:true, display:false, showlabel:false },
                  { name: 'vendor__name', type: 'string', label: 'Vendor',width: 150 ,
                     							popupgrid:{datatable:'vendor', needfield:'id', setfield:'vendor', fields:
														[
															{name:'id', type:'number', label:'No.', width:70, gridColumnProperties:{hidden:true}},
															{name:'name', type:'string', label:'Name', width:250}
                                                          	
														] 
														
													}
                     },
                  { name: 'maintenance_vendor', type: 'number', label: 'Maintenance Vendor',width: 50, hidden:true, display:false, showlabel:false },
                  { name: 'maintenance_vendor__name', type: 'string', label: 'Maintenance Vendor',width: 150 ,
                     							popupgrid:{datatable:'vendor', needfield:'id', setfield:'maintenance_vendor', fields:
														[
															{name:'id', type:'number', label:'No.', width:70, gridColumnProperties:{hidden:true}},
															{name:'name', type:'string', label:'Name', width:250}
                                                          	
														] 
														
													}
                     },
                  { name: 'warranty_exp_date', type: 'date', label: 'Waranty Exp. Date',width: 100, fieldformat:'MM/dd/yyyy', editable:true, gridColumnProperties:{filtertype:'range'} },
                  { name: 'responsible_empl', type: 'number', label: 'Responsible Employee',width: 50 ,
                     							popupgrid:{datatable:'employee', needfield:'id', setfield:'responsible_empl', fields:
														[
															{name:'id', type:'number', label:'No.', width:70},
															{name:'first_name', type:'string', label:'First Name', width:250},
                                                         	{name:'last_name', type:'string', label:'Last Name', width:250}
                                                          	
														] 
														
													}
                     },
                  { name: 'serial_no', type: 'string', label: 'Serial No.',width: 150, editable:true },
                  { name: 'next_service_date', type: 'date', label: 'Next Service Date',width: 100, fieldformat:'MM/dd/yyyy', editable:true, gridColumnProperties:{filtertype:'range'} },
                  { name: 'inactive', type:'bool', label:'Inactive', width:70, editable:true, gridColumnProperties:{columntype:'checkbox'}},
                  { name: 'insured', type:'bool', label:'Insured', width:70, editable:true, gridColumnProperties:{columntype:'checkbox'}},
                ]};

var subdata = [
  				{name:'fa_depr_book',
				label:'Depr. Book',
				dataSourceTableName:'fa_depreciation_book',
				fields:[
				{ name: 'id', type: 'number', label: 'No.',width: 70, hidden:true, showlabel:false, display:false },
				{ name: 'depreciation_book', type: 'number',label: 'depreciation_book', width: 1, hidden:true,showlabel:false, display:false, editable:true},
                { name: 'depreciation_book__code', type: 'string',label: 'Depr. Book', width: 120,
                								dropdowngrid:{datatable:'depreciation_book', needfield:'id', setfield:'depreciation_book', fields:
														[
															{name:'id', type:'number', label:'No.', width:70, gridColumnProperties:{hidden:true}},
															{name:'code', type:'string', label:'Code', width:150}
														], gridProperties:{showfilterrow: false, pageable: false,showheader: false}
													}
                }, 
				{ name: 'depreciation_method', type: 'number',label: 'depreciation_method', width: 400, hidden:true,showlabel:false, display:false},
                { name: 'depreciation_method__code', type: 'string',label: 'Depr. Method', width: 100, 
                								dropdowngrid:{datatable:'fa_depr_methods', needfield:'id', setfield:'depreciation_method', fields:
														[
															{name:'id', type:'number', label:'No.', width:70, gridColumnProperties:{hidden:true}},
															{name:'code', type:'string', label:'Code', width:70},
                                                          	{name:'description', type:'string', label:'Description', width:150}
														], gridProperties:{showfilterrow: false, pageable: false,showheader: false}
													}
                },
				{ name: 'depreciation_starting_date', type: 'date',label: 'Depr. Starting Date',fieldformat:'MM/dd/yyyy', gridColumnProperties:{filtertype:'range'}, width: 150, editable:true},
                { name: 'depreciation_ending_date', type: 'date',label: 'Depr. Ending Date',fieldformat:'MM/dd/yyyy', gridColumnProperties:{filtertype:'range'}, width: 150, editable:true},  
				{ name: 'straight_line_percent', type: 'number',label: 'Straight Line %', width: 50,input:{decimalDigits: 2}, editable:true},
				{ name: 'num_of_depr_years', type: 'number',label: 'Number of Depr. Years', width:50,input:{decimalDigits: 2},editable:true},
                { name: 'num_of_depr_months', type: 'number',label: 'Number of Depr. Months', width:50,input:{decimalDigits: 2},editable:true},
                { name: 'fixed_depr_amount', type: 'number',label: 'Fixed Depr. Amount', width:150,input:{decimalDigits: 2},editable:true},
                { name: 'fa_posting_group', type: 'number',label: 'fa_posting_group', width: 400, hidden:true,showlabel:false, display:false},
                { name: 'fa_posting_group__code', type: 'string',label: 'FA Posting Group', width: 100, 
                								dropdowngrid:{datatable:'fa_posting_group', needfield:'id', setfield:'fa_posting_group', fields:
														[
															{name:'id', type:'number', label:'No.', width:70, gridColumnProperties:{hidden:true}},
															{name:'code', type:'string', label:'Code', width:100}
														], gridProperties:{showfilterrow: false, pageable: false,showheader: false}
													}
                },
                 { name: 'acquisition_date', type: 'date',label: 'Acquisition Date',fieldformat:'MM/dd/yyyy', gridColumnProperties:{filtertype:'range'}, width: 150, editable:true}, 
                 { name: 'disposal_date', type: 'date',label: 'Disposal Date',fieldformat:'MM/dd/yyyy', gridColumnProperties:{filtertype:'range'}, width: 150, editable:true},
                 { name: 'last_depreciation_date', type: 'date',label: 'Last Depreciation Date',fieldformat:'MM/dd/yyyy', gridColumnProperties:{filtertype:'range'}, width: 150, editable:true},
                 { name: 'last_appreciation_date', type: 'date',label: 'Last Appreciation Date',fieldformat:'MM/dd/yyyy', gridColumnProperties:{filtertype:'range'}, width: 150, editable:true}
				] ,
				formTriggers:{
					beforeInsert: function(row={}){
						row.fixed_asset=maindata.formState.rowdata.id;
						return row;
						
					}
				},
				addFilters: function(){
					return [['fixed_asset', 'EQUAL', maindata.formState.rowdata.id]];
				},
				gridProperties:{showfilterrow: false}	
				},
  				{name:'fa_maintenance_reg',
				label:'Mainten. Reg.',
				dataSourceTableName:'fa_maintenance_reg',
				fields:[
				{ name: 'id', type: 'number', label: 'No.',width: 70, hidden:true, showlabel:false, display:false },
                { name: 'service_date', type: 'date',label: 'Service Date',fieldformat:'MM/dd/yyyy', gridColumnProperties:{filtertype:'range'}, width: 150, editable:true},
                { name: 'service_agent_name', type: 'string',label: 'Service Agent Name', width: 250, editable:true},
				{ name: 'service_agent_phone', type: 'string',label: 'Service Agent Phone', width: 150, editable:true},
                { name: 'service_agent_phone2', type: 'string',label: 'Service Agent Phone 2', width: 150, editable:true},
                { name: 'vendor_no', type: 'number',label: 'fa_posting_group', width: 400, hidden:true,showlabel:false, display:false},
                { name: 'vendor_no__name', type: 'string',label: 'Service Vendor', width: 300, 
                								popupgrid:{datatable:'vendor', needfield:'id', setfield:'vendor_no', fields:
														[
															{name:'id', type:'number', label:'No.', width:70, gridColumnProperties:{hidden:false}},
															{name:'name', type:'string', label:'Name', width:400}
														]
													}
                },
                { name: 'comment', type: 'string',label: 'Comment', editable:true, width:100, hidden:true, textarea:{width:600, height:150}}
				] ,
				formTriggers:{
					beforeInsert: function(row={}){
						row.fa_no=maindata.formState.rowdata.id;
						return row;
						
					}
				},
				addFilters: function(){
					return [['fa_no', 'EQUAL', maindata.formState.rowdata.id]];
				},
				gridProperties:{showfilterrow: true}	
				},
  				{name:'fa_insurance',
				label:'Insurance',
				dataSourceTableName:'fa_insurance',
				fields :[
                  { name: 'id', type: 'number', label: 'No.',width: 50, hidden:true, showlabel:false,display:false },
                  { name: 'insurance_vendor', type: 'number', label: 'Insurance Vendor',width: 50, hidden:true, display:false, showlabel:false },
                  { name: 'insurance_vendor__name', type: 'string', label: 'Insurance Vendor',width: 150 ,
                     							popupgrid:{datatable:'vendor', needfield:'id', setfield:'insurance_vendor', fields:
														[
															{name:'id', type:'number', label:'No.', width:70},
															{name:'name', type:'string', label:'Vendor Name', width:250}
														] 
														
													}
                     },
                  { name: 'insurance_type', type: 'number', label: 'Insurance Type',width: 50, hidden:true, display:false, showlabel:false },
                  { name: 'insurance_type__code', type: 'string', label: 'Insurance Type',width: 150 ,
                     							dropdowngrid:{datatable:'fa_insurance_type', needfield:'id', setfield:'insurance_type', fields:
														[
															{name:'id', type:'number', label:'No.', width:70, gridColumnProperties:{hidden:true}},
															{name:'code', type:'string', label:'Code', width:150}
                                                          	
														] 
                                                        , gridProperties:{showfilterrow: false, pageable: false,showheader: false}      
														
													}
                     },
                 
                  { name: 'effective_date', type: 'date', label: 'Effective Date',width: 100, fieldformat:'MM/dd/yyyy', editable:true, gridColumnProperties:{filtertype:'range'} },
				  { name: 'expiration_date', type: 'date', label: 'Expiration Date',width: 100, fieldformat:'MM/dd/yyyy', editable:true, gridColumnProperties:{filtertype:'range'} },                
                  { name: 'policy_no', type: 'string', label: 'Policy No.',width: 150, editable:true },
                  { name: 'annual_premium', type:'number', label:'Annual Premium', width:150, editable:true, gridColumnProperties:{cellsformat:'c'},input:{decimalDigits: 2} },
                  { name: 'policy_coverage', type:'number', label:'Policy Coverage', width:150, editable:true, gridColumnProperties:{cellsformat:'c'},input:{decimalDigits: 2} }
                ] ,
				formTriggers:{
					beforeInsert: function(row={}){
						row.fa=maindata.formState.rowdata.id;
						return row;
						
					}
				},
				addFilters: function(){
					return [['fa', 'EQUAL', maindata.formState.rowdata.id]];
				},
				gridProperties:{showfilterrow: true}	
				}


];


var buttons = [{name:'button', label:'TEST BUT', onClick:function(){
						alert('TEST BUTTON CLICK!');
}}];

runForm(maindata, buttons, subdata);
