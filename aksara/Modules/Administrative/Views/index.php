<div class="container-fluid pt-3 pb-3">
	<?php if(1 == get_userdata('group_id')) { ?>
	<div class="row">
		<div class="col-md-4">
			<a href="<?php echo go_to('users'); ?>" class="card text-white bg-secondary mb-3 --xhr">
				<div class="card-body pt-2 pe-3 pb-2 ps-3">
					<div class="row">
						<div class="col-3">
							<i class="mdi mdi-account-group mdi-3x"></i>
						</div>
						<div class="col">
							<h5 class="card-title mb-0">
								<?php echo phrase('users'); ?>
							</h5>
							<p class="card-text">
								<?php echo phrase('manage_users'); ?>
							</p>
						</div>
					</div>
				</div>
			</a>
		</div>
		<div class="col-md-4">
			<a href="<?php echo go_to('groups'); ?>" class="card text-white bg-secondary mb-3 --xhr">
				<div class="card-body pt-2 pe-3 pb-2 ps-3">
					<div class="row">
						<div class="col-3">
							<i class="mdi mdi-sitemap mdi-3x"></i>
						</div>
						<div class="col">
							<h5 class="card-title mb-0">
								<?php echo phrase('groups'); ?>
							</h5>
							<p class="card-text">
								<?php echo phrase('manage_groups'); ?>
							</p>
						</div>
					</div>
				</div>
			</a>
		</div>
		<div class="col-md-4">
			<a href="<?php echo go_to('groups/privileges'); ?>" class="card text-white bg-secondary mb-3 --xhr">
				<div class="card-body pt-2 pe-3 pb-2 ps-3">
					<div class="row">
						<div class="col-3">
							<i class="mdi mdi-account-check-outline mdi-3x"></i>
						</div>
						<div class="col">
							<h5 class="card-title mb-0">
								<?php echo phrase('privileges'); ?>
							</h5>
							<p class="card-text">
								<?php echo phrase('manage_group_privileges'); ?>
							</p>
						</div>
					</div>
				</div>
			</a>
		</div>
	</div>
	<div class="row">
		<div class="col-md-4">
			<a href="<?php echo go_to('settings'); ?>" class="card text-white bg-secondary mb-3 --xhr">
				<div class="card-body pt-2 pe-3 pb-2 ps-3">
					<div class="row">
						<div class="col-3">
							<i class="mdi mdi-wrench mdi-flip-h mdi-3x"></i>
						</div>
						<div class="col">
							<h5 class="card-title mb-0">
								<?php echo phrase('site_settings'); ?>
							</h5>
							<p class="card-text">
								<?php echo phrase('update_site_configuration'); ?>
							</p>
						</div>
					</div>
				</div>
			</a>
		</div>
		<div class="col-md-4">
			<a href="<?php echo go_to('menus'); ?>" class="card text-white bg-secondary mb-3 --xhr">
				<div class="card-body pt-2 pe-3 pb-2 ps-3">
					<div class="row">
						<div class="col-3">
							<i class="mdi mdi-menu mdi-3x"></i>
						</div>
						<div class="col">
							<h5 class="card-title mb-0">
								<?php echo phrase('menus'); ?>
							</h5>
							<p class="card-text">
								<?php echo phrase('manage_menus'); ?>
							</p>
						</div>
					</div>
				</div>
			</a>
		</div>
		<div class="col-md-4">
			<a href="<?php echo go_to('translations'); ?>" class="card text-white bg-secondary mb-3 --xhr">
				<div class="card-body pt-2 pe-3 pb-2 ps-3">
					<div class="row">
						<div class="col-3">
							<i class="mdi mdi-translate mdi-3x"></i>
						</div>
						<div class="col">
							<h5 class="card-title mb-0">
								<?php echo phrase('translations'); ?>
							</h5>
							<p class="card-text">
								<?php echo phrase('manage_translations'); ?>
							</p>
						</div>
					</div>
				</div>
			</a>
		</div>
	</div>
	<div class="row">
		<div class="col-md-4">
			<a href="<?php echo go_to('countries'); ?>" class="card text-white bg-secondary mb-3 --xhr">
				<div class="card-body pt-2 pe-3 pb-2 ps-3">
					<div class="row">
						<div class="col-3">
							<i class="mdi mdi-account-edit mdi-3x"></i>
						</div>
						<div class="col">
							<h5 class="card-title mb-0">
								<?php echo phrase('countries'); ?>
							</h5>
							<p class="card-text">
								<?php echo phrase('manage_countries'); ?>
							</p>
						</div>
					</div>
				</div>
			</a>
		</div>
		<div class="col-md-4">
			<a href="<?php echo go_to('logs'); ?>" class="card text-white bg-secondary mb-3 --xhr">
				<div class="card-body pt-2 pe-3 pb-2 ps-3">
					<div class="row">
						<div class="col-3">
							<i class="mdi mdi-information-outline mdi-3x"></i>
						</div>
						<div class="col">
							<h5 class="card-title mb-0">
								<?php echo phrase('logs'); ?>
							</h5>
							<p class="card-text">
								<?php echo phrase('check_app_logs'); ?>
							</p>
						</div>
					</div>
				</div>
			</a>
		</div>
	</div>
	<?php } ?>
	<div class="row">
		<div class="col-md-4">
			<a href="<?php echo go_to('account'); ?>" class="card text-white bg-secondary mb-3 --xhr">
				<div class="card-body pt-2 pe-3 pb-2 ps-3">
					<div class="row">
						<div class="col-3">
							<i class="mdi mdi-cogs mdi-3x"></i>
						</div>
						<div class="col">
							<h5 class="card-title mb-0">
								<?php echo phrase('account'); ?>
							</h5>
							<p class="card-text">
								<?php echo phrase('update_your_profile_info'); ?>
							</p>
						</div>
					</div>
				</div>
			</a>
		</div>
	</div>
</div>
