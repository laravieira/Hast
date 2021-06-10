<?PHP

// Start code
include "../build/php/data.php";

// Login Verify
if(session_status() != PHP_SESSION_ACTIVE)
	session_start();
if(!isset($_SESSION["email"]) || empty($_SESSION["email"])) {
	session_destroy();
	header("Location: ".$general["root"].$general["pageLogin"]);
	return true;
}
try {
	// Conecta no bando de dados
	$Connect = new PDO("mysql:host=".$GLOBALS["database"]["host"].";dbname=".$GLOBALS["database"]["dbname"], $GLOBALS["database"]["user"], $GLOBALS["database"]["pass"]);
	$Connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	if(isset($_GET["change"])) {
		if($_GET["change"] == "image" && $_FILES["image"]["size"] < 10) {
			if($_FILES["image"]["size"] < 10000 && strtolower(pathinfo(basename($_FILES["image"]["name"]),PATHINFO_EXTENSION)) == "png")
			// If user already have image
			$db = $Connect->query("SELECT * FROM ".$GLOBALS["database"]["t_users"]." WHERE email='".$_SESSION["email"]."'")->fetch();
			if(!empty($db["image_profile"])) {
				move_uploaded_file($_FILES["image"]["tmp_name"], $general["profileIconsPath"].$db["image_profile"].".png");
			}else {
				if(move_uploaded_file($_FILES["image"]["tmp_name"], $general["profileIconsPath"].base64_encode($_FILES["image"]["tmp_name"].$db["name"].".png"))) {
					
				}
			}
		}
	}
?><!DOCTYPE html>
<html lang="<?PHP echo $general["lang"]; ?>">
	<head>

		<!-- Set basic configs -->
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<!-- Set FavIcon File and Title -->
		<link rel="icon" href="<?PHP echo $general["root"].$general["favicon"]; ?>" type="image/ico" />
		<title><?PHP echo $general["name"]." | Perfil" ?></title>

		<!-- Basic CSS Files -->
		<link rel="stylesheet" href="<?PHP echo $general["root"].$general["bootstrapCSS"]; ?>"><!-- Bootstrap          -->
		<link rel="stylesheet" href="<?PHP echo $general["root"].$general["font"];         ?>"><!-- Font Awesome       -->
		<link rel="stylesheet" href="<?PHP echo $general["root"].$general["nProgressCSS"]; ?>"><!-- NProgress          -->
		<link rel="stylesheet" href="<?PHP echo $general["root"].$general["basicCSS"];     ?>"><!-- Custom Theme Style -->
	
	</head>
	
	<body class="nav-md">
		<div class="container body">
			<div class="main_container">
				<!-- sidebar -->
				<div class="col-md-3 left_col">
					<div class="left_col scroll-view">
						<div class="navbar nav_title" style="border: 0;">
							<a href="<?PHP echo $general["root"].$general["pageDashboard"]; ?>" class="site_title">
								<img class="fa fa-paw" src="<?PHP echo $general["root"].$general["icon"]; ?>"></img>
								<span><?PHP echo $general["name"]; ?></span>
							</a>
						</div>
						<div class="clearfix"></div>
						
						<!-- sidebar menu -->
						<div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
							<div class="menu_section direct">
								<ul class="nav side-menu">
									<li><a href="<?PHP echo $general["root"].$general["pageDashboard"]; ?>"><i class="fa fa-home"></i>Painel Principal</a></li>
								</ul>
							</div>
							<div class="menu_section">
								<h3>SETORES</h3>
								<ul class="nav side-menu"><?PHP
									// Exibe o menu das seções, caso tenha seções para este usuário
									$db = $Connect->query("SELECT * FROM ".$GLOBALS["database"]["t_sections"]." WHERE email='".$_SESSION["email"]."'");
									if($db->rowCount() > 0) {
										foreach($db->fetchAll() as $data) {
											echo '<li><a href="'.$general["root"].$general["pageSection"].'?id='.$data["id"].'"><i class="';
											if(!empty($data["icon"]))
												echo 'fa fa-'.$data["icon"];
											else
												echo 'fa fa-tag';
											echo '"></i>'.$data["name"].'</a></li>';
										}
									}else {
										echo "<p class='side-menu_info'>Não há setores disponíveis</p>";//<br><a href='".$general["root"].$general["pageSections"]."'> Gerenciar setores</a></p>";
									}
								?></ul>
							</div>
							<?PHP
								// Exibe o menu de gerenciamento de acordo com a hierarquia
								$db = $Connect->query("SELECT * FROM ".$GLOBALS["database"]["t_users"]." WHERE email='".$_SESSION["email"]."'")->fetch();
								if($db["hierarchy"] == 0) {
									echo '<div class="menu_section"><h3>GERENCIAMENTO</h3><ul class="nav side-menu">';
									echo '<li><a href="'.$general["root"].$general["pageSections"].'"><i class="fa fa-cubes"></i>Setores</a></li>';
									echo '<li><a href="'.$general["root"].$general["pageSensors"].'"><i class="fa fa-puzzle-piece"></i>Sensores</a></li>';
									echo '<li><a href="'.$general["root"].$general["pageActuators"].'"><i class="fa fa-toggle-off"></i>Atuadores</a></li>';
									echo '<li><a href="'.$general["root"].$general["pageAutomation"].'"><i class="fa fa-cogs"></i>Ações Automáticas</a></li>';
									echo '<li><a href="'.$general["root"].$general["pageGraphics"].'"><i class="fa fa-area-chart"></i>Gráficos</a></li>';
									echo '</ul></div>';
								}else if($db["hierarchy"] == 1) {
									echo '<div class="menu_section"><h3>GERENCIAMENTO</h3><ul class="nav side-menu">';
									echo '<li><a href="'.$general["root"].$general["pageSections"].'"><i class="fa fa-cubes"></i>Setores</a></li>';
									echo '<li><a href="'.$general["root"].$general["pageAutomation"].'"><i class="fa fa-cogs"></i>Ações Automáticas</a></li>';
									echo '<li><a href="'.$general["root"].$general["pageGraphics"].'"><i class="fa fa-area-chart"></i>Gráficos</a></li>';
									echo '</ul></div>';
								}
							?>
						</div>
						<!-- /sidebar menu -->

						<!-- /menu footer buttons -->
						<div class="sidebar-footer hidden-small">
				  			<a data-toggle="tooltip" data-placement="top" title="Settings" href="<?PHP echo $general["root"].$general["pageConfig"];  ?>">
								<span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
				  			</a>
				  			<a data-toggle="tooltip" data-placement="top" title="FullScreen">
								<span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
				  			</a>
				  			<a data-toggle="tooltip" data-placement="top" title="Help" href="<?PHP echo $general["root"].$general["pageHelp"];    ?>">
								<span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span>
				  			</a>
				  			<a data-toggle="tooltip" data-placement="top" title="Logout" href="<?PHP echo $general["root"].$general["pageLogout"];  ?>">
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
									<a class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
										<?PHP
											// Busca pelo usuário
											$db = $Connect->query("SELECT * FROM ".$GLOBALS["database"]["t_users"]." WHERE email='".$_SESSION["email"]."'")->fetch();
											if(empty($db["image_profile"]))
												echo '<i class="fa fa-user" style="margin: 0px 10px 0px 0px">&nbsp;&nbsp;&nbsp;'.$db["name"].'</i>';
											else
												echo '<img src="'.$GLOBALS["general"]["root"].$GLOBALS["general"]["profileIconsPath"].$db["image_profile"].'.png" alt="">'.$db["name"]."&nbsp;&nbsp;";
										?>
										<span class=" fa fa-angle-down"></span>
									</a>
									<ul class="dropdown-menu dropdown-usermenu pull-right">
										<li><a href="<?PHP echo $general["root"].$general["pageProfile"]; ?>">Perfil</a></li>
										<li><a href="<?PHP echo $general["root"].$general["pageConfig"];  ?>">Configurações</a></li>
										<li><a href="<?PHP echo $general["root"].$general["pageHelp"];    ?>">Ajuda</a></li>
										<li><a href="<?PHP echo $general["root"].$general["pageLogout"];  ?>"><i class="fa fa-sign-out pull-right"></i>Sair</a></li>
									</ul>
								</li>

								<!--<li role="presentation" class="dropdown">
									<a class="dropdown-toggle info-number" data-toggle="dropdown" aria-expanded="false">
										<i class="fa fa-envelope-o"></i>
										<span class="badge bg-green">6</span>->
									</a>
									<ul id="menu1" class="dropdown-menu list-unstyled msg_list" role="menu">
										<li>
											<div class="text-center">
												<h4>Não há notificações</h4>
												<a>
													<strong>Ver Todas</strong>
													<i class="fa fa-angle-right"></i>
												</a>
											</div>
										</li>
									</ul>
								</li>-->
							</ul>
						</nav>
					</div>
				</div>
				<!-- /top navigation -->

				<!-- page content -->
				<div class="right_col" role="main">
					<div class="row">
						<div class="col-md-4 col-sm-4 col-xs-12">
							<div class="x_panel">
								<div class="x_title">
									<h2>Imagem de Perfil</h2>
									<div class="clearfix"></div>
								</div>
								<div class="x_content">
									<div class="row">
										<div class="col-md-2 col-sm-2 col-xs-2"></div>
										<div class="image-profile col-md-8 col-sm-8 col-xs-8"><?PHP 
										  $db = $Connect->query("SELECT * FROM ".$GLOBALS["database"]["t_users"]." WHERE email='".$_SESSION["email"]."'")->fetch();
										  if(empty($db["image_profile"]))
											echo '<i class="fa fa-user" style="margin: 0px 10px 0px 0px"></i>';
										  else
											echo '<img src="'.$GLOBALS["general"]["root"].$GLOBALS["general"]["profileIconsPath"].$db["image_profile"].'.png" alt="">';
										?></div>
									</div>
									<div class="divider-dashed"></div>

									<form class="form-horizontal form-label-left" method="post" action="?change=image"  enctype="multipart/form-data">
									  <div class="image-profile form-group">
										<h4>Enviar uma nova imagem</h4>
										<div class="row">
										  <div class="input-group">
											<input class="form-control" type="file" required="required" name="image">
											<span class="input-group-btn">
												<button class="btn btn-primary" type="submit">Enviar</button>
											</span>
										  </div>
										</div>
									  </div>
									</form>
								</div>
							</div>
						</div>

						<div class="col-md-8 col-sm-8 col-xs-12">
							<div class="x_panel">
								<div class="x_title">
									<h2>Informações</h2>
									<div class="clearfix"></div>
								</div>
								<div class="x_content">
									
									<!-- Form to change name -->
									<form class="form-horizontal form-label-left" method="post" action="?change=name">
										<div class="form-group">
											<label class="col-sm-3 control-label" for="name">Nome</label>
											<div class="col-sm-9">
												<div class="input-group">
													<input class="form-control" type="text" name="name" data-validate-length-range="6" data-validate-words="2" required="required">
													<span class="input-group-btn">
														<button class="btn btn-primary" type="submit">Mudar</button>
													</span>
												</div>
											</div>
										</div>
									</form>
									
									<div class="divider-dashed"></div>
									
									<!-- Form to change email -->
									<form class="form-horizontal form-label-left" method="post" action="?change=email">
										<div class="form-group">
											<label class="col-sm-3 control-label" for="email">E-mail</label>
											<div class="col-md-9 col-sm-9 col-xs-12">
												<div class="input-group">
													<input class="form-control" type="text" required="required" name="email">
													<span class="input-group-btn">
														<button class="btn btn-primary" type="submit">Mudar</button>
													</span>
												</div>
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label" for="confirm-email">Confirme e-mail</label>
											<div class="col-md-9 col-sm-9 col-xs-12">
												<div class="input-group col-md-12 col-sm-12 col-xs-12" style="padding-right: 5px">
													<input class="form-control" type="text" data-validate-linked="email" required="required" name="confirm-email">
												</div>
											</div>
										</div>
									</form>
									
									<div class="divider-dashed"></div>
									
									<!-- Form to change password -->
									<form class="form-horizontal form-label-left" method="post" action="?change=pass">
										<div class="form-group">
											<label class="col-sm-3 control-label" for="pass">Senha</label>
											<div class="col-md-9 col-sm-9 col-xs-12">
												<div class="input-group">
													<input class="form-control" type="password" name="pass" required="required" data-validate-length="6,8">
													<span class="input-group-btn">
														<button class="btn btn-primary" type="submit">Mudar</button>
													</span>
												</div>
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label" for="confirm-pass">Confirme Senha</label>
											<div class="col-md-9 col-sm-9 col-xs-12">
												<div class="input-group col-md-12 col-sm-12 col-xs-12" style="padding-right: 5px">
													<input class="form-control col-sm-12" type="password" name="confirm-pass" required="required" data-validate-linked="pass">
												</div>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- /page content -->

				<!-- footer content -->
				<footer>
					<div class="pull-right"><?PHP
						echo "&copy; ".date("Y")." - <a title='";
						echo $general["name"]."' href='".$general["officialPage"]."'>".$general["name"]."</a> Manager, by ";
						echo '<a href="https://jwdouglas.net">JWDouglas Vieira</a>. | ';
						echo 'Using Gentelella theme by <a href="https://colorlib.com">Colorlib</a>.';
					?></div>
					<div class="clearfix"></div>
				</footer>
				<!-- /footer content -->

			</div>
		</div>

		<!-- Basic Scripts Files -->
		<script src="<?PHP echo $general["root"].$general["jQuery"];      ?>"></script><!-- jQuery               -->
		<script src="<?PHP echo $general["root"].$general["bootstrapJS"]; ?>"></script><!-- Bootstrap            -->
		<script src="<?PHP echo $general["root"].$general["nProgressJS"]; ?>"></script><!-- NProgress            -->
		<script src="<?PHP echo $general["root"].$general["validator"];   ?>"></script><!-- Validator            -->
		<script src="<?PHP echo $general["root"].$general["basicJS"];     ?>"></script><!-- Custom Theme Scripts -->

	</body>
</html><?PHP

}catch(PDOException $Ex) {
	return false;
}

?>
