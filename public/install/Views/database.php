<form action="<?php echo site_url('security'); ?>" method="POST" class="--validate-form">
	<h4>
		<?php echo phrase('database_configuration'); ?>
	</h4>
	<p>
		<?php echo phrase('fill_the_requested_fields_below_with_your_database_connection'); ?>
	</p>
	<hr class="row" />
	<?php
		$driver_list								= null;
		
		foreach($driver as $key => $val)
		{
			$driver_list							.= '<option value="' . $val . '"' . (session()->get('database_driver') == $val ? ' selected' : null) . '>' . $val . '</option>';
		}
	?>
	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
				<label class="d-block mb-0">
					<?php echo phrase('database_driver'); ?>
					<b class="text-danger">*</b>
				</label>
				<select name="database_driver" class="form-control form-control-sm">
					<?php echo $driver_list; ?>
				</select>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-8">
			<div class="form-group">
				<label class="d-block mb-0">
					<?php echo phrase('hostname'); ?>
					<b class="text-danger">*</b>
				</label>
				<input type="text" name="database_hostname" class="form-control form-control-sm" placeholder="e.g: localhost" value="<?php echo session()->get('database_hostname'); ?>" />
			</div>
		</div>
		<div class="col-md-4">
			<div class="form-group">
				<label class="d-block mb-0">
					<?php echo phrase('port'); ?>
				</label>
				<input type="number" name="database_port" class="form-control form-control-sm" placeholder="e.g: 3306" value="<?php echo session()->get('database_port'); ?>" />
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
				<label class="d-block mb-0">
					<?php echo phrase('username'); ?>
					<b class="text-danger">*</b>
				</label>
				<input type="text" name="database_username" class="form-control form-control-sm" placeholder="e.g: root" value="<?php echo session()->get('database_username'); ?>" />
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<label class="d-block mb-0">
					<?php echo phrase('password'); ?>
				</label>
				<input type="password" name="database_password" class="form-control form-control-sm" value="<?php echo session()->get('database_password'); ?>" />
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="form-group">
				<label class="d-block mb-0">
					<?php echo phrase('initial_database'); ?>
					<b class="text-danger">*</b>
				</label>
				<input type="text" name="database_initial" class="form-control form-control-sm" value="<?php echo session()->get('database_initial'); ?>" />
			</div>
		</div>
	</div>
	<hr class="row" />
	<div class="--validation-callback"></div>
	<div class="row">
		<div class="col-sm-6">
			<a href="<?php echo site_url('requirement'); ?>" class="btn btn-light btn-block --xhr">
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