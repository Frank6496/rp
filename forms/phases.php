var maindata = {dataSourceTableName:'phrases',
            fields : [
                    { name: 'id', type: 'number', label: '№ пп',width: 70 },
					{ name: 'phrase', type: 'string',label: 'фраза', width: 400, editable:true }
					
					
					
]}
};

initForm(maindata, userSettings /*,formButtons,slaveData */);
