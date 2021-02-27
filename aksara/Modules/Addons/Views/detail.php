<?php
	$carousel										= null;
	
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
?>
<div class="container-fluid pt-3 pb-3">
	<div class="row">
		<div class="col-md-6">
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
		<div class="col-md-6">
			<h5 class="font-weight-light">
				<?php echo $detail->name; ?>
				<?php echo ($detail->type == 'backend' ? '<span class="badge badge-warning float-right">' . phrase('back_end') . '</span>' : '<span class="badge badge-success float-right">' . phrase('front_end') . '</span>'); ?>
			</h5>
			<hr />
			<div class="row">
				<div class="col-4">
					<label class="text-muted d-block mb-1">
						<?php echo phrase('version'); ?>
					</label>
				</div>
				<div class="col-8">
					<p class="mb-1">
						<?php echo $detail->version; ?>
					</p>
				</div>
			</div>
			<div class="row">
				<div class="col-4">
					<label class="text-muted d-block mb-1">
						<?php echo phrase('author'); ?>
					</label>
				</div>
				<div class="col-8">
					<p class="mb-1">
						<?php echo (isset($detail->website) ? '<a href="' . $detail->website . '" target="_blank"><b>' . $detail->author . '</b></a>' : '<b>' . $detail->author . '</b>'); ?>
					</p>
				</div>
			</div>
			<div class="row">
				<div class="col-4">
					<label class="text-muted d-block mb-1">
						<?php echo phrase('publisher'); ?>
					</label>
				</div>
				<div class="col-8">
					<p class="mb-1">
						<?php echo (isset($detail->publisher_url) ? '<a href="' . $detail->publisher_url . '" target="_blank"><b>' . $detail->publisher . '</b></a>' : '<b>' . $detail->publisher . '</b>'); ?>
					</p>
				</div>
			</div>
			<p class="mb-0">
				<?php echo nl2br($detail->description); ?>
			</p>
		</div>
	</div>
	<hr class="row" />
	<div class="row">
		<div class="col-md-3">
		</div>
		<div class="col-md-3">
		</div>
		<div class="col-md-3">
			<a href="<?php echo current_page('../install', array('item' => $detail->slug, 'type' => $detail->addon_type)); ?>" class="btn btn-primary btn-block btn-sm --xhr --keep-modal show-progress">
				<i class="mdi mdi-plus"></i>
				<?php echo phrase('install'); ?>
			</a>
		</div>
		<div class="col-md-3">
			<a href="<?php echo $detail->demo_url; ?>" class="btn btn-outline-primary btn-block btn-sm" target="_blank">
				<i class="mdi mdi-magnify"></i>
				<?php echo phrase('preview'); ?>
			</a>
		</div>
	</div>
</div>
