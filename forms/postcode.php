var maindata = {dataSourceTableName:'postcode',
				fields :[
                    { name: 'id', type: 'number', label: 'No.',width: 50 },
                    
					{ name: 'code', type: 'string',label: 'Code', width:150, editable:true },
                  { name: 'city', type: 'string',label: 'City', width:400, editable:true },
					
                   
                ]};
runForm(maindata);
