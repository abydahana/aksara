<form action="<?php echo site_url('run'); ?>" method="POST" class="--validate-form">
	<h4>
		<?php echo phrase('all_catched_up'); ?>
	</h4>
	<p>
		<?php echo phrase('your_application_is_ready_to_install_using_the_provided_configurations'); ?>
	</p>
	<hr class="row" />
	<p>
		<b>
			<?php echo phrase('just_one_more_step'); ?>
		</b>
	</p>
	<p>
		<?php echo phrase('make_sure_what_you_filled_in_on_the_previous_form_is_correct'); ?>
		<?php echo phrase('once_you_have_successfully_run_the_installer_there_is_no_more_back_button'); ?>
	</p>
	<p>
		<?php echo phrase('click_run_installer_to_applying_your_configuration'); ?>
	</p>
	<hr class="row" />
	<div class="--validation-callback"></div>
	<div class="row">
		<div class="col-sm-6">
			<a href="<?php echo site_url('system'); ?>" class="btn btn-light btn-block --xhr">
				<i class="mdi mdi-arrow-left"></i>
				<?php echo phrase('back'); ?>
			</a>
		</div>
		<div class="col-sm-6 text-right">
			<input type="hidden" name="_token" value="<?php echo sha1(time()); ?>" />
			<button type="submit" class="btn btn-primary btn-block">
				<i class="mdi mdi-check"></i>
				<?php echo phrase('run_installer'); ?>
			</button>
		</div>
	</div>
</form>