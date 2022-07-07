<div class="container-fluid pt-3 pb-3">
	<form action="<?php echo current_page(); ?>" method="POST" class="--validate-form" enctype="multipart/form-data">
		<div class="row">
			<div class="col-md-2">
				<img src="<?php echo get_image('users', $userdata->photo, 'thumb'); ?>" class="img-fluid rounded" alt="..." />
			</div>
			<div class="col-md-8">
				<div class="row">
					<label class="col-4 col-md-3 text-muted">
						<?php echo phrase('user_id'); ?>
					</label>
					<label class="col-8 col-md-9">
						<?php echo $userdata->user_id; ?>
					</label>
				</div>
				<div class="row">
					<label class="col-4 col-md-3 text-muted">
						<?php echo phrase('username'); ?>
					</label>
					<label class="col-8 col-md-9">
						<?php echo $userdata->username; ?>
					</label>
				</div>
				<div class="row">
					<label class="col-4 col-md-3 text-muted">
						<?php echo phrase('full_name'); ?>
					</label>
					<label class="col-8 col-md-9">
						<?php echo $userdata->first_name . ' ' . $userdata->last_name; ?>
					</label>
				</div>
				<div class="row">
					<label class="col-4 col-md-3 text-muted">
						<?php echo phrase('user_group'); ?>
					</label>
					<label class="col-8 col-md-9">
						<?php echo $userdata->group_name; ?>
					</label>
				</div>
				<?php if($year) { ?>
				<div class="row">
					<label class="col-4 col-md-3 text-muted">
						<?php echo phrase('access_year'); ?>
					</label>
					<label class="col-8 col-md-4">
						<?php
							$options				= null;
							if($year)
							{
								foreach($year as $key => $val)
								{
									$options		.= '<option value="' . $val->year . '"' . (isset($form_data->access_year) && $form_data->access_year == $val->year ? ' selected' : null) . '>' . $val->year . '</option>';
								}
							}
						?>
						<select name="access_year" class="form-control" placeholder="<?php echo phrase('please_choose'); ?>">
							<?php echo $options; ?>
						</select>
					</label>
				</div>
				<?php } ?>
			</div>
		</div>
		<hr class="row" />
		<div class="row">
			<div class="col-md-5">
				<div class="mb-3">
					<label class="text-muted d-block" for="menus_input">
						<?php echo phrase('accessible_menus'); ?>
					</label>
					<?php echo $visible_menu; ?>
				</div>
			</div>
			<div class="col-md-5">
				<div class="mb-3">
					<label class="text-muted d-block" for="kegiatan_input">
						<?php echo phrase('the_sub_level_can_be_accessed'); ?>
					</label>
					<?php
						if($sub_level_1)
						{
							$option				= null;
							foreach($sub_level_1 as $key => $val)
							{
								$option			.= '<option value="' . $val->id . '"' . ($val->id == $userdata->sub_level_1 ? ' selected' : null) . '>' . $val->nm_uptd . '</option>';
							}
							
							echo '
								<select name="sub_level_1" class="form-control" id="sub_level_1_input" placeholder="' . phrase('please_choose') . '">
									' . $option . '
								</select>
							';
						}
						else
						{
							echo '
								<div class="alert alert-warning">
									' . phrase('no_sub_level_are_available') . '
								</div>
							';
						}
					?>
				</div>
			</div>
		</div>
		<div class="opt-btn-overlap-fix"></div><!-- fix the overlap -->
		<div class="row opt-btn">
			<div class="col-md-10">
			
				<div class="--validation-callback mb-0"></div>
				
				<a href="<?php echo current_page('../', array('user_id' => null)); ?>" class="btn btn-link --xhr">
					<i class="mdi mdi-arrow-left"></i>
					&nbsp;
					<?php echo phrase('back'); ?>
				</a>
				<button type="submit" class="btn btn-primary float-end">
					<i class="mdi mdi-check"></i>
					&nbsp;
					<?php echo phrase('update'); ?>
					<em class="text-sm">(ctrl+s)</em>
				</button>
			</div>
		</div>
	</form>
</div>
