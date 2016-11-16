<?php
use \packages\base;
use \packages\base\frontend;
use \packages\base\frontend\theme;


$this->addJSFile(theme::url('assets/plugins/flot/jquery.flot.js'));
$this->addJSFile(theme::url('assets/plugins/flot/jquery.flot.pie.js'));
$this->addJSFile(theme::url('assets/plugins/flot/jquery.flot.resize.min.js'));
$this->addJSFile(theme::url('assets/plugins/jquery.sparkline/jquery.sparkline.js'));
$this->addJSFile(theme::url('assets/plugins/jquery-easy-pie-chart/jquery.easy-pie-chart.js'));
$this->addJSFile(theme::url('assets/plugins/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js'));
$this->addJSFile(theme::url('assets/plugins/fullcalendar/fullcalendar/fullcalendar.js'));
$this->addJSFile(theme::url('assets/js/index.js'));
$this->addJS('jQuery(document).ready(function() {Index.init();});');
$this->the_header();
?>
<!-- start: PAGE CONTENT -->

<?php
echo $this->generateShortcuts();
echo $this->generateRows(); ?>
<?php
require_once('footer.php');
?>
