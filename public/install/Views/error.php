<form action="<?php echo site_url('run'); ?>" method="POST" class="--validate-form">
	<h4>
		<?php echo phrase('the_installer_was_interrupted'); ?>
	</h4>
	<p>
		<?php echo phrase('the_installer_was_unable_to_write_the_configuration_file') . ' ' . phrase('please_follow_the_instructions_below_to_continue'); ?>
	</p>
	<hr class="row" />
	<p class="mb-0">
		<b>
			<?php echo phrase('problem_found'); ?>:
		</b>
	</p>
	<p class="text-break-word">
		<?php echo phrase('unable_to_create_or_write_file'); ?>
	</p>
	<p class="mb-0">
		<b>
			<?php echo phrase('solution'); ?>:
		</b>
	</p>
	<p class="text-break-word">
		<?php echo phrase('please_download_the_configuration_file_below_and_upload_or_paste_it_manually_under_the_following_folder') . ' <code>' . substr(ROOTPATH, 0, strrpos(ROOTPATH, '/public')) . '</code>'; ?>
		<br />
		<a href="<?php echo site_url('run'); ?>?download=1" target="_blank" class="btn btn-success btn-sm">
			<i class="mdi mdi-download"></i>
			<?php echo phrase('download_configuration'); ?>
		</a>
	</p>
	<p>
		<?php echo phrase('once_the_configuration_file_were_uploaded_please_click_the_refresh_button_to_continue_the_installation'); ?>
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
				<i class="mdi mdi-reload"></i>
				<?php echo phrase('refresh'); ?>
			</button>
		</div>
	</div>
</form>