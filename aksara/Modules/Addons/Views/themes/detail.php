<?php
	$carousel										= null;
	$attribution									= null;
	
	if(isset($detail->screenshot) && $detail->screenshot)
	{
		foreach($detail->screenshot as $key => $val)
		{
			if(file_exists(ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . $detail->folder . DIRECTORY_SEPARATOR . str_replace(array('../', '..\\', './', '.\\'), '', $val->src)))
			{
				$screenshot							= base_url('themes/' . $detail->folder . '/' . str_replace(array('../', '..\\', './', '.\\'), '', $val->src));
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
					<?php if(sizeof($detail->screenshot) > 1) { ?>
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
				<?php echo ($detail->type == 'backend' ? '<span class="badge bg-dark float-end">' . phrase('back_end') . '</span>' : '<span class="badge bg-success float-end">' . phrase('front_end') . '</span>'); ?>
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
		<div class="col-md-6 col-lg-7">
			<a href="<?php echo current_page('../update', array('item' => $detail->folder)); ?>" class="btn btn-outline-success btn-sm --modal">
				&nbsp; 
				<i class="mdi mdi-auto-fix"></i>
				<?php echo phrase('update'); ?>
				&nbsp; 
			</a>
		</div>
		<div class="col-md-6 col-lg-5">
			<div class="row">
				<div class="col-sm-4">
					<div class="d-grid">
						<?php if(($detail->type == 'backend' && $detail->folder == get_setting('backend_theme')) || ($detail->type == 'frontend' && $detail->folder == get_setting('frontend_theme'))) { ?>
						<a href="<?php echo current_page('../customize', array('theme' => $detail->folder)); ?>" class="btn btn-dark btn-sm --modal">
							<i class="mdi mdi-cogs"></i>
							<?php echo phrase('customize'); ?>
						</a>
						<?php } else { ?>
						<a href="<?php echo current_page('../activate'); ?>" class="btn btn-success btn-sm --modal">
							<i class="mdi mdi-check"></i>
							<?php echo phrase('activate'); ?>
						</a>
						<?php } ?>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="d-grid">
						<a href="<?php echo base_url(('backend' == $detail->type ? 'dashboard' : null), array('aksara_mode' => 'preview-theme', 'aksara_theme' => $detail->folder, 'integrity_check' => $detail->integrity)); ?>" class="btn btn-outline-primary btn-sm" target="_blank">
							<i class="mdi mdi-magnify"></i>
							<?php echo phrase('preview'); ?>
						</a>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="d-grid">
						<a href="<?php echo current_page('../delete', array('item' => $detail->folder)); ?>" class="btn btn-outline-danger btn-sm --modal">
							<i class="mdi mdi-window-close"></i>
							<?php echo phrase('delete'); ?>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
