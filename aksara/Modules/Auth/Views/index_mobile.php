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
<style type="text/css">
	
	#footer-wrapper
	{
		display: none
	}
</style>
<div class="">
	<div class="<?php echo (!$activation ? 'd-flex align-items-end justify-content-center' : null); ?>">
		<div class="container-fluid">
			<div class="card border-0 rounded-top rounded-more">
				<div class="card-body">
					<div class="mb-5">
						<p class="text-center">
							<i class="mdi mdi-account-circle-outline mdi-5x text-dark"></i>
						</p>
					</div>
					<form action="<?php echo current_page(); ?>" method="POST" class="--validate-form" enctype="multipart/form-data">
						<div class="mb-3">
							<input type="text" name="username" class="form-control" id="username_input" placeholder="<?php echo phrase('enter_your_username_or_email'); ?>" />
						</div>
						<div class="mb-3">
							<div class="input-group">
								<input type="password" name="password" class="form-control" id="password_input" placeholder="<?php echo phrase('enter_your_password'); ?>" autocomplete="new-password" style="border-right:0" />
								<span class="input-group-text bg-white" style="border-left:0">
									<i class="mdi mdi-eye-outline" style="width:22px"></i>
								</span>
							</div>
						</div>
						<?php
							if($years)
							{
								$option						= null;
								
								foreach($years as $key => $val)
								{
									$option					.= '<option value="' . $val->value . '"' . ($val->selected ? ' selected' : null) . '>' . $val->label . '</option>';
								}
								
								echo '
									<div class="mb-3">
										<select name="year" class="form-control" placeholder="' . phrase('choose_year') . '" id="year_input">
											' . $option . '
										</select>
									</div>
								';
							}
						?>
						
						<div class="--validation-callback mb-3"></div>
						
						<div class="mb-3">
							<a href="<?php echo current_page('forgot'); ?>" class="--xhr">
								<?php echo phrase('forgot_password'); ?>
							</a>
						</div>
						<div class="mb-3">
							<div class="d-grid">
								<button type="submit" class="btn btn-primary">
									<i class="mdi mdi-check"></i> 
									<?php echo phrase('sign_in'); ?>
								</button>
							</div>
						</div>
					</form>
					<?php if(get_setting('frontend_registration') && ((get_setting('google_client_id') && get_setting('google_client_secret')) || (get_setting('facebook_app_id') && get_setting('facebook_app_secret')))){ ?>
						<p class="text-center text-muted">
							<?php echo phrase('or_sign_in_with_your_social_account'); ?>
						</p>
						<div class="row mb-5">
							<?php if(get_setting('google_client_id') && get_setting('google_client_secret')) { ?>
							<div class="col-6">
								<div class="d-grid">
									<a href="<?php echo base_url('auth/google'); ?>" class="btn btn-outline-danger btn-sm">
										<i class="mdi mdi-google"></i>
										<?php echo phrase('google'); ?>
									</a>
								</div>
							</div>
							<?php } ?>
							<?php if(get_setting('facebook_app_id') && get_setting('facebook_app_secret')) { ?>
							<div class="col-6">
								<div class="d-grid">
									<a href="<?php echo base_url('auth/facebook'); ?>" class="btn btn-outline-primary btn-sm">
										<i class="mdi mdi-facebook"></i>
										<?php echo phrase('facebook'); ?>
									</a>
								</div>
							</div>
							<?php } ?>
						</div>
					<?php } ?>
					<?php if(get_setting('frontend_registration')){ ?>
						<div class="text-center text-muted">
							<?php echo phrase('do_not_have_an_account'); ?>
							<a href="<?php echo base_url('auth/register'); ?>" class="--xhr">
								<b>
									<?php echo phrase('register_an_account'); ?>
								</b>
							</a>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>
