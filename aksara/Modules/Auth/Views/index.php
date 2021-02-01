<div class="container-fluid pt-5">
	<div class="row">
		<div class="<?php echo (get_setting('frontend_registration') ? 'col-md-8 offset-md-2' : 'col-md-4 offset-md-4'); ?>">
			<div class="card-group">
				<div class="card shadow">
					<div class="card-body">
						<form action="<?php echo current_page(); ?>" method="POST" class="--validate-form" enctype="multipart/form-data">
							<div class="form-group pt-5 pb-3">
								<p class="text-center text-muted">
									<i class="mdi mdi-lock-open-outline mdi-5x"></i>
								</p>
							</div>
							<div class="form-group pb-2">
								<p class="text-center text-muted">
									<?php echo phrase('use_your_account_information_to_start_session'); ?>
								</p>
							</div>
							<div class="form-group">
								<div class="input-group flex-nowrap">
									<div class="input-group-prepend">
										<span class="input-group-text" id="addon-wrapping">
											<i class="mdi mdi-account-outline" style="width:22px"></i>
										</span>
									</div>
									<input type="text" name="username" class="form-control" id="username_input" placeholder="<?php echo phrase('enter_your_username_or_email'); ?>" />
								</div>
							</div>
							<div class="form-group">
								<div class="input-group flex-nowrap">
									<div class="input-group-prepend">
										<span class="input-group-text" id="addon-wrapping">
											<i class="mdi mdi-fingerprint" style="width:22px"></i>
										</span>
									</div>
									<input type="password" name="password" class="form-control" id="password_input" placeholder="<?php echo phrase('enter_password'); ?>" autocomplete="new-password" />
								</div>
							</div>
							
							<div class="--validation-callback mb-0"></div>
							
							<div class="form-group">
								<div class="row">
									<div class="col-7">
										<label class="mt-2">
											<a href="<?php echo current_page('forgot'); ?>" class="--xhr">
												<?php echo phrase('forgot_password'); ?>
											</a>
										</label>
									</div>
									<div class="col-5">
										<button type="submit" class="btn btn-primary btn-block">
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
							<p class="lead text-center text-muted">
								<?php echo phrase('do_not_have_an_account'); ?>
							</p>
							<?php if(get_setting('google_client_id') && get_setting('google_client_secret')) { ?>
							<p class="lead text-center">
								<a href="<?php echo base_url('auth/google'); ?>" class="btn btn-outline-danger btn-block text-left">
									<i class="mdi mdi-google"></i>
									<?php echo phrase('sign_with_google'); ?>
								</a>
							</p>
							<?php } ?>
							<?php if(get_setting('facebook_app_id') && get_setting('facebook_app_secret')) { ?>
							<p class="lead text-center">
								<a href="<?php echo base_url('auth/facebook'); ?>" class="btn btn-outline-primary btn-block text-left">
									<i class="mdi mdi-facebook"></i>
									<?php echo phrase('sign_with_facebook'); ?>
								</a>
							</p>
							<?php } ?>
							<p class="lead text-center">
								<a href="<?php echo base_url('auth/register'); ?>" class="btn btn-outline-info btn-block text-left --xhr">
									<i class="mdi mdi-account-plus"></i>
									<?php echo phrase('register_an_account'); ?>
								</a>
							</p>
						</div>
					</div>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>
