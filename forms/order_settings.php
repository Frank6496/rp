var maindata = {dataSourceTableName:'order_settings',
				fields :[
                    { name: 'id', type: 'number', label: '№ пп',width: 50, hidden:true },
                    { name: 'preffix', type: 'string',label: 'Префикс', width:150, editable:true },
					{ name: 'next', type:'number', label:'Следующий номер заказа', width:200, editable:true},
					{ name: 'suffix', type: 'string',label: 'Суфикс', width:150, editable:true },
					{ name: 'template', type: 'string',label: 'Шаблон номера', width:150, editable:true }
					
                   
                ],
				panelButtons:{addButton:{disabled:true}, deleteButton:{disabled:true}},
				gridProperties:{showtoolbar: false,showfilterrow: false}};
initForm(maindata, userSettings);	
