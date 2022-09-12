<div class="container-fluid pt-3 pb-3">
	<div class="row">
		<div class="col-md-4">
			<a href="<?php echo go_to('activities'); ?>" class="card text-white bg-secondary mb-3 --xhr">
				<div class="card-body pt-2 pe-3 pb-2 ps-3">
					<div class="row">
						<div class="col-3">
							<i class="mdi mdi-calendar-clock mdi-3x"></i>
						</div>
						<div class="col">
							<h5 class="card-title mb-0">
								<?php echo phrase('activities'); ?>
							</h5>
							<p class="card-text">
								<?php echo phrase('show_activity_logs'); ?>
							</p>
						</div>
					</div>
				</div>
			</a>
		</div>
		<div class="col-md-4">
			<a href="<?php echo go_to('errors'); ?>" class="card text-white bg-secondary mb-3 --xhr">
				<div class="card-body pt-2 pe-3 pb-2 ps-3">
					<div class="row">
						<div class="col-3">
							<i class="mdi mdi-bug mdi-3x"></i>
						</div>
						<div class="col">
							<h5 class="card-title mb-0">
								<?php echo phrase('errors'); ?>
							</h5>
							<p class="card-text">
								<?php echo phrase('show_error_logs'); ?>
							</p>
						</div>
					</div>
				</div>
			</a>
		</div>
	</div>
</div>
