<?PHP

// Get links
$index = yaml_parse_file($_SERVER["DOCUMENT_ROOT"]."/core/config.yml");
if(!$index) {
  http_response_code(500);
  return false;
}

// Pre-sets
date_default_timezone_set($index["system_timezone"]);
include $_SERVER["DOCUMENT_ROOT"]."/".$index["core_language"];
if(!loadLanguage($_SERVER["DOCUMENT_ROOT"]."/", $index["system_language"])) {
  http_response_code(500);
  return false;
}

// Login Verify
if(session_status() != PHP_SESSION_ACTIVE) {
	session_start();
}if(!isset($_SESSION["email"]) || empty($_SESSION["email"])) {
	session_destroy();
	header("Location: ".$index["system_url"].$index["loc_page_login"]);
	return true;
}
try {
	// Connect to databases
	$connect  = new PDO("mysql:host=".$index["database_general_host"].";dbname=".$index["database_general_name"], $index["database_general_user"], $index["database_general_pass"]);
	$connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sconnect = new PDO("mysql:host=".$index["database_data_host"].";dbname=".$index["database_data_name"], $index["database_data_user"], $index["database_data_pass"]);
	$sconnect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Check if user can access this page
	if($connect->query("SELECT level FROM ".$index["database_table_users"]." WHERE email='".$_SESSION["email"]."'")->fetchColumn() > 0) {
		header("Location: ".$index["system_url"].$index["loc_page_dashboard"]);
		return true;
	}

  // Create a new Sensor
	if(isset($_GET["create"])) {
    $options = array();
    if(!isset($_POST["type"])) {
      header("Location: ".$index["system_url"].$index["loc_page_sensors"]);
      return true;
    }else if(!(isset($_POST["name"]) && !empty($_POST["name"]))) {
      header("Location: ".$index["system_url"].$index["loc_page_sensors"]);
      return true;
    }else if(!isset($_POST["unity"]) && ($_POST["type"] > 0)) {
      header("Location: ".$index["system_url"].$index["loc_page_sensors"]);
      return true;
    }else if(!isset($_POST["decimal"]) && ($_POST["type"] > 1)) {
      header("Location: ".$index["system_url"].$index["loc_page_sensors"]);
      return true;
    }else {
      $options["collection"]             = isset($_POST["option1"]);
      $options["date_data"]              = isset($_POST["option2"]);
      $options["date_request_time_data"] = isset($_POST["option3"]);
      $options["time_data"]              = isset($_POST["option4"]);
      $options["no_data_time"]           = isset($_POST["option5"]);
      $options["do_log"]                 = isset($_POST["option6"]);
      
      $option  = json_encode($options);
      $date    = date("Y-m-d");
      $key     = md5(rand(1000, 100000).$_POST["type"].$_POST["name"].date("i:s:h"));
      $id      = date("ydhs").$connect->query("SELECT COUNT(*) FROM ".$index["database_table_sensors"])->fetchColumn();
      $type    = "0";
      if($_POST["type"] == 0) {
        $type  = $_POST["type"];
        $unity = "";
      }else if($_POST["type"] == 1) {
        $type  = $_POST["type"];
        $unity = $_POST["unity"];
      }else {
        $type  = $_POST["type"].$_POST["decimal"];
        $unity = $_POST["unity"];
      }
      $create = $connect->prepare("INSERT INTO ".$index["database_table_sensors"]." (id, name, skey, type, unity, options, screate) VALUES (:ids, :nms, :kes, :tps, :uns, :pts, :cts)");
      $create->bindParam(":ids", $id);
      $create->bindParam(":nms", $_POST["name"]);
      $create->bindParam(":kes", $key);
      $create->bindParam(":tps", $type);
      $create->bindParam(":uns", $unity);
      $create->bindParam(":pts", $option);
      $create->bindParam(":cts", $date);
      $create->execute();

      if($type == 0) {
        $create = $sconnect->prepare("CREATE TABLE s".$id." (time DATETIME PRIMARY KEY, value BOOLEAN)");
        $create->execute();
      }else if($type == 1) {
        $create = $sconnect->prepare("CREATE TABLE s".$id." (time DATETIME PRIMARY KEY, value INTEGER)");
        $create->execute();
      }else {
        $create = $sconnect->prepare("CREATE TABLE s".$id." (time DATETIME PRIMARY KEY, value FLOAT)");
        $create->execute();
      }

      header("Location: ".$index["system_url"].$index["loc_page_sensors"]);
      return true;
    }
	}

  // Delete a sensor
  if(isset($_GET["delete"]) && isset($_POST["id"]) && !empty($_POST["id"])) {
    if($connect->query("SELECT COUNT(*) FROM ".$index["database_table_sensors"]." WHERE id='".$_POST["id"]."'")->fetchColumn() != 1) {
      header("Location: ".$index["system_url"].$index["loc_page_sensors"]);
      return true;
    }else {

      $drop = $connect->prepare("DELETE FROM ".$index["database_table_sensors"]." WHERE id=:ids");
      $drop->bindParam(":ids", $_POST["id"]);
      $drop->execute();

      $sdrop = $sconnect->prepare("DROP TABLE s".$_POST["id"]);
      $sdrop->execute();

      header("Location: ".$index["system_url"].$index["loc_page_sensors"]);
      return true;
    }
  }

  // Get sensor details
  if(isset($_GET["getDetails"]) && isset($_POST["id"]) && !empty($_POST["id"])) {
    $obj  = $connect->query("SELECT * FROM ".$index["database_table_sensors"]." WHERE id=".$_POST["id"])->fetchObject();
    $json["id"]      = (int)$obj->id;
    $json["name"]    = $obj->name;
    $json["key"]     = $obj->skey;
    $json["type"]            = (int)substr($obj->type, 0, true);
    $json["unity"]           = $obj->unity;
    $json["decimal"]         = (int)substr($obj->type, 1);
    $json["options"]         = json_decode($obj->options);
    $json["create"]["day"]   = (int)explode("-", $obj->screate)[2];
    $json["create"]["month"] = (int)explode("-", $obj->screate)[1];
    $json["create"]["year"]  = (int)explode("-", $obj->screate)[0];
    echo json_encode($json);
    return true;
  }

  // Update sensor details
  if(isset($_GET["saveChanges"])) {
    $options = array();
    if(!isset($_POST["sid"]) && !empty($_POST["sid"])) {
      header("Location: ".$index["system_url"].$index["loc_page_sensors"]);
      return true;
    }else if(!isset($_POST["type"]) && !empty($_POST["type"])) {
      header("Location: ".$index["system_url"].$index["loc_page_sensors"]);
      return true;
    }else if(!(isset($_POST["name"]) && !empty($_POST["name"]))) {
      header("Location: ".$index["system_url"].$index["loc_page_sensors"]);
      return true;
    }else if(!isset($_POST["unity"]) && ($_POST["type"] > 0)) {
      header("Location: ".$index["system_url"].$index["loc_page_sensors"]);
      return true;
    }else if(!isset($_POST["decimal"]) && ($_POST["type"] > 1)) {
      header("Location: ".$index["system_url"].$index["loc_page_sensors"]);
      return true;
    }else if(!isset($_POST["id"])) {
      header("Location: ".$index["system_url"].$index["loc_page_sensors"]);
      return true;
    }else if(!isset($_POST["key"])) {
      header("Location: ".$index["system_url"].$index["loc_page_sensors"]);
      return true;
    }else {
      $options["collection"]             = isset($_POST["option1"]);
      $options["date_data"]              = isset($_POST["option2"]);
      $options["date_request_time_data"] = isset($_POST["option3"]);
      $options["time_data"]              = isset($_POST["option4"]);
      $options["no_data_time"]           = isset($_POST["option5"]);
      $options["do_log"]                 = isset($_POST["option6"]);
      $id                                = empty($_POST["id"])?false:$_POST["id"];
      $key                               = empty($_POST["key"])?false:$_POST["key"];
      $clear                             = isset($_POST["clear"]);
      $option  = json_encode($options);
      if(!$key) {$key = md5(rand(1000, 100000).$_POST["type"].$_POST["name"].date("i:s:h"));}
      if(!$id) {$id = date("ydhs").$connect->query("SELECT COUNT(*) FROM ".$index["database_table_sensors"])->fetchColumn();}
      
      $type    = 0;
      if($_POST["type"] == 0) {
        $type  = $_POST["type"];
        $unity = "";
      }else if($_POST["type"] == 1) {
        $type  = $_POST["type"];
        $unity = $_POST["unity"];
      }else {
        $type  = $_POST["type"].$_POST["decimal"];
        $unity = $_POST["unity"];
      }

      $sup = $connect->query("SELECT id, type FROM ".$index["database_table_sensors"]." WHERE id='".$_POST["sid"]."'")->fetchObject();
      
      $up = $connect->prepare("UPDATE ".$index["database_table_sensors"]." SET id=:sid, name=:snm, skey=:sky, type=:stp, unity=:snt, options=:spt WHERE id=:ids");
      $up->bindParam(":ids", $sup->id);
      $up->bindParam(":sid", $id);
      $up->bindParam(":snm", $_POST["name"]);
      $up->bindParam(":sky", $key);
      $up->bindParam(":stp", $type);
      $up->bindParam(":snt", $unity);
      $up->bindParam(":spt", $option);
      $up->execute();
      
      if($id == $sup->id && $type == $sup->type && $clear) {
        $clear = $sconnect->prepare("DELETE FROM s".$sup->id);
        $clear->execute();
      }else if($id != $sup->id && $type == $sup->type && $clear) {
        $clear = $sconnect->prepare("DELETE FROM s".$sup->id);
        $clear->execute();
        $rename = $sconnect->prepare("ALTER TABLE s".$sup->id." RENAME TO s".$id);
        $rename->execute();
      }else if($id != $sup->id && $type == $sup->type && !$clear) {
        $rename = $sconnect->prepare("ALTER TABLE s".$sup->id." RENAME TO s".$id);
        $rename->execute();
      }else if($type != $sup->type) {
        $drop = $sconnect->prepare("DROP TABLE s".$sup->id);
        $drop->execute();
        if($type == 0) {
          $create = $sconnect->prepare("CREATE TABLE s".$id." (time DATETIME PRIMARY KEY, value BOOLEAN)");
          $create->execute();
        }else if($type == 1) {
          $create = $sconnect->prepare("CREATE TABLE s".$id." (time DATETIME PRIMARY KEY, value INTEGER)");
          $create->execute();
        }else {
          $create = $sconnect->prepare("CREATE TABLE s".$id." (time DATETIME PRIMARY KEY, value FLOAT)");
          $create->execute();
        }
      }

      header("Location: ".$index["system_url"].$index["loc_page_sensors"]);
      return true;
    }
  }

?><!DOCTYPE html>
<html lang="<?PHP echo $index["system_language"]; ?>">
  <head>
	
	<!-- Set basic configs -->
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	
	<!-- Set FavIcon File and Title -->
	<link rel="icon" href="<?PHP echo $index["system_url"].$index["loc_favicon"]; ?>" type="image/ico" />
    <title><?PHP echo $index["system_name"]." | ".lang("actuators_title"); ?></title>

	<!-- Basic CSS Files -->
    <link rel="stylesheet" href="<?PHP echo $index["system_url"].$index["file_bootstrap_CSS"];       ?>"><!-- Bootstrap          -->
    <link rel="stylesheet" href="<?PHP echo $index["system_url"].$index["file_fonts"];               ?>"><!-- Font Awesome       -->
    <link rel="stylesheet" href="<?PHP echo $index["system_url"].$index["file_nProgress_CSS"];       ?>"><!-- NProgress          -->
    <link rel="stylesheet" href="<?PHP echo $index["system_url"].$index["file_progress_bar_CSS"];    ?>"><!-- Progress Bar       -->
    <link rel="stylesheet" href="<?PHP echo $index["system_url"].$index["file_daterangepicker_CSS"]; ?>"><!-- DaterangePicker    -->
    <link rel="stylesheet" href="<?PHP echo $index["system_url"].$index["file_dataTables_BS"];       ?>"><!-- Data Table         -->
    <link rel="stylesheet" href="<?PHP echo $index["system_url"].$index["file_switchery_CSS"];       ?>"><!-- Switchery          -->
    <link rel="stylesheet" href="<?PHP echo $index["system_url"].$index["file_basic_CSS"];           ?>"><!-- Custom Theme Style -->
    <style> /* pre-settings to change background */
      body {background: url(<?PHP echo $index["system_url"].$index["background_image"]; ?>) fixed no-repeat;}
      .nav_menu {background: rgba(237, 237, 237, <?PHP echo 1.00-$index["menu_top_opacity"]; ?>);}
      .left_col.scroll-view {background: rgba(42, 63, 84, <?PHP echo 1.00-$index["menu_left_opacity"]; ?>);}
      .right_col {background: rgba(237, 237, 237, <?PHP echo 1.00-$index["body_opacity"]; ?>);}
    </style>
    <script> // language pack for JS functions
      var sensor_type_boolean       = "<?PHP echoLang("sensor_type_boolean");       ?>";
      var sensor_type_whole         = "<?PHP echoLang("sensor_type_whole");         ?>";
      var sensor_type_float         = "<?PHP echoLang("sensor_type_float");         ?>";
      var lang_sensor_delete_title  = "<?PHP echoLang("sensor_delete_title");       ?>";
      var lang_sensor_delete_msg    = "<?PHP echoLang("sensor_delete_msg");         ?>";
      var lang_sensor_delete_button = "<?PHP echoLang("sensor_delete_button");      ?>";
      var sensor_edit_title         = "<?PHP echoLang("sensor_edit_title");         ?>";
      var sensor_edit_button        = "<?PHP echoLang("sensor_edit_button");        ?>";
      var sensor_edit_loading       = "<?PHP echoLang("sensor_edit_loading");       ?>";
      var sensor_edit_load_error    = "<?PHP echoLang("sensor_edit_load_error");    ?>";
      var sensor_create_name        = "<?PHP echoLang("sensor_create_name");        ?>";
      var sensor_create_unity       = "<?PHP echoLang("sensor_create_unity");       ?>";
      var sensor_create_decimal     = "<?PHP echoLang("sensor_create_decimal");     ?>";
      var sensor_create_option_1    = "<?PHP echoLang("sensor_create_option_1");    ?>";
      var sensor_create_option_2    = "<?PHP echoLang("sensor_create_option_2");    ?>";
      var sensor_create_option_3    = "<?PHP echoLang("sensor_create_option_3");    ?>";
      var sensor_create_option_4    = "<?PHP echoLang("sensor_create_option_4");    ?>";
      var sensor_create_option_5    = "<?PHP echoLang("sensor_create_option_5");    ?>";
      var sensor_create_option_6    = "<?PHP echoLang("sensor_create_option_6");    ?>";
      var sensor_edit_id            = "<?PHP echoLang("sensor_edit_id");            ?>";
      var sensor_edit_key           = "<?PHP echoLang("sensor_edit_key");           ?>";
      var sensor_edit_id_msg        = "<?PHP echoLang("sensor_edit_id_msg");        ?>";
      var sensor_edit_key_msg       = "<?PHP echoLang("sensor_edit_key_msg");       ?>";
      var sensor_edit_history_keep  = "<?PHP echoLang("sensor_edit_history_keep");  ?>";
      var sensor_edit_history_clear = "<?PHP echoLang("sensor_edit_history_clear"); ?>";
    </script>
	  
  </head>

  <body class="nav-md">
    <div class="container body">
      <div class="main_container">
		  
		<!-- sidebar -->
        <div class="col-md-3 left_col menu_fixed">
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
					$db = $connect->query("SELECT * FROM ".$index["database_table_users"]." WHERE email='".$_SESSION["email"]."'")->fetch();
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
              <a data-toggle="tooltip" data-placement="top" title="<?PHP echoLang("nav_settings"); ?>" href="<?PHP echo $index["system_url"].$index["loc_page_settings"];  ?>">
                <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
              </a>
              <a data-toggle="tooltip" data-placement="top" title="<?PHP echoLang("nav_fullscreen"); ?>">
                <span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
              </a>
              <a data-toggle="tooltip" data-placement="top" title="<?PHP echoLang("nav_help"); ?>" href="<?PHP echo $index["system_url"].$index["loc_page_help"];    ?>">
                <span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span>
              </a>
              <a data-toggle="tooltip" data-placement="top" title="<?PHP echoLang("nav_logout"); ?>" href="<?PHP echo $index["system_url"].$index["loc_page_logout"];  ?>">
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
					  $db = $connect->query("SELECT * FROM ".$index["database_table_users"]." WHERE email='".$_SESSION["email"]."'")->fetch();
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

            <!-- Dynamic Table -->
            <div class="clearfix"></div>
            <div class="col-md-12 col-sm-12 col-xs-12">
              <div class="x_panel">
                <div class="row x_title">
                  <div class="col-md-11 col-sm-11 col-xs-10">
                    <h2><?PHP echoLang("actuators_table_title"); ?></h2>
                  </div>
                  <div class="col-md-1 col-sm-1 col-xs-2">
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a title="<?PHP echoLang("panel_button_help"); ?>" class="link" target="_blank" href="<?PHP echo $index["system_url"].$index["loc_page_help"].""; ?>"><i class="fa fa-question"></i></a></li>
                      <li><a title="<?PHP echoLang("panel_button_dropdown"); ?>" class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                      <li><a title="<?PHP echoLang("panel_button_close"); ?>" class="close-link"><i class="fa fa-close"></i></a></li>
                    </ul>
                  </div>
                </div>
                <div class="col-md-12 col-sm-12 col-xs-12 x_content">
                  <table id="datatable" class="table table-hover table-striped">
                    <thead>
                      <tr>
                        <th><?PHP echoLang("actuators_table_header_name"); ?></th>
                        <th><?PHP echoLang("actuators_table_header_id"); ?></th>
                        <th><?PHP echoLang("actuators_table_header_sensor"); ?></th>
                        <th><?PHP echoLang("actuators_table_header_key"); ?></th>
                        <th><?PHP echoLang("actuators_table_header_type"); ?></th>
                        <th><?PHP echoLang("actuators_table_header_options"); ?></th>
                      </tr>
                    </thead>


                    <tbody><?PHP

					$query = $connect->query("SELECT id, name, skey, type, sensor FROM ".$index["database_table_actuators"])->fetchAll();
					foreach($query as $a) {
						echo '<tr id="'.$a["id"].'">';
            // name column
						echo '<td>'.$a["name"].'</td>';
            // ID column
						echo '<td class="table_align">
						  <p name="id" class="alignleft">'.$a["id"].'</p>
              <button name="actuator-copy-id" id="'.$a["id"].'" class="btn btn-link alignleft" data-toggle="tooltip" data-placement="top" title="'.lang("actuators_copy_id").'"><i class="fa  fa-tag"></i></button>
              </td>';
            // Sensor column
						echo '<td class="table_align">';
            if(!empty($a["sensor"])) {
						  echo '<p name="key" class="alignleft">'.$a["sensor"].'</p>
						    <button name="actuator-copy-sensor" id="'.$a["id"].'" class="btn btn-link alignleft" data-toggle="tooltip" data-placement="top" title="'.lang("actuators_copy_sensor").'"><i class="fa  fa-key"></i></button>
                </td>';
            }else {
              echo '<td>'.lang("actuators_table_no_sensor").'</td>';
            }
            // Key column
					  echo '<td class="table_align">
						  <p name="key" class="alignleft">'.$a["skey"].'</p>
						  <button name="actuator-copy-key" id="'.$a["id"].'" class="btn btn-link alignleft" data-toggle="tooltip" data-placement="top" title="'.lang("actuators_copy_key").'"><i class="fa  fa-key"></i></button>
					    </td>';
            // Type column
						switch($a["type"]) {
							case 0: 
                echo '<td>'.lang("sensor_type_boolean").'</td>';
                break;
							case 1:
                echo '<td>'.lang("sensor_type_whole").'</td>';
                break;
							default: 
                echo '<td>'.lang("sensor_type_float").'('.(int)substr($a["type"], 1).')</td>';
                break;
						}
            // Options column
						echo '<td>
                          <button name="actuator-edit" id="'.$a["id"].'" class="btn btn-sm" data-toggle="tooltip" data-placement="top" title="'.lang("actuators_edit").'"><i class="fa fa-cogs"></i></button>
                          <button name="actuator-delete" id="'.$a["id"].'" class="btn btn-sm" data-toggle="tooltip" data-placement="top" title="'.lang("actuators_delete").'"><i class="fa fa-trash-o"></i></button>
                        </td>';
						echo "</tr>";
					}

					?></tbody>
                  </table>
                </div>
              </div>
            </div>

            <!-- Inputs -->
            <div class="clearfix"></div>
            <div class="col-md-12 col-xs-12">
              <div class="x_panel">
                <div class="x_title">
                  <div class="col-md-11 col-sm-11 col-xs-10">
                    <h2><?PHP echoLang("actuators_create_title"); ?></h2>
                  </div>
                  <div class="col-md-1 col-sm-1 col-xs-2">
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a title="<?PHP echoLang("panel_button_help"); ?>" class="link" target="_blank" href="<?PHP echo $index["system_url"].$index["loc_page_help"].""; ?>"><i class="fa fa-question"></i></a></li>
                      <li><a title="<?PHP echoLang("panel_button_dropdown"); ?>" class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                      <li><a title="<?PHP echoLang("panel_button_close"); ?>" class="close-link"><i class="fa fa-close"></i></a></li>
                    </ul>
                  </div>
                  <div class="clearfix"></div>
                </div>
                <div class="x_content">
                  <br />
                  <form class="form-horizontal form-label-left input_mask" name="actuatorCreate" method="POST" action="?create">

                    <div class="form-side-left col-md-6 col-ms-6 col-xs-12">

                      <div class="form-group form-toggle-buttons">
                        <div class="col-md-9 col-sm-9 col-xs-11 col-md-offset-3 col-sm-offset-3 col-xs-offset-1">
                          <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default" name="create" value="0">
                              <input type="radio" name="type" value="0" required="required"><?PHP echoLang("actuators_type_boolean"); ?>
                            </label>
                            <label class="btn btn-default" name="create" value="1">
                              <input type="radio" name="type" value="1" required="required"><?PHP echoLang("actuators_type_whole"); ?>
                            </label>
                            <label class="btn btn-default" name="create" value="2">
                              <input type="radio" name="type" value="2" required="required"><?PHP echoLang("actuators_type_float"); ?>
                            </label>
                          </div>
                        </div>
                      </div>

                      <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                        <input type="text" class="form-control has-feedback-left" name="name" id="name-create" placeholder="<?PHP echoLang("actuators_create_name"); ?>" required="required">
                        <span class="fa fa-user form-control-feedback left" aria-hidden="true"></span>
                      </div>

                      <div class="form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                          <input type="text" class="form-control has-feedback-left" name="unity" id="unity-create" placeholder="<?PHP echoLang("actuators_create_unity"); ?>" required="required">
                          <span class="fa fa-italic form-control-feedback left" aria-hidden="true"></span>
                        </div>
                      </div>

                      <div class="form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                          <input type="number" class="form-control has-feedback-left" name="decimal" id="decimal-create" placeholder="<?PHP echoLang("actuators_create_decimal"); ?>" required="required">
                          <span class="fa fa-terminal form-control-feedback left" aria-hidden="true"></span>
                        </div>
                      </div>
                    </div>

                    <div class="form-side-right col-md-6 col-ms-6 col-xs-12">

                      <div class="form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                          <label for="sensor"><?PHP echoLang("actuators_create_choose"); ?></label>
                          <select class="form-control" name="sensor" id="sensor" required="required">
                            <option value="nothing" required="required"><?PHP echoLang("actuators_create_choose_no"); ?></option>
                            <?PHP
                              $sensors = $connect->query("SELECT id, name FROM ".$index["database_table_sensors"])->fetchAll();
                              foreach($sensors as $s)
                                echo '<option value"'.$s["id"].'" required="required">'.$s["name"]." (".$s["id"].")</option>";
                            ?>
                          </select>
                        </div>
                      </div>
                      <div class="form-group">
                        <div class="col-md-9 col-sm-9 col-xs-12">
                          <div class="">
                            <label>
                              <input name="only_system" type="checkbox" class="js-switch" /> <?PHP echoLang("actuators_create_only_system"); ?>
                            </label>
                          </div>
                          <div class="">
                            <label>
                              <input name="do_log" type="checkbox" class="js-switch" /> <?PHP echoLang("actuators_create_do_log"); ?>
                            </label>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group col-md-12 col-ms-12 col-xs-12">
                      <div class="ln_solid"></div>
                      <div class="alignright">
                        <button type="submit" class="btn btn-success right"><?PHP echoLang("actuators_create_button"); ?></button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>

            <div class="clearfix"></div>
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

    <!-- Modal -->
    <div class="modal" id="the-modal" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="x_panel">
          <div class="row x_title">
            <h2>Title</h2>
            <ul class="nav navbar-right panel_toolbox">
              <li><a title="<?PHP echoLang("panel_button_help"); ?>" class="link" target="_blank" href="<?PHP echo $index["system_url"].$index["loc_page_help"].""; ?>"><i class="fa fa-question"></i></a></li>
              <li><a title="<?PHP echoLang("panel_button_dropdown"); ?>" class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
              <li><a title="<?PHP echoLang("panel_button_close"); ?>" data-dismiss="modal"><i class="fa fa-close"></i></a></li>
            </ul>
          </div>
          <div class="col-md-12 col-sm-12 col-xs-12 x_content">
            <p>Content</p>
          </div>
        </div>
      </div>
    </div>
  
	  <!-- Basic Scripts Files -->
    <script src="<?PHP echo $index["system_url"].$index["file_jQuery"];             ?>"></script><!-- jQuery               -->
    <script src="<?PHP echo $index["system_url"].$index["file_bootstrap_JS"];       ?>"></script><!-- Bootstrap            -->
    <script src="<?PHP echo $index["system_url"].$index["file_nProgress_JS"];       ?>"></script><!-- NProgress            -->
    <script src="<?PHP echo $index["system_url"].$index["file_progress_bar_JS"];    ?>"></script><!-- Progress Bar         -->
    <script src="<?PHP echo $index["system_url"].$index["file_moment"];             ?>"></script><!-- Moment               -->
    <script src="<?PHP echo $index["system_url"].$index["file_daterangepicker_JS"]; ?>"></script><!-- DateRangePicker      -->
    <script src="<?PHP echo $index["system_url"].$index["file_date_JS"];            ?>"></script><!-- Date                 -->
    <script src="<?PHP echo $index["system_url"].$index["file_flot"];               ?>"></script><!-- Flot                 -->
    <script src="<?PHP echo $index["system_url"].$index["file_flot_pie"];           ?>"></script><!-- Flot Pie             -->
    <script src="<?PHP echo $index["system_url"].$index["file_flot_time"];          ?>"></script><!-- Flot Time            -->
    <script src="<?PHP echo $index["system_url"].$index["file_flot_stack"];         ?>"></script><!-- Flot Stack           -->
    <script src="<?PHP echo $index["system_url"].$index["file_flot_resize"];        ?>"></script><!-- Flot Resize          -->
    <script src="<?PHP echo $index["system_url"].$index["file_flot_order_bars"];    ?>"></script><!-- Flot Order Bars      -->
    <script src="<?PHP echo $index["system_url"].$index["file_flot_spline"];        ?>"></script><!-- Flot Spline          -->
    <script src="<?PHP echo $index["system_url"].$index["file_dataTables_JS"];      ?>"></script><!-- Data Table           -->
    <script src="<?PHP echo $index["system_url"].$index["file_dataTables_JS_BS"];   ?>"></script><!-- Data Table BS        -->
    <script src="<?PHP echo $index["system_url"].$index["file_switchery_JS"];       ?>"></script><!-- Switchery            -->
    <script src="<?PHP echo $index["system_url"].$index["file_basic_JS"];           ?>"></script><!-- Custom Theme Scripts -->
	
  </body>
</html><?PHP 
}catch(PDOException $Ex) {
  echo $Ex;
}
?>