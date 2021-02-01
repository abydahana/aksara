<?php
	$images											= json_decode($results[0]->gallery_images);
	$attributes										= json_decode($results[0]->gallery_attributes);
	$current										= service('request')->uri->getSegment(3);
?>

<style type="text/css">
	.modal-header
	{
		display: none
	}
</style>
<div class="photo-view">
	<a href="javascript:void(0)" class="close text-light text-shadow absolute" data-dismiss="modal" style="top:15px; right:15px">
		<i class="mdi mdi-window-close"></i>
	</a>
	<a href="<?php echo get_image('galleries', $current); ?>" target="_blank">
		<img src="<?php echo get_image('galleries', $current); ?>" class="img-fluid card-img-top" alt="<?php echo service('request')->getGet('item'); ?>" />
	</a>
</div>
<div class="card-body">
	<div class="row mb-3">
		<div class="col-2 col-md-1 pr-0">
			<a href="<?php echo base_url('user/' . $results[0]->username); ?>" class="--xhr">
				<img src="<?php echo get_image('users', $results[0]->photo, 'thumb'); ?>" class="img-fluid rounded" />
			</a>
		</div>
		<div class="col-10 col-md-7">
			<a href="<?php echo base_url('user/' . $results[0]->username); ?>" class="--xhr">
				<b class="mb-0">
					<?php echo $results[0]->first_name . ' ' . $results[0]->last_name; ?>
				</b>
			</a>
			<p>
				<span class="text-sm text-muted" data-toggle="tooltip" title="<?php echo $results[0]->updated_timestamp; ?>">
					<?php echo time_ago($results[0]->updated_timestamp); ?>
				</span>
			</p>
		</div>
		<div class="col-md-4">
			<div class="btn-group btn-group-sm d-flex">
				<a href="//www.facebook.com/sharer/sharer.php?u=<?php echo current_page(); ?>" class="btn btn-primary" data-toggle="tooltip" title="<?php echo phrase('share_to_facebook'); ?>" target="_blank">
					<i class="mdi mdi-facebook"></i>
				</a>
				<a href="//www.twitter.com/share?url=<?php echo current_page(); ?>" class="btn btn-info" data-toggle="tooltip" title="<?php echo phrase('share_to_twitter'); ?>" target="_blank">
					<i class="mdi mdi-twitter"></i>
				</a>
				<a href="//wa.me/?text=<?php echo current_page(); ?>" class="btn btn-success" data-toggle="tooltip" title="<?php echo phrase('send_to_whatsapp'); ?>" target="_blank">
					<i class="mdi mdi-whatsapp"></i>
				</a>
			</div>
		</div>
	</div>
	<div class="text-break-word mb-3">
		<?php echo (isset($images->$current) ? $images->$current : null); ?>
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
</div>
<div class="card-footer">
	<?php echo load_comment_plugin(current_page()); ?>
</div>
