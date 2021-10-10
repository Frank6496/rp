var maindata = {dataSourceTableName:'userroles',
				fields : [
                    { name: 'id', type: 'number', label: 'No.',width: 50 },
					{ name: 'user', type: 'number', label: 'User', width:50, gridColumnProperties:{hidden:true} },
                    { name: 'user__username', type: 'string', label: 'User', width:250 },
					{ name: 'role', type: 'number', label: 'Role', width:50, gridColumnProperties:{hidden:true} },
					{ name: 'role__description', type: 'string', label: 'Role', width:250 }
                ]
			   };
initForm(maindata, userSettings);
