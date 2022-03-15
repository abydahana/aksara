<form action="<?php echo site_url('finalizing'); ?>" method="POST" class="--validate-form">
	<h4>
		<?php echo phrase('system_configuration'); ?>
	</h4>
	<p>
		<?php echo phrase('enter_the_basic_system_configuration'); ?>
		<?php echo phrase('you_will_able_to_change_it_after_installation'); ?>
	</p>
	<hr class="row" />
	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
				<label class="d-block mb-0">
					<?php echo phrase('installation_mode'); ?>
					<b class="text-danger">*</b>
				</label>
				<select name="installation_mode" class="form-control form-control-sm">
					<option value="0"<?php echo (!session()->get('installation_mode') ? ' selected' : null); ?>><?php echo phrase('developer_without_sample'); ?></option>
					<option value="1"<?php echo (session()->get('installation_mode') ? ' selected' : null); ?>><?php echo phrase('basic_with_sample'); ?></option>
				</select>
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<label class="d-block mb-0">
					<?php echo phrase('timezone'); ?>
					<b class="text-danger">*</b>
				</label>
				<?php
					$timezone_list					= null;
					
					foreach($timezone as $key => $val)
					{
						$timezone_list				.= '<option value="' . $val . '"' . (session()->get('timezone') == $val ? ' selected' : null) . '>' . $val . '</option>';
					}
				?>
				<select name="timezone" class="form-control form-control-sm">
					<?php echo $timezone_list; ?>
				</select>
			</div>
		</div>
	</div>
	<hr />
	<h5>
		<?php echo phrase('site_settings'); ?>
	</h5>
	<div class="row">
		<div class="col-md-12">
			<div class="form-group">
				<label class="d-block mb-0">
					<?php echo phrase('site_title'); ?>
					<b class="text-danger">*</b>
				</label>
				<input type="text" name="site_title" class="form-control form-control-sm" placeholder="<?php echo phrase('enter_the_site_title'); ?>" value="<?php echo session()->get('site_title'); ?>" />
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="form-group">
				<label class="d-block mb-0">
					<?php echo phrase('site_description'); ?>
					<b class="text-danger">*</b>
				</label>
				<textarea name="site_description" class="form-control form-control-sm" placeholder="<?php echo phrase('enter_the_site_description'); ?>" rows="1"><?php echo session()->get('site_description'); ?></textarea>
			</div>
		</div>
	</div>
	<hr/>
	<h5>
		<?php echo phrase('upload_setting'); ?>
	</h5>
	<div class="form-group">
		<label class="d-block mb-0">
			<?php echo phrase('allowed_file_extension'); ?>
			<b class="text-danger">*</b>
		</label>
		<input type="text" name="file_extension" class="form-control form-control-sm" placeholder="<?php echo phrase('separate_with_comma'); ?>" value="<?php echo (session()->get('file_extension') ? session()->get('file_extension') : 'jpg,jpeg,gif,png,pdf,xls,xlsx,doc,docx,csv'); ?>" />
	</div>
	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
				<label class="d-block mb-0">
					<?php echo phrase('allowed_image_extension'); ?>
					<b class="text-danger">*</b>
				</label>
				<input type="text" name="image_extension" class="form-control form-control-sm" placeholder="<?php echo phrase('separate_with_comma'); ?>" value="<?php echo (session()->get('image_extension') ? session()->get('image_extension') : 'jpg,png,gif'); ?>" />
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<label class="d-block mb-0">
					<?php echo phrase('maximum_upload_size'); ?>
					<b class="text-danger">*</b>
				</label>
				<div class="input-group input-group-sm">
					<input type="number" name="max_upload_size" class="form-control form-control-sm" placeholder="e.g: 2048" value="<?php echo (session()->get('max_upload_size') ? session()->get('max_upload_size') : 10); ?>" />
					<div class="input-group-append">
						<span class="input-group-text">
							MB
						</span>
					</div>
				</div>
			</div>
		</div>
	</div>
	<hr/>
	<h5>
		<?php echo phrase('image_width_dimension'); ?> (px)
	</h5>
	<div class="row">
		<div class="col-md-4">
			<div class="form-group">
				<label class="d-block mb-0">
					<?php echo phrase('original'); ?>
					<b class="text-danger">*</b>
				</label>
				<div class="input-group input-group-sm">
					<input type="number" name="image_dimension" class="form-control form-control-sm" placeholder="in pixel" value="<?php echo (session()->get('image_dimension') ? session()->get('image_dimension') : 1024); ?>" />
					<div class="input-group-append">
						<span class="input-group-text">
							px
						</span>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-4">
			<div class="form-group">
				<label class="d-block mb-0">
					<?php echo phrase('thumbnail'); ?>
					<b class="text-danger">*</b>
				</label>
				<div class="input-group input-group-sm">
					<input type="number" name="thumbnail_dimension" class="form-control form-control-sm" placeholder="in pixel" value="<?php echo (session()->get('thumbnail_dimension') ? session()->get('thumbnail_dimension') : 256); ?>" />
					<div class="input-group-append">
						<span class="input-group-text">
							px
						</span>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-4">
			<div class="form-group">
				<label class="d-block mb-0">
					<?php echo phrase('icon'); ?>
					<b class="text-danger">*</b>
				</label>
				<div class="input-group input-group-sm">
					<input type="number" name="icon_dimension" class="form-control form-control-sm" placeholder="in pixel" value="<?php echo (session()->get('icon_dimension') ? session()->get('icon_dimension') : 64); ?>" />
					<div class="input-group-append">
						<span class="input-group-text">
							px
						</span>
					</div>
				</div>
			</div>
		</div>
	</div>
	<hr class="row" />
	<div class="--validation-callback"></div>
	<div class="row">
		<div class="col-6">
			<a href="<?php echo site_url('security'); ?>" class="btn btn-light btn-block --xhr">
				<i class="mdi mdi-arrow-left"></i>
				<?php echo phrase('back'); ?>
			</a>
		</div>
		<div class="col-6">
			<input type="hidden" name="_token" value="<?php echo sha1(time()); ?>" />
			<button type="submit" class="btn btn-primary btn-block">
				<i class="mdi mdi-check"></i>
				<?php echo phrase('continue'); ?>
			</button>
		</div>
	</div>
</form>