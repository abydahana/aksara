<div class="jumbotron jumbotron-fluid bg-transparent">
	<div class="container">
		<div class="text-center text-md-left">
			<h3 class="mb-0<?php echo (!$meta->description ? ' mt-3' : null); ?>">
				<?php echo $meta->title; ?>
			</h3>
			<p class="lead">
				<?php echo truncate($meta->description, 256); ?>
			</p>
		</div>
	</div>
</div>

<div class="container">
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
								<a href="' . go_to(array($val->gallery_slug, $src)) . '" class="--modal">
									<img src="' . get_image('galleries', $src, 'thumb') . '" class="shadow rounded w-100 mb-4" alt="' . $alt . '" />
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
				<div class="alert alert-warning mt-5">
					<i class="mdi mdi-information-outline"></i>
					' . phrase('no_image_found_in_this_album') . '
				</div>
			';
		}
	?>
</div>
