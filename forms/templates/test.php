<?php
function run($uri=''){
	if($uri=='')die();	
	//header("Cache-Control:no-cache, no-store, must-revalidate, max-age=0");
	
?>

<html lang="en">
<head>
<link rel="stylesheet" href="../../scripts/form.css" type="text/css" />
<link rel="stylesheet" href="../../jqwidgets/styles/jqx.base.css" type="text/css" />
<link rel="stylesheet" href="../../jqwidgets/styles/jqx.<?php echo $_SESSION['usertheme'];?>.css" type="text/css" />
<script type="text/javascript" src="../../scripts/datasource.js"></script>
<script type="text/javascript" src="../../scripts/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="../../scripts/localization.js"></script>
<script type="text/javascript" src="../../scripts/localization_m.js"></script>
<script type="text/javascript" src="../../jqwidgets/jqxcore.js"></script>
<script type="text/javascript" src="../../jqwidgets/jqxbuttons.js"></script>
<script type="text/javascript" src="../../jqwidgets/jqxdropdownbutton.js"></script>
<script type="text/javascript" src="../../jqwidgets/jqxmenu.js"></script>
<script type="text/javascript" src="../../jqwidgets/jqxcheckbox.js"></script>
<script type="text/javascript" src="../../jqwidgets/jqxdropdownlist.js"></script>
<script type="text/javascript" src="../../jqwidgets/jqxcalendar.js"></script>
<script type="text/javascript" src="../../jqwidgets/jqxgrid.js"></script>
<script type="text/javascript" src="../../jqwidgets/jqxgrid.edit.js"></script> 
<script type="text/javascript" src="../../jqwidgets/jqxgrid.aggregates.js"></script>
<script type="text/javascript" src="../../jqwidgets/jqxgrid.columnsreorder.js"></script>
<script type="text/javascript" src="../../jqwidgets/jqxgrid.columnsresize.js"></script>
<script type="text/javascript" src="../../jqwidgets/jqxgrid.pager.js"></script>
<script type="text/javascript" src="../../jqwidgets/jqxgrid.storage.js"></script>
<script type="text/javascript" src="../../jqwidgets/jqxgrid.selection.js"></script> 
<script type="text/javascript" src="../../jqwidgets/jqxgrid.filter.js"></script>
<script type="text/javascript" src="../../jqwidgets/jqxgrid.sort.js"></script>
<script type="text/javascript" src="../../jqwidgets/jqxgrid.columnsresize.js"></script>
<script type="text/javascript" src="../../jqwidgets/jqxgrid.grouping.js"></script>
<script type="text/javascript" src="../../jqwidgets/jqxdata.js"></script>
<script type="text/javascript" src="../../jqwidgets/jqxtooltip.js"></script>
<script type="text/javascript" src="../../jqwidgets/globalization/globalize.js"></script>
<script type="text/javascript" src="../../jqwidgets/globalization/globalize.culture.<?php echo $_SESSION['i18n_lang'];?>.js"></script>
<script type="text/javascript" src="../../jqwidgets/jqxscrollbar.js"></script>
<script type="text/javascript" src="../../jqwidgets/jqxwindow.js"></script>
<script type="text/javascript" src="../../jqwidgets/jqxinput.js"></script>
<script type="text/javascript" src="../../jqwidgets/jqxpanel.js"></script>
<script type="text/javascript" src="../../jqwidgets/jqxtabs.js"></script>
<script type="text/javascript" src="../../jqwidgets/jqxdate.js"></script>    
<script type="text/javascript" src="../../jqwidgets/jqxdatetimeinput.js"></script>   
<script type="text/javascript" src="../../jqwidgets/jqxlistbox.js"></script>
<script type="text/javascript" src="../../jqwidgets/jqxcombobox.js"></script>
<script type="text/javascript" src="../../jqwidgets/jqxmaskedinput.js"></script>
<script type="text/javascript" src="../../jqwidgets/jqxtextarea.js"></script>
<script type="text/javascript" src="../../jqwidgets/jqxpasswordinput.js"></script>
<script type="text/javascript" src="../../jqwidgets/jqxnotification.js"></script>
<script type="text/javascript" src="../../jqwidgets/jqxcolorpicker.js"></script>
<script type="text/javascript" src="../../jqwidgets/jqxeditor.js"></script>
<script type="text/javascript">
	$(document).ready(function () {
		var userSettings = {theme:'<?php echo $_SESSION['usertheme'];?>', culture:'<?php echo $_SESSION['i18n_lang'];?>',localization:'<?php echo $_SESSION['i18n'];?>'};
		var theme = userSettings.theme;
		});


    </script>
</head>
<body class='default'>
	<?php include $uri;?>
</body>
</html>
<?php } ?>
