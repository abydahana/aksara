<div class="bg-light text-secondary">
	<div class="container pt-5 pb-5">
		<div class="row">
			<div class="col-md-8 offset-md-2">
				<h1 class="text-center">
					<?php echo $meta->title; ?>
				</h1>
				<p class="lead text-center">
					<?php echo truncate($meta->description, 256); ?>
				</p>
			</div>
		</div>
	</div>
</div>

<div class="container pt-5 pb-5">
	<?php
		if($results)
		{
			$output							= null;
			
			foreach($results as $key => $val)
			{
				$output						.= '
					<div class="col-sm-6 col-md-4 col-lg-3">
						<div class="card border-0 rounded-4 shadow-sm mb-3 overflow-hidden">
							<a href="' . go_to($val->people_slug) . '" class="--xhr">
								<img src="' . get_image('peoples', $val->photo, 'thumb') . '" class="card-img-top" alt="' . $val->first_name . ' '  . $val->last_name . '" width="100%" />
							</a>
							<div class="card-body">
								<a href="' . go_to($val->people_slug) . '" class="--xhr">
									<h5 class="card-title text-center text-truncate">
										' . truncate($val->first_name, 22) . ' ' . truncate($val->last_name, 22) . '
									</h5>
								</a>
								<a href="' . go_to($val->people_slug) . '" class="--xhr">
									<h6 class="card-subtitle fw-light text-center mb-2 text-muted text-truncate">
										' . truncate($val->position, 22) . '
									</h6>
								</a>
							</div>
						</div>
					</div>
				';
			}
			
			echo '
				<div class="row mb-3">
					' . $output . '
				</div>
			';
			
			if($total > $limit)
			{
				echo '
					<div class="pt-3 pb-3">
						' . $template->pagination . '
					</div>
				';
			}
		}
		else
		{
			echo '
				<div class="text-muted">
					<i class="mdi mdi-information-outline"></i>
					' . phrase('no_people_is_available') . '
				</div>
			';
		}
	?>
</div>
