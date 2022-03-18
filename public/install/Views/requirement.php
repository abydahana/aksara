<?php
	$error											= false;
	
	if(phpversion() < 7.3 || !in_array('mbstring', $extension) || !in_array('intl', $extension) || !in_array('gd', $extension) || !in_array('json', $extension) || !in_array('xml', $extension))
	{
		$error										= true;
	}
?>
<form action="<?php echo site_url('database'); ?>" method="POST" class="--validate-form">
	<h4>
		<?php echo phrase('awesome'); ?>
	</h4>
	<p>
		<?php echo phrase('you_just_read_our_notes_and_pretend_to_agree_with_it'); ?>
	</p>
	<hr class="row" />
	<p>
		<?php echo phrase('we_will_help_you_to_prepare_your_application_using_this_installation_wizard'); ?>
		<?php echo phrase('before_you_go_make_sure_this_pre_requirements_are_fulfilled_without_any_warning'); ?>
		<?php echo phrase('otherwise_your_application_will_not_work_properly'); ?>
	</p>
	<hr class="row" />
	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
				<label class="d-block mb-0">
					<?php echo phrase('php_version'); ?>
				</label>
				<p>
					<?php echo (phpversion() < 7.3 ? '<b class="text-danger">' . phpversion() . '</b>, ' . phrase('the_minimum_required_version_is') . ' <b>7.3</b>' : '<b class="text-success">' . phpversion() . '</b>'); ?>
				</p>
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<label class="d-block mb-0">
					<?php echo phrase('rewrite_module'); ?>
				</label>
				<p>
					<?php echo (!$mod_rewrite ? '<b class="text-danger">' . phrase('off') . '</b>, ' . phrase('turn_it_on') : '<b class="text-success">' . phrase('on') . '</b>'); ?>
				</p>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
				<label class="d-block mb-0">
					<?php echo phrase('internationalization'); ?> (intl)
				</label>
				<p>
					<?php echo (!in_array('intl', $extension) ? '<b class="text-danger">' . phrase('off') . '</b>, ' . phrase('turn_it_on') : '<b class="text-success">' . phrase('on') . '</b>'); ?>
				</p>
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<label class="d-block mb-0">
					<?php echo phrase('zip_archiver'); ?>
				</label>
				<p>
					<?php echo (!class_exists('ZipArchive') ? '<b class="text-danger">' . phrase('disabled') . '</b>' : '<b class="text-success">' . phrase('available') . '</b>'); ?>
				</p>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
				<label class="d-block mb-0">
					<?php echo phrase('multibyte_string'); ?> (mbstring)
				</label>
				<p>
					<?php echo (!in_array('mbstring', $extension) ? '<b class="text-danger">' . phrase('off') . '</b>, ' . phrase('turn_it_on') : '<b class="text-success">' . phrase('on') . '</b>'); ?>
				</p>
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<label class="d-block mb-0">
					<?php echo phrase('php_gd'); ?>
				</label>
				<p>
					<?php echo (!in_array('gd', $extension) ? '<b class="text-danger">' . phrase('off') . '</b>, ' . phrase('turn_it_on') : '<b class="text-success">' . phrase('on') . '</b>'); ?>
				</p>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
				<label class="d-block mb-0">
					<?php echo phrase('json'); ?>
				</label>
				<p>
					<?php echo (!in_array('json', $extension) ? '<b class="text-danger">' . phrase('off') . '</b>, ' . phrase('turn_it_on') : '<b class="text-success">' . phrase('on') . '</b>'); ?>
				</p>
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<label class="d-block mb-0">
					<?php echo phrase('xml'); ?>
				</label>
				<p>
					<?php echo (!in_array('xml', $extension) ? '<b class="text-danger">' . phrase('off') . '</b>, ' . phrase('turn_it_on') : '<b class="text-success">' . phrase('on') . '</b>'); ?>
				</p>
			</div>
		</div>
	</div>
	<?php echo ($error ? '<div class="alert alert-warning failure"><b>' . phrase('whoops') . '</b> ' . phrase('some_requirement_are_not_yet_fulfilled') . ' ' . phrase('please_update_your_server_configuration_and_click_on_refresh_button_to_continue_the_installation') . '</div>' : (!$mod_rewrite ? '<div class="alert alert-warning failure"><b>' . phrase('whoops') . '</b> ' . phrase('the_rewrite_module_is_disabled_by_your_server') . ' ' . phrase('you_can_continue_the_installation_but_we_recommend_to_enable_it') . '</div>' : null)); ?>
	<hr class="row" />
	<div class="--validation-callback"></div>
	<div class="row">
		<div class="col-md-6">
			<?php echo ($error ? '<a href="' . site_url('requirement') . '" class="btn btn-light btn-block --xhr"><i class="mdi mdi-reload"></i> ' . phrase('refresh') . '</a>' : '&nbsp;'); ?>
		</div>
		<div class="col-md-6 text-right">
			<input type="hidden" name="_token" value="<?php echo sha1(time()); ?>" />
			<button type="submit" class="btn btn-primary btn-block"<?php echo ($error ? ' disabled' : null); ?>>
				<i class="mdi mdi-check"></i>
				<?php echo phrase('continue'); ?>
			</button>
		</div>
	</div>
</form>