<?php
use \packages\base;
use \packages\base\frontend\theme;
use \packages\userpanel\authentication;
use \themes\clipone\navigation;
use \themes\clipone\breadcrumb;
?><!DOCTYPE html>
<!--[if IE 8]><html class="ie8 no-js" lang="en"><![endif]-->
<!--[if IE 9]><html class="ie9 no-js" lang="en"><![endif]-->
<!--[if !IE]><!-->
<html lang="en" class="no-js">
	<!--<![endif]-->
	<head>
		<title><?php echo $this->getTitle(); ?></title>
		<meta charset="utf-8" />
		<!--[if IE]><meta http-equiv='X-UA-Compatible' content="IE=edge,IE=9,IE=8,chrome=1" /><![endif]-->
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<?php
		$description = $this->getDescription();
		if($description){
			echo("<meta content=\"{$description}\" name=\"description\" />");
		}
		$this->loadCSS();
		?>
		<!--[if IE 7]>
		<link rel="stylesheet" href="<?php echo theme::url('assets/plugins/font-awesome/css/font-awesome-ie7.min.css'); ?>">
		<![endif]-->
	</head>
	<body class="rtl">
		<!-- start: HEADER -->
		<div class="navbar navbar-inverse navbar-fixed-top">
			<!-- start: TOP NAVIGATION CONTAINER -->
			<div class="container">
				<div class="navbar-header">
					<button data-target=".navbar-collapse" data-toggle="collapse" class="navbar-toggle" type="button"><span class="clip-list-2"></span></button>
					<a class="navbar-brand" href="<?php echo base\url('userpanel'); ?>">CLIP<i class="clip-clip"></i>ONE</a>
				</div>
				<div class="navbar-tools">
					<!-- start: TOP NAVIGATION MENU -->
					<ul class="nav navbar-right">
						<!-- start: USER DROPDOWN -->
						<li class="dropdown current-user">
							<a data-toggle="dropdown" data-hover="dropdown" class="dropdown-toggle" data-close-others="true" href="#">
								<img src="<?php echo theme::url('assets/images/avatar-1-small.jpg'); ?>" class="circle-img" alt="">
								<span class="username"><?php echo authentication::getName(); ?></span>
								<i class="clip-chevron-down"></i>
							</a>
							<ul class="dropdown-menu">
								<li><a href="<?php echo base\url('userpanel/lock'); ?>"><i class="clip-locked"></i>&nbsp;خروج موقت </a></li>
								<li><a href="<?php echo base\url('userpanel/logout'); ?>"><i class="clip-exit"></i> &nbsp;خروج </a></li>
							</ul>
						</li>
						<!-- end: USER DROPDOWN -->
					</ul>
					<!-- end: TOP NAVIGATION MENU -->
				</div>
			</div>
			<!-- end: TOP NAVIGATION CONTAINER -->
		</div>
		<!-- end: HEADER -->
		<!-- start: MAIN CONTAINER -->
		<div class="main-container">
			<div class="navbar-content">
				<!-- start: SIDEBAR -->
				<div class="main-navigation navbar-collapse collapse">
					<!-- start: MAIN MENU TOGGLER BUTTON -->
					<div class="navigation-toggler">
						<i class="clip-chevron-left"></i>
						<i class="clip-chevron-right"></i>
					</div>
					<!-- end: MAIN MENU TOGGLER BUTTON -->
					<!-- start: MAIN NAVIGATION MENU -->
					<ul class="main-navigation-menu">
						<?php
						echo navigation::build();
						?>
					</ul>
					<!-- end: MAIN NAVIGATION MENU -->
				</div>
				<!-- end: SIDEBAR -->
			</div>
			<!-- start: PAGE -->
			<div class="main-content">
				<!-- end: SPANEL CONFIGURATION MODAL FORM -->
				<div class="container">
					<!-- start: PAGE HEADER -->
					<div class="row">
						<div class="col-sm-12">
							<!-- start: PAGE TITLE & BREADCRUMB -->
							<ol class="breadcrumb">
								<?php
								echo breadcrumb::build();
								?>
								<li class="search-box">
									<form class="sidebar-search">
										<div class="form-group">
											<input type="text" placeholder="Start Searching...">
											<button class="submit">
												<i class="clip-search-3"></i>
											</button>
										</div>
									</form>
								</li>
							</ol>
							<div class="page-header">
								<h1><?php echo $this->title[count($this->title)-1]; if($this->shortdescription){ ?> <small><?php echo $this->shortdescription; ?></small><?php } ?></h1>
							</div>
							<!-- end: PAGE TITLE & BREADCRUMB -->
						</div>
					</div>
					<!-- end: PAGE HEADER -->
