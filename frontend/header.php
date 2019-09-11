<?php
use packages\base;
use packages\base\{frontend\Theme, Translator};
use packages\userpanel;
use packages\userpanel\Authentication;
use themes\clipone\{breadcrumb, Navigation, ViewTrait};
?>
<!DOCTYPE html>
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
		<?php
			if ($this->getFavicon()) {
				echo '<link rel="icon" href="' . $this->getFavicon() . '" type="image/x-icon" />';
			}
		?>
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<?php
		$description = $this->getDescription();
		if($description){
			echo("<meta content=\"{$description}\" name=\"description\" />");
		}
		$this->loadCSS();
		?>
	</head>
	<body class="<?php echo $this->genBodyClasses(); ?>">
		<!-- start: HEADER -->
		<div class="navbar navbar-inverse <?php if ($this->isFixedHeader()) echo "navbar-fixed-top"; ?>">
			<!-- start: TOP NAVIGATION CONTAINER -->
			<div class="container">
				<div class="navbar-header">
					<button data-target=".navbar-collapse" data-toggle="collapse" class="navbar-toggle" type="button"><span class="clip-list-2"></span></button>
					<a class="navbar-brand" href="<?php echo userpanel\url(''); ?>"><?php echo $this->getLogoHTML(); ?></a>
				</div>
				<div class="navbar-tools">
					<!-- start: TOP NAVIGATION MENU -->
					<ul class="nav navbar-right">
						<!-- start: USER DROPDOWN -->
						<li class="dropdown current-user">
							<a data-toggle="dropdown" data-hover="dropdown" class="dropdown-toggle" data-close-others="true" href="#">
								<img src="<?php echo $this->getSelfAvatarURL(); ?>" width="30" height="30" class="circle-img" alt="">
								<span class="username"><?php echo authentication::getName(); ?></span>
								<i class="clip-chevron-down"></i>
							</a>
							<ul class="dropdown-menu">
								<?php
								if($this->canViewProfile()){
								?>
								<li><a href="<?php echo userpanel\url('profile/view'); ?>"><i class="clip-user-2"></i>&nbsp;<?php echo translator::trans('profile.view'); ?></a></li>
								<li class="divider"></li>
								<?php } ?>
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
									<form class="sidebar-search" action="<?php echo userpanel\url('search'); ?>" method="get">
										<div class="form-group">
											<input type="text" name="word" placeholder="<?php echo translator::trans('searchbox.placeholder'); ?>">
											<button class="submit"><i class="clip-search-3"></i></button>
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
					<div class="row">
						<div class="col-xs-12 errors">
						<?php
						if($errorcode = $this->getErrorsHTML()){
							echo $errorcode;
						}
						?></div>
					</div>
