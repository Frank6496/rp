var maindata = {dataSourceTableName:'item_classes',
            fields : [
                    { name: 'id', type: 'number', label: 'No.',width: 70 },
                    { name: 'code', type: 'string',label: 'Code', width: 100, editable:true },
                    { name: 'description', type: 'string',label: 'Description', width: 400, editable:true }
                    
]
};
runForm(maindata);
