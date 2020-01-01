<?php
use packages\base;
use packages\base\Translator;
$codeLang = Translator::getCodeLang();
$availableLangs = Translator::getAvailableLangs();
$isRTL = Translator::getLang()->isRTL();
$direction = ($isRTL) ? "left" : "right"
?>

<!DOCTYPE html>
<!--[if IE 8]><html class="ie8 no-js" lang="en"><![endif]-->
<!--[if IE 9]><html class="ie9 no-js" lang="en"><![endif]-->
<!--[if !IE]><!-->
<html lang="en" class="no-js">
	<!--<![endif]-->
	<!-- start: HEAD -->
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
	</head>
	<!-- end: HEAD -->
	<!-- start: BODY -->
	<body class="login example1 <?php echo $this->genBodyClasses(); ?>">
		<div class="main-login col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3">
			<div class="logo<?php if (count($availableLangs) == 1) { echo " text-center";} ?>"><span><?php echo $this->getLogoHTML(); ?></span>
			<?php 
				if (count($availableLangs) > 1) {
			?>
				<div class="btn-group lang-select">
					<button class="btn dropdown-toggle btn-lang-select" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
						<span class="flag-icon flag-icon-<?php echo strtolower(substr($codeLang, -2)); ?>"></span>
						<?php echo t("translations.langs." . Translator::getShortCodeLang()); ?>
					</button>
					<div class="dropdown-menu">
						<?php
						foreach ($availableLangs as $lang) {
							if ($lang == $codeLang) {
								continue;
							}
							$shortCode = Translator::getShortCodeLang($lang);
							$direction = Translator::getLang($lang)->isRTL() ? "rtl" : "ltr";
						?>
							<li class="<?php echo $direction ?>">
								<a href="<?php echo base\url(".", array("@lang" => $shortCode)); ?>">
									<span class="flag-icon flag-icon-<?php echo strtolower(substr($lang, -2)); ?>"></span>
									<span class="lang-text"><?php echo t("translations.langs." . $shortCode) ?></span>
								</a>
							</li>
					</div>
					<?php
						}
					?>
				</div>
			<?php
				}
			?>
			</div>
