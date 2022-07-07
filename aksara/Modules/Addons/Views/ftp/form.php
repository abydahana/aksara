<div class="container-fluid pb-3">
	<div class="row border-bottom bg-white mb-3 sticky-top" style="overflow-x:auto">
		<ul class="nav" style="flex-wrap: nowrap">
			<li class="nav-item">
				<a href="<?php echo go_to('../'); ?>" class="nav-link no-wrap --xhr">
					<i class="mdi mdi-cart"></i>
					<?php echo phrase('market'); ?>
				</a>
			</li>
			<li class="nav-item">
				<a href="<?php echo go_to('../themes'); ?>" class="nav-link no-wrap --xhr">
					<i class="mdi mdi-palette"></i>
					<?php echo phrase('installed_theme'); ?>
				</a>
			</li>
			<li class="nav-item">
				<a href="<?php echo go_to('../modules'); ?>" class="nav-link no-wrap --xhr">
					<i class="mdi mdi-puzzle"></i>
					<?php echo phrase('installed_module'); ?>
				</a>
			</li>
			<li class="nav-item">
				<a href="<?php echo current_page(); ?>" class="nav-link no-wrap --xhr text-bg-primary">
					<i class="mdi mdi-console-network"></i>
					<?php echo phrase('ftp_configuration'); ?>
				</a>
			</li>
		</ul>
	</div>
	<form action="<?php echo current_page(); ?>" method="POST" class="--validate-form" enctype="multipart/form-data">
		<div class="row">
			<div class="col-md-6">
				<div class="row">
					<div class="col-md-9">
						<div class="form-group mb-3">
							<label class="text-muted d-block" for="hostname_input">
								<?php echo $results->form_data->hostname->label; ?>
								<?php echo ($results->form_data->hostname->required ? '<span class="float-end text-sm text-danger">' . phrase('required') . '</span>' : null); ?>
							</label>
							<?php echo $results->form_data->hostname->content; ?>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group mb-3">
							<label class="text-muted d-block" for="port_input">
								<?php echo $results->form_data->port->label; ?>
								<?php echo ($results->form_data->port->required ? '<span class="float-end text-sm text-danger">' . phrase('required') . '</span>' : null); ?>
							</label>
							<?php echo $results->form_data->port->content; ?>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group mb-3">
							<label class="text-muted d-block" for="username_input">
								<?php echo $results->form_data->username->label; ?>
								<?php echo ($results->form_data->username->required ? '<span class="float-end text-sm text-danger">' . phrase('required') . '</span>' : null); ?>
							</label>
							<?php echo $results->form_data->username->content; ?>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group mb-3">
							<label class="text-muted d-block" for="password_input">
								<?php echo $results->form_data->password->label; ?>
								<?php echo ($results->form_data->password->required ? '<span class="float-end text-sm text-danger">' . phrase('required') . '</span>' : null); ?>
							</label>
							<?php echo $results->form_data->password->content; ?>
						</div>
					</div>
				</div>
				<div class="form-group mb-3">
					<label class="text-muted d-block">
						<input type="checkbox" name="checking" class="form-check-input" value="1" />
						<?php echo phrase('check_connection_while_submitting'); ?>
					</label>
				</div>
				<div class="--validation-callback mb-0"></div>
			</div>
		</div>
		<div class="opt-btn-overlap-fix"></div><!-- fix the overlap -->
		<div class="row opt-btn">
			<div class="col-md-6">
				<a href="<?php echo go_to('../'); ?>" class="btn btn-link --xhr">
					<i class="mdi mdi-arrow-left"></i>
					<?php echo phrase('back'); ?>
				</a>
				<button type="submit" class="btn btn-primary float-end">
					<i class="mdi mdi-check"></i>
					<?php echo phrase('update'); ?>
					<em class="text-sm">(ctrl+s)</em>
				</button>
			</div>
		</div>
	</form>
</div>
