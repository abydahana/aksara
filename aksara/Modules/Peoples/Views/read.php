<?php if($results) { ?>
<div class="bg-light">
	<div class="container">
		<br />
		<br />
		<br />
		<br />
		<br />
		<br />
		<br />
		<br />
	</div>
</div>
<?php } ?>

<div class="container">
	<div class="row">
		<div class="col-md-8 offset-md-2">
			<?php
				if($results)
				{
					foreach($results as $key => $val)
					{
						echo '
							<div class="text-center" style="margin-top:-150px">
								<a href="' . get_image('peoples', $val->photo) . '" target="_blank">
									<img src="' . get_image('peoples', $val->photo, 'thumb') . '" class="img-fluid rounded-pill mb-5" style="border:5px solid #fff" alt="' . $val->first_name . ' ' . $val->last_name . '" />
								</a>
							</div>
							<div class="mb-5">
								<label class="text-muted d-block">
									' . phrase('full_name') . '
								</label>
								<h6 class="mb-3">
									' . $val->first_name . ' ' . $val->last_name . '
								</h6>
							</div>
							<div class="mb-5">
								<label class="text-muted d-block">
									' . phrase('position') . '
								</label>
								<h6 class="mb-3">
									' . $val->position . '
								</h6>
							</div>
							<div class="row mb-3 mb-5">
								<div class="col-sm-6">
									<label class="text-muted d-block">
										' . phrase('email') . '
									</label>
									<h6 class="mb-3">
										' . $val->email . '
									</h6>
								</div>
								<div class="col-sm-6">
									<label class="text-muted d-block">
										' . phrase('mobile') . '
									</label>
									<h6 class="mb-3">
										' . $val->mobile . '
									</h6>
								</div>
							</div>
							<div class="mb-5">
								<div class="article text-secondary">
									<i class="mdi mdi-format-quote-close mdi-3x text-muted"></i>' . $val->biography . '
								</div>
							</div>
							<div class="mb-3">
								<div class="row">
									' . ($val->instagram ? '
										<div class="col-sm-6 col-md-4">
											<a href="' . $val->instagram . '" class="btn btn-outline-danger d-block  rounded-pill mb-3" target="_blank">
												<i class="mdi mdi-instagram"></i>
												Instagram
											</a>
										</div>
									' : '') . '
									' . ($val->facebook ? '
										<div class="col-sm-6 col-md-4">
											<a href="' . $val->facebook . '" class="btn btn-outline-primary d-block  rounded-pill mb-3" target="_blank">
												<i class="mdi mdi-facebook"></i>
												Facebook
											</a>
										</div>
									' : '') . '
									' . ($val->twitter ? '
										<div class="col-sm-6 col-md-4">
											<a href="' . $val->twitter . '" class="btn btn-outline-info d-block rounded-pill mb-3" target="_blank">
												<i class="mdi mdi-twitter"></i>
												Twitter
											</a>
										</div>
									' : '') . '
								</div>
							</div>
						';
					}
				}
				else
				{
					echo '
						<div class="pt-5 pb-5">
							<div class="text-center" style="margin-top:-150px">
								<div class="rounded-circle bg-danger d-flex align-items-center justify-content-center" style="border:5px solid #fff; width:128px; height:128px; margin:0 auto">
									<i class="mdi mdi-account-off-outline mdi-5x text-light"></i>
								</div>
							</div>
							<div class="row mb-5">
								<div class="col-md-8 offset-md-2">
									<h2 class="text-center">
										' . phrase('the_people_you_are_looking_for_was_not_found') . '
									</h2>
									<p class="lead text-center mb-5">
										' . phrase('the_people_you_requested_does_not_exist') . '
									</p>
									<div class="text-center mt-5">
										<a href="' . go_to('../') . '" class="btn btn-outline-primary rounded-pill --xhr">
											<i class="mdi mdi-arrow-left"></i>
											' . phrase('back_to_peoples') . '
										</a>
									</div>
								</div>
							</div>
						</div>
					';
				}
			?>
		</div>
	</div>
</div>
<div class="container mt-5">
	<div class="row mb-5">
		<div class="col-md-8 offset-md-2">
			<?php
				if(sizeof($similar) > 0)
				{
					$output							= null;
					foreach($similar as $key => $val)
					{
						$output						.= '
							<div class="col-6 col-md-4' . ($key > 2 ? ' d-md-none' : null) . '">
								<div class="card border-0 rounded-more shadow-sm mb-3">
									<a href="' . go_to('../' . $val->people_slug) . '" class="--xhr">
										<img src="' . get_image('peoples', $val->photo, 'thumb') . '" class="card-img-top" alt="' . $val->first_name . ' '  . $val->last_name . '" width="100%" />
									</a>
									<div class="card-body">
										<a href="' . go_to('../' . $val->people_slug) . '" class="--xhr">
											<h6 class="card-title text-center text-truncate">
												' . truncate($val->first_name, 22) . ' ' . truncate($val->last_name, 22) . '
											</h6>
										</a>
										<a href="' . go_to('../' . $val->people_slug) . '" class="--xhr">
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
						<h6>
							<i class="mdi mdi-dots-horizontal"></i>
							' . phrase('meet_another_team') . '
						</h6>
						<div class="row">
							' . $output . '
						</div>
					';
				}
			?>
		</div>
	</div>
</div>
