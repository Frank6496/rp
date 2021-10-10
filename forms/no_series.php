var maindata = {dataSourceTableName:'no_series',
				fields :[
                    { name: 'id', type: 'number', label: 'No.',width: 50 },
                    { name: 'code', type: 'string',label: 'Code', width:150, editable:true },
					{ name: 'description', type: 'string',label: 'Description', width:150, editable:true },
                  	{ name: 'starting_no', type: 'string',label: 'Starting No.', width:150, editable:true },
                 	{ name: 'ending_no', type: 'string',label: 'Ending No.', width:150, editable:true },
                  	{ name: 'increment', type: 'number',label: 'Increment', width:150, editable:true },
                  	{ name: 'last_no_used', type: 'string',label: 'Last No. Used', width:150, editable:true }
                ]};
runForm(maindata);