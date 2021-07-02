<div class="container-fluid pt-3 pb-3">
	<a href="<?php echo base_url('dashboard'); ?>" class="d-block">
		<i class="mdi mdi-monitor-dashboard"></i>
		<?php echo phrase('dashboard'); ?>
	</a>
	<hr />
	<a href="<?php echo base_url('user'); ?>" class="d-block">
		<i class="mdi mdi-account-circle-outline"></i>
		<?php echo phrase('profile'); ?>
	</a>
	<hr />
	<a href="<?php echo base_url('administrative/account'); ?>" class="d-block">
		<i class="mdi mdi-account-outline"></i>
		<?php echo phrase('account'); ?>
	</a>
	<hr />
	<a href="<?php echo base_url('auth/sign_out'); ?>" class="--xhr d-block text-danger">
		<i class="mdi mdi-logout"></i>
		<?php echo phrase('sign_out'); ?>
	</a>
</div>