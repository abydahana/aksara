<?php
	if(isset($updater->changelog))
	{
		$changelog									= null;
		$parsedown									= new \Aksara\Libraries\Parsedown;
		
		foreach($updater->changelog as $key => $val)
		{
			if($key)
			{
				$changelog							.= '<hr class="mt-1 mb-1" />';
			}
			
			$changelog								.= '
				<div class="row">
					<div class="col-4 col-md-2 col-lg-1 pt-1">
						<a href="' . $val->profile_url . '" target="_blank">
							<img src="' . $val->profile_avatar . '" class="img-fluid rounded" />
						</a>
					</div>
					<div class="col-8 col-md-10 col-lg-11">
						<p class="mb-3">
							<a href="' . $val->profile_url . '" target="_blank">
								<b>' . $val->committer . '</b>
							</a>
							<small>
								@' . $val->date . '
							</small>
						</p>
						<a href="' . $val->commit_url . '" target="_blank">
							<h4>
								' . $val->title . '
								<i class="mdi mdi-launch"></i>
							</h4>
						</a>
						<hr />
						<p>
							' . $parsedown->parse($val->message) . '
						</p>
					</div>
				</div>
			';
		}
		
		echo '
			<div class="alert alert-info text-sm rounded-0 border-0 p-3 mb-0">
				<h5>
					' . phrase('update_available') . '
				</h5>
				<p class="mb-0">
					' . phrase('a_newer_version_of_aksara_is_available') . '
					' . phrase('click_the_button_below_to_update_your_core_system_directly') . '
					' . phrase('your_created_module_and_theme_will_not_be_overwritten') . '
				</p>
			</div>
			<div class="container-fluid pt-3 pb-3">
				<div class="row">
					<div class="col-md-8">
						' . $changelog . '
					</div>
				</div>
				<hr class="row" />
				<a href="' . base_url('administrative/updater/update') . '" class="btn btn-success --xhr show-progress">
					<i class="mdi mdi-update"></i>
					' . phrase('update_now') . '
				</a>
				' . (isset($updater->server_version) ? '
				<a href="//www.aksaracms.com/updater/file.zip" class="btn btn-dark ml-3">
					<i class="mdi mdi-hammer"></i>
					' . phrase('manual_update') . '
				</a>
				' : null) . '
			</div>
		';
	}
	else
	{
		echo '
			<div class="alert alert-success text-sm rounded-0 border-0 p-3 mb-0">
				<h5>
					' . phrase('your_core_system_is_up_to_date') . '
				</h5>
				<p>
					' . phrase('no_update_available_at_this_time') . ' The update will be inform to you if available.
				</p>
				<hr />
				<a href="' . base_url('administrative/updater') . '" class="btn btn-sm btn-success --xhr show-progress">
					<i class="mdi mdi-update"></i>
					' . phrase('check_again') . '
				</a>
			</div>
		';
	}
