<div class="container-fluid pb-3">
	<div class="row border-bottom bg-white mb-1 sticky-top" style="overflow-x: auto; top:88px">
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
				<a href="<?php echo current_page(); ?>" class="nav-link no-wrap --xhr active">
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
	<div class="row border-bottom pb-1 mb-3">
		<div class="col-md-6 offset-md-6 text-right">
			<a href="<?php echo current_page('import'); ?>" class="btn btn-primary btn-sm --xhr">
				<i class="mdi mdi-import"></i>
				<?php echo phrase('import_module'); ?>
			</a>
		</div>
	</div>
	<div class="row">
		<?php
			if($installed)
			{
				foreach($installed as $key => $val)
				{
					if(isset($val->screenshot[0]->src) && file_exists('../modules/' . $val->folder . '/' . $val->screenshot[0]->src))
					{
						$screenshot					= base_url('modules/' . $val->folder . '/' . $val->screenshot[0]->src);
					}
					else
					{
						$screenshot					= get_image(null, 'placeholder_thumb.png');
					}
					
					echo '
						<div class="col-sm-6 col-md-4 col-lg-3">
							<div class="card shadow border-0 rounded-more mb3">
								<a href="' . current_page('detail', array('item' => $val->folder)) . '" class="--modal">
									<div class="relative rounded-top" style="background:url(' . $screenshot . ') center center no-repeat; background-size: cover; height: 256px">
										<div class="clip gradient-top rounded-top"></div>
										<div class="absolute bottom p-3">
											<h5 class="text-light" data-toggle="tooltip" title="' . $val->name . '">
												' . truncate($val->name, 80) . '
											</h5>
										</div>
									</div>
								</a>
								<div class="card-body p-3">
									<div class="row">
										<div class="col-6">
											<a href="' . current_page('activate', array('item' => $val->folder)) . '" class="btn btn-outline-primary btn-block btn-xs --modal" target="_blank">
												' . phrase('up_to_date') . '
											</a>
										</div>
										<div class="col-6">
											<a href="' . current_page('delete', array('item' => $val->folder)) . '" class="btn btn-danger btn-block btn-xs --modal">
												' . phrase('uninstall') . '
											</a>
										</div>
									</div>
								</div>
							</div>
						</div>
					';
				}
			}
			else
			{
				echo '
					<div class="col-sm-12">
						<div class="alert alert-warning">
							' . phrase('no_module_installed_yet') . '
						</div>
					</div>
				';
			}
		?>
	</div>
</div>
