<?php
			if(!isset($_SESSION)){session_start();}
			
			if(!isset($_SESSION['userid'])) {
				header("location: /");
			}
			
			//header("Cache-Control:no-cache, no-store, must-revalidate, max-age=0");
			
			include ($_SERVER['DOCUMENT_ROOT'].'/panel/modules/data/connect.php');
			include ($_SERVER['DOCUMENT_ROOT'].'/panel/modules/data/table.php');
			include ($_SERVER['DOCUMENT_ROOT'].'/panel/modules/security/security.php');
			$mysqli = new mysqli($hostname, $username, $password, $database);
			if (mysqli_connect_errno())
				{
					printf("Connect failed: %s\n", mysqli_connect_error());
					exit();
				}
			
			//error_reporting(0);	
			if(!isset($_SESSION['usertheme'])){
				$viewsettings= new Table('viewsettings', $mysqli);
				$viewsettings->setFilter('userid','=', $_SESSION['userid']);
				$viewsettings->getFirstRowByFilter();
				$themeid = $viewsettings->get_Themeid();
				$theme= new Table('themes', $mysqli);
				
				if(is_null($themeid)){
					$theme->getFirstRowByFilter();
				}else{
					$theme->getById($themeid);
				};
				
				
				if(is_null($viewsettings->get_I18n())){
					$_SESSION['i18n'] = 'en';
					$_SESSION['i18n_lang'] = 'en-US';
				}else
				{
					$i18n = new Table('i18n', $mysqli);
					$i18n->getById($viewsettings->get_I18n());
					$_SESSION['i18n'] = $i18n->get_Culture();
					$_SESSION['i18n_lang'] = $i18n->get_Language();
				}
				
				$_SESSION['usertheme']=$theme->get_Theme_name();
			}
			
?>
<html>
  <head>
    <link rel="stylesheet" href="https://www.jqwidgets.com/jquery-widgets-demo/jqwidgets/styles/jqx.base.css" type="text/css" />
    <link rel="stylesheet" href="https://www.jqwidgets.com/jquery-widgets-demo/jqwidgets/styles/jqx.<?php echo $_SESSION['usertheme'];?>.css" type="text/css" /> 
	<link rel="stylesheet" href="../../scripts/style.css">
    <script type="text/javascript" src="../../scripts/jquery-1.12.4.min.js"></script>
	<script type="text/javascript" src="../../scripts/menu.js"></script>
    <script type="text/javascript" src="https://www.jqwidgets.com/jquery-widgets-demo/jqwidgets/jqxcore.js"></script>
    <script type="text/javascript" src="https://www.jqwidgets.com/jquery-widgets-demo/jqwidgets/jqxbuttons.js"></script>
    <script type="text/javascript" src="https://www.jqwidgets.com/jquery-widgets-demo/jqwidgets/jqxscrollbar.js"></script>
    <script type="text/javascript" src="https://www.jqwidgets.com/jquery-widgets-demo/jqwidgets/jqxpanel.js"></script>
    <script type="text/javascript" src="https://www.jqwidgets.com/jquery-widgets-demo/jqwidgets/jqxtree.js"></script>
    <script type="text/javascript" src="https://www.jqwidgets.com/jquery-widgets-demo/jqwidgets/jqxcheckbox.js"></script>
    <script type="text/javascript" src="https://www.jqwidgets.com/jquery-widgets-demo/jqwidgets/jqxmenu.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            // Create jqxTree
            
            $('#jqxTree').jqxTree({height: $(document).height()*0.93, width: '230px',theme: '<?php echo $_SESSION['usertheme'];?>' });
            $('#jqxTree').css('visibility', 'visible');
            $('#jqxTree').on('itemClick', function (event) {
				
				var el = event.args.element;
				var fn = document.getElementById('fname');
				link = el.getAttribute("data-link");
				if(link!==null&&link!==undefined){
					if(typeof frames['iframe'].saveformstate === 'function'){
							frames['iframe'].saveformstate();
						};
					
					frames['iframe'].location.href = link;
					var item = $('#jqxTree').jqxTree('getItem',el);
					fn.innerHTML=' FORM:<b>'+item.label+'<b>';
				}else{
					$('#fname').innerHTML='';
				}	
			});
			var bg = $('#jqxTree').css('background');
			var cl = $('.jqx-widget-content-<?php echo $_SESSION['usertheme'];?>').css('color');
			$('.menu').css({background:bg});
			$('.icon-menu').css({background:bg});
			$('.icon-menu').css({color:cl});
			$('body').css({background:bg});
        });
	
    </script>
    
   
    
  </head>
  <body>
	<iframe name="iframe" id="iframe" src=""  align="right" frameborder="0" scrolling="no" style="overflow:hidden;overflow-x:hidden;overflow-y:hidden;z-index: 0;height:100%;width:100%;position:absolute;top:30px;left:10px;right:0px;bottom:0px">If you can see this, your browser does not support iframes!</iframe>
	 <!-- Main body -->
     <div class="icon-menu">
        <img src="images/menu-ham-icon.png">
        Menu <?php echo ' user : <b>'.security::userName();?></b>&nbsp<span id="fname"></span>
      </div>
    <div class="menu">
      
      <!-- Icon Menu -->
      <div class="icon-close">
        <img src="images/close-btn.png">
      </div>

      <!-- Menu -->
      <div id='jqxWidget' style="float: left;">
        <div id='jqxTree' style='visibility: hidden; float: left; margin-top: 10px;'>
		
		<?php
				$menu = new Table('menu',$mysqli);
				$menu->setFilter('parent','IS NULL');
				$menu->setOrder('orderitems');
				$forms = new Table('sys_forms',$mysqli);
				$formsHashMap = $forms->fetchHashMap('name');
				
				if($_SESSION['username']!='admin'){
					$menu->setFilter('admin_only','=',0);
				}
				
				$result = $menu->getCursor();
				print '<ul>';
				while ($row = $result->fetch_assoc()){
					renderMenuItem($row,$menu,$formsHashMap);
				}
				print '<li><a href="logout.php"><img style="float: left; margin-right: 5px;" src="../../images/icons/logout.png" />Logout</a></li>';
				print '</ul>';
				function renderMenuItem($r=array(),$m,$fHm){
					if(!security::hasPermissionOnMenuItem($r['id'])){return;}
					$template = ($fHm[$r['link']]['template']=='')?'':'&template='.$fHm[$r['link']]['template'];
					$icon = ($r['icon']=='')?'folder':$r['icon'];
					print '<li id="'.$r['name'].'"'.((is_null($r['link'])||($r['link']==''))?'':(' data-link="/panel/forms/?id='.$r['link'].$template.($r['admin_only']?'&admin=1':'').'"')).'><img style="float: left; margin-right: 5px;" src="../../images/icons/'.$icon.'.png" />'.$r['showname'];
					$m->resetFilters();
					$m->setFilter('parent','=',$r['id']);
					$m->setOrder('orderitems');
					$res=$m->getCursor();
					if(!is_null($res)){print '<ul>';}
					while($rr=$res->fetch_assoc()){
						renderMenuItem($rr,$m,$fHm);
					}
					if(!is_null($res)){print '</ul>';}
					print '</li>';
				}
				
			$mysqli->close();
		?>
			
         </div> 
		
    </div>
    </div>

   
    
    
    
  </body>
</html>
