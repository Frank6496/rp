<?php
function run($uri=''){
	if(is_null(security::userName()))die();
	if($uri=='')die();	
	//header("Cache-Control:no-cache, no-store, must-revalidate, max-age=0");
	
?>

<html lang="en">
<head>
<link rel="stylesheet" href="../../scripts/form.css" type="text/css" />
<link rel="stylesheet" href="https://www.jqwidgets.com/jquery-widgets-demo/jqwidgets/styles/jqx.base.css" type="text/css" />
<link rel="stylesheet" href="https://www.jqwidgets.com/jquery-widgets-demo/jqwidgets/styles/jqx.<?php echo $_SESSION['usertheme'];?>.css" type="text/css" />
<link rel="stylesheet" href="https://codemirror.net/lib/codemirror.css">
<link rel="stylesheet" href="https://codemirror.net/theme/liquibyte.css">
<link rel="stylesheet" href="https://codemirror.net/lib/codemirror.css">
<link rel="stylesheet" href="https://codemirror.net/addon/dialog/dialog.css">

<link rel="stylesheet" href="https://codemirror.net/addon/search/matchesonscrollbar.css">
<script type="text/javascript" src="https://codemirror.net/lib/codemirror.js"></script>
<script type="text/javascript" src="https://codemirror.net/addon/edit/matchbrackets.js"></script>
<script type="text/javascript" src="https://codemirror.net/addon/selection/active-line.js"></script>
<script type="text/javascript" src="https://codemirror.net/mode/javascript/javascript.js"></script>
<script type="text/javascript" src="https://codemirror.net/mode/htmlmixed/htmlmixed.js"></script>
<script type="text/javascript" src="https://codemirror.net/mode/clike/clike.js"></script>
<script type="text/javascript" src="https://codemirror.net/mode/php/php.js"></script>
<script type="text/javascript" src="https://codemirror.net/mode/xml/xml.js"></script>
<script type="text/javascript" src="https://codemirror.net/mode/css/css.js"></script>
<script type="text/javascript" src="https://codemirror.net/addon/dialog/dialog.js"></script>
<script type="text/javascript" src="https://codemirror.net/addon/search/searchcursor.js"></script>
<script type="text/javascript" src="https://codemirror.net/addon/search/search.js"></script>
<script type="text/javascript" src="https://codemirror.net/addon/scroll/annotatescrollbar.js"></script>
<script type="text/javascript" src="https://codemirror.net/addon/search/matchesonscrollbar.js"></script>
<script type="text/javascript" src="https://codemirror.net/addon/search/jump-to-line.js"></script>
<script type="text/javascript" src="../../scripts/datasource.js"></script>
<script type="text/javascript" src="https://www.jqwidgets.com/jquery-widgets-demo/scripts/jquery-1.12.4.min.js"></script>
<script type="text/javascript" src="../../scripts/localization.js"></script>
<script type="text/javascript" src="../../scripts/localization_m.js"></script>
<script type="text/javascript" src="https://www.jqwidgets.com/jquery-widgets-demo/jqwidgets/jqxcore.js"></script>
<script type="text/javascript" src="https://www.jqwidgets.com/jquery-widgets-demo/jqwidgets/jqxbuttons.js"></script>
<script type="text/javascript" src="https://www.jqwidgets.com/jquery-widgets-demo/jqwidgets/jqxdropdownbutton.js"></script>
<script type="text/javascript" src="https://www.jqwidgets.com/jquery-widgets-demo/jqwidgets/jqxmenu.js"></script>
<script type="text/javascript" src="https://www.jqwidgets.com/jquery-widgets-demo/jqwidgets/jqxcheckbox.js"></script>
<script type="text/javascript" src="https://www.jqwidgets.com/jquery-widgets-demo/jqwidgets/jqxdropdownlist.js"></script>
<script type="text/javascript" src="https://www.jqwidgets.com/jquery-widgets-demo/jqwidgets/jqxcalendar.js"></script>
<script type="text/javascript" src="https://www.jqwidgets.com/jquery-widgets-demo/jqwidgets/jqxgrid.js"></script>
<script type="text/javascript" src="https://www.jqwidgets.com/jquery-widgets-demo/jqwidgets/jqxgrid.edit.js"></script> 
<script type="text/javascript" src="https://www.jqwidgets.com/jquery-widgets-demo/jqwidgets/jqxgrid.aggregates.js"></script>
<script type="text/javascript" src="https://www.jqwidgets.com/jquery-widgets-demo/jqwidgets/jqxgrid.columnsreorder.js"></script>
<script type="text/javascript" src="https://www.jqwidgets.com/jquery-widgets-demo/jqwidgets/jqxgrid.columnsresize.js"></script>
<script type="text/javascript" src="https://www.jqwidgets.com/jquery-widgets-demo/jqwidgets/jqxgrid.pager.js"></script>
<script type="text/javascript" src="https://www.jqwidgets.com/jquery-widgets-demo/jqwidgets/jqxgrid.storage.js"></script>
<script type="text/javascript" src="https://www.jqwidgets.com/jquery-widgets-demo/jqwidgets/jqxgrid.selection.js"></script> 
<script type="text/javascript" src="https://www.jqwidgets.com/jquery-widgets-demo/jqwidgets/jqxgrid.filter.js"></script>
<script type="text/javascript" src="https://www.jqwidgets.com/jquery-widgets-demo/jqwidgets/jqxgrid.sort.js"></script>
<script type="text/javascript" src="https://www.jqwidgets.com/jquery-widgets-demo/jqwidgets/jqxgrid.columnsresize.js"></script>
<script type="text/javascript" src="https://www.jqwidgets.com/jquery-widgets-demo/jqwidgets/jqxgrid.grouping.js"></script>
<script type="text/javascript" src="https://www.jqwidgets.com/jquery-widgets-demo/jqwidgets/jqxdata.js"></script>
<script type="text/javascript" src="https://www.jqwidgets.com/jquery-widgets-demo/jqwidgets/jqxtooltip.js"></script>
<script type="text/javascript" src="https://www.jqwidgets.com/jquery-widgets-demo/jqwidgets/globalization/globalize.js"></script>
<script type="text/javascript" src="https://www.jqwidgets.com/jquery-widgets-demo/jqwidgets/globalization/globalize.culture.<?php echo $_SESSION['i18n_lang'];?>.js"></script>
<script type="text/javascript" src="https://www.jqwidgets.com/jquery-widgets-demo/jqwidgets/jqxscrollbar.js"></script>
<script type="text/javascript" src="https://www.jqwidgets.com/jquery-widgets-demo/jqwidgets/jqxwindow.js"></script>
<script type="text/javascript" src="https://www.jqwidgets.com/jquery-widgets-demo/jqwidgets/jqxinput.js"></script>
<script type="text/javascript" src="https://www.jqwidgets.com/jquery-widgets-demo/jqwidgets/jqxnumberinput.js"></script>
<script type="text/javascript" src="https://www.jqwidgets.com/jquery-widgets-demo/jqwidgets/jqxpanel.js"></script>
<script type="text/javascript" src="https://www.jqwidgets.com/jquery-widgets-demo/jqwidgets/jqxtabs.js"></script>
<script type="text/javascript" src="https://www.jqwidgets.com/jquery-widgets-demo/jqwidgets/jqxdate.js"></script>    
<script type="text/javascript" src="https://www.jqwidgets.com/jquery-widgets-demo/jqwidgets/jqxdatetimeinput.js"></script>   
<script type="text/javascript" src="https://www.jqwidgets.com/jquery-widgets-demo/jqwidgets/jqxlistbox.js"></script>
<script type="text/javascript" src="https://www.jqwidgets.com/jquery-widgets-demo/jqwidgets/jqxcombobox.js"></script>
<script type="text/javascript" src="https://www.jqwidgets.com/jquery-widgets-demo/jqwidgets/jqxmaskedinput.js"></script>
<script type="text/javascript" src="https://www.jqwidgets.com/jquery-widgets-demo/jqwidgets/jqxtextarea.js"></script>
<script type="text/javascript" src="https://www.jqwidgets.com/jquery-widgets-demo/jqwidgets/jqxpasswordinput.js"></script>
<script type="text/javascript" src="https://www.jqwidgets.com/jquery-widgets-demo/jqwidgets/jqxnotification.js"></script>
<script type="text/javascript" src="https://www.jqwidgets.com/jquery-widgets-demo/jqwidgets/jqxcolorpicker.js"></script>
<script type="text/javascript" src="https://www.jqwidgets.com/jquery-widgets-demo/jqwidgets/jqxeditor.js"></script>
<script type="text/javascript">
	function saveformstate(){
			var state = $("#jqxgrid").jqxGrid('savestate');
			var formname = <?php echo "'".htmlspecialchars(strip_tags($_GET['id']))."'";?>;
			rcf('utils','saveformstate',[formname,JSON.stringify(state)],true);
			
		}
	$(document).ready(function () {
		
		var formname = <?php echo "'".htmlspecialchars(strip_tags($_GET['id']))."'";?>;
		var userSettings = {theme:'<?php echo $_SESSION['usertheme'];?>', culture:'<?php echo $_SESSION['i18n_lang'];?>',localization:'<?php echo $_SESSION['i18n'];?>',formname:formname};
		var theme = userSettings.theme;
		var runForm = function(maindata, buttons=[], slavedata=[]){
				initForm(maindata, userSettings, buttons, slavedata);
			};
<?php include $uri;?>
});
    </script>
</head>
<body class='default'>
</body>
</html>
<?php } ?>

