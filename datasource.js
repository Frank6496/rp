function createParams(tf, rd){
	var tr={};
	var fs=[];
	for(k in tf){
		fs.push(tf[k].name);
		if(rd[tf[k].name]===undefined){continue;}
		tr[tf[k].name] = prepareParam(tf[k].type, rd[tf[k].name])
	}
	$.extend(tr,{fieldset:fs.join(',')});
	return tr;
	
}

function prepareParam(t, v){
	switch(t){
			case 'date':
						if(v==''||v==undefined||v===null){
							return null;
						}else{
							var d= new Date(v);
							return getMysqlDateString(d);
						}	
						break;
			 case 'bool':return v?1:0;
						break; 
			default:return v;
					break;
		};
}

function getFieldSet(tf){
	var fs=[];
	for(var k in tf){
		fs.push(tf[k].name)
	}
	return fs.join(',');
}

function getMysqlDateString(d){
	return d.getFullYear()+'-'+((d.getMonth()+1)>9?'':'0')+(d.getMonth()+1)+'-'+(d.getDate()>9?'':'0')+d.getDate();
}

function dataSourceUrl(){
	return '../../panel/modules/data/data.php';
}
function classRunUrl(){
	return '../../panel/modules/classes/run.php';
}
function reportRunUrl(){
	return '../../panel/modules/reports/run.php';
}		
function getDataSource(tf){
		var source =
		{
			datatype: "json",
			datafields: tf,
			cache: false,
			url: dataSourceUrl(),
			addrow: function (rowid, rowdata, position, commit) {
									commit(true);
								},
			deleterow: function (rowid, commit) {
									commit(true);
								},
			updaterow: function (rowid, rowdata, commit) {
									commit(true);
								},							
			
			root: 'Rows',
			type: 'POST'
		};
return source;		
};

function showError(m, t=8000){
	showMessage(m, 'error', t);
}
function showWarning(m, t=8000){
	showMessage(m, 'warning', t);
}
function showInfo(m, t=8000){
	showMessage(m, 'info', t);
}
function showSuccess(m, t=8000){
	showMessage(m, 'success', t);
}

function getDTSource(tf){
	var source = {
			datatype: "json",
			datafields: tf,
			cache: false,
			url: dataSourceUrl(),
			root: 'Rows',
			type: 'POST',
			beforeprocessing: function(data)
			{		
			if (data != null)
				{
					this.totalrecords = data.TotalRows;					
				}
			}
			}
	return source;
};

function rcf(cN, fN, a=[],async=false){
	
	var d = {className:cN,funcName:fN, argcount:a.length};
	var p = [];
	for (var i = 0; i < a.length; i++){
		p['arg'+i]= a[i];
	};
	$.extend(d, p);
	d = $.param(d);
	var r=null;
	$.ajax({
			url: classRunUrl(),
			data: d,
			type: 'POST',
			async: async,
			cache: false,
			success: function (data, status, xhr) {
				r=data;			
											},
			error: function (jqXHR, textStatus, errorThrown) {
						if (jqXHR.status === 0) {
							//alert('No connection.\n Verify Network.');
						} else if (jqXHR.status == 404) {
							alert('Requested page not found. [404]');
						} else if (jqXHR.status == 500) {
							alert('Internal Server Error [500].');
						} else if (textStatus === 'timeout') {
							alert('Time out error.');
						} else if (textStatus === 'abort') {
							alert('Ajax request aborted.');
						} else {
							alert('Uncaught Error.\n' + jqXHR.responseText);
						};
					}
		});	
	return r;
};

function rr(rN, a=[]){
	
	var d = {reportName:rN, argcount:a.length};
	var p = [];
	for (var i = 0; i < a.length; i++){
		p['arg'+i]= a[i];
	};
	$.extend(d, p);
	d = $.param(d);
	var r=null;
	$.ajax({
			url: reportRunUrl(),
			data: d,
			type: 'POST',
			async: false,
			cache: false,
			success: function (data, status, xhr) {
				r=data;			
											},
			error: function (jqXHR, textStatus, errorThrown) {
						if (jqXHR.status === 0) {
							//alert('No connection.\n Verify Network.');
						} else if (jqXHR.status == 404) {
							alert('Requested page not found. [404]');
						} else if (jqXHR.status == 500) {
							alert('Internal Server Error [500].');
						} else if (textStatus === 'timeout') {
							alert('Time out error.');
						} else if (textStatus === 'abort') {
							alert('Ajax request aborted.');
						} else {
							alert('Uncaught Error.\n' + jqXHR.responseText);
						};
					}
		});	
	return r;
};

function htmlspecialchars(text) {
	return text
	.replace(/&/g, "&amp;")
	.replace(/"/g, "&quot;")
	.replace(/'/g, "&#039;")
	.replace(/</g, "&lt")
	.replace(/>/g, "&gt");
}

function callDbUpdate(rd,t,tf){
									var data = $.param({update:true, table:t})+'&'+$.param(createParams(tf,rd));
									var r=null;
									var c=true;
									var err='';
									$.ajax({
											url: dataSourceUrl(),
											data: data,
											datatype: "json",
											cache: false,
											async: false,
											type: 'POST',
											success: function (data, status, xhr) {
												try{
													r=JSON.parse(data);
												} catch(e){
													err = r+' Serverside:'+data;
												}	
												if(r.Error!=undefined){
													err = r.Error;
												};
												c=true;
											},
											error: function (jqXHR, textStatus, errorThrown) {
												err = "jqXHR:"+jqXHR.status+" textStatus:"+textStatus+" error:"+errorThrown;
												c=false;
												}
											});
		if(err!=''){ r.err=err;r.commit=false;return r;}
		
		r=r.Rows[0];
		r.commit=c;
		return r;									
};

function replaceFilterDates(data, fS){
		var dt = data;
		if(dt.filterscount==0){return dt;}
		for(var i=0; i<dt.filterscount; i++){
			if(fS[dt['filterdatafield'+i]].type=='date'){
				dt['filtervalue'+i]=replaceDateByFormat(dt['filtervalue'+i], fS[dt['filterdatafield'+i]].fieldformat);
			}
		}
	return dt;
}

function replaceDateByFormat(d, f){
		switch(f){
					case 'dd.MM.yyyy':return getMysqlDateString(new Date(d.replace(/(\d+).(\d+).(\d+)/, '$3-$2-$1')));
					case 'MM.dd.yyyy':return getMysqlDateString(new Date(d.replace(/(\d+).(\d+).(\d+)/, '$3-$1-$2')));
					case 'dd.MM.yyyy HH:mm:ss':return getMysqlDateString(new Date(d.replace(/(\d+).(\d+).(\d+) (\d+):(\d+):(\d+)/, '$3-$2-$1T$4:$5:$6')));
					case 'MM.dd.yyyy HH:mm:ss':return getMysqlDateString(new Date(d.replace(/(\d+).(\d+).(\d+) (\d+):(\d+):(\d+)/, '$3-$1-$2T$4:$5:$6')));
					case 'dd-MM-yyyy':return getMysqlDateString(new Date(d.replace(/(\d+)-(\d+)-(\d+)/, '$3-$2-$1')));
					case 'MM-dd-yyyy':return getMysqlDateString(new Date(d.replace(/(\d+)-(\d+)-(\d+)/, '$3-$1-$2')));
					case 'dd-MM-yyyy HH:mm:ss':return getMysqlDateString(new Date(d.replace(/(\d+)-(\d+)-(\d+) (\d+):(\d+):(\d+)/, '$3-$2-$1T$4:$5:$6')));
					case 'MM-dd-yyyy HH:mm:ss':return getMysqlDateString(new Date(d.replace(/(\d+)-(\d+)-(\d+) (\d+):(\d+):(\d+)/, '$3-$1-$2T$4:$5:$6')));
					case 'dd/MM/yyyy':return getMysqlDateString(new Date(d.replace(/(\d+)\/(\d+)\/(\d+)/, '$3-$2-$1')));
					case 'MM/dd/yyyy':return getMysqlDateString(new Date(d.replace(/(\d+)\/(\d+)\/(\d+)/, '$3-$1-$2')));
					case 'dd/MM/yyyy HH:mm:ss':return getMysqlDateString(new Date(d.replace(/(\d+)\/(\d+)\/(\d+) (\d+):(\d+):(\d+)/, '$3-$2-$1T$4:$5:$6')));
					case 'MM/dd/yyyy HH:mm:ss':return getMysqlDateString(new Date(d.replace(/(\d+)\/(\d+)\/(\d+) (\d+):(\d+):(\d+)/, '$3-$1-$2T$4:$5:$6')));
				}
}

function State(){
	return {tabclicked:0, fromtab:0, rowdata:{},rowindex:-1, savedFilerSorting:{}, additionalfilters:[], selectedrows:{}, editrowdata:{}, editrowindex:-1, editdatafield:'', ddrowdata:{}, calcFilters:{},
							getSelectedRowsIndexes: function(){
								tmp=[];
								for(var i in this.selectedrows){
									if(this.selectedrows[i]!=undefined){tmp.push(this.selectedrows[i].id)};
								}
								if(tmp.length>0){return tmp.join(',');}
								else{ return '';}
	}};
}

function createTabControls(f, theme, s=''){
			var m='';
			var p='<table id="ctrls">';
			for(var k in f) {
					var label = ((f[k].showlabel!=undefined)&&f[k].showlabel)||(f[k].showlabel==undefined)?f[k].label:'';
					var display = (f[k].display==undefined||f[k].display)?'inline-block':'none';
					var t='<tr>';
					if(f[k].type=='string'||f[k].type=='number'){
						t+= '<td class="jqx-widget-content-'+theme+'"><div id="label-'+s+f[k].name+'">'+label+'</div></td><td><div style="display:'+display+';">'+
						((f[k].textarea==undefined)?'<input id="'+s+f[k].name+'" style="float:left;"/>':'<textarea id="'+s+f[k].name+'" style="float:left;"></textarea>');
					}else{
							t+= '<td class="jqx-widget-content-'+theme+'"><div id="label-'+s+f[k].name+'">'+label+'</div></td><td><div style="display:'+display+';"><div id="'+s+f[k].name+'"></div>';
					};
					 if(f[k].dropdowngrid!=undefined){
						t+='<div style="float:left;" id="dropdownbutton-'+s+f[k].name+'"><div style="border-color: transparent;" id="jqxgrid-'+s+f[k].name+'"></div></div>';
					}; 
					 if(f[k].popupgrid!=undefined){
						t+='<input type="button" style="float:left;" id="dropdownbutton-'+s+f[k].name+'" />';
					};
					t+= '</div></td></tr>';
					
					if(f[k].ctrlcoords!=undefined||((f[k].display!=undefined)&&(!f[k].display))){
						m+=t;
					} else{
						p+=t;
					}
				
			};
				p+=m+'</table>';
				return p;
		}

function print(text, orientation='portrait', size='A4', margin='1%'){
				var printing_css='<style media=print>tr:nth-child(even) td{background: #f0f0f0;} @page { size: '+size+' '+orientation+';margin: '+margin+'; }</style>';
				var html_to_print=printing_css+text;
				
				var iframe=$('<iframe id="print_frame">');
				$('body').append(iframe);
				var doc = $('#print_frame')[0].contentDocument || $('#print_frame')[0].contentWindow.document;
				var win = $('#print_frame')[0].contentWindow || $('#print_frame')[0];
				doc.getElementsByTagName('body')[0].innerHTML=html_to_print;
				console.log(doc);
				setTimeout(function() {
						win.print();
						$('iframe').remove();
						}, 250);
				
		}
function download(d, fn) {
				var a = document.createElement("a"),
				f = new Blob([d]);
				var url = URL.createObjectURL(f);
				a.href = url;
				a.download = fn;
				document.body.appendChild(a);
				a.click();
				setTimeout(function() {
					document.body.removeChild(a);
					window.URL.revokeObjectURL(url);  
				}, 0); 
		}
function showMessage(notificationText, template, showTime=8000){
				var notificaion_element=$('<div id="notification"><div>'+notificationText+'</div></div>');
				$('body').append(notificaion_element);
				
				$("#notification").jqxNotification({
					opacity: 1,
					autoOpen: false,
					autoClose: true,
					showCloseButton: true,
					autoCloseDelay: showTime,
					browserBoundsOffset: $(document).width()*0.05,
					template: template,
					position: "bottom-left"
				});
				
				$("#notification").jqxNotification('open');
				$('#notification').remove();
			}		

function createGrid(tblName, flds, gridSelector, properties){
									var source = getDTSource(flds);
									source = addFilterSortOptions(source, gridSelector);	
									$.extend(source, properties.sourceProperties);
										var dataAdapter = new $.jqx.dataAdapter(source,
											$.extend({
												formatData: function(data){
													var fldsSettings = [];
													for(var i in flds){
														fldsSettings[flds[i].name]=flds[i]; 
													}
													data = replaceFilterDates(data, fldsSettings);
													
													var addfiltercount=0;
													for(var i in properties.filter){
															var filterobj={};
															filterobj["addfilterdatafield"+addfiltercount] = properties.filter[i][0];
															filterobj["addfiltercondition"+addfiltercount] = properties.filter[i][1];
															filterobj["addfiltervalue"+addfiltercount] = properties.filter[i][2];
															$.extend(data,filterobj);
															
															addfiltercount+=1;				
														
													}
													if(addfiltercount>0){
														$.extend(data,{addfiltercount:addfiltercount});
													} 
													$.extend(data, {table:tblName, fieldset: getFieldSet(flds)});
													
												}
											}, properties.dataadapterProperties));
										
										var columns=[];
										var grdWidth=0;
										for(var i in flds){
											var clmn={};
											clmn.text=flds[i].label;
											clmn.type=flds[i].type;
											clmn.datafield=flds[i].name;
											clmn.width=(flds[i].width==undefined?100:flds[i].width);
											grdWidth+=((flds[i].gridColumnProperties!=undefined)&&flds[i].gridColumnProperties.hidden)?0:clmn.width;
											$.extend(clmn, flds[i].gridColumnProperties);
											columns.push(clmn);
										}
										
										$("#"+gridSelector).jqxGrid(
										 $.extend({
											width: grdWidth,
											source: dataAdapter,
											autoheight: true,
											selectionmode: 'singlerow',
											pageable: true,
											pagesize: 10,
											pagesizeoptions: ['10', '15', '25','50'],
											pagermode: 'simple',
											virtualmode: true,
											filterable: true,
											sortable: true,
											showfilterrow: true,
											rendergridrows: function(obj)
											{
												return obj.data; 
											}, 
											columnsresize: true,
											columns: columns
											 
										 }, properties.gridProperties));
			}
			
function createDropdownGrid(f, gridSettings, formState, us, sub=undefined){
						var theme = us.theme;
						var fName = (sub==undefined)?((gridSettings.setfield!=undefined)?gridSettings.setfield:f):((gridSettings.setfield!=undefined)?(sub.name+gridSettings.setfield):(sub.name+f));
						var sName = (sub==undefined)?'':sub.name;
						var flds = gridSettings.fields;
						var dtName = gridSettings.datatable;
						var nField = gridSettings.needfield;
						var gridSelector= "jqxgrid-"+sName+f;
						var addProperties = $.extend({theme: theme, localization: getLocalization(us.localization)}, gridSettings.gridProperties);
						addProperties = {gridProperties:addProperties};
						if(gridSettings.filterBefore!=undefined){addProperties = $.extend(addProperties, {filter:gridSettings.filterBefore(formState.rowdata)});}
						createGrid(dtName, flds, gridSelector, addProperties);
						
					$("#dropdownbutton-"+sName+f).jqxDropDownButton({
							width: 25, height: 20
						});
					$("#dropdownbutton-"+sName+f).jqxDropDownButton('setContent', "...");
							
					$("#"+gridSelector).on('rowdoubleclick', function (event) {
								if($("#"+fName).val()!=event.args.row.bounddata[nField]){
									$("#"+fName).val(event.args.row.bounddata[nField]);
									$("#"+fName).change();
								}		
							$("#dropdownbutton-"+sName+f).jqxDropDownButton('close');
					});	
					$("#dropdownbutton-"+sName+f).on('open', function(){
						formState.ddrowdata[fName]=formState.editrowdata[fName];
						$("#"+gridSelector).jqxGrid('selectrow', 0);
					});
				}

function dialog(obj){
			var theme = obj.theme;
			var localization = (obj.localization=='undefined')?'en':obj.localization;
			var localization_m = getLocalization_m(localization);
			var width = (obj.width==undefined)?400:obj.width;
			var height = (obj.height==undefined)?300:obj.height;
			$('body').append('<div id="confirmDialog"><div>'+obj.title+'</div><div>'+obj.message+'<div style="position: absolute;bottom:5;"><div style="margin-left:'+(width-160)+'""><table><tr><td><input type="button" id="ok" value="'+localization_m.ok+'" /></td><td><input type="button" id="cancel" value="'+localization_m.cancel+'" /></td></tr></table></div></div></div></div>');
			var jqxWidget = $('#jqxTabs');
			if(obj.beforeOpen!=undefined){obj.beforeOpen();}
            var offset = jqxWidget.offset();
			$('#confirmDialog').jqxWindow({
                maxHeight:height,
				maxWidth:width,
				height: height, 
				theme: theme,
				width: width,
				position: { x: (obj.x==undefined)?($(document).width()/2-width/2):obj.x, y: (obj.y==undefined)?($(document).height()/2-height/2):obj.y} ,
                resizable: false, isModal: true, modalOpacity: 0.4,
                okButton: $('#ok'),
				theme:theme,
				cancelButton: $('#cancel'),
                initContent: function () {
                    $('#ok').jqxButton({ width: '65px' });
                    $('#cancel').jqxButton({ width: '65px' });
                    $('#ok').focus();
                }
            });
			 $('#confirmDialog').on('close', function (event) {
               var timeoutId = setTimeout(function(){$('#confirmDialog').remove();if(obj.onClose!=undefined){obj.onClose();}; clearTimeout(timeoutId);},200);
            });
			$('#ok').on('click', function(event){if(obj.success!=undefined){obj.success();}});
			$('#cancel').on('click', function(event){if(obj.cancel!=undefined){obj.cancel();}});
			$('#confirmDialog').jqxWindow('open');
			
		}				
				
function createPopupGrid(f, gS, fS, us, sl=undefined){
					var theme = us.theme;
					var localization = (us.localization=='undefined')?'en':us.localization;
					var localization_m = getLocalization_m(localization);
					
					var sName = (sl==undefined)?'':sl.name;
					var gSel= "jqxgrid-"+sName+f;
					$("#dropdownbutton-"+sName+f).jqxButton({
							width: 25, height: 22, value:'...'
						});
								
					$("#dropdownbutton-"+sName+f).on('click',
					function(){
						var gridWidth=0;
						var popupgrid;
						if(typeof gS =='function') {
							popupgrid = gS(fS.rowdata);
							if(popupgrid==undefined)return;
						}else{
							popupgrid = gS;
							}
						
						var fName = (sl==undefined)?
								((popupgrid.setfield!=undefined)?popupgrid.setfield:f)
								:((popupgrid.setfield!=undefined)?(sl.name+popupgrid.setfield):(sl.name+f));
								
						var nField = popupgrid.needfield;
						for(var i in popupgrid.fields){
							gridWidth+=(popupgrid.fields[i].gridColumnProperties!=undefined&&popupgrid.fields[i].gridColumnProperties.hidden)?0:popupgrid.fields[i].width;
						}
						
						dialog({
								title:localization_m.choosevalue, 
								message:'<div id="'+gSel+'"></div>', 
								nField:nField,
								fName:fName,
								theme:theme,
								localization:localization,
								saveData:function(row){this.rowdata=row;},
								beforeOpen: function(){
									var filter=[];
									filter = (popupgrid.filterBefore!=undefined)?popupgrid.filterBefore(fS.rowdata):[];
									var passParams = {localization:getLocalization(us.locaization)};
									if(filter!=[]){$.extend(passParams, {filter:filter});}
									var gridProperties = {autoheight: false, theme: theme};
									$.extend(gridProperties, gS.gridProperties);
									$.extend(passParams, {gridProperties:gridProperties});
									createGrid(popupgrid.datatable, popupgrid.fields, gSel, passParams);
									
									
									 $('#'+gSel).on('rowselect', function (event){
										fS.ddrowdata[fName]=event.args.row[nField];
									});
									$('#'+gSel).on('rowdoubleclick', function (event){
											$("#ok").click();
										});  
								},
								success:function(){
									if($("#"+fName).val()!=fS.ddrowdata[fName]){
												$("#"+fName).val(fS.ddrowdata[fName]);
												$("#"+fName).change();
											};
									
								},
								cancel:function(){
									
								},
								width:(popupgrid.width==undefined)?(gridWidth+10):popupgrid.width,
								height:(popupgrid.height==undefined)?500:popupgrid.height,
								x:(popupgrid.x==undefined)?50:popupgrid.x,
								y:(popupgrid.y==undefined)?50:popupgrid.y,
								});		
				});	 
		}

function placeControl(f, s=''){
	if(f.ctrlcoords==undefined){return;}
	var los= $('#label-'+s+f.name).offset();
	var offset = $("#"+s+f.name).offset();
	var go = $('#ctrls').offset();
	var od;
	if(f.ctrlcoords.x!=undefined){
		od = offset.left-los.left;
		$('#label-'+s+f.name).offset({left:go.left+f.ctrlcoords.x});
		$("#"+s+f.name).offset({left:go.left+f.ctrlcoords.x+od});
		if(f.dropdowngrid!=undefined||f.popupgrid!=undefined){
			$('#dropdownbutton-'+s+f.name).offset({left:go.left+f.ctrlcoords.x+od+f.width+2});
		}
	}
					
	if(f.ctrlcoords.y!=undefined){
		offset = $("#"+s+f.name).offset();
		$("#"+s+f.name).offset({top:go.top+f.ctrlcoords.y}); 
		$('#label-'+s+f.name).offset({top:go.top+f.ctrlcoords.y});
		if(f.dropdowngrid!=undefined||f.popupgrid!=undefined){
			$('#dropdownbutton-'+s+f.name).offset({top:go.top+f.ctrlcoords.y});
		}
	}
};	

function addFilterSortOptions(s,g='jqxgrid'){
	return $.extend(s, {
				filter: function()
				{
					$("#"+g).jqxGrid('updatebounddata', 'filter');
				},
				sort: function()
				{
					$("#"+g).jqxGrid('updatebounddata', 'sort');
				},
				beforeprocessing: function(data)
				{		
				if (data != null)
					{
						this.totalrecords = data.TotalRows;					
					}
				}
			});
	
}	

function initTriggers(){
	return { beforeInsert:function(row={}){
				return row;
				},
			beforeUpdate:function(row){
				return row;
	}};
} 

function updateData(gridname="jqxgrid"){
			$("#"+gridname).jqxGrid('updatebounddata','data');
		}

function hideshowcolumns(gridname,theme,us){
							var content = '';
							var cols = $("#"+gridname).jqxGrid("columns");
							var colStates = [];
							for (var i = 0; i < cols.records.length; i++) {
								content='<div id="showhidelistbox"></div>';
								colStates.push({value:cols.records[i].datafield,checked:!cols.records[i].hidden, label:cols.records[i].text});
							}
							
							dialog({
									title:'Hide/Show Columns',
									theme:theme,
									localization:us.localization,
									message:content, 
									beforeOpen: function(){
										
												var listSource = colStates;
												$("#showhidelistbox").jqxListBox({ source: listSource, width: 200, height: 550,  checkboxes: true, theme:theme });
												
													$('#showhidelistbox').on('checkChange', function(event){
															
															$("#"+gridname).jqxGrid('beginupdate');
																if (event.args.checked) {
																	$("#"+gridname).jqxGrid('showcolumn', event.args.value);
																}
																else {
																	$("#"+gridname).jqxGrid('hidecolumn', event.args.value);
																}
																$("#"+gridname).jqxGrid('endupdate');
																
															});	
													
													},
									success:function(){
                                                  
													
												},
									width:220,
									height:640,
									x:50,
									y:50
												
											});	
							
					};

function initForm(m,us, b=[], s=[]){	
		var theme = us.theme;
		var localization = (us.localization=='undefined')?'en':us.localization;
		var localization_m = getLocalization_m(localization);
		var subData = s;
		var formname = us.formname;
		var fields = m.fields;
		var dataSourceTableName = m.dataSourceTableName;
		var formFilters = (m.formFilters==undefined)?[]:m.formFilters;
		var formButtons = b;
		var formTriggers = new initTriggers();
		$.extend(formTriggers,m.formTriggers);
		
		$('body').append("<div id='jqxTabs' style='float:left;'></div>");
		$('#jqxTabs').append("<ul id='mainTabList'></ul>");
		$('#mainTabList').append('<li>'+localization_m.view+'</li>');
		$('#mainTabList').append('<li>'+localization_m.details+'</li>');
		
		$('#jqxTabs').append("<div id='jqxgrid'></div>");
		$('#jqxTabs').append("<div id='tab1'></div>");
		for(var i in subData){
			$('#mainTabList').append('<li>'+subData[i].label+'</li>');
			$('#jqxTabs').append("<div id='tab_sub"+subData[i].name+"'></div>");
			$('#tab_sub'+subData[i].name).append("<div id='jqxTabs"+subData[i].name+"'></div>");
			$('#jqxTabs'+subData[i].name).append("<ul id='tabList"+subData[i].name+"'></ul>");
			$('#tabList'+subData[i].name).append("<li>"+localization_m.view+"</li>");
			$('#tabList'+subData[i].name).append("<li>"+localization_m.details+"</li>");
			$('#jqxTabs'+subData[i].name).append("<div id='"+subData[i].name+"jqxgrid'></div>");
			$('#jqxTabs'+subData[i].name).append("<div id='"+subData[i].name+"tab1'></div>"); 
		} 
		
		$('body').append("<div id='jqxButtonTab' style='float:left;margin-left:1px'><ul id='jqxRightTabList'><li>"+localization_m.actions+"</li></ul><div id='ButtonSet' style='float:none;margin-top:20px;margin-left:10px'></div></div>");
		
		if(m.calcFilters!=undefined){
			$('#jqxRightTabList').append('<li>'+localization_m.filters+'</li>');
			$('#jqxButtonTab').append('<div id="CalcFiltersSet" style="float:none;margin-top:20px;margin-left:10px">');
			}
		
		var formState = new State();
		m.formState=formState;	
		var tab1 = document.getElementById('tab1');
		tab1.innerHTML = createTabControls(fields, theme);
		
			 
		function fillTabFields(fields){
				if(formState.rowdata==undefined)return;
				formState.editrowdata = formState.rowdata;
                    for( k in fields) { 
					 				if($("#"+fields[k].name).val()!=formState.editrowdata[fields[k].name]){
										
										$("#"+fields[k].name).val(formState.editrowdata[fields[k].name]);
										
									}	
									if(fields[k].columntype=='maskedinput'&&formState.editrowdata[fields[k].name]==null){
										$("#"+fields[k].name).jqxMaskedInput('clearValue');
									}
					};
			};	
		function updateTabField(f){
				var flagChanged = false;
				var saveValue=formState.editrowdata[f.name];
				switch(f.type){
						case 'date':if(formState.editrowdata[f.name]!=$("#"+f.name).val('date')){
										formState.editrowdata[f.name]=$("#"+f.name).val('date');
										flagChanged=true;
									}
									break;
						case 'bool':if(formState.editrowdata[f.name]!=$("#"+f.name).jqxCheckBox('val')){	
										formState.editrowdata[f.name]=$("#"+f.name).jqxCheckBox('val');
										flagChanged=true;
									}	
									break;
						default:
								if((f.editor!=undefined)&&(f.editor)){
									if(formState.editrowdata[f.name]!=$("#"+f.name).jqxEditor('val')){
										formState.editrowdata[f.name]=$("#"+f.name).jqxEditor('val');
										flagChanged=true;
									}
								break;
								}
								
								if(formState.editrowdata[f.name]!=$("#"+f.name).val()){
									formState.editrowdata[f.name]=$("#"+f.name).val();
									flagChanged=true;
									
								}
									
								break;
					 };
				
					 if(flagChanged&&formState.editRowInProgress==undefined){
						formState.editRowInProgress=formState.editrowindex;
						var datarecord=formState.editrowdata; 						
						var requestrecord={};
						requestrecord['id']=datarecord['id'];
						requestrecord[f.name]=datarecord[f.name];
						var returnedData = updateRow(requestrecord,dataSourceTableName,dataSourceFields);
						if(returnedData.err!=undefined){formState.editRowInProgress=undefined;formState.editrowdata[f.name]=saveValue;$("#"+f.name).val(saveValue); showError(returnedData.err); return;}
						if(returnedData.commit){
										for(var j in dataSourceFields){
											if(returnedData[dataSourceFields[j].name]!=datarecord[dataSourceFields[j].name]){
													switch (dataSourceFields[j].type){
														case 'date':
																	var d = datarecord[dataSourceFields[j].name];
																		if((d!=null)&&(getMysqlDateString(d)!=returnedData[dataSourceFields[j].name])||(d==null)){
																		$("#"+dataSourceFields[j].name).val(returnedData[dataSourceFields[j].name]);
																		formState.editrowdata[dataSourceFields[j].name]=$("#"+dataSourceFields[j].name).val('date');
																		}
																	break;
														case 'bool':var b=datarecord[dataSourceFields[j].name]?1:0;
																		if(b!=returnedData[dataSourceFields[j].name]){
																			$("#"+fields[j].name).val(returnedData[dataSourceFields[j].name]);
																			formState.editrowdata[dataSourceFields[j].name]=$("#"+dataSourceFields[j].name).val();
																			
																		}
																	break;
														default:
																if((f.editor!=undefined)&&(f.editor)){
																	$("#"+dataSourceFields[j].name).jqxEditor('val', returnedData[dataSourceFields[j].name]);
																	formState.editrowdata[dataSourceFields[j].name]=$("#"+dataSourceFields[j].name).jqxEditor('val');
																	
																}else{
																$("#"+dataSourceFields[j].name).val(returnedData[dataSourceFields[j].name]);
																formState.editrowdata[dataSourceFields[j].name]=$("#"+dataSourceFields[j].name).val();}
																break;
													}
											};
										}
										var timeoutId = setTimeout(function(){formState.editRowInProgress=undefined; clearTimeout(timeoutId);},20);
						}
						
					 }				
			};
		
		function updateGridFieldsFromTab(){
				var editrow = $("#jqxgrid").jqxGrid('getselectedrowindex');
				for(var j in dataSourceFields){
						$("#jqxgrid").jqxGrid('setcellvalue', editrow, dataSourceFields[j].name, formState.editrowdata[dataSourceFields[j].name]);
				}	
		}
		
		function updateRow(rowdata,tblname,tblfields,sub=undefined){
			try{
				if(sub==undefined){
					return callDbUpdate(formTriggers.beforeUpdate(rowdata),tblname,tblfields);
				} else{
					return callDbUpdate(sub.formTriggers.beforeUpdate(rowdata),tblname,tblfields);
				}
					
			}catch(e){
				rowdata.err = e.toString();
				return rowdata;
			}	
		}
		
		var gridColumns=[];
		var dataSourceFields=[];
		var gridWidth=0;
		var fieldcount=0;
		var fieldsettings={};
		for(var k in fields) {
				fieldcount+=1;
				fieldsettings[fields[k].name]=fields[k];
				gridWidth+=fields[k].width;
				column={};
				 
				column = {name:fields[k].name, type:fields[k].type};
				dataSourceFields.push(column);
				
			 	column = {};
				column = {text:fields[k].label, datafield:fields[k].name, width:fields[k].width};
				if(fields[k].displayfield!=undefined){$.extend(column,{displayfield:fields[k].displayfield});}
				if(fields[k].hidden!=undefined){$.extend(column,{hidden:fields[k].hidden});}
				$.extend(column,{editable:(fields[k].editable!=undefined?fields[k].editable:false)});
				$.extend(column, {cellvaluechanging: function (row, column, columntype, oldvalue, newvalue) {
								var timeoutIdxValue=12;
								if(columntype=='checkbox'){timeoutIdxValue=20;}
								if(columntype!='checkbox'&&oldvalue==newvalue) return oldvalue;
								if((columntype=='checkbox')&&(newvalue?oldvalue:!oldvalue)){return oldvalue;}
								var timeoutIdx= setTimeout(function(){
								if(formState.editRowInProgress==undefined){
									formState.editRowInProgress=row;
									var datarecord =  $('#jqxgrid').jqxGrid('getrowdata', row);
									var requestrecord = {};
									requestrecord.id = datarecord.id;
									requestrecord[column] =newvalue;
									var returnedData = updateRow(requestrecord,dataSourceTableName,dataSourceFields);
									if(returnedData.err!=undefined){formState.editRowInProgress=undefined;showError(returnedData.err);
																	$("#jqxgrid").jqxGrid('setcellvalue', row, fieldsettings[column].name, oldvalue);return;}
									if(returnedData.commit){
									for(var j in dataSourceFields){
										if(returnedData[dataSourceFields[j].name]!=datarecord[dataSourceFields[j].name]){
												switch (dataSourceFields[j].type){
													case 'date':
																var d = datarecord[dataSourceFields[j].name];
																	if((d!=null)&&(getMysqlDateString(d)!=returnedData[dataSourceFields[j].name])||(d==null)){
																	$("#jqxgrid").jqxGrid('setcellvalue', row, dataSourceFields[j].name, returnedData[dataSourceFields[j].name]);
																	}
																break;
													case 'bool':var b=datarecord[dataSourceFields[j].name]?1:0;
																	if(b!=returnedData[dataSourceFields[j].name]){
																		$("#jqxgrid").jqxGrid('setcellvalue', row, dataSourceFields[j].name, returnedData[dataSourceFields[j].name]);
																	}
																break;
													default:$("#jqxgrid").jqxGrid('setcellvalue', row, dataSourceFields[j].name, returnedData[dataSourceFields[j].name]);
															break;
												}
										};
										if(j==(fieldcount-1)){	
											var timeoutId= setTimeout(function(){formState.editRowInProgress=undefined; clearTimeout(timeoutId);},100);	 
										}
									}
									clearTimeout(timeoutIdx);
									
									}
									
								 }
								},timeoutIdxValue); 
				}
				});
				if(fields[k].filtertype!=undefined){
					$.extend(column,{filtertype:fields[k].filtertype});
				}
				
				
				customizeColumnControl(column, fields[k]);
				placeControl(fields[k]);
				$.extend(column, fields[k].gridColumnProperties);
				gridColumns.push(column);  
				setFieldsOnchange(fields[k]);
			};
		
		function customizeColumnControl(c, f, s=''){
			switch(f.type){
					case 'number':
							$("#"+s+f.name).jqxNumberInput({width: f.width+'px', height:((f.height==undefined)?'20px':f.height), disabled:((f.editable==undefined)?true:!f.editable),inputMode: 'simple', spinButtons: false, decimalDigits: 0  });
							if(f.input!=undefined){$("#"+s+f.name).jqxNumberInput(f.input);}
							if(f.name=='id'){$("#"+s+f.name).jqxNumberInput({disabled:true})};
							break;
					case 'string':
							if(f.textarea!=undefined){
								if((f.editor!=undefined)&&f.editor){
									var e = f.textarea;
									if(f.input!=undefined){e=$.extend(e,f.input);}
									$("#"+s+f.name).jqxEditor(e);
									break;
								}else{
								$("#"+s+f.name).jqxTextArea(f.textarea);}
								break;
							}
							$("#"+s+f.name).jqxInput({width: f.width+'px', height:((f.height==undefined)?'20px':f.height), disabled:((f.editable==undefined)?true:!f.editable) });
							if(f.input!=undefined){$("#"+s+f.name).jqxInput(f.input);}
							if(f.name=='id'){$("#"+s+f.name).jqxInput({disabled:true})};
							
							if((f.mask !== undefined) && (f.mask !== null)){
									$.extend(c,{text:f.label, datafield:f.name, width:f.width, editable:false});  
									$("#"+s+f.name).jqxMaskedInput({  width: f.width+'px', height: ((f.height==undefined)?'20px':f.height), mask: f.mask, disabled:((f.editable==undefined)?true:!f.editable)});
									if(f.input!=undefined){$("#"+f.name).jqxMaskedInput(f.input);}
								};
						break;
					case 'bool':
						$("#"+s+f.name).jqxCheckBox({  width: f.width+'px', height: ((f.height==undefined)?'20px':f.height), disabled:((f.editable==undefined)?true:!f.editable) });
						if(f.input!=undefined){$("#"+s+f.name).jqxCheckBox(f.input);}
						$.extend(c,{text:f.label, datafield:f.name,filtertype: 'bool', columntype:'checkbox', width:f.width}); 
						break;
					case 'date':
						$.extend(c,{text:f.label, datafield:f.name, width:f.width, columntype:'datetimeinput', cellsformat:f.fieldformat});
						$("#"+s+f.name).jqxDateTimeInput({ formatString: f.fieldformat, culture: us.culture, theme: theme, disabled:((f.editable==undefined)?true:!f.editable) });
						if(f.input!=undefined){$("#"+s+f.name).jqxDateTimeInput(f.input);}
						break;
				};
		}
		
		function setFieldsOnchange(field){
				
				$("#"+field.name).on('change', 
							function (event) {
								if($('#'+'jqxTabs').jqxTabs('selectedItem')==1){
									updateTabField(field);
								};	
						});  
				};			
			
			$('#jqxButtonTab').jqxTabs({ width: $(document).width()*0.13, height: $(document).height()*0.95,theme: theme});
			$('#jqxTabs').jqxTabs({ width: $(document).width()*0.8, height: $(document).height()*0.95,theme: theme, reorder: true, position: 'top'});
			
			 $('#jqxTabs').on('selected', function (event) {
				formState.fromtab=formState.tabclicked; 
				formState.tabclicked = event.args.item;
					 if((formState.tabclicked == 0)&&(formState.fromtab==1))
					{	
						formState.editRowInProgress=-1;
						updateGridFieldsFromTab();
						var timeoutId = setTimeout(function(){formState.editRowInProgress=undefined; clearTimeout(timeoutId);},100);
						
					};
					
					
					 if((formState.tabclicked >1)&&(formState.fromtab!=formState.tabclicked)){
						$('#jqxTabs'+subData[formState.tabclicked-2].name).jqxTabs({ width: $(document).width()*0.795, height: $(document).height()*0.91,theme: theme, reorder: true , position: 'top' });	
						$('#jqxTabs'+subData[formState.tabclicked-2].name).jqxTabs('select',0);
						$('#'+subData[formState.tabclicked-2].name+'jqxgrid').jqxGrid('updatebounddata','cells');
						$('#'+subData[formState.tabclicked-2].name+'jqxgrid').jqxGrid('clearselection');
						$('#'+subData[formState.tabclicked-2].name+'jqxgrid').jqxGrid('selectrow',-1);
					 }
				});
		
		for(var k in m.calcFilters){
				var f=m.calcFilters[k];
				$('#CalcFiltersSet').append('<div style="margin-top:6px" id="calcFilterLabel'+f.name+'">'+f.label+'<div>');
				switch(f.type){
					case 'number':
					case 'string':$('#CalcFiltersSet').append('<input style="margin-top:3px" id="calcFilter'+f.name+'"/>');
					default:$('#CalcFiltersSet').append('<div style="margin-top:3px" id="calcFilter'+f.name+'"><div>');
					}
				
				
				switch(f.type){
					case 'number':
					case 'string':
							$("#calcFilter"+f.name).jqxInput({width: f.width+'px', height:((f.height==undefined)?'20px':f.height) });
							if(f.input!=undefined){$("#calcFilter"+f.name).jqxInput(f.input);}
												
							if((f.mask !== undefined) && (f.mask !== null)){
									$("#calcFilter"+f.name).jqxMaskedInput({  width: f.width+'px', height: ((f.height==undefined)?'20px':f.height), mask: f.mask});
									if(f.input!=undefined){$("#calcFilter"+f.name).jqxMaskedInput(f.input);}
								};
								
						break;
					case 'bool':
						$("#calcFilter"+f.name).jqxCheckBox({  width: f.width+'px', height: ((f.height==undefined)?'20px':f.height) });
						if(f.input!=undefined){$("#"+f.name).jqxCheckBox(f.input);}
						 
						break;
					case 'date':
						
						$("#calcFilter"+f.name).jqxDateTimeInput({ formatString: f.fieldformat, culture: us.culture, theme: theme });
						if(f.input!=undefined){$("#calcFilter"+s+f.name).jqxDateTimeInput(f.input);}
						break;
				};
				
				
				
				function setCalcFilterOnChange(fc){
					
							$("#calcFilter"+fc.name).on('change', function(){
								if(fc.type=='date')
								{
									formState.calcFilters[fc.name] = replaceDateByFormat( $("#calcFilter"+fc.name).val(), fc.fieldformat);
								}
								else
								{	
									formState.calcFilters[fc.name] = $("#calcFilter"+fc.name).val();
								}
							});
						
					}
				setCalcFilterOnChange(f);
				
				
				if(f.defaultvalue!=undefined){
								$("#calcFilter"+f.name).val(f.defaultvalue);
								$("#calcFilter"+f.name).change();
				}
					
				}
		
		
		 var source = getDataSource(dataSourceFields);
		 source = addFilterSortOptions(source);
		
		var dataadapter = new $.jqx.dataAdapter(source, {
			async:false,
			loadError: function(xhr, status, error)
			{
				showError(error);
			},
			formatData: function (data) {
				
				$.extend(data,{table:dataSourceTableName});
				if(m.defaultSorting!=undefined){
					
						$.extend(data,{defaultSortingFields:m.defaultSorting.fields, defaultSortingOrder:(m.defaultSorting.order==undefined)?'ASC':m.defaultSorting.order});
				};
				var fieldset=[];
				 for(var k in fields){
					fieldset.push(fields[k].name);
				}
				
				data = replaceFilterDates(data, fieldsettings);
				var addfiltercount=0;
				for(var i in formFilters){
						var filterobj={};
						filterobj["addfilterdatafield"+addfiltercount] = formFilters[i][0];
						filterobj["addfiltercondition"+addfiltercount] = formFilters[i][1];
						filterobj["addfiltervalue"+addfiltercount] = formFilters[i][2];
						$.extend(data,filterobj); 
						addfiltercount++;					
				}
				
				$.extend(data,{fieldset:fieldset.join(',')});
				if(addfiltercount>0){
					$.extend(data,{addfiltercount:addfiltercount});
				}  
				
				var calcfieldscount=0;
				for(var i in fields){
					if(fields[i].calcField==undefined) {continue;}
					var calcfieldobj = {};
					calcfieldobj["calcfieldname"+calcfieldscount]=fields[i].name;
					calcfieldobj["calcfieldproperties"+calcfieldscount]=JSON.stringify(fields[i].calcField());
					$.extend(data,calcfieldobj);
					calcfieldscount++;	
				}
				if(calcfieldscount>0){
					$.extend(data,{calcfieldscount:calcfieldscount});
				} 	
				
				
				
					
				if(data.pagenum!=undefined){formState.savedFilerSorting = data; return data;}
				else {return formState.savedFilerSorting;}
				
			}
		}
		);
		
		$("#jqxgrid").jqxGrid(
		$.extend({		
			source: dataadapter,
			theme: theme,
			width: $(document).width()*0.8,
			filterable: true,
			sortable: true,
			editable: true,
			enablebrowserselection: false,
			columnsreorder: true,
			editmode: 'dblclick',
			height: $(document).height()*0.9,
			pageable: true,
			pagesize: 25,
			pagesizeoptions: ['15', '25','50'],
			virtualmode: true,
			localization: getLocalization(localization),
			showfilterrow: true,
			rendergridrows: function(obj)
			{
				return obj.data; 
			},
			columns: gridColumns,
			selectedrowindex: 0,
			selectionmode:'multiplerowsextended',
			toolbarheight: 35,
			columnsresize: true,
			showtoolbar: true,
			rendertoolbar: function (toolBar) {
                     var toTheme = function (className) {
                        if (theme == "") return className;
                        return className + " " + className + "-" + theme;
                    };
                   
					var buttonTemplate = "<div style='float: left; padding: 3px; margin: 2px;'><div style='margin: 4px; width: 16px; height: 16px;'></div></div>";
					var buttonTemplateRight = "<div style='float: right; padding: 3px; margin: 2px;'><div style='margin: 4px; width: 16px; height: 16px;'></div></div>";
					var container = $("<div style='overflow: hidden; position: relative; height: 100%; width: 100%;'></div>");
					var addButton = $(buttonTemplate);
					var deleteButton = $(buttonTemplate);
					var reloadButton = $(buttonTemplate);
					var hideButton = $(buttonTemplateRight);
					container.append(addButton);
                    container.append(deleteButton);
                    container.append(reloadButton);
					container.append(hideButton);
                    toolBar.append(container);
                    addButton.jqxButton({cursor: "pointer", enableDefault: false,  height: 25, width: 25 });
                    addButton.find('div:first').addClass(toTheme('jqx-icon-plus'));
                    addButton.jqxTooltip({ position: 'bottom', content: localization_m.add});
                    deleteButton.jqxButton({ cursor: "pointer", enableDefault: false,  height: 25, width: 25 });
                    deleteButton.find('div:first').addClass(toTheme('jqx-icon-delete'));
                    deleteButton.jqxTooltip({ position: 'bottom', content: localization_m.delete});
					reloadButton.jqxButton({ cursor: "pointer", enableDefault: false,  height: 25, width: 25 });
                    reloadButton.find('div:first').addClass('jqx-icon-reload-'+theme);
                    reloadButton.jqxTooltip({ position: 'bottom', content: localization_m.reload});                    
                    hideButton.jqxButton({ cursor: "pointer", enableDefault: false,  height: 25, width: 25 });
                    hideButton.find('div:first').addClass('jqx-icon-hide-'+theme);
                    hideButton.jqxTooltip({ position: 'bottom', content: localization_m.hide});                    
                    
                    
					if(m.panelButtons!=undefined){
						if(m.panelButtons.addButton!=undefined){
							addButton.jqxButton(m.panelButtons.addButton);
						} else{
							addButton.jqxButton({ disabled: false });
						}
						if(m.panelButtons.deleteButton!=undefined){
							deleteButton.jqxButton(m.panelButtons.deleteButton);
						} else{
						deleteButton.jqxButton({ disabled: false });
						}
					}
					if(!deleteButton.jqxButton('disabled')){
						deleteButton.click(function () {
							if(formState.getSelectedRowsIndexes()==''){
								showWarning('Choose Row(s) to delete');
								return;
							}
							if(confirm('Delete row(s) '+formState.getSelectedRowsIndexes()+'?')){
								var response='';
								response=rcf('utils', 'deleterow', [dataSourceTableName, formState.getSelectedRowsIndexes()]);
								if(response!=''){showError(response);}
								else{
									$("#jqxgrid").jqxGrid('updatebounddata', 'data');
									formState.selectedrows={};
								}
							}
							
						});
					}
					if(!addButton.jqxButton('disabled')){
						addButton.click(function () {
							var dVals;
							try{
								dVals = formTriggers.beforeInsert();
							}catch(e){
								showError(e.toString());
								return;
							}
							try{
								dVals = JSON.stringify(dVals);
								}catch(e){dVals='';}
							var response='';
							response=rcf('utils', 'addrow', [dataSourceTableName, dVals]);
							if(response!=''){showError(response);}
							else{
								dataadapter.dataBind();
								$("#jqxgrid").jqxGrid('updatebounddata','cells');
							}
						});
					}
					reloadButton.click(function(){
						$("#jqxgrid").jqxGrid('updatebounddata','data');
					});
					
					
					hideButton.click(function(){hideshowcolumns('jqxgrid',theme,us);});
				
					
		}
		}, m.gridProperties));
		
		$("#jqxgrid").on('rowunselect', function (event) {
					var args = event.args;
					formState.selectedrows["row_"+args.rowindex]=undefined;
		});
		$("#jqxgrid").on('rowselect', function (event) {
					var args = event.args;
					formState.rowdata=args.row;
					formState.rowindex=args.rowindex;
					formState.selectedrows['row_'+args.rowindex]=args.row;
					clearUnselectedRows();
					fillTabFields(fields);
					
		});
		
		function clearUnselectedRows(){
			var selectedrows = $('#jqxgrid').jqxGrid('getselectedrowindexes');
			if(selectedrows.length>0){
					var tmp={};
					for(var i in selectedrows){
						tmp['row_'+selectedrows[i]]=formState.selectedrows['row_'+selectedrows[i]];
					}
					formState.selectedrows={};
					formState.selectedrows=tmp;
					
			}		
		}
		
		 $("#jqxgrid").on('cellbeginedit', function (event) {
			var args = event.args;
			formState.editrowdata=args.row;
			formState.editrowindex=args.rowindex;
			formState.editdatafield=args.datafield;
		});
		
		$("#jqxgrid").on('cellclick', function (event) {
			
			var args = event.args;
			if(fieldsettings[args.datafield].type=='bool'){
				formState.editrowdata=$('#jqxgrid').jqxGrid('getrowdata', args.rowindex);
				formState.editrowindex=args.rowindex;
				formState.editdatafield=args.datafield;
			}	
		});
	
		
		

		for(var k in fields){
				 if(fields[k].dropdowngrid!=undefined){
					 createDropdownGrid(fields[k].name,fields[k].dropdowngrid, formState, us);
				 };
				  if(fields[k].popupgrid!=undefined){
					 createPopupGrid(fields[k].name,fields[k].popupgrid, formState, us);
				 };
		};
	
		
			
			$("#jqxgrid").jqxGrid('selectrow', 0);
			formState.rowdata=$("#jqxgrid").jqxGrid('getrowdata', 0);
			formState.editrowdata=formState.rowdata;
			fillTabFields(fields);
			
			loadformstate();
			
			function loadformstate(){
				var state = rcf('utils','loadformstate',[formname]);
				if(state!=''){
					state = JSON.parse(state);
					state.width = $(document).width()*0.8;
					state.height = $(document).height()*0.9;
					$("#jqxgrid").jqxGrid('loadstate', state);
				}
			}
			
			var mapButtons=[];
			for(var k in formButtons){
				$("#ButtonSet").append('<input style="margin-top:3px" type="button" id="'+formButtons[k].name+'" />');
				$("#"+formButtons[k].name).jqxButton({ width: $(document).width()*0.09, height: 30, theme:theme, template: 'inverse', value:formButtons[k].label});
				if(formButtons[k].buttonProperties!=undefined){$("#"+formButtons[k].name).jqxButton(formButtons[k].buttonProperties);}
				mapButtons[formButtons[k].name] = formButtons[k];
				bindOnClick(formButtons[k].name);
			}
			
			function bindOnClick(buttonName){
				$("#"+buttonName).on('click', function(){ mapButtons[buttonName].onClick();});
			}
			
			
			
		
		var subs=[];
		for(var s in subData){
			createsubForm(subData[s]);
		}	
		function createsubForm(sub){
			var tab1 = document.getElementById(sub.name+'tab1');
			tab1.innerHTML = createTabControls(sub.fields, theme, sub.name);
			
			function fillTabFields(fields){
				if(subs[sub.name].formState.rowdata==undefined)return;
				subs[sub.name].formState.editrowdata = subs[sub.name].formState.rowdata;
                    for( k in fields) { 
					 				if($("#"+sub.name+fields[k].name).val()!=subs[sub.name].formState.editrowdata[fields[k].name]){
										
										$("#"+sub.name+fields[k].name).val(subs[sub.name].formState.editrowdata[fields[k].name]);
										
									}	
									if(fields[k].columntype=='maskedinput'&&subs[sub.name].formState.editrowdata[fields[k].name]==null){
										$("#"+sub.name+fields[k].name).jqxMaskedInput('clearValue');
									}
					};
			};	
			
			function updatesubGridFieldsFromTab(){
				var editrow = $('#'+sub.name+'jqxgrid').jqxGrid('getselectedrowindex');
				for(var j in subs[sub.name].dataSourceFields){
						$('#'+sub.name+'jqxgrid').jqxGrid('setcellvalue', editrow, subs[sub.name].dataSourceFields[j].name, subs[sub.name].formState.editrowdata[subs[sub.name].dataSourceFields[j].name]);
				}	
			}
			
						
			function updatesubTabField(f){
				
				var flagChanged = false;
				var saveValue=subs[sub.name].formState.editrowdata[f.name];
				switch(f.type){
						case 'date':if(subs[sub.name].formState.editrowdata[f.name]!=$("#"+sub.name+f.name).val('date')){
										subs[sub.name].formState.editrowdata[f.name]=$("#"+sub.name+f.name).val('date');
										flagChanged=true;
									}
									break;
						case 'bool':if(subs[sub.name].formState.editrowdata[f.name]!=$("#"+sub.name+f.name).jqxCheckBox('val')){	
										subs[sub.name].formState.editrowdata[f.name]=$("#"+sub.name+f.name).jqxCheckBox('val');
										flagChanged=true;
									}	
									break;
						default:if(subs[sub.name].formState.editrowdata[f.name]!=$("#"+sub.name+f.name).val()){
									subs[sub.name].formState.editrowdata[f.name]=$("#"+sub.name+f.name).val();
									flagChanged=true;
									
								}	
								break;
					 };
				
					 if(flagChanged&&(subs[sub.name].formState.editRowInProgress==undefined)){
						
						subs[sub.name].formState.editRowInProgress=subs[sub.name].formState.editrowindex;
						var datarecord = subs[sub.name].formState.editrowdata;
						var requestrecord={};
						requestrecord['id']=datarecord['id'];
						requestrecord[f.name]=datarecord[f.name];
						var returnedData = updateRow(requestrecord,sub.dataSourceTableName,subs[sub.name].dataSourceFields, sub);
						if(returnedData.err!=undefined){subs[sub.name].formState.editRowInProgress=undefined;subs[sub.name].formState.editrowdata[f.name]=saveValue;$("#"+sub.name+f.name).val(saveValue); showError(returnedData.err); return;}
						if(returnedData.commit){
										for(var j in subs[sub.name].dataSourceFields){
											if(returnedData[subs[sub.name].dataSourceFields[j].name]!=datarecord[subs[sub.name].dataSourceFields[j].name]){
													switch (subs[sub.name].dataSourceFields[j].type){
														case 'date':
																	var d = datarecord[subs[sub.name].dataSourceFields[j].name];
																		if((d!=null)&&(getMysqlDateString(d)!=returnedData[subs[sub.name].dataSourceFields[j].name])||(d==null)){
																		$("#"+sub.name+subs[sub.name].dataSourceFields[j].name).val(returnedData[subs[sub.name].dataSourceFields[j].name]);
																		subs[sub.name].formState.editrowdata[subs[sub.name].dataSourceFields[j].name]=$("#"+sub.name+subs[sub.name].dataSourceFields[j].name).val('date');
																		}
																	break;
														case 'bool':var b=datarecord[subs[sub.name].dataSourceFields[j].name]?1:0;
																		if(b!=returnedData[subs[sub.name].dataSourceFields[j].name]){
																			$("#"+sub.name+subs[sub.name].dataSourceFields[j].name).val(returnedData[subs[sub.name].dataSourceFields[j].name]);
																			subs[sub.name].formState.editrowdata[subs[sub.name].dataSourceFields[j].name]=$("#"+sub.name+subs[sub.name].dataSourceFields[j].name).val();
																			
																		}
																	break;
														default:$("#"+sub.name+subs[sub.name].dataSourceFields[j].name).val(returnedData[subs[sub.name].dataSourceFields[j].name]);
																subs[sub.name].formState.editrowdata[subs[sub.name].dataSourceFields[j].name]=$("#"+sub.name+subs[sub.name].dataSourceFields[j].name).val();
																break;
													}
											};
										}
										var timeoutId = setTimeout(function(){subs[sub.name].formState.editRowInProgress=undefined; clearTimeout(timeoutId);},20);
						}
						
					 }				
			}; 
			
			
			var column;
			subs[sub.name]={};
			subs[sub.name].fieldsettings = [];
			subs[sub.name].dataSourceFields=[];
			subs[sub.name].gridColumns=[]; 
			subs[sub.name].formState= new State();
			subs[sub.name].gridWidth=0;
			subs[sub.name].fieldcount=0;
			sub.formTriggers = $.extend(new initTriggers(),sub.formTriggers);
			for(var k in sub.fields) {
				subs[sub.name].fieldcount+=1;
				subs[sub.name].fieldsettings[sub.fields[k].name]=sub.fields[k];
				subs[sub.name].gridWidth+=sub.fields[k].width;
				column = {};
				column = {name:sub.fields[k].name, type:sub.fields[k].type};
				subs[sub.name].dataSourceFields.push(column);
				column = {};
				column = {text:sub.fields[k].label, datafield:sub.fields[k].name, width:sub.fields[k].width};
				if(sub.fields[k].displayfield!=undefined){$.extend(column,{displayfield:sub.fields[k].displayfield});}
				if(sub.fields[k].hidden!=undefined){$.extend(column,{hidden:sub.fields[k].hidden});}
				$.extend(column,{editable:(sub.fields[k].editable!=undefined?sub.fields[k].editable:false)});
				$.extend(column, {cellvaluechanging: function (row, column, columntype, oldvalue, newvalue) {
								
						 		 var timeoutIdxValue=12;
								if(columntype=='checkbox'){timeoutIdxValue=20;}
								if(columntype!='checkbox'&&oldvalue==newvalue) return oldvalue;
								if((columntype=='checkbox')&&(newvalue?oldvalue:!oldvalue)){return oldvalue;}
								var timeoutIdx= setTimeout(function(){
								if(subs[sub.name].formState.editRowInProgress==undefined){
									subs[sub.name].formState.editRowInProgress=row;
									
									var datarecord = $('#'+sub.name+'jqxgrid').jqxGrid('getrowdata', row);
									var requestrecord={}; 
									requestrecord.id = datarecord.id;
									requestrecord[column] =newvalue;
									var returnedData = updateRow(requestrecord,sub.dataSourceTableName,subs[sub.name].dataSourceFields, sub);
									if(returnedData.err!=undefined){subs[sub.name].formState.editRowInProgress=undefined;showError(returnedData.err);
																	$("#"+sub.name+"jqxgrid").jqxGrid('setcellvalue', row, subs[sub.name].fieldsettings[column].name, oldvalue);return;}
									if(returnedData.commit){
									for(var j in subs[sub.name].dataSourceFields){
										if(returnedData[subs[sub.name].dataSourceFields[j].name]!=datarecord[subs[sub.name].dataSourceFields[j].name]){
												switch (subs[sub.name].dataSourceFields[j].type){
													case 'date':
																var d = datarecord[subs[sub.name].dataSourceFields[j].name];
																	if((d!=null)&&(getMysqlDateString(d)!=returnedData[subs[sub.name].dataSourceFields[j].name])||(d==null)){
																	$("#"+sub.name+"jqxgrid").jqxGrid('setcellvalue', row, subs[sub.name].dataSourceFields[j].name, returnedData[subs[sub.name].dataSourceFields[j].name]);
																	}
																break;
													case 'bool':var b=datarecord[subs[sub.name].dataSourceFields[j].name]?1:0;
																	if(b!=returnedData[subs[sub.name].dataSourceFields[j].name]){
																		$("#"+sub.name+"jqxgrid").jqxGrid('setcellvalue', row, subs[sub.name].dataSourceFields[j].name, returnedData[subs[sub.name].dataSourceFields[j].name]);
																	}
																break;
													default:$("#"+sub.name+"jqxgrid").jqxGrid('setcellvalue', row, subs[sub.name].dataSourceFields[j].name, returnedData[subs[sub.name].dataSourceFields[j].name]);
															break;
												}
										};
										if(j==(subs[sub.name].fieldcount-1)){	
											var timeoutId= setTimeout(function(){subs[sub.name].formState.editRowInProgress=undefined; clearTimeout(timeoutId);},100);	 
										}
									}
									clearTimeout(timeoutIdx);
									
									}
									
								 }
								},timeoutIdxValue);  
				} 
				});
				
				if(sub.fields[k].filtertype!=undefined){
					$.extend(column,{filtertype:sub.fields[k].filtertype});
				}
				
				customizeColumnControl(column, sub.fields[k], sub.name);
				placeControl(sub.fields[k], sub.name);
				$.extend(column, sub.fields[k].gridColumnProperties);
				subs[sub.name].gridColumns.push(column);   
				setsubFieldsOnchange(sub.fields[k]);
			}
			
			
			function setsubFieldsOnchange(field){
				$("#"+sub.name+field.name).on('change', 
							function (event) { 
								if($('#'+'jqxTabs'+sub.name).jqxTabs('selectedItem')==1){
									updatesubTabField(field);
								};	
						});  
				};	
			
			$('#jqxTabs'+sub.name).on('selected', function (event) {
				subs[sub.name].formState.fromtab=subs[sub.name].formState.tabclicked; 
				subs[sub.name].formState.tabclicked = event.args.item;
					 if((subs[sub.name].formState.tabclicked == 0)&&(subs[sub.name].formState.fromtab==1))
					{	
						subs[sub.name].formState.editRowInProgress=-1;
						updatesubGridFieldsFromTab();
						var timeoutId = setTimeout(function(){subs[sub.name].formState.editRowInProgress=undefined; clearTimeout(timeoutId);},100);
						
					};
					if((subs[sub.name].formState.tabclicked == 1)&&(subs[sub.name].formState.fromtab==0)){
						subs[sub.name].formState.editRowInProgress=-1;
						fillTabFields(sub.fields);
						var timeoutId = setTimeout(function(){subs[sub.name].formState.editRowInProgress=undefined; clearTimeout(timeoutId);},100);
					};  
					 
				});
			
			
			subs[sub.name].source = getDataSource(subs[sub.name].dataSourceFields);
			subs[sub.name].source = addFilterSortOptions(subs[sub.name].source, sub.name+'jqxgrid');
		 
			subs[sub.name].dataadapter = new $.jqx.dataAdapter(subs[sub.name].source, {
			async:false,
			loadError: function(xhr, status, error)
			{
				showError(status+' '+error);
			},
			formatData: function (data) {
				
				$.extend(data,{table:sub.dataSourceTableName});
				
				subs[sub.name].fieldset=[];
				 for(var k in sub.fields){
					subs[sub.name].fieldset.push(sub.fields[k].name);
				}
				
				data = replaceFilterDates(data, subs[sub.name].fieldsettings);
				var addfiltercount=0;
				var combinedFilters=[];
				var tmp_filter=sub.formFilters;
				for(var i in tmp_filter){
					combinedFilters.push(tmp_filter[i]);
				}	
				var tmp_filter=sub.addFilters();
				for(var i in tmp_filter){
					combinedFilters.push(tmp_filter[i]);
				}
				
				for(var i in combinedFilters){
						var filterobj={};
						filterobj["addfilterdatafield"+addfiltercount] =combinedFilters[i][0];
						filterobj["addfiltercondition"+addfiltercount] = combinedFilters[i][1];
						filterobj["addfiltervalue"+addfiltercount] = combinedFilters[i][2];
						$.extend(data,filterobj); 
						addfiltercount+=1;				
					
				}
				
				$.extend(data,{fieldset:subs[sub.name].fieldset.join(',')});
				if(addfiltercount>0){
					$.extend(data,{addfiltercount:addfiltercount});
				}  
						
				if(data.pagenum!=undefined){subs[sub.name].formState.savedFilerSorting = data; return data;}
				else {return subs[sub.name].formState.savedFilerSorting;}
				
			}
		}
		);
		
		$("#"+sub.name+"jqxgrid").jqxGrid(
		$.extend({		
			source: subs[sub.name].dataadapter,
			theme: theme,
			width: $(document).width()*0.79,
			filterable: true,
			sortable: true,
			editable: true,
			enablebrowserselection: false,
			columnsreorder: true,
			editmode: 'dblclick',
			height: $(document).height()*0.85,
			pageable: true,
			pagesize: 25,
			pagesizeoptions: ['15', '25','50'],
			virtualmode: true,
			showfilterrow: true,
			localization: getLocalization(localization),
			rendergridrows: function(obj)
			{
				return obj.data; 
			},
			columns: subs[sub.name].gridColumns,
			selectedrowindex: 0,
			selectionmode:'multiplerowsextended',
			toolbarheight: 35,
			columnsresize: true,
			showtoolbar: true,
			rendertoolbar: function (toolBar) {
                     var toTheme = function (className) {
                        if (theme == "") return className;
                        return className + " " + className + "-" + theme;
                    };
                   
					var buttonTemplate = "<div style='float: left; padding: 3px; margin: 2px;'><div style='margin: 4px; width: 16px; height: 16px;'></div></div>";
                    var container = $("<div style='overflow: hidden; position: relative; height: 100%; width: 100%;'></div>");
					var addButton = $(buttonTemplate);
					var deleteButton = $(buttonTemplate);
				
                    container.append(addButton);
                    container.append(deleteButton);
                    
                    toolBar.append(container);
                    addButton.jqxButton({cursor: "pointer", height: 25, width: 25, theme:theme });
                    addButton.find('div:first').addClass(toTheme('jqx-icon-plus'));
                    addButton.jqxTooltip({ position: 'bottom', content: localization_m.add});
                    deleteButton.jqxButton({ cursor: "pointer", height: 25, width: 25, theme:theme });
                    deleteButton.find('div:first').addClass(toTheme('jqx-icon-delete'));
                    deleteButton.jqxTooltip({ position: 'bottom', content: localization_m.delete});
                    
					if(sub.panelButtons!=undefined){
						if(sub.panelButtons.addButton!=undefined){
							addButton.jqxButton(sub.panelButtons.addButton);
						}else{
							addButton.jqxButton({ disabled: false });
						}	
						if(sub.panelButtons.deleteButton!=undefined){
							deleteButton.jqxButton(sub.panelButtons.deleteButton);
						}else{
							deleteButton.jqxButton({ disabled: false });
						}
					}
					
					if(!deleteButton.jqxButton('disabled')){
						deleteButton.click(function () {
							if(subs[sub.name].formState.getSelectedRowsIndexes()==''){
								showWarning('Choose Row(s) to delete');
								return;
							}
							if(confirm('Delete row(s) '+subs[sub.name].formState.getSelectedRowsIndexes()+'?')){
								var response='';
								response=rcf('utils', 'deleterow', [sub.dataSourceTableName, subs[sub.name].formState.getSelectedRowsIndexes()]);
								if(response!=''){showError(response);}
								else{
									$("#"+sub.name+"jqxgrid").jqxGrid('updatebounddata', 'data');
									subs[sub.name].formState.selectedrows={};
								}
							}
						});
					}
					if(!addButton.jqxButton('disabled')){
						addButton.click(function () {
							var dVals;
							try{
								dVals = sub.formTriggers.beforeInsert();
							}catch(e){
								showError(e.toString());
								return;
							}
							try{
								dVals = JSON.stringify(dVals);
								}catch(e){dVals='';}
							var response='';
							response=rcf('utils', 'addrow', [sub.dataSourceTableName, dVals]);
							if(response!=''){showError(response);}
							else{
								subs[sub.name].dataadapter.dataBind();
								//$("#"+sub.name+"jqxgrid").jqxGrid('updatebounddata','cells');
							} 
						});	
					}
					
		}
		}, sub.gridProperties));
		
		$("#"+sub.name+"jqxgrid").on('rowunselect', function (event) {
					var args = event.args;
					subs[sub.name].formState.selectedrows["row_"+args.rowindex]=undefined;
		});
		 $("#"+sub.name+"jqxgrid").on('rowselect', function (event) {
					var args = event.args;
					subs[sub.name].formState.rowdata=args.row;
					subs[sub.name].formState.rowindex=args.rowindex;
					subs[sub.name].formState.selectedrows['row_'+args.rowindex]=args.row;
					clearsubUnselectedRows();
					fillTabFields(sub.fields);
					
		});
		
		function clearsubUnselectedRows(){
			var selectedrows = $('#'+sub.name+'jqxgrid').jqxGrid('getselectedrowindexes');
			if(selectedrows.length>0){
					var tmp={};
					for(var i in selectedrows){
						tmp['row_'+selectedrows[i]]=subs[sub.name].formState.selectedrows['row_'+selectedrows[i]];
					}
					subs[sub.name].formState.selectedrows={};
					subs[sub.name].formState.selectedrows=tmp;
			}		
		}  
		 
		 $("#"+sub.name+"jqxgrid").on('cellbeginedit', function (event) {
			var args = event.args;
			subs[sub.name].formState.editrowdata=args.row;
			subs[sub.name].formState.editrowindex=args.rowindex;
			subs[sub.name].formState.editdatafield=args.datafield;
		});
		
		$("#"+sub.name+"jqxgrid").on('cellclick', function (event) {
			
			var args = event.args;
			if(subs[sub.name].fieldsettings[args.datafield].type=='bool'){
				subs[sub.name].formState.editrowdata=$("#"+sub.name+"jqxgrid").jqxGrid('getrowdata', args.rowindex);
				subs[sub.name].formState.editrowindex=args.rowindex;
				subs[sub.name].formState.editdatafield=args.datafield;
			}	
		});
		 
		for(var k in sub.fields){
				if(sub.fields[k].dropdowngrid!=undefined){
					 createDropdownGrid(sub.fields[k].name,sub.fields[k].dropdowngrid, subs[sub.name].formState, us, sub);
				 };
				if(sub.fields[k].popupgrid!=undefined){
					 createPopupGrid(sub.fields[k].name,sub.fields[k].popupgrid, subs[sub.name].formState, us, sub);
				 }; 
		}; 
		 
		}
	}
