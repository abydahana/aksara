<div class="bg-light pt-5 pb-5">
	<div class="container">
		<div class="row align-items-center">
			<div class="col-3 col-sm-2 col-md-1 offset-md-2">
				<i class="<?php echo $meta->icon; ?> mdi-4x"></i>
			</div>
			<div class="col-9 col-sm-10 col-md-6">
				<h3 class="mb-0<?php echo (!$meta->description ? ' mt-3' : null); ?>">
					<?php echo $meta->title; ?>
				</h3>
				<p class="lead">
					<?php echo truncate($meta->description, 256); ?>
				</p>
			</div>
		</div>
	</div>
</div>
<div class="container pt-5 pb-5">
	<form action="<?php echo current_page(); ?>" method="POST" class="--validate-form">
		<div class="row">
			<div class="col-md-6 offset-md-3">
				<div class="row">
					<div class="col-sm-6">
						<div class="form-group mb-4">
							<label class="d-block fw-bold" for="first_name_input">
								<?php echo phrase('first_name'); ?>
							</label>
							<input type="text" name="first_name" class="form-control" id="first_name_input" placeholder="<?php echo phrase('your_first_name'); ?>" autocomplete="off" maxlength="64" />
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group mb-4">
							<label class="d-block fw-bold" for="last_name_input">
								<?php echo phrase('last_name'); ?>
							</label>
							<input type="text" name="last_name" class="form-control" id="last_name_input" placeholder="<?php echo phrase('your_last_name'); ?>" autocomplete="off" maxlength="64" />
						</div>
					</div>
				</div>
				<div class="form-group mb-4">
					<label class="d-block fw-bold" for="email_input">
						<?php echo phrase('email_address'); ?>
					</label>
					<input type="email" name="email" class="form-control" id="email_input" placeholder="<?php echo phrase('enter_your_email_address'); ?>" autocomplete="off" maxlength="128" />
				</div>
				<div class="row">
					<div class="col-sm-6">
						<div class="form-group mb-4">
							<label class="d-block fw-bold" for="username_input">
								<?php echo phrase('username'); ?>
							</label>
							<input type="text" name="username" class="form-control" id="username_input" placeholder="<?php echo phrase('choose_your_username'); ?>" autocomplete="off" maxlength="32" />
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group mb-4 position-relative">
							<label class="d-block fw-bold" for="password_input">
								<?php echo phrase('password'); ?>
							</label>
							<div class="input-group">
								<input type="password" name="password" class="form-control" id="password_input" placeholder="<?php echo phrase('minimum'); ?> 6 <?php echo phrase('characters'); ?>" maxlength="32" style="border-right:0" />
								<span class="input-group-text bg-white" style="border-left:0">
									<i class="mdi mdi-eye-outline" style="width:22px"></i>
								</span>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group mb-4">
							<label class="d-block fw-bold" for="email_input">
								<?php echo phrase('phone_number'); ?>
							</label>
							<input type="phone" name="phone" class="form-control" id="phone_input" placeholder="0812XXXX" autocomplete="off" maxlength="16" />
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group mb-4">
							<label class="d-block" for="captcha_input">
								<?php echo phrase('enter_shown_characters'); ?>
							</label>
							<div class="input-group">
								<span class="input-group-text bg-dark p-0">
									<?php
										if($captcha->string)
										{
											echo '<b class="text-light pe-3 ps-3">' . $captcha->string . '</b>';
										}
										else
										{
											echo '<img src="' . $captcha->image . '" class="img-fluid" alt="..." />';
										}
									?>
								</span>
								<input type="text" name="captcha" class="form-control" id="captcha_input" placeholder="XXXXXX" maxlength="32" />
							</div>
						</div>
					</div>
				</div>
				
				<div class="--validation-callback"></div>
				
				<div class="mb-3">
					<div class="d-grid">
						<button type="submit" class="btn btn-outline-primary">
							<i class="mdi mdi-check"></i>
							<?php echo phrase('register_account'); ?>
						</button>
					</div>
				</div>
				<div class="mt-4 mb-4">
					<label class="text-muted d-block">
						<?php echo phrase('by_submitting_this_form_you_are_agree_about_all_future_action_related_to_your_account_related_to_the'); ?>
						<a href="<?php echo base_url('pages/guidelines/terms-and-conditions'); ?>" target="_blank">
							<b>
								<?php echo phrase('terms_and_conditions'); ?>
							</b>
						</a>
					</label>
				</div>
			</div>
		</div>
	</form>
</div>
