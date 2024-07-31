<?php
require_once 'header.php';
use packages\base\Translator;
use packages\userpanel;

$usertype = $this->getUserType();
?>
<!-- start: PAGE CONTENT -->
<div class="row">
	<div class="col-sm-12">
		<form action="<?php echo userpanel\url('settings/usertypes/delete/'.$usertype->id); ?>" method="POST" role="form" id="delete_form" class="form-horizontal">
			<div class="alert alert-block alert-warning fade in">
				<h4 class="alert-heading"><i class="fa fa-exclamation-triangle"></i> <?php echo t('attention'); ?>!</h4>
				<p>
					<?php
                    echo t('usertype.delete.warning', ['usertype_id' => $usertype->id]);
?>
				</p>
				<p>
					<a href="<?php echo userpanel\url('settings/usertypes'); ?>" class="btn btn-light-grey"><i class="fa fa-chevron-circle-<?php echo Translator::isRTL() ? 'right' : 'left'; ?>"></i> <?php echo t('back'); ?></a>
					<button type="submit" class="btn btn-danger"><i class="fa fa-trash"></i> <?php echo t('usertype.delete'); ?></button>
				</p>
			</div>
		</form>

	</div>
</div>
<?php
require_once 'footer.php';
