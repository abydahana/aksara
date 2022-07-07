<?php if($results) { ?>
<div class="bg-light text-secondary pt-5 pb-5">
	<div class="container">
		<div class="text-center">
			<h3 class="mb-0<?php echo (!$meta->description ? ' mt-3' : null); ?>">
				<?php echo $meta->title; ?>
			</h3>
			<p class="lead">
				<?php echo truncate($meta->description, 256); ?>
			</p>
		</div>
	</div>
</div>
<?php } ?>

<div class="container pt-5 pb-5">
	<?php
		if($results)
		{
			$output									= null;
			
			foreach($results as $key => $val)
			{
				$images								= json_decode($val->gallery_images, true);
				$labels								= explode(',', $val->gallery_tags);
				
				if(is_array($images) && sizeof($images) > 0)
				{
					foreach($images as $src => $alt)
					{
						if(!$src) continue;
						
						$output						.= '
							<div class="col-sm-6 col-md-3">
								<a href="' . current_page($src) . '" class="--xhr">
									<img src="' . get_image('galleries', $src, 'thumb') . '" class="rounded-4 shadow-sm w-100 mb-4" alt="' . $alt . '" />
								</a>
							</div>
						';
					}
				}
			}
			
			echo '<div class="row">' . $output . '</div>';
		}
		else
		{
			echo '
				<div class="container pt-5 pb-5">
					<div class="text-center pt-5 pb-5">
						<h1 class="text-muted">
							404
						</h1>
						<i class="mdi mdi-dropbox mdi-5x text-muted"></i>
					</div>
					<div class="row mb-5">
						<div class="col-md-6 offset-md-3">
							<h2 class="text-center">
								' . phrase('album_not_found') . '
							</h2>
							<p class="lead text-center mb-5">
								' . phrase('the_album_you_requested_was_not_found_or_its_already_removed') . '
							</p>
							<div class="text-center mt-5">
								<a href="' . go_to('../') . '" class="btn btn-outline-primary rounded-pill --xhr">
									<i class="mdi mdi-arrow-left"></i>
									' . phrase('back_to_galleries') . '
								</a>
							</div>
						</div>
					</div>
				</div>
			';
		}
	?>
</div>
