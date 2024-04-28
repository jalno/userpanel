<?php
use packages\base;
use packages\base\Translator;
use packages\userpanel;
use packages\userpanel\Authentication;
use themes\clipone\Breadcrumb;
use themes\clipone\Navigation;

$codeLang = Translator::getCodeLang();
$shortCodeLang = Translator::getShortCodeLang();
$availableLangs = Translator::getAvailableLangs();
?>
<!DOCTYPE html>
<!--[if IE 8]><html class="ie8 no-js" lang="<?php echo $shortCodeLang; ?>"><![endif]-->
<!--[if IE 9]><html class="ie9 no-js" lang="<?php echo $shortCodeLang; ?>"><![endif]-->
<!--[if !IE]><!-->
<html lang="<?php echo $shortCodeLang; ?>" class="no-js">
	<!--<![endif]-->
	<head>
		<title><?php echo $this->getTitle(); ?></title>
		<meta charset="utf-8" />
		<!--[if IE]><meta http-equiv='X-UA-Compatible' content="IE=edge,IE=9,IE=8,chrome=1" /><![endif]-->
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
		<?php
            if ($this->getFavicon()) {
                echo '<link rel="icon" href="'.$this->getFavicon().'" type="image/x-icon" />';
                echo '<link rel="shortcut icon" href="'.$this->getFavicon().'" type="image/x-icon" />';
            }
?>
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<?php
$description = $this->getDescription();
if ($description) {
    echo "<meta content=\"{$description}\" name=\"description\" />";
}
$this->loadCSS();
?>
	</head>
	<body class="<?php echo $this->genBodyClasses(); ?>">
		<div class="navbar navbar-inverse <?php if ($this->isFixedHeader()) {
		    echo 'navbar-fixed-top';
		} ?>">
			<div class="container">
				<div class="navbar-header">
					<button data-target=".navbar-collapse" data-toggle="collapse" class="navbar-toggle" type="button"><span class="clip-list-2"></span></button>
					<a class="navbar-brand" href="<?php echo userpanel\url(''); ?>"><?php echo $this->getLogoHTML(); ?></a>
				</div>
				<div class="navbar-tools">
					<ul class="nav navbar-right">
						<?php
		                    if (count($availableLangs) > 1) {
		                        ?>
						<li class="dropdown nav-lang-selector">
							<a data-toggle="dropdown" data-hover="dropdown" class="dropdown-toggle" data-close-others="true" href="#">
								<!-- <i class="fa fa-globe"></i> -->
								<span class="flag-icon flag-icon-<?php echo strtolower(substr($codeLang, -2)); ?>"></span>
								<span class="lang-name"><?php echo t('translations.langs.'.$shortCodeLang); ?></span>
							</a>
							<ul class="dropdown-menu langs-dropdown-menu">
								<?php
		                                foreach ($availableLangs as $lang) {
		                                    if ($lang == $codeLang) {
		                                        // continue;
		                                    }
		                                    $shortCode = Translator::getShortCodeLang($lang);
		                                    $direction = Translator::getLang($lang)->isRTL() ? 'rtl' : 'ltr';
		                                    ?>
									<li class="lang <?php echo $direction; ?>">
										<a href="<?php echo base\url('.', ['@lang' => $shortCode]); ?>">
											<span class="flag-icon flag-icon-<?php echo strtolower(substr($lang, -2)); ?>"></span>
											<span class="lang-text"><?php echo t('translations.langs.'.$shortCode); ?></span>
										</a>
									</li>
								<?php
		                                }
		                        ?>
							</ul>
						</li>
						<?php
		                    }
?>

						<li class="dropdown current-user">
							<a data-toggle="dropdown" data-hover="dropdown" class="dropdown-toggle" data-close-others="true" href="#">
								<img src="<?php echo $this->getSelfAvatarURL(); ?>" width="30" height="30" class="circle-img" alt="">
								<span class="username"><?php echo Authentication::getName(); ?></span>
								<i class="clip-chevron-down"></i>
							</a>
							<ul class="dropdown-menu">
								<?php
        if ($this->canViewProfile()) {
            ?>
								<li><a href="<?php echo userpanel\url('profile/view'); ?>"><i class="clip-user-2"></i>&nbsp;<?php echo Translator::trans('profile.view'); ?></a></li>
								<li class="divider"></li>
								<?php } ?>
								<li><a href="<?php echo base\url('userpanel/lock'); ?>"><i class="clip-locked"></i> <?php echo t('userpanel.lock'); ?></a></li>
								<li><a href="<?php echo base\url('userpanel/logout'); ?>"><i class="clip-exit"></i> <?php echo t('userpanel.logout'); ?></a></li>
							</ul>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<div class="main-container">
			<div class="navbar-content">
				<div class="main-navigation navbar-collapse collapse">
					<div class="navigation-toggler">
						<i class="clip-chevron-left"></i>
						<i class="clip-chevron-right"></i>
					</div>
					<ul class="main-navigation-menu">
						<?php echo Navigation::build(); ?>
					</ul>
				</div>
			</div>
			<div class="main-content">
				<div class="container">
					<div class="row">
						<div class="col-sm-12">
							<ol class="breadcrumb">
								<?php echo Breadcrumb::build(); ?>
								<li class="search-box">
									<form class="sidebar-search" action="<?php echo userpanel\url('search'); ?>" method="get">
										<div class="form-group">
											<input type="text" name="word" placeholder="<?php echo Translator::trans('searchbox.placeholder'); ?>">
											<button class="submit"><i class="clip-search-3"></i></button>
										</div>
									</form>
								</li>
							</ol>
							<div class="page-header">
								<h1><?php echo $this->title[count($this->title) - 1];
if ($this->shortdescription) { ?> <small><?php echo $this->shortdescription; ?></small><?php } ?></h1>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12 errors">
						<?php
                        if ($errorcode = $this->getErrorsHTML()) {
                            echo $errorcode;
                        }
?></div>
					</div>
