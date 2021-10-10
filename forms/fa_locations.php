var maindata = {dataSourceTableName:'fa_locations',
				fields :[
                    { name: 'id', type: 'number', label: 'No.',width: 50 },
                    { name: 'code', type: 'string',label: 'Code', width:150, editable:true },
					{ name: 'description', type: 'string',label: 'Description', width:250, editable:true }       
                ]};
runForm(maindata);
