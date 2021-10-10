var maindata = {dataSourceTableName:'phrases',
            fields : [
                    { name: 'id', type: 'number', label: '№ пп',width: 70 },
					{ name: 'phrase', type: 'string',label: 'фраза', width: 400, editable:true }
					
					
					
]};
var formButtons = [
						
						{name:'loadimages', label:'Загрузить', 
							onClick: function(){
								
								var result = rcf('yandex','savephrases');
								updateData();
								
								if(result!=''){	showError(result, 20000);}
								
							}
						}
					];

initForm(maindata, userSettings ,formButtons/*,slaveData */);
