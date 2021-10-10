var maindata = {dataSourceTableName:'gl_tax_prod_post_gr',
				fields :[
                    { name: 'id', type: 'number', label: 'No.',width: 50 },
                    { name: 'code', type: 'string',label: 'Code', width:150, editable:true },
					{ name: 'description', type: 'string',label: 'Description', width:250, editable:true }       
                ]};
runForm(maindata);
