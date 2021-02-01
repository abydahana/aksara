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
			$output							= null;
			foreach($results as $key => $val)
			{
				$output						.= '
					<div class="col-6 col-md-4 col-lg-3">
						<div class="card border-0 shadow mb-3">
							<a href="' . go_to($val->people_slug) . '" class="--xhr">
								<img src="' . get_image('peoples', $val->photo, 'thumb') . '" class="card-img-top" alt="' . $val->first_name . ' '  . $val->last_name . '" width="100%" />
							</a>
							<div class="card-body">
								<a href="' . go_to($val->people_slug) . '" class="--xhr">
									<h6 class="card-title text-center text-truncate">
										' . truncate($val->first_name, 22) . ' ' . truncate($val->last_name, 22) . '
									</h6>
								</a>
								<a href="' . go_to($val->people_slug) . '" class="--xhr">
									<h6 class="card-subtitle font-weight-light text-center mb-2 text-muted text-truncate">
										' . truncate($val->position, 22) . '
									</h6>
								</a>
							</div>
						</div>
					</div>
				';
			}
			
			echo '
				<div class="row form-group">
					' . $output . '
				</div>
			';
		}
	?>
</div>
