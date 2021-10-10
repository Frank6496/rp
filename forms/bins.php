var dataSourceTableName = 'bin';
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
					},
					{ name: 'zoneid', type: 'number',label: 'Zone', width:50, editable:true, gridColumnProperties:{hidden:true}, showlabel:false, display:false },
					{ name: 'zoneid__code', type: 'string',label: 'Zone', width:70,
												popupgrid:{datatable:'zone', needfield:'id', setfield:'zoneid', fields:
														[
															{name:'id', type:'number', label:'No.', width:70},
															{name:'code', type:'string', label:'Code', width:150}
														], gridProperties:{showfilterrow: false, showheader: false},
														filterBefore:function(row){
															return [['locationid','EQUAL', row.locationid]];
														}
													}
					},
					{ name: 'bintype', type: 'number',label: 'Bin Type', width:50, editable:true, gridColumnProperties:{hidden:true}, showlabel:false, display:false },
					{ name: 'bintype__code', type: 'string',label: 'Bin Type', width:70,
												popupgrid:{datatable:'bintypes', needfield:'id', setfield:'bintype', fields:
														[
															{name:'id', type:'number', label:'No.', width:70},
															{name:'code', type:'string', label:'Code', width:150},
															{name:'receive', type:'bool', label:'Receive', width:50,gridColumnProperties:{columntype:'checkbox'}},
															{name:'ship', type:'bool', label:'Ship', width:50,gridColumnProperties:{columntype:'checkbox'}},
															{name:'put_away', type:'bool', label:'Put Away', width:50,gridColumnProperties:{columntype:'checkbox'}},
															{name:'pick', type:'bool', label:'Pick', width:50,gridColumnProperties:{columntype:'checkbox'}}
														], gridProperties:{showfilterrow: false}
													}
					},
					{ name: 'adjustment_bin', type:'bool', label:'Adjustment Bin', width:70, editable:true, gridColumnProperties:{columntype:'checkbox'}},
					{ name: 'maximum_cubage', type: 'number',label: 'Maximum Cubage', width: 120, editable:true},
					{ name: 'maximum_weight', type: 'number',label: 'Maximum Weight', width: 120, editable:true},
					{ name: 'block_movement', type:'bool', label:'Block Movement', width:70, editable:true, gridColumnProperties:{columntype:'checkbox'}},
					{ name: 'empty', type:'bool', label:'Empty', width:70, editable:true, gridColumnProperties:{columntype:'checkbox'}},
					{ name: 'binranking', type: 'number',label: 'Bin Ranking', width: 120, editable:true}
                ];
				
	var maindata = {
				dataSourceTableName:dataSourceTableName,
				fields:fields,
				panelButtons:{addButton:{disabled:false}, deleteButton:{disabled:false}},
			};
	runForm(maindata);
