<div class="container-fluid pt-3 pb-3">
	<div class="row">
		<div class="col-md-5">
			<a href="<?php echo base_url('administrative/profile'); ?>" class="card text-white bg-secondary mb-3 --xhr">
				<div class="card-body">
					<div class="row">
						<div class="col-3">
							<i class="mdi mdi-account-circle-outline mdi-3x"></i>
						</div>
						<div class="col">
							<h5 class="card-title mb-0">
								<?php echo phrase('view_profile'); ?>
							</h5>
							<p class="card-text">
								<?php echo phrase('show_your_profile_info'); ?>
							</p>
						</div>
					</div>
				</div>
			</a>
		</div>
	</div>
	<div class="row">
		<div class="col-md-5">
			<a href="<?php echo base_url('administrative/account'); ?>" class="card text-white bg-secondary mb-3 --xhr">
				<div class="card-body">
					<div class="row">
						<div class="col-3">
							<i class="mdi mdi-account-edit mdi-3x"></i>
						</div>
						<div class="col">
							<h5 class="card-title mb-0">
								<?php echo phrase('update_profile'); ?>
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