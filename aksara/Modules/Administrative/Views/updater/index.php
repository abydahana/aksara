<div class="container-fluid pb-5">
	<?php
		if(isset($updater->changelog))
		{
			$changelog								= null;
			$parsedown								= new \Aksara\Libraries\Parsedown;
			
			foreach($updater->changelog as $key => $val)
			{
				if($key)
				{
					$changelog						.= '<hr class="mt-1 mb-1" />';
				}
				
				$changelog							.= '
					<a href="' . $val->commit_url . '" target="_blank">
						<h2>
							' . $val->title . '
							<i class="mdi mdi-launch"></i>
						</h2>
					</a>
					<hr />
					<div class="row no-gutters">
						<div class="col-4 col-md-2 col-lg-1 pt-1">
							<a href="' . $val->profile_url . '" target="_blank">
								<img src="' . $val->profile_avatar . '" class="img-fluid rounded-more" />
							</a>
						</div>
						<div class="col-8 col-md-10 col-lg-11 ps-3 text-break-word">
							<a href="' . $val->profile_url . '" target="_blank">
								<h5>
									' . $val->committer . '
									<i class="mdi mdi-launch"></i>
								</h5>
							</a>
							<span>
								' . $val->date . '
							</span>
						</div>
					</div>
					<hr />
					' . $parsedown->parse($val->message) . '
				';
			}
			
			echo '
				<div class="alert alert-info rounded-0 border-0" style="margin-left:-1rem; margin-right:-1rem">
					<h5>
						' . phrase('update_available') . '
					</h5>
					<p class="mb-0">
						' . phrase('a_newer_version_of_aksara_is_available') . '
						' . phrase('click_the_button_below_to_update_your_core_system_directly') . '
						' . phrase('your_created_module_and_theme_will_not_be_overwritten') . '
					</p>
				</div>
				<form action="' . current_page() . '" method="POST" class="--validate-form">
					<div class="row">
						<div class="col-lg-8">
							' . $changelog . '
						</div>
					</div>
					<hr class="row" />
					<div class="row">
						<div class="col-lg-8">
							<div class="--validation-callback"></div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-8">
							<button type="submit" class="btn btn-success rounded-pill">
								<i class="mdi mdi-reload"></i>
								' . phrase('update_now') . '
							</button>
							' . (isset($updater->server_version) ? '
							<a href="//www.aksaracms.com/updater/file.zip" class="btn btn-dark rounded-pill ms-3">
								<i class="mdi mdi-hammer"></i>
								' . phrase('manual_update') . '
							</a>
							' : null) . '
						</div>
					</div>
				</form>
			';
		}
		else
		{
			echo '
				<div class="alert alert-success rounded-more mt-3">
					<h5>
						' . phrase('your_core_system_is_up_to_date') . '
					</h5>
					<p>
						' . phrase('no_update_available_at_this_time') . ' The update will be inform to you if available.
					</p>
					<hr />
					<a href="' . base_url('administrative/updater') . '" class="btn btn-sm btn-success rounded-pill --xhr show-progress">
						<i class="mdi mdi-update"></i>
						' . phrase('check_again') . '
					</a>
				</div>
			';
		}
	?>
</div>
