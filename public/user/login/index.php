<?PHP

// Get links
$index = yaml_parse_file($_SERVER["DOCUMENT_ROOT"]."/"."core/config.yml");
if(!$index) {
  http_response_code(500);
  return true;
}

// Pre-sets
date_default_timezone_set($index["system_timezone"]);
include $_SERVER["DOCUMENT_ROOT"]."/".$index["core_language"];
if(!loadLanguage($_SERVER["DOCUMENT_ROOT"]."/", $index["system_language"])) {
  http_response_code(500);
  return false;
}

// Start session
if(session_status() != PHP_SESSION_ACTIVE) {
	session_start();
}

// Verify session and logout
if(isset($_SESSION["email"]) && !empty($_SESSION["email"]) && !isset($_GET["logout"])) {
  try{
    $Connect = new PDO("mysql:host=".$index["database_general_host"].";dbname=".$index["database_general_name"], $index["database_general_user"], $index["database_general_pass"]);
    $Connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if($Connect->query("SELECT * FROM ".$index["database_table_users"]." WHERE email='".$_SESSION["email"]."'")->rowCount() > 0) {
      header("Location: ".$index["system_url"].$index["loc_page_dashboard"]);
    }else {
      session_unset();
      session_destroy();
    }
  }catch(PDOException $Ex) {}
}

$login    = false;
$logon    = false;
$recovery = false;

// Do logout
if(isset($_GET["logout"])) {
  session_unset();
  session_destroy();
  header("Location: ".$index["system_url"].$index["loc_page_login"]);
}

// Do login
if(isset($_GET["login"])) {
	if(isset($_POST["email"]) && isset($_POST["pass"])) {
    if(!empty($_POST["email"]) && !empty($_POST["pass"])) {
      try{
        $Connect = new PDO("mysql:host=".$index["database_general_host"].";dbname=".$index["database_general_name"], $index["database_general_user"], $index["database_general_pass"]);
        $Connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if($Connect->query("SELECT * FROM ".$index["database_table_users"]." WHERE email='".$_POST["email"]."' AND pass='".$_POST["pass"]."'")->rowCount() > 0) {
          $_SESSION["email"] = $_POST["email"];
          header("Location: ".$index["system_url"].$index["loc_page_dashboard"]);
          return;
        }else {$login = "LOGIN_WRONG";}
      }catch(PDOException $Ex) {echo $Ex; $login = "LOGIN_ERROR";}
    }else {$login = "LOGIN_ERROR";}
  }else {$login = "LOGIN_ERROR";}
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
    <title><?PHP echo $index["system_name"]." | ".lang("login_title"); ?></title>

	<!-- Basic CSS Files -->
    <link rel="stylesheet" href="<?PHP echo $index["system_url"].$index["file_bootstrap_CSS"]; ?>"><!-- Bootstrap          -->
    <link rel="stylesheet" href="<?PHP echo $index["system_url"].$index["file_fonts"];         ?>"><!-- Font Awesome       -->
    <link rel="stylesheet" href="<?PHP echo $index["system_url"].$index["file_nProgress_CSS"]; ?>"><!-- NProgress          -->
    <link rel="stylesheet" href="<?PHP echo $index["system_url"].$index["file_animate"];       ?>"><!-- Animate            -->
    <link rel="stylesheet" href="<?PHP echo $index["system_url"].$index["file_basic_CSS"];     ?>"><!-- Custom Theme Style -->
    <?PHP echo '<script type="text/javascript">';
      if($login)    echo 'var login    = "'.$login.'";';
      if($logon)    echo 'var logon    = "'.$logon.'";';
      if($recovery) echo 'var recovery = "'.$recovery.'";';
    echo '</script>'; ?>
	  
  </head>

  <body class="login">
    <div>
      <a class="hiddenanchor" id="signup"></a>
      <a class="hiddenanchor" id="signin"></a>
      <a class="hiddenanchor" id="recover"></a>

      <div class="login_wrapper">
        <div class="animate form login_form">
          <section class="login_content">
            <form action="?login" method="post">
              <img class="icon" alt="icon" src="<?PHP echo $index["system_url"].$index["loc_favicon"]; ?>">
              <div>
                <input name="email" type="email" class="form-control" placeholder="<?PHP echoLang("login_placeholder_email"); ?>" required="" />
              </div>
              <div>
                <input name="pass" type="password" class="form-control" placeholder="<?PHP echoLang("login_placeholder_pass"); ?>" required="" />
              </div>
              <div>
                <input type="submit" class="btn btn-default submit" value="<?PHP echoLang("login_login_submit"); ?>">
                <p class="reset_pass"><input type="checkbox" name="keep"><?PHP echoLang("login_keep_logged"); ?></p>
              </div>

              <div class="clearfix"></div>

              <div class="separator">
                <p class="change_link"><a href="#recover" class="to_register"><?PHP echoLang("login_pass_recovery"); ?></a></p>
					      <p class="change_link"><?PHP echoLang("login_new_msg"); ?><a href="#signup" class="to_register"> <?PHP echoLang("login_new_link"); ?></a></p>
                <div class="clearfix"></div>
                <br />
                <div class="form-footer">
                  <?PHP echo "&copy; ".date("Y")." | <a href='".$index["system_url"]."' title='".$index["system_name"]."'>".$index["system_name"]." ".lang("fotter_msg_one")."</a>, ".lang("fotter_msg_two").' <a href="https://jwdouglas.net" title="'.lang("fotter_title_jwdouglas").'" target="_blank">JWDouglas Vieira</a>. <br>'.lang("fotter_msg_thee").' <a href="https://github.com/ColorlibHQ/gentelella" title="'.lang("fotter_title_github").'" target="_blank">Gentelella Theme</a> '.lang("fotter_msg_two").' <a href="https://colorlib.com" title="'.lang("fotter_title_colorlib").'" target="_blank">Colorlib</a>.'; ?>
                </div>
              </div>
            </form>
          </section>
        </div>

        <div id="
        " class="animate form registration_form">
          <section class="login_content">
            <form action="?logon" method="post">
              <img class="icon" alt="icon" src="<?PHP echo $index["system_url"].$index["loc_favicon"]; ?>">
              <div>
                <input type="text" class="form-control" placeholder="<?PHP echoLang("login_logon_user"); ?>" required="" />
              </div>
              <div>
                <input type="email" class="form-control" placeholder="<?PHP echoLang("login_placeholder_email"); ?>" required="" />
              </div>
              <div>
                <input type="password" class="form-control" placeholder="<?PHP echoLang("login_placeholder_pass"); ?>" required="" />
              </div>
              <div>
                <input type="password" class="form-control" placeholder="<?PHP echoLang("login_logon_repeat"); ?>" required="" />
              </div>
              <div>
                <input type="submit" class="btn active btn-default" value="<?PHP echoLang("login_logon_submit"); ?>">
              </div>

              <div class="clearfix"></div>

              <div class="separator">
                <p class="change_link"><?PHP echoLang("login_already_msg"); ?>
                  <a href="#signin" class="to_register"> <?PHP echoLang("login_already_link"); ?></a>
                </p>

                <div class="clearfix"></div>
                <br />

                <div>
                  <?PHP echo "&copy; ".date("Y")." | <a href='".$index["system_url"]."' title='".$index["system_name"]."'>".$index["system_name"]." ".lang("fotter_msg_one")."</a>, ".lang("fotter_msg_two").' <a href="https://jwdouglas.net" title="'.lang("fotter_title_jwdouglas").'" target="_blank">JWDouglas Vieira</a>. <br>'.lang("fotter_msg_thee").' <a href="https://github.com/ColorlibHQ/gentelella" title="'.lang("fotter_title_github").'" target="_blank">Gentelella Theme</a> '.lang("fotter_msg_two").' <a href="https://colorlib.com" title="'.lang("fotter_title_colorlib").'" target="_blank">Colorlib</a>.'; ?>
                </div>
              </div>
            </form>
          </section>
        </div>

        <div id="recover" class="animate form recovery_form">
          <section class="login_content">
            <form action="?recover" method="post">
              <img class="icon" alt="icon" src="<?PHP echo $index["system_url"].$index["loc_favicon"]; ?>">
              <div>
                <input type="email" class="form-control" placeholder="<?PHP echoLang("login_placeholder_email"); ?>" required="" />
              </div>
              <div>
                <input type="submit" class="btn active btn-default" value="<?PHP echoLang("login_recovery_submit"); ?>">
              </div>

              <div class="clearfix"></div>

              <div class="separator">
                <p class="change_link"><?PHP echoLang("login_recovery_msg"); ?>
                  <a href="#signin" class="to_register"> <?PHP echoLang("login_recovery_link"); ?></a>
                </p>

                <div class="clearfix"></div>
                <br />

                <div>
                  <?PHP echo "&copy; ".date("Y")." | <a href='".$index["system_url"]."' title='".$index["system_name"]."'>".$index["system_name"]." ".lang("fotter_msg_one")."</a>, ".lang("fotter_msg_two").' <a href="https://jwdouglas.net" title="'.lang("fotter_title_jwdouglas").'" target="_blank">JWDouglas Vieira</a>. <br>'.lang("fotter_msg_thee").' <a href="https://github.com/ColorlibHQ/gentelella" title="'.lang("fotter_title_github").'" target="_blank">Gentelella Theme</a> '.lang("fotter_msg_two").' <a href="https://colorlib.com" title="'.lang("fotter_title_colorlib").'" target="_blank">Colorlib</a>.'; ?>
                </div>
              </div>
            </form>
          </section>
        </div>
      </div>
    </div>
	<style>
    .login_wrapper {
      margin:0px auto;
    }
		img.icon {
			margin: 0px 0px 50px 0px;
      width: 128px;
		}
	</style>
  </body>
</html>
