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
				<a href="<?php echo current_page('../'); ?>" class="nav-link no-wrap --xhr">
					<i class="mdi mdi-puzzle"></i>
					<?php echo phrase('installed_module'); ?>
				</a>
			</li>
			<li class="nav-item">
				<a href="<?php echo go_to('../ftp'); ?>" class="nav-link no-wrap --xhr">
					<i class="mdi mdi-console-network"></i>
					<?php echo phrase('ftp_configuration'); ?>
				</a>
			</li>
		</ul>
	</div>
	<div class="row">
		<div class="col-md-6">
			<form action="<?php echo current_page(); ?>" method="POST" class="--validate-form" enctype="multipart/form-data">
				<h5>
					<?php echo phrase('notes_before_you_import'); ?>
				</h5>
				<ul class="mb-5">
					<li>
						<?php echo phrase('make_sure_the_package_you_would_to_import_is_downloaded_from_the_official_market'); ?> (<a href="//www.aksaracms.com/market" class="text-primary fw-bold" target="_blank">Aksara Market</a>);
					</li>
					<li>
						<?php echo phrase('the_package_that_you_download_from_outside_the_official_market_may_contain_exploit_tool_that_will_risk_your_server'); ?>;
					</li>
					<li>
						<?php echo phrase('make_sure_you_pass_the_test_the_package_from_your_development_server'); ?>;
					</li>
					<li>
						<?php echo phrase('do_import_with_your_own_risk'); ?>.
					</li>
				</ul>
				<div class="--validation-callback"></div>
				<div class="form-group mb-3">
					<div class="input-group">
						<input type="file" name="file" class="form-control" />
						<button type="submit" class="btn btn-primary">
							<i class="mdi mdi-import"></i>
							<?php echo phrase('import'); ?>
						</button>
					</div>
				</div>
				<div class="form-group mb-3">
					<label>
						<input type="checkbox" name="upgrade" class="form-check-input" value="1" />
						<?php echo phrase('upgrade_existing_module'); ?>
					</label>
				</div>
			</form>
		</div>
	</div>
</div>
