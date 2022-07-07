<?php
	$results										= (isset($results[0]) ? $results[0] : null);
	
	if($results)
	{
		echo '
			<div class="bg-light">
				<div class="container pt-5 pb-5">
					<div class="row align-items-center">
						<div class="col-lg-2 text-center">
							<img src="' . get_image('users', $results->photo, 'thumb') . '" class="img-fluid rounded-circle" alt="..." />
						</div>
						<div class="col-lg-10">
							<div class="text-center text-lg-start">
								<h2 class="mb-0">
									' . $meta->title . '
								</h2>
								<p class="lead">
									@' . $results->username . '
								</p>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="border-top border-bottom bg-white pt-1 pb-1">
				<div class="container">
					<div class="row">
						<div class="col-lg-10 offset-lg-2">
							<ul class="nav nav-pills">
								<li class="nav-item">
									<a href="' . go_to($results->username) . '" class="nav-link no-wrap --xhr active">
										<i class="mdi mdi-information-outline"></i>
										' . phrase('about') . '
									</a>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
			<div class="container pt-5 pb-5">
				<div class="row">
					<div class="col-lg-10 offset-lg-2">
						<div class="mb-5">
							<h4>
								' . phrase('bio') . '
							</h4>
							<p class="lead">
								' . $results->bio . '
							</p>
						</div>
					</div>
				</div>
			</div>
		';
	}
	else
	{
		$link_left									= null;
		$link_right									= null;

		if(isset($suggestions) && $suggestions)
		{
			foreach($suggestions as $key => $val)
			{
				if(($key + 1) % 2 == 0)
				{
					$link_right						.= '
						<li>
							<a href="' . go_to($val->username) . '" class="--xhr">
								' . $val->first_name . ' ' . $val->last_name . '
							</a>
						</li>
					';
				}
				else
				{
					$link_left						.= '
						<li>
							<a href="' . go_to($val->username) . '" class="--xhr">
								' . $val->first_name . ' ' . $val->last_name . '
							</a>
						</li>
					';
				}
			}
		}
		
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
							' . phrase('user_not_found') . '
						</h2>
						<p class="lead text-center mb-5">
							' . phrase('the_user_you_requested_does_not_exist') . '
						</p>
						<div class="text-center mt-5">
							<a href="' . base_url() . '" class="btn btn-outline-primary rounded-pill --xhr">
								<i class="mdi mdi-arrow-left"></i>
								' . phrase('back_to_home') . '
							</a>
						</div>
					</div>
				</div>
				<div class="row mb-2">
					<div class="col-md-10 offset-md-1">
						<h5>
							<i class="mdi mdi-lightbulb-on-outline"></i>
							' . phrase('our_suggestions') . '
							<blink>_</blink>
						</h5>
					</div>
				</div>
				<div class="row">
					<div class="col-md-5 offset-md-1">
						<ul>
							' . $link_left . '
						</ul>
					</div>
					<div class="col-md-5">
						<ul>
							' . $link_right . '
						</ul>
					</div>
				</div>
			</div>
		';
	}
