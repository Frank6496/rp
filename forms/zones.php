var dataSourceTableName = 'zone';
            var fields = [
                    { name: 'id', type: 'number', label: 'No.',width: 70},
					{ name: 'code', type: 'string',label: 'Code', width: 100, editable:true },
					{ name: 'description', type: 'string',label: 'Description', width: 200, editable:true },
					{ name: 'locationid', type: 'number',label: 'Location', width:50, editable:true, gridColumnProperties:{hidden:true}, showlabel:false, display:false },
					{ name: 'locationid__code', type: 'string',label: 'Location', width:70,
												dropdowngrid:{datatable:'location', needfield:'id', setfield:'locationid', fields:
														[
															{name:'id', type:'number', label:'No.', width:70, gridColumnProperties:{hidden:true}},
															{name:'code', type:'string', label:'Code', width:150}
														], gridProperties:{showfilterrow: false, pageable: false,showheader: false}
													}
					}
					
                ];
				
	var masterSettings = {
				dataSourceTableName:dataSourceTableName,
				fields:fields,
				panelButtons:{addButton:{disabled:false}, deleteButton:{disabled:false}},
			};
	runForm(masterSettings);
