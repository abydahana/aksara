<?php
	$count											= 0;
	$images											= json_decode($results[0]->gallery_images);
	$attributes										= json_decode($results[0]->gallery_attributes);
	$current										= service('request')->uri->getSegment(3);
	$carousel										= null;
	
	if($images)
	{
		foreach($images as $key => $val)
		{
			$carousel								.= '
				<div class="carousel-item text-center' . ($current == $key ? ' active' : null) . '">
					<div class="full-height d-flex align-items-center justify-content-center bg-secondary">
						<img src="' . get_image('galleries', $key) . '" class="img-fluid" alt="' . $val . '">
						<div class="carousel-caption d-none d-md-block text-shadow">
							' . $val . '
						</div>
					</div>
				</div>
			';
			
			$count++;
		}
	}
?>
<style type="text/css">
	.modal-header
	{
		display: none
	}
</style>
<div class="row no-gutters bg-light">
	<div class="col-lg-8">
		<div class="sticky-top" style="top:15px">
			<div class="photo-view">
				<div class="relative" style="overflow: hidden">
					<div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
						<div class="carousel-inner">
							<?php echo $carousel; ?>
						</div>
						<?php if($count > 1) { ?>
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
		</div>
	</div>
	<div class="col-lg-4 p-3 bg-white">
		<div class="sticky-top" style="top:15px">
			<div class="row mb-3">
				<div class="col-3 col-md-2 pr-0">
					<a href="<?php echo base_url('user/' . $results[0]->username); ?>" class="--xhr">
						<img src="<?php echo get_image('users', $results[0]->photo, 'thumb'); ?>" class="img-fluid rounded" />
					</a>
				</div>
				<div class="col-9 col-md-10">
					<a href="<?php echo current_page('../../', array('gallery_slug' => null)); ?>" class="float-right font-weight-bold --xhr">
						<i class="mdi mdi-window-close"></i>
					</a>
					<a href="<?php echo base_url('user/' . $results[0]->username); ?>" class="--xhr">
						<b class="mb-0">
							<?php echo $results[0]->first_name . ' ' . $results[0]->last_name; ?>
						</b>
					</a>
					<p class="mt-0">
						<span class="text-sm text-muted" data-toggle="tooltip" title="<?php echo $results[0]->updated_timestamp; ?>">
							<?php echo time_ago($results[0]->updated_timestamp); ?>
						</span>
					</p>
				</div>
			</div>
			<?php
				if($attributes)
				{
					foreach($attributes as $key => $val)
					{
						if(!isset($val->label) && !isset($val->value)) continue;
						
						echo '
							<div class="row">
								<div class="col-sm-4 col-lg-3">
									<label class="d-block text-muted">
										' . $val->label . '
									</label>
								</div>
								<div class="col-sm-8 col-lg-9">
									<label>
										' . $val->value . '
									</label>
								</div>
							</div>
						';
					}
				}
			?>
			<div>
				<?php echo $results[0]->gallery_description; ?>
			</div>
			<div>
				<?php echo load_comment_plugin(current_page()); ?>
			</div>
		</div>
	</div>
</div>
