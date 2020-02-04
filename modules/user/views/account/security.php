<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php $form_data = $results->form_data; ?>
<div class="container-fluid sticky-top bg-white border-bottom pt-2" style="top:56px;z-index:1021">
	<div class="">
		<div class="row">
			<div class="col-md-8 offset-md-1 mb-2">
				<ul class="nav nav-pills">
					<li class="nav-item text-center">
						<a class="nav-link no-wrap --xhr" href="<?php echo base_url('user'); ?>">
							<i class="mdi mdi-arrow-left"></i>
							<?php echo phrase('profile'); ?>
						</a>
					</li>
					<li class="nav-item text-center">
						<a class="nav-link no-wrap --xhr" href="<?php echo base_url('user/account'); ?>">
							<i class="mdi mdi-account-edit"></i>
							<?php echo phrase('general'); ?>
						</a>
					</li>
					<li class="nav-item text-center">
						<a class="nav-link active no-wrap --xhr" href="<?php echo base_url('user/account/security'); ?>">
							<i class="mdi mdi-security"></i>
							<?php echo phrase('security'); ?>
						</a>
					</li>
					<li class="nav-item text-center">
						<a class="nav-link no-wrap --xhr" href="<?php echo base_url('user/account/privacy'); ?>">
							<i class="mdi mdi-incognito"></i>
							<?php echo phrase('privacy'); ?>
						</a>
					</li>
				</ul>
			</div>
			<div class="col-md-2 mb-2 d-none">
				<button type="button" class="btn btn-outline-danger btn-block">
					<i class="mdi mdi-power-off"></i>
					<?php echo phrase('deactivate'); ?>
				</button>
			</div>
		</div>
	</div>
</div>
<div class="container-fluid pt-5 pb-5">
	<form action="<?php echo current_page(); ?>" method="POST" class="--validate-form">
		<div class="row">
			<div class="col-md-6 offset-md-1">
				<div class="alert alert-warning">
					<?php echo phrase('submit_only_if_you_want_to_change_your_current_password'); ?>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label class="text-muted d-block" for="old_password_input">
								<?php echo phrase('current_password'); ?>
							</label>
							<input type="password" name="old_password" class="form-control" id="old_password_input" />
							<div class="invalid-feedback d-block"></div>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="text-muted d-block" for="password_input">
						<?php echo $form_data->password->label; ?>
					</label>
					<?php echo $form_data->password->content; ?>
					<?php echo ($form_data->password->required ? '<div class="invalid-feedback d-block">' . phrase('required') . '</div>' : null); ?>
				</div>
			</div>
		</div>
		<div class="opt-btn-overlap-fix"></div><!-- fix the overlap -->
		<div class="row opt-btn">
			<div class="col-md-6 offset-md-1">
				<input type="hidden" name="token" value="<?php echo $token; ?>" />
				<a href="<?php echo base_url('user/account'); ?>" class="btn btn-link --xhr">
					<i class="mdi mdi-arrow-left"></i>
					<?php echo phrase('back'); ?>
				</a>
				<button type="submit" class="btn btn-primary float-right">
					<i class="mdi mdi-check"></i>
					<?php echo phrase('submit'); ?>
					<em class="text-sm">(ctrl+s)</em>
				</button>
			</div>
		</div>
	</form>
</div>