var maindata = {dataSourceTableName:'wm_shipment_method',
				fields :[
                    { name: 'id', type: 'number', label: 'No.',width: 50 },
                    { name: 'code', type: 'string',label: 'Code', width:150, editable:true },
                  	{name:'description', type:'string', label:'Description', width:400, editable:true}
                   
                ]};

runForm(maindata);
