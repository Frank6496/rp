var maindata = {dataSourceTableName:'order_status',
				fields :[
                    { name: 'id', type: 'number', label: '№ пп',width: 50 },
                    { name: 'description', type: 'string',label: 'Название', width:400, editable:true },
					{ name: 'need_email', type:'bool', label:'Уведомить по email', width:140, editable:true, gridColumnProperties:{columntype:'checkbox'}}
					
                   
                ]};
initForm(maindata, userSettings);				
