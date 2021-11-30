<?php
	$carousel										= null;
	$attribution									= null;
	
	if($detail->screenshot)
	{
		foreach($detail->screenshot as $key => $val)
		{
			$carousel								.= '
				<div class="carousel-item rounded' . (!$key ? ' active' : null) . '">
					<a href="' . $val->src . '" target="_blank">
						<img src="' . $val->src . '" class="d-block rounded w-100" alt="' . $val->alt . '">
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
			<div class="relative rounded bg-dark" style="overflow: hidden">
				<div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
					<div class="carousel-inner">
						<?php echo $carousel; ?>
					</div>
					<?php if(sizeof($detail->screenshot) > 1) { ?>
						<a class="carousel-control-prev gradient-right" href="#carouselExampleControls" role="button" data-slide="prev">
							<span class="carousel-control-prev-icon" aria-hidden="true"></span>
							<span class="sr-only">
								<?php echo phrase('previous'); ?>
							</span>
						</a>
						<a class="carousel-control-next gradient-left" href="#carouselExampleControls" role="button" data-slide="next">
							<span class="carousel-control-next-icon" aria-hidden="true"></span>
							<span class="sr-only">
								<?php echo phrase('next'); ?>
							</span>
						</a>
					<?php } ?>
				</div>
			</div>
		</div>
		<div class="col-md-6 col-lg-5">
			<h5>
				<?php echo $detail->name; ?>
				<?php echo ($detail->type == 'backend' ? '<span class="badge badge-dark float-right">' . phrase('back_end_theme') . '</span>' : ($detail->type == 'frontend' ? '<span class="badge badge-success float-right">' . phrase('front_end_theme') . '</span>' : '<span class="badge badge-primary float-right">' . phrase('module') . '</span>')); ?>
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
					<a href="<?php echo current_page('../install', array('item' => $detail->slug, 'type' => $detail->addon_type)); ?>" class="btn btn-primary btn-block btn-sm --xhr --keep-modal show-progress">
						<i class="mdi mdi-plus"></i>
						<?php echo phrase('install'); ?>
					</a>
				</div>
				<div class="col-sm-6">
					<a href="<?php echo $detail->demo_url; ?>" class="btn btn-outline-primary btn-block btn-sm" target="_blank">
						<i class="mdi mdi-magnify"></i>
						<?php echo phrase('preview'); ?>
					</a>
				</div>
			</div>
		</div>
	</div>
</div>
