<div class="jumbotron jumbotron-fluid bg-transparent">
	<div class="container-fluid">
		<div class="row">
			<div class="col-3 col-sm-2 col-md-1 offset-md-2">
				<i class="<?php echo $meta->icon; ?> mdi-4x text-muted"></i>
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
<div class="container-fluid">
	<form action="<?php echo current_page(); ?>" method="POST" class="--validate-form">
		<div class="row">
			<div class="col-md-6 offset-md-3">
				<div class="row">
					<div class="col-sm-6">
						<div class="form-group mb-4">
							<label class="d-block font-weight-bold" for="first_name_input">
								<?php echo phrase('first_name'); ?>
							</label>
							<input type="text" name="first_name" class="form-control font-weight-light" id="first_name_input" placeholder="<?php echo phrase('your_first_name'); ?>" autocomplete="off" maxlength="64" />
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group mb-4">
							<label class="d-block font-weight-bold" for="last_name_input">
								<?php echo phrase('last_name'); ?>
							</label>
							<input type="text" name="last_name" class="form-control font-weight-light" id="last_name_input" placeholder="<?php echo phrase('your_last_name'); ?>" autocomplete="off" maxlength="64" />
						</div>
					</div>
				</div>
				<div class="form-group mb-4">
					<label class="d-block font-weight-bold" for="email_input">
						<?php echo phrase('email_address'); ?>
					</label>
					<input type="email" name="email" class="form-control font-weight-light" id="email_input" placeholder="<?php echo phrase('type_your_email_address'); ?>" autocomplete="off" maxlength="128" />
				</div>
				<div class="row">
					<div class="col-sm-6">
						<div class="form-group mb-4">
							<label class="d-block font-weight-bold" for="username_input">
								<?php echo phrase('username'); ?>
							</label>
							<input type="text" name="username" class="form-control font-weight-light" id="username_input" placeholder="<?php echo phrase('choose_your_username'); ?>" autocomplete="off" maxlength="32" />
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group mb-4" style="position: relative">
							<label class="d-block font-weight-bold" for="password_input">
								<?php echo phrase('password'); ?>
							</label>
							<input type="password" name="password" class="form-control font-weight-light" id="password_input" placeholder="<?php echo phrase('minimum'); ?> 6 <?php echo phrase('characters'); ?>" maxlength="32" />
							<i class="mdi mdi-eye-outline mdi-2x password-peek text-muted" style="position:absolute; right: 8px; bottom: 8px; background: #fff"></i>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group mb-4">
							<label class="d-block font-weight-bold" for="email_input">
								<?php echo phrase('phone_number'); ?>
							</label>
							<input type="phone" name="phone" class="form-control font-weight-light" id="phone_input" placeholder="0812XXXX" autocomplete="off" maxlength="16" />
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group mb-4">
							<label class="d-block" for="captcha_input">
								<?php echo phrase('type_the_shown_characters_below'); ?>
							</label>
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text bg-dark p-0">
										<?php
											if($captcha->string)
											{
												echo '<b class="text-light pr-3 pl-3">' . $captcha->string . '</b>';
											}
											else
											{
												echo '<img src="' . $captcha->image . '" class="img-fluid" alt="..." />';
											}
										?>
									</span>
								</div>
								<input type="text" name="captcha" class="form-control font-weight-light" id="captcha_input" placeholder="XXXXXX" maxlength="32" />
							</div>
						</div>
					</div>
				</div>
				
				<div class="--validation-callback"></div>
				
				<div class="form-group">
					<button type="submit" class="btn btn-outline-primary btn-block">
						<i class="mdi mdi-check"></i>
						<?php echo phrase('register_account'); ?>
					</button>
				</div>
				<div class="form-group mt-4 mb-4">
					<label class="text-muted d-block">
						<?php echo phrase('by_submitting_this_form_you_are_agree_about_all_future_action_related_to_your_account'); ?>
						<a href="<?php echo base_url('pages/guidelines/terms-and-conditions'); ?>" target="_blank" data-toggle="tooltip" title="<?php echo phrase('detail_of_terms_and_conditions'); ?>">
							<i class="mdi mdi-help-circle-outline"></i>
						</a>
					</label>
				</div>
			</div>
		</div>
	</form>
</div>
<script type="text/javascript">
	$(document).ready(function()
	{
		$('body').off('click.password-peek touch.password-peek');
		$('body').on('click.password-peek touch.password-peek', '.password-peek', function(e)
		{
			e.preventDefault(),
			$(this).toggleClass('peek'),
			($(this).hasClass('peek') ? $(this).removeClass('mdi-eye-outline').addClass('mdi-eye-off-outline').prev('input').attr('type', 'text') : $(this).removeClass('mdi-eye-off-outline').addClass('mdi-eye-outline').prev('input').attr('type', 'password'))
		})
	})
</script>
