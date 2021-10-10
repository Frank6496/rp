var cellsrenderer = function (row, column, value, defaultHtml,columnsettings, data) {
                var element = $(defaultHtml);
                element.css({  'font-size': 18-data.intendation+'px' });
  					if(data.intendation<1){element.css({ 'font-weight': 'Bold'});}
                var intendation = '';
  				for(var i=0;i<data.intendation;i++){intendation+='&nbsp&nbsp&nbsp&nbsp';}
  				element[0].innerHTML = intendation+value;
                return element[0].outerHTML;
            };

var maindata = {dataSourceTableName : 'purchase_header',
				fields :[
                    {name:'id', type: 'number', label: 'No.',width: 50, hidden:true },
					{name:'document_no', type: 'string',label: 'Document No.', width: 150, editable:true },
                  	{name:'buy_from_vendor_no', type:'number', label:'Buy-From Vendor No.', width:70, editable:false,
                    								popupgrid:{datatable:'vendor', needfield:'id', 
                                                              fields:[{name:'id', type:'number',label:'No.',width:70, hidden:true},
                                                                      {name:'name', type:'string', label:'Vendor', width:250}
                                                              			
                                                              ]
                                                              }
                    },
                  	{name:'buy_from_vendor_no__name', type:'string', label:'Buy-From Vendor Name', width:250, editable:false},
                  	{name:'pay_to_vendor_no', type:'number', label:'Pay-To Vendor No.', width:70, editable:false,
                    								popupgrid:{datatable:'vendor', needfield:'id', 
                                                              fields:[{name:'id', type:'number',label:'No.',width:70, hidden:true},
                                                                      {name:'name', type:'string', label:'Vendor', width:250}
                                                              			
                                                              ]
                                                              }
                    },
                  	{name:'pay_to_vendor_no__name', type:'string', label:'Pay-To Vendor Name', width:250, editable:false},
                  	{name:'order_date', type:'date', label:'Order Date', width:100, fieldformat:'MM/dd/yyyy', editable: true, gridColumnProperties:{filtertype:'range'}},
                  	{name:'posting_date', type:'date', label:'Posting Date', width:100, fieldformat:'MM/dd/yyyy', editable: true, gridColumnProperties:{filtertype:'range'}},
                  	{name:'due_date', type:'date', label:'Due Date', width:100, fieldformat:'MM/dd/yyyy', editable: true, gridColumnProperties:{filtertype:'range'}},
                  	{name:'expected_receipt_date', type:'date', label:'Expected Rcpt. Date', width:100, fieldformat:'MM/dd/yyyy', editable: true, gridColumnProperties:{filtertype:'range'}},
                  	{name:'shipment_method', type:'string', label:'Shipment Method', width:100, editable:false,hidden:true, display:false,showlabel:false},
                  	{name:'shipment_method__code', type:'string', label:'Shipment Method', width:100, editable:false,
                    								popupgrid:{datatable:'wm_shipment_method', needfield:'id',setfield:'shipment_method',
                                                              fields:[{name:'id', type:'number',label:'No.',width:70, gridColumnProperties:{hidden:true}},
                                                                      {name:'code', type:'string', label:'Code', width:50},
                                                                      {name:'description', type:'string', label:'Description', width:250}
                                                              		
                                                              ]                                                                 
                                                              }
                    },
                  	{name:'location', type:'number', label:'Location', width:70, editable:false, hidden:true,display:false,showlabel:false},
                  	{name:'location__code', type:'string', label:'Location', width:70, editable:false,
                    								popupgrid:{datatable:'location', needfield:'id',setfield:'location',
                                                              fields:[{name:'id', type:'number',label:'No.',width:70, gridColumnProperties:{hidden:true}},
                                                                      {name:'code', type:'string', label:'Code', width:50},
                                                                      {name:'description', type:'string', label:'Description', width:250}
                                                              		
                                                              ]                                                                 
                                                              }
                    },
                  	{name:'prices_including_vat', type:'bool', label:'Prices Incl. VAT', width:70, editable:true, gridColumnProperties:{columntype:'checkbox'}},
                  	//{name:'receive', type:'bool', label:'Receive', width:70, gridColumnProperties:{columntype:'checkbox'}},
                  	//{name:'invoice', type:'bool', label:'Invoice', width:70, gridColumnProperties:{columntype:'checkbox'}},
                  	{name:'released', type:'bool', label:'Released', width:70, gridColumnProperties:{columntype:'checkbox'}},
                  	{name:'vendor_order_no', type: 'string',label: 'Vendor Order No.', width: 150, editable:true, hidden:true, display:false, showlabel:false },
                  	{name:'vendor_shipment_no', type: 'string',label: 'Vendor Shipment No.', width: 150, editable:true, hidden:true, display:false, showlabel:false },
                  	{name:'vendor_invoice_no', type: 'string',label: 'Vendor Invoice No.', width: 150, editable:true, hidden:true, display:false, showlabel:false },
                  	{name:'vendor_credit_memo_no', type: 'string',label: 'Vendor Cr.Memo No.', width: 150, editable:true, hidden:true, display:false, showlabel:false },
                  	{name:'buy_from_contact_no', type:'number', label:'Buy-From Contact No.', width:70, editable:false, hidden:true},
                  	{name:'pay_to_contact_no', type:'number', label:'Pay-To Contact No.', width:70, editable:false},
                ],
               		formFilters:[['document_type','EQUAL',1]],
                	formTriggers:{
                    	beforeInsert:function(row={}){
                        	row.document_type=1;
                          	return row;
                        }
                    }
               };

var subdata = [{name:'purchase_line', label:'Lines', dataSourceTableName:'purchase_line',
                 fields:[
                  			{name:'id', type: 'number', label: 'Line No.',width: 50, hidden:true },
                   			{name:'header__document_no', type: 'string', label: 'Document No.',width: 150 },
                 			{name:'type', type: 'number', label: 'Type ',width: 50,hidden:true, showlabel:false, display:false},
                   			{name:'type__code', type: 'string', label: 'Type',width: 100,
                            								dropdowngrid:{datatable:'order_line_types', needfield:'id', setfield:'type',
                                                                         fields:[{name:'id',type:'number',label:'id',width:1,gridColumnProperties:{hidden:true}},
                                                                                 {name:'code', type:'string',label:'Code', width:100},
                                                                                ],
                                                                          gridProperties:{showfilterrow: false, pageable: false,showheader: false}
                                                                         }
                            },
                   			{name:'no', type: 'number', label: 'No.',width: 50 ,
                            								popupgrid:function(row){
                             											switch(row.type){
                                                                          case 1: return	{datatable:'gl_accounts', needfield:'id', 
                                                                        		 fields:[{name:'id',type:'number',label:'id',width:1,gridColumnProperties:{hidden:true}},
                                                                                		 {name:'code', type:'string',label:'Code', width:70},
                                                                                		 {name:'name', type:'string',label:'Account Name', width:300,gridColumnProperties:{cellsrenderer:cellsrenderer}},
                                                                                         {name:'intendation',type:'number',label:'id',width:1,gridColumnProperties:{hidden:true}},
                                                                             			]
                                                                                 
                                                                         	};
                                                                          case 2: return  {datatable:'items', needfield:'id', 
                                                                        		 fields:[{name:'id',type:'number',label:'No.',width:70},
                                                                                		 {name:'description', type:'string',label:'Description', width:250},
                                                                             			]
                                                                         	};
                                                                          case 3: return  {datatable:'resources', needfield:'id', 
                                                                        		 fields:[{name:'id',type:'number',label:'No.',width:70},
                                                                                		 {name:'description', type:'string',label:'Description', width:250},
                                                                             			]
                                                                         	};
                                                                          case 4: return  {datatable:'fixed_assets', needfield:'id', 
                                                                        		 fields:[{name:'id',type:'number',label:'No.',width:70},
                                                                                		 {name:'description', type:'string',label:'Description', width:250},
                                                                             			]
                                                                         	};
                                                            }
                            }},
                   			{name:'description', type: 'string', label: 'Description',width: 250, editable:true },
                   			{name:'unit_of_measure', type: 'number', label: 'Unit Of Mes.',width: 100, hidden:true, showlabel:false, display:false},
                   			{name:'quantity', type: 'number', label: 'Quantity',width: 100, editable:true, gridColumnProperties:{cellsformat:'f4'}, input:{decimalDigits:4} },
                   			{name:'unit_of_measure__code', type: 'string', label: 'Unit Of Mes.',width: 100,
                            							popupgrid:{datatable:'unitofmeasure', needfield:'id',setfield:'unit_of_measure',
                                                                         fields:[{name:'id',type:'number',label:'id',width:1,gridColumnProperties:{hidden:true}},
                                                                                 {name:'code', type:'string',label:'Code', width:100},
                                                                                 {name:'description', type:'string',label:'Description', width:150},
                                                                                ]
                                                                            }
                            },
                   			{name:'unit_price', type: 'number', label: 'Unit Price',width: 100, editable:true, gridColumnProperties:{cellsformat:'f2'}, input:{decimalDigits:2} },
                   			{name:'line_amount', type: 'number', label: 'Line Amount',width: 100, gridColumnProperties:{cellsformat:'f2'}, input:{decimalDigits:2} },
                 ],
                  addFilters:function(){return [['header','EQUAL',maindata.formState.rowdata.id]];},
                  formTriggers:{
					beforeInsert: function(row={}){
						row.header=maindata.formState.rowdata.id;
						return row;
						
					}
				}
                 }


];
var buttons = [
  				{name:'release', label:'Release', onClick:function(){
                		var result=rcf('release_purchase_document','release',[maindata.formState.rowdata.id]);
                  		if(result!=''){
                        	showError(result);
                          	return;
                        }
                  		showSuccess('Document '+maindata.formState.rowdata.document_no+' released.');
                  		updateData();
                
                }},
				{name:'reopen', label:'Reopen', onClick:function(){
                		var result=rcf('release_purchase_document','reopen',[maindata.formState.rowdata.id]);
                  		if(result!=''){
                        	showError(result);
                          	return;
                        }
                  		showSuccess('Document '+maindata.formState.rowdata.document_no+' reopened.');
                		updateData();
                
                }},
  				{name:'print', label:'Print', onClick:function(){
                		var result = rr('purchase_quote',[maindata.formState.rowdata.id]);
                  		if(result!=''){
                        	print(result);
                        
                        }
                
                }},
  				{name:'makeorder', label:'Make Order', onClick:function(){}}
];
runForm(maindata, buttons, subdata);
