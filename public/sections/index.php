<?PHP

// Get links
$index = yaml_parse_file($_SERVER["DOCUMENT_ROOT"]."core/config.yml");
if(!$index) {
  header("Location: ".$_SERVER["DOCUMENT_ROOT"]."error/canLoadConfig.php");
  return true;
}

// Pre-sets
date_default_timezone_set($index["system_timezone"]);
include $_SERVER["DOCUMENT_ROOT"].$index["core_language"];
loadLanguage($_SERVER["DOCUMENT_ROOT"], $index["system_language"]);

// Login Verify
if(session_status() != PHP_SESSION_ACTIVE) {
	session_start();
}if(!isset($_SESSION["email"]) || empty($_SESSION["email"])) {
	session_destroy();
	header("Location: ".$index["system_url"].$index["loc_page_login"]);
	return true;
}
try {
	// Conecta no bando de dados
	$Connect = new PDO("mysql:host=".$index["database_general_host"].";dbname=".$index["database_general_name"], $index["database_general_user"], $index["database_general_pass"]);
	$Connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

?><!DOCTYPE html>
<html lang="<?PHP echo $index["system_language"]." | ".lang("sections_title"); ?>">
  <head>
	
	<!-- Set basic configs -->
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	
	<!-- Set FavIcon File and Title -->
	<link rel="icon" href="<?PHP echo $index["system_url"].$index["loc_favicon"]; ?>" type="image/ico" />
    <title><?PHP echo $index["system_name"]; ?></title>

	<!-- Basic CSS Files -->
    <link rel="stylesheet" href="<?PHP echo $index["system_url"].$index["file_bootstrap_CSS"]; ?>"><!-- Bootstrap          -->
    <link rel="stylesheet" href="<?PHP echo $index["system_url"].$index["file_fonts"];         ?>"><!-- Font Awesome       -->
    <link rel="stylesheet" href="<?PHP echo $index["system_url"].$index["file_nProgress_CSS"]; ?>"><!-- NProgress          -->
    <link rel="stylesheet" href="<?PHP echo $index["system_url"].$index["file_basic_CSS"];     ?>"><!-- Custom Theme Style -->
    <style>
      body {background: url(<?PHP echo $index["system_url"].$index["background_image"]; ?>) fixed no-repeat;}
      .nav_menu {background: rgba(237, 237, 237, <?PHP echo 1.00-$index["menu_top_opacity"]; ?>);}
      .left_col.scroll-view {background: rgba(42, 63, 84, <?PHP echo 1.00-$index["menu_left_opacity"]; ?>);}
      .right_col {background: rgba(237, 237, 237, <?PHP echo 1.00-$index["body_opacity"]; ?>);}
    </style>
	  
  </head>

  <body class="nav-md">
    <div class="container body">
      <div class="main_container">
		  
		<!-- sidebar -->
        <div class="col-md-3 left_col">
          <div class="left_col scroll-view">
            <div class="navbar nav_title" style="border: 0;">
              <a href="<?PHP echo $index["system_url"].$index["loc_page_dashboard"]; ?>" class="site_title"><img class="fa fa-paw" src="<?PHP echo $index["system_url"].$index["loc_icon"]; ?>"> <span><?PHP echo $index["system_name"]; ?></span></a>
            </div>

            <div class="clearfix"></div>

            <!-- sidebar menu -->
            <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
              <div class="menu_section">
                <ul class="nav side-menu">
                  <li><a href="<?PHP echo $index["system_url"].$index["loc_page_dashboard"]; ?>"><i class="fa fa-home"></i><?PHP echoLang("nav_dashboard"); ?></a></li>
<?PHP
					// Exibe o menu de gerenciamento de acordo com a hierarquia
					$db = $Connect->query("SELECT * FROM ".$index["database_table_users"]." WHERE email='".$_SESSION["email"]."'")->fetch();
					if($db["level"] == 0) {
						echo '<li><a href="'.$index["system_url"].$index["loc_page_sections"].'"><i class="fa fa-cubes"></i>'.lang("nav_manager_sections").'</a></li>';
						echo '<li><a href="'.$index["system_url"].$index["loc_page_sensors"].'"><i class="fa  fa-bug"></i>'.lang("nav_manager_sensors").'</a></li>';
						echo '<li><a href="'.$index["system_url"].$index["loc_page_actuators"].'"><i class="fa fa-toggle-off"></i>'.lang("nav_manager_actuators").'</a></li>';
						echo '<li><a href="'.$index["system_url"].$index["loc_page_automation"].'"><i class="fa fa-cogs"></i>'.lang("nav_manager_automations").'</a></li>';
						echo '<li><a href="'.$index["system_url"].$index["loc_page_graphics"].'"><i class="fa fa-area-chart"></i>'.lang("nav_manager_graphics").'</a></li>';
						echo '</ul></div>';
					}else if($db["level"] == 1) {
						echo '<li><a href="'.$index["system_url"].$index["loc_page_sections"].'"><i class="fa fa-cubes"></i>'.lang("nav_manager_sections").'</a></li>';
						echo '<li><a href="'.$index["system_url"].$index["loc_page_automation"].'"><i class="fa fa-cogs"></i>'.lang("nav_manager_automations").'</a></li>';
						echo '<li><a href="'.$index["system_url"].$index["loc_page_graphics"].'"><i class="fa fa-area-chart"></i>'.lang("nav_manager_graphics").'</a></li>';
						echo '</ul></div>';
					}
	
				?></div>
            <!-- /sidebar menu -->

            <!-- /menu footer buttons -->
            <div class="sidebar-footer hidden-small">
              <a data-toggle="tooltip" data-placement="top" title="Settings" href="<?PHP echo $index["system_url"].$index["loc_page_settings"];  ?>">
                <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
              </a>
              <a data-toggle="tooltip" data-placement="top" title="FullScreen">
                <span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
              </a>
              <a data-toggle="tooltip" data-placement="top" title="Help" href="<?PHP echo $index["system_url"].$index["loc_page_help"];    ?>">
                <span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span>
              </a>
              <a data-toggle="tooltip" data-placement="top" title="Logout" href="<?PHP echo $index["system_url"].$index["loc_page_logout"];  ?>">
                <span class="glyphicon glyphicon-log-out" aria-hidden="true"></span>
              </a>
            </div>
            <!-- /menu footer buttons -->
          </div>
        </div>
		<!-- /sidebar -->
		
        <!-- top navigation -->
        <div class="top_nav">
          <div class="nav_menu">
            <nav>
              <div class="nav toggle">
                <a id="menu_toggle"><i class="fa fa-bars"></i></a>
              </div>
              <ul class="nav navbar-nav navbar-right">
                <li class="">
                  <a class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><?PHP
					  // Busca pelo usuário
					  $db = $Connect->query("SELECT * FROM ".$index["database_table_users"]." WHERE email='".$_SESSION["email"]."'")->fetch();
					  if(empty($db["image_profile"])) {
					  	echo '<i class="fa fa-user" style="margin: 0px 10px 0px 0px">&nbsp;&nbsp;&nbsp;'.$db["name"].'</i>';
            }else {
					  	echo '<img src="'.$index["system_url"].$index["loc_profiles_icons"].$db["image_profile"].'.png" alt="">'.$db["name"]."&nbsp;&nbsp;";
            }
                    ?><span class=" fa fa-angle-down"></span>
                  </a>
                  <ul class="dropdown-menu dropdown-usermenu pull-right">
                    <li><a href="<?PHP echo $index["system_url"].$index["loc_page_profile"];  ?>"><?PHP echoLang("nav_profile"); ?></a></li>
                    <li><a href="<?PHP echo $index["system_url"].$index["loc_page_settings"]; ?>"><?PHP echoLang("nav_settings"); ?></a></li>
                    <li><a href="<?PHP echo $index["system_url"].$index["loc_page_help"];     ?>"><?PHP echoLang("nav_help"); ?></a></li>
                    <li><a href="<?PHP echo $index["system_url"].$index["loc_page_logout"];   ?>"><i class="fa fa-sign-out pull-right"></i><?PHP echoLang("nav_logout"); ?></a></li>
                  </ul>
                </li>

                <li role="presentation" class="dropdown">
                  <a class="dropdown-toggle info-number" data-toggle="dropdown" aria-expanded="false">
                    <i class="fa fa-newspaper-o"></i>
                    <!--<span class="badge bg-green">6</span>-->
                  </a>
                  <ul id="menu1" class="dropdown-menu list-unstyled msg_list" role="menu">
                    <li>
                      <div class="text-center">
						  <h4><?PHP echoLang("nav_notifications_clear"); ?></h4>
                        <a>
                          <strong><?PHP echoLang("nav_notifications_see_all"); ?></strong>
                          <i class="fa fa-angle-right"></i>
                        </a>
                      </div>
                    </li>
                  </ul>
                </li>
              </ul>
            </nav>
          </div>
        </div>
        <!-- /top navigation -->

        <!-- page content -->
        <div class="right_col" role="main">
          <div class="row">
            <div class="col-12">
              <div class="x_panel">
                <div class="x_title">
                  <h3>Hello!</h3>
                </div>
                <div class="x_content">
                    <p>Section page.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- /page content -->

        <!-- footer content -->
        <footer>
          <div class="pull-right">
            <?PHP echo "&copy; ".date("Y")." | <a href='".$index["system_url"]."' title='".$index["system_name"]."'>".$index["system_name"]." ".lang("fotter_msg_one")."</a>, ".lang("fotter_msg_two").' <a href="https://jwdouglas.net" title="'.lang("fotter_title_jwdouglas").'" target="_blank">JWDouglas Vieira</a>. '.lang("fotter_msg_thee").' <a href="https://github.com/ColorlibHQ/gentelella" title="'.lang("fotter_title_github").'" target="_blank">Gentelella Theme</a> '.lang("fotter_msg_two").' <a href="https://colorlib.com" title="'.lang("fotter_title_colorlib").'" target="_blank">Colorlib</a>.'; ?>
          </div>
          <div class="clearfix"></div>
        </footer>
        <!-- /footer content -->
		  
      </div>
    </div>

	<!-- Basic Scripts Files -->
    <script src="<?PHP echo $index["system_url"].$index["file_jQuery"];       ?>"></script><!-- jQuery               -->
    <script src="<?PHP echo $index["system_url"].$index["file_bootstrap_JS"]; ?>"></script><!-- Bootstrap            -->
    <script src="<?PHP echo $index["system_url"].$index["file_nProgress_JS"]; ?>"></script><!-- NProgress            -->
    <script src="<?PHP echo $index["system_url"].$index["file_basic_JS"];     ?>"></script><!-- Custom Theme Scripts -->
	
  </body>
</html><?PHP 
}catch(PDOException $Ex) {
  echo $Ex;
}
?>