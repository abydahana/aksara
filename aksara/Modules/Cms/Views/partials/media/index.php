<div class="container-fluid">
	<div class="row">
		<div class="col-lg-8 pt-3 pb-3 bg-white border-end" style="margin-right:-1px">
			<div class="row align-items-end">
				<?php
					if($results->directory && !isset($key))
					{
						echo '
							<div class="col-4 col-sm-3 col-xl-2 text-center">
								<a href="' . current_page(null, array('directory' => $results->parent_directory, 'file' => null)) . '" class="--xhr">
									<div class="p-3">
										<i class="mdi mdi-arrow-left mdi-4x"></i>
									</div>
									<label class="d-block text-truncate">
										' . phrase('back') . '
									</label>
								</a>
							</div>
						';
					}
					
					if($results->data)
					{
						foreach($results->data as $key => $val)
						{
							if($val->type == 'directory')
							{
								echo '
									<div class="col-4 col-sm-3 col-xl-2 text-center">
										<a href="' . current_page(null, array('directory' => ($results->directory ? $results->directory . '/' : null) . $val->source, 'file' => null)) . '" class="--xhr">
											<div class="p-3">
												<i class="mdi mdi-folder-image mdi-4x text-info"></i>
											</div>
											<label class="d-block text-truncate">
												' . $val->label . '
											</label>
										</a>
									</div>
								';
							}
							else
							{
								echo '
									<div class="col-4 col-sm-3 col-xl-2 text-center">
										<a href="' . current_page(null, array('file' => ($results->directory ? $results->directory . '/' : null) . $val->source)) . '" class="--xhr">
											<div class="p-3">
												<img src="' . $val->icon . '" class="img-fluid rounded bg-light w-50" alt="..." />
											</div>
											<label class="d-block text-truncate">
												' . $val->label . '
											</label>
										</a>
									</div>
								';
							}
						}
					}
				?>
			</div>
		</div>
		<div class="col-lg-4 pt-3 pb-3 full-height bg-white border-start" style="margin-left:-1px">
			<?php
				if($results->description)
				{
					if(in_array($results->description->mime_type, array('image/jpg', 'image/jpeg', 'image/png', 'image/gif')))
					{
						echo '
							<div class="text-center mb-3">
								<a href="' . base_url($results->description->server_path) . '" target="_blank">
									<img src="' . base_url($results->description->server_path) . '" class="img-fluid rounded-more" alt="" />
								</a>
							</div>
						';
					}
					
					echo '
						<div class="mb-3">
							<label class="d-block text-muted mb-0">
								' . phrase('filename') . '
							</label>
							<label class="d-block text-break-word">
								<a href="' . base_url($results->description->server_path) . '" download="' . $results->description->name . '">
									' . $results->description->name . '
								</a>
							</label>
						</div>
						<div class="row">
							<div class="col-sm-6">
								<div class="mb-3">
									<label class="d-block text-muted mb-0">
										' . phrase('mime_type') . '
									</label>
									<label class="d-block text-break-word">
										' . $results->description->mime_type . '
									</label>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="mb-3">
									<label class="d-block text-muted mb-0">
										' . phrase('size') . '
									</label>
									<label class="d-block text-break-word">
										' . get_filesize(service('request')->getGet('directory'), $results->description->name) . '
									</label>
								</div>
							</div>
						</div>
						<div class="mb-3">
							<label class="d-block text-muted mb-0">
								' . phrase('date_modified') . '
							</label>
							<label class="d-block text-break-word">
								' . date('Y-m-d H:i:s', $results->description->date) . '
							</label>
						</div>
						<div class="row">
							<div class="col-6">
								<a href="' . base_url($results->description->server_path) . '" class="btn btn-primary btn-sm d-block rounded-pill"  download="' . $results->description->name . '">
									<i class="mdi mdi-download"></i>
									' . phrase('download') . '
								</a>
							</div>
							<div class="col-6">
								<a href="' . current_page(null, array('action' => 'delete')) . '" class="btn btn-danger btn-sm d-block rounded-pill --xhr">
									<i class="mdi mdi-window-close"></i>
									' . phrase('remove') . '
								</a>
							</div>
						</div>
					';
				}
			?>
		</div>
	</div>
</div>
