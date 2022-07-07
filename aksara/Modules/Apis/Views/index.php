<div class="container-fluid pt-3 pb-3">
	<div class="row">
		<div class="col-md-5">
			<a href="<?php echo go_to('services'); ?>" class="card text-white bg-secondary mb-3 --xhr">
				<div class="card-body pt-2 pe-3 pb-2 ps-3">
					<div class="row">
						<div class="col-3">
							<i class="mdi mdi-link-variant mdi-3x"></i>
						</div>
						<div class="col">
							<h5 class="card-title mb-0">
								<?php echo phrase('services'); ?>
							</h5>
							<p class="card-text">
								<?php echo phrase('manage_service_urls'); ?>
							</p>
						</div>
					</div>
				</div>
			</a>
		</div>
		<div class="col-md-5">
			<a href="<?php echo go_to('clients'); ?>" class="card text-white bg-secondary mb-3 --xhr">
				<div class="card-body pt-2 pe-3 pb-2 ps-3">
					<div class="row">
						<div class="col-3">
							<i class="mdi mdi-account-check-outline mdi-3x"></i>
						</div>
						<div class="col">
							<h5 class="card-title mb-0">
								<?php echo phrase('clients'); ?>
							</h5>
							<p class="card-text">
								<?php echo phrase('manage_api_clients'); ?>
							</p>
						</div>
					</div>
				</div>
			</a>
		</div>
	</div>
	<div class="row">
		<div class="col-md-5">
			<a href="<?php echo go_to('apis'); ?>" class="card text-white bg-secondary mb-3 --xhr">
				<div class="card-body pt-2 pe-3 pb-2 ps-3">
					<div class="row">
						<div class="col-3">
							<i class="mdi mdi-security-network mdi-3x"></i>
						</div>
						<div class="col">
							<h5 class="card-title mb-0">
								<?php echo phrase('permissions'); ?>
							</h5>
							<p class="card-text">
								<?php echo phrase('manage_client_permissions'); ?>
							</p>
						</div>
					</div>
				</div>
			</a>
		</div>
		<div class="col-md-5">
			<a href="<?php echo go_to('debugger'); ?>" class="card text-white bg-secondary mb-3 --xhr">
				<div class="card-body pt-2 pe-3 pb-2 ps-3">
					<div class="row">
						<div class="col-3">
							<i class="mdi mdi-android-debug-bridge mdi-3x"></i>
						</div>
						<div class="col">
							<h5 class="card-title mb-0">
								<?php echo phrase('debug_tool'); ?>
							</h5>
							<p class="card-text">
								<?php echo phrase('debug_created_api_services'); ?>
							</p>
						</div>
					</div>
				</div>
			</a>
		</div>
	</div>
</div>
