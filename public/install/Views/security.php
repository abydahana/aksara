<form action="<?php echo base_url('system'); ?>" method="POST" class="--validate-form">
	<h4>
		<?php echo phrase('security_configuration'); ?>
	</h4>
	<p>
		<?php echo phrase('enter_your_secret_formula_to_secure_your_application'); ?>
	</p>
	<hr class="row" />
	<div class="form-group">
		<label class="d-block mb-0">
			<?php echo phrase('encryption_key'); ?>
			<b class="text-danger">*</b>
		</label>
		<input type="text" name="encryption" class="form-control form-control-sm" placeholder="<?php echo phrase('your_encryption_key'); ?>" value="<?php echo $encryption_key; ?>" />
	</div>
	<div class="form-group">
		<label class="d-block mb-0">
			<?php echo phrase('cookie_name'); ?>
			<b class="text-danger">*</b>
		</label>
		<input type="text" name="cookie_name" class="form-control form-control-sm" placeholder="<?php echo phrase('unique_cookie_name_to_prevent_conflict'); ?>" value="<?php echo $cookie_name; ?>" />
	</div>
	<br/>
	<h5>
		<?php echo phrase('superuser'); ?>
	</h5>
	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
				<label class="d-block mb-0">
					<?php echo phrase('first_name'); ?>
					<b class="text-danger">*</b>
				</label>
				<input type="text" name="first_name" class="form-control form-control-sm" placeholder="e.g: John" value="<?php echo session()->get('first_name'); ?>" />
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<label class="d-block mb-0">
					<?php echo phrase('last_name'); ?>
				</label>
				<input type="text" name="last_name" class="form-control form-control-sm" placeholder="e.g: Doe" value="<?php echo session()->get('last_name'); ?>" />
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
				<label class="d-block mb-0">
					<?php echo phrase('email_address'); ?>
					<b class="text-danger">*</b>
				</label>
				<input type="email" name="email" class="form-control form-control-sm" placeholder="e.g: johndoe@example.com" value="<?php echo session()->get('email'); ?>" />
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<label class="d-block mb-0">
					<?php echo phrase('username'); ?>
					<b class="text-danger">*</b>
				</label>
				<input type="text" name="username" class="form-control form-control-sm" placeholder="<?php echo phrase('create_username_for_superuser'); ?>" value="<?php echo session()->get('username'); ?>" />
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
				<label class="d-block mb-0">
					<?php echo phrase('password'); ?>
					<b class="text-danger">*</b>
				</label>
				<input type="password" name="password" class="form-control form-control-sm" placeholder="<?php echo phrase('password_for_superuser'); ?>" />
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<label class="d-block mb-0">
					<?php echo phrase('confirm_password'); ?>
					<b class="text-danger">*</b>
				</label>
				<input type="password" name="confirm_password" class="form-control form-control-sm" placeholder="<?php echo phrase('retype_the_password'); ?>" />
			</div>
		</div>
	</div>
	<hr class="row" />
	<div class="--validation-callback"></div>
	<div class="row">
		<div class="col-sm-6">
			<a href="<?php echo base_url('database'); ?>" class="btn btn-light btn-block --xhr">
				<i class="mdi mdi-arrow-left"></i>
				<?php echo phrase('back'); ?>
			</a>
		</div>
		<div class="col-sm-6 text-right">
			<input type="hidden" name="_token" value="<?php echo sha1(time()); ?>" />
			<button type="submit" class="btn btn-primary btn-block">
				<i class="mdi mdi-check"></i>
				<?php echo phrase('continue'); ?>
			</button>
		</div>
	</div>
</form>