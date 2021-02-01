<div class="container-fluid">
	<div class="row border-bottom pt-3">
		<div class="col-6 col-lg-3 mb-3">
			<a href="<?php echo base_url('clients/orders'); ?>" class="d-block --xhr" data-toggle="tooltip" title="<?php echo phrase('manage_your_order'); ?>">
				<div class="card border-0 bg-primary text-center text-sm-left" style="overflow:hidden">
					<div class="row">
						<div class="col-sm-4">
							<div class="p-3 text-center" style="background:rgba(0, 0, 0, .1)">
								<i class="mdi mdi-square-inc-cash mdi-2x text-light"></i>
							</div>
						</div>
						<div class="col-sm-8 p-3">
							<h5 class="m-0 text-truncate text-light">
								<?php echo phrase('orders'); ?>
							</h5>
						</div>
					</div>
				</div>
			</a>
		</div>
		<div class="col-6 col-lg-3 mb-3">
			<a href="<?php echo base_url('clients/guidances'); ?>" class="d-block --xhr" data-toggle="tooltip" title="<?php echo phrase('order_guidance_conversation'); ?>">
				<div class="card border-0 bg-info text-center text-sm-left" style="overflow:hidden">
					<div class="row">
						<div class="col-sm-4">
							<div class="p-3 text-center" style="background:rgba(0, 0, 0, .1)">
								<i class="mdi mdi-message-text mdi-2x text-light"></i>
							</div>
						</div>
						<div class="col-sm-8 p-3">
							<h5 class="m-0 text-truncate text-light">
								<?php echo phrase('guidances'); ?>
							</h5>
						</div>
					</div>
				</div>
			</a>
		</div>
		<div class="col-6 col-lg-3 mb-3">
			<a href="<?php echo base_url('clients/invoices'); ?>" class="d-block --xhr" data-toggle="tooltip" title="<?php echo phrase('manage_issued_invoices'); ?>">
				<div class="card border-0 bg-danger text-center text-sm-left" style="overflow:hidden">
					<div class="row">
						<div class="col-sm-4">
							<div class="p-3 text-center" style="background:rgba(0, 0, 0, .1)">
								<i class="mdi mdi-format-float-left mdi-2x text-light"></i>
							</div>
						</div>
						<div class="col-sm-8 p-3">
							<h5 class="m-0 text-truncate text-light">
								<?php echo phrase('invoices'); ?>
							</h5>
						</div>
					</div>
				</div>
			</a>
		</div>
		<div class="col-6 col-lg-3 mb-3">
			<a href="<?php echo base_url('clients/projects'); ?>" class="d-block --xhr" data-toggle="tooltip" title="<?php echo phrase('monitor_your_running_projects'); ?>">
				<div class="card border-0 bg-dark text-center text-sm-left" style="overflow:hidden">
					<div class="row">
						<div class="col-sm-4">
							<div class="p-3 text-center" style="background:rgba(0, 0, 0, .1)">
								<i class="mdi mdi-progress-clock mdi-2x text-light"></i>
							</div>
						</div>
						<div class="col-sm-8 p-3">
							<h5 class="m-0 text-truncate text-light">
								<?php echo phrase('projects'); ?>
							</h5>
						</div>
					</div>
				</div>
			</a>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-8 bg-white border-right" style="margin-right:-1px">
			<div class="sticky-top pt-3" style="top:86px; z-index:0">
			</div>
		</div>
		<div class="col-lg-4 bg-white border-left" style="margin-left:-1px">
			<div class="sticky-top pt-3" style="top:86px; z-index:0">
			</div>
		</div>
	</div>
</div>
