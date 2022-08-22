<?php
	$carousel										= null;
	$attribution									= null;
	
	if(isset($detail->screenshot) && $detail->screenshot)
	{
		foreach($detail->screenshot as $key => $val)
		{
			if(file_exists(ROOTPATH . 'modules' . DIRECTORY_SEPARATOR . $detail->folder . DIRECTORY_SEPARATOR . $detail->screenshot[0]->src))
			{
				$screenshot							= base_url('modules/' . $detail->folder . '/' . $val->src);
			}
			else
			{
				$screenshot							= get_image(null, 'placeholder_thumb.png');
			}
			
			$carousel								.= '
				<div class="carousel-item rounded' . (!$key ? ' active' : null) . '">
					<a href="' . $screenshot . '" target="_blank">
						<img src="' . $screenshot . '" class="d-block rounded w-100" alt="' . $val->alt . '">
					</a>
				</div>
			';
		}
	}
	
	if(isset($detail->attribution) && $detail->attribution)
	{
		foreach($detail->attribution as $key => $val)
		{
			$attribution							.= '
				<div class="row">
					<div class="col-4">
						<label class="mb-0 text-muted">
							' . $key . '
						</label>
					</div>
					<div class="col-8">
						<label class="mb-0">
							' . $val . '
						</label>
					</div>
				</div>
			';
		}
	}
?>
<div class="container-fluid pt-3 pb-3">
	<div class="row">
		<div class="col-md-6 col-lg-7">
			<div class="position-relative rounded" style="overflow: hidden">
				<div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
					<div class="carousel-inner">
						<?php echo $carousel; ?>
					</div>
					<?php if(isset($detail->screenshot) && sizeof($detail->screenshot) > 1) { ?>
						<a class="carousel-control-prev gradient-right" href="#carouselExampleControls" role="button" data-slide="prev">
							<span class="carousel-control-prev-icon" aria-hidden="true"></span>
						</a>
						<a class="carousel-control-next gradient-left" href="#carouselExampleControls" role="button" data-slide="next">
							<span class="carousel-control-next-icon" aria-hidden="true"></span>
						</a>
					<?php } ?>
				</div>
			</div>
		</div>
		<div class="col-md-6 col-lg-5">
			<h5>
				<?php echo $detail->name; ?>
			</h5>
			<hr class="mt-1 mb-1" />
			<div class="row">
				<div class="col-4">
					<label class="mb-0 text-muted">
						<?php echo phrase('author'); ?>
					</label>
				</div>
				<div class="col-8">
					<label class="mb-0">
						<?php echo (isset($detail->website) ? '<a href="' . $detail->website . '" target="_blank"><b>' . $detail->author . '</b></a>' : '<b>' . $detail->author . '</b>'); ?>
					</label>
				</div>
			</div>
			<div class="row">
				<div class="col-4">
					<label class="mb-0 text-muted">
						<?php echo phrase('version'); ?>
					</label>
				</div>
				<div class="col-8">
					<label class="mb-0">
						<?php echo $detail->version; ?>
					</label>
				</div>
			</div>
			<?php echo $attribution; ?>
			<hr class="mt-1" />
			<div class="mb-0">
				<?php echo nl2br($detail->description); ?>
			</div>
		</div>
	</div>
	<hr class="row" />
	<div class="row">
		<div class="col-md-6 offset-md-6 col-lg-5 offset-lg-7">
			<div class="row">
				<div class="col-sm-6">
					<div class="d-grid">
						<a href="<?php echo current_page('../update', array('item' => $detail->folder)); ?>" class="btn btn-outline-primary btn-sm" target="_blank">
							<i class="mdi mdi-auto-fix"></i>
							<?php echo phrase('update'); ?>
						</a>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="d-grid">
						<a href="<?php echo current_page('../delete', array('item' => $detail->folder)); ?>" class="btn btn-outline-danger btn-sm --modal">
							<i class="mdi mdi-delete"></i>
							<?php echo phrase('uninstall'); ?>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
