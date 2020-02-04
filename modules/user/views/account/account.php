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
						<a class="nav-link active no-wrap --xhr" href="<?php echo base_url('user/account'); ?>">
							<i class="mdi mdi-account-edit"></i>
							<?php echo phrase('general'); ?>
						</a>
					</li>
					<li class="nav-item text-center">
						<a class="nav-link no-wrap --xhr" href="<?php echo base_url('user/account/security'); ?>">
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
				<div class="row">
					<div class="col-6">
						<div class="form-group">
							<label class="text-muted d-block" for="first_name_input">
								<?php echo $form_data->first_name->label; ?>
							</label>
							<?php echo $form_data->first_name->content; ?>
							<?php echo ($form_data->first_name->required ? '<div class="invalid-feedback d-block">' . phrase('required') . '</div>' : null); ?>
						</div>
					</div>
					<div class="col-6">
						<div class="form-group">
							<label class="text-muted d-block" for="last_name_input">
								<?php echo $form_data->last_name->label; ?>
							</label>
							<?php echo $form_data->last_name->content; ?>
							<?php echo ($form_data->last_name->required ? '<div class="invalid-feedback d-block">' . phrase('required') . '</div>' : null); ?>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="text-muted d-block" for="email_input">
						<?php echo $form_data->email->label; ?>
					</label>
					<?php echo $form_data->email->content; ?>
					<?php echo ($form_data->email->required ? '<div class="invalid-feedback d-block">' . phrase('required') . '</div>' : null); ?>
				</div>
				<?php if(get_setting('enable_username_changes')) { ?>
				<div class="form-group">
					<label class="text-muted d-block" for="username_input">
						<?php echo $form_data->username->label; ?>
					</label>
					<div class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text">
								<span class="d-none d-md-block d-lg-block d-xl-block">
									<?php echo base_url(); ?>
								</span>
								user/
							</span>
						</div>
						<?php echo $form_data->username->content; ?>
					</div>
					<?php echo ($form_data->username->required ? '<div class="invalid-feedback d-block">' . phrase('required') . '</div>' : null); ?>
				</div>
				<?php } ?>
				<div class="form-group">
					<label class="text-muted d-block" for="bio_input">
						<?php echo $form_data->bio->label; ?>
					</label>
					<?php echo $form_data->bio->content; ?>
					<?php echo ($form_data->bio->required ? '<div class="invalid-feedback d-block">' . phrase('required') . '</div>' : null); ?>
				</div>
				<div class="form-group">
					<label class="text-muted d-block" for="address_input">
						<?php echo $form_data->address->label; ?>
					</label>
					<?php echo $form_data->address->content; ?>
					<?php echo ($form_data->address->required ? '<div class="invalid-feedback d-block">' . phrase('required') . '</div>' : null); ?>
				</div>
				<div class="row">
					<div class="col-sm-6">
						<div class="form-group">
							<label class="text-muted d-block" for="country_input">
								<?php echo $form_data->country->label; ?>
							</label>
							<?php echo $form_data->country->content; ?>
							<?php echo ($form_data->country->required ? '<div class="invalid-feedback d-block">' . phrase('required') . '</div>' : null); ?>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<label class="text-muted d-block" for="language_input">
								<?php echo $form_data->language->label; ?>
							</label>
							<?php echo $form_data->language->content; ?>
							<?php echo ($form_data->language->required ? '<div class="invalid-feedback d-block">' . phrase('required') . '</div>' : null); ?>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-2 col-md-2">
				<label class="text-muted d-block" for="photo_input">
					<?php echo $form_data->photo->label; ?>
				</label>
				<?php echo $form_data->photo->content; ?>
				<?php echo ($form_data->photo->required ? '<div class="invalid-feedback d-block">' . phrase('required') . '</div>' : null); ?>
			</div>
		</div>
		<div class="opt-btn-overlap-fix"></div><!-- fix the overlap -->
		<div class="row opt-btn">
			<div class="col-md-6 offset-md-1">
				<input type="hidden" name="token" value="<?php echo $token; ?>" />
				<a href="<?php echo base_url('user'); ?>" class="btn btn-link --xhr">
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