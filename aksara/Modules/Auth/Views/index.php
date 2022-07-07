<?php
	if($activation)
	{
		echo '
			<div class="alert alert-info border-0 rounded-0">
				<div class="container text-center">
					<i class="mdi mdi-check-circle mdi-5x"></i>
					<h3>
						' . phrase('account_registered') . '
					</h3>
					' . phrase('follow_the_link_we_sent_to_your_email_to_activate_your_account') . '
				</div>
			</div>
		';
	}
?>
<div class="container-fluid pt-5 pb-5">
	<div class="row">
		<div class="<?php echo (get_setting('frontend_registration') ? 'col-md-8 offset-md-2 col-xl-6 offset-xl-3' : 'col-md-4 offset-md-4'); ?>">
			<div class="card-group pt-5 pb-5">
				<div class="card shadow">
					<div class="card-body p-4">
						<form action="<?php echo current_page(); ?>" method="POST" class="--validate-form" enctype="multipart/form-data">
							<div class="mb-3">
								<p class="text-center">
									<i class="mdi mdi-account-circle-outline mdi-5x text-black-50"></i>
								</p>
							</div>
							<div class="mb-3">
								<p class="text-center text-black-50">
									<?php echo phrase('please_enter_your_account_information_to_sign_in'); ?>
								</p>
							</div>
							<div class="form-group mb-3">
								<div class="input-group">
									<span class="input-group-text">
										<i class="mdi mdi-account" style="width:22px"></i>
									</span>
									<input type="text" name="username" class="form-control" id="username_input" placeholder="<?php echo phrase('enter_your_username_or_email'); ?>" />
								</div>
							</div>
							<div class="form-group mb-3 position-relative">
								<div class="input-group">
									<span class="input-group-text">
										<i class="mdi mdi-fingerprint" style="width:22px"></i>
									</span>
									<input type="password" name="password" class="form-control" id="password_input" placeholder="<?php echo phrase('enter_password'); ?>" autocomplete="new-password" style="border-right:0" />
									<span class="input-group-text bg-white" style="border-left:0">
										<i class="mdi mdi-eye-outline" style="width:22px"></i>
									</span>
								</div>
							</div>
							
							<?php
								if($years)
								{
									$option			= null;
									
									foreach($years as $key => $val)
									{
										$option		.= '<option value="' . $val->value . '"' . ($val->selected ? ' selected' : null) . '>' . $val->label . '</option>';
									}
									
									echo '
										<div class="form-group mb-3">
											<div class="input-group">
												<span class="input-group-text">
													<i class="mdi mdi-calendar-check" style="width:22px"></i>
												</span>
												<select name="year" class="form-control" placeholder="' . phrase('choose_year') . '" id="year_input">
													' . $option . '
												</select>
											</div>
										</div>
									';
								}
							?>
							
							<div class="--validation-callback mb-0"></div>
							
							<div class="row">
								<div class="col-7">
									<label class="mt-2">
										<a href="<?php echo current_page('forgot'); ?>" class="--xhr">
											<?php echo phrase('forgot_password'); ?>
										</a>
									</label>
								</div>
								<div class="col-5">
									<div class="d-grid">
										<button type="submit" class="btn btn-primary">
											<i class="mdi mdi-check"></i> 
											<?php echo phrase('sign_in'); ?>
										</button>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
				<?php if(get_setting('frontend_registration')){ ?>
				<div class="card shadow">
					<div class="card-body d-flex align-items-center justify-content-center">
						<div>
							<p class="lead text-center text-black-50">
								<?php echo phrase('do_not_have_an_account'); ?>
							</p>
							<?php if(get_setting('google_client_id') && get_setting('google_client_secret')) { ?>
							<div class="d-grid mb-2">
								<a href="<?php echo base_url('auth/google'); ?>" class="btn btn-outline-danger">
									<i class="mdi mdi-google"></i>
									<?php echo phrase('sign_in_with_google'); ?>
								</a>
							</div>
							<?php } ?>
							<?php if(get_setting('facebook_app_id') && get_setting('facebook_app_secret')) { ?>
							<div class="d-grid mb-2">
								<a href="<?php echo base_url('auth/facebook'); ?>" class="btn btn-outline-primary">
									<i class="mdi mdi-facebook"></i>
									<?php echo phrase('sign_in_with_facebook'); ?>
								</a>
							</div>
							<?php } ?>
							<div class="d-grid">
								<a href="<?php echo base_url('auth/register'); ?>" class="btn btn-outline-success --xhr">
									<i class="mdi mdi-account-plus"></i>
									<?php echo phrase('register_an_account'); ?>
								</a>
							</div>
						</div>
					</div>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>
