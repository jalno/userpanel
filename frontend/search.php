<?php
use \packages\base\translator;
use \packages\userpanel;
$this->the_header();
?>
<!-- start: PAGE CONTENT -->
<div class="row">
	<div class="col-md-12">
		<!-- start: SEARCH RESULT -->
		<div class="search-classic">
			<form action="<?php echo userpanel\url('search'); ?>" method="get" class="form-inline">
				<div class="input-group well">
					<input type="text" class="form-control" name="word" value="<?php echo $this->getDataForm('word'); ?>" placeholder="<?php echo translator::trans('searchbox.placeholder'); ?>">
					<span class="input-group-btn">
						<button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> <?php echo translator::trans('search'); ?></button>
					</span>
				</div>
			</form>
			<?php if($this->getTotalResults()){ ?>
				<h3><?php echo translator::trans('search.result.byWord-Count', array(
					'word' => $this->getDataForm('word'),
					'count' => $this->getTotalResults()
				)); ?></h3>
				<hr>
				<?php
				$first = true;
				foreach($this->getResults() as $result){
					if($first){
						$first = false;
					}else{
						echo("<hr>");
					}

				?>
				<div class="search-result">
					<h4><a href="<?php echo $result->getLink(); ?>"><?php echo $result->getTitle(); ?></a></h4>
					<p><?php echo $result->getDescription(); ?></p>
				</div>
			<?php
				}
				$this->paginator();
			}
			?>
		</div>
		<!-- end: SEARCH RESULT -->
	</div>
</div>
<!-- end: PAGE CONTENT-->
<?php $this->the_footer(); ?>
