<div class="container-fluid pt-3 pb-3">
	<div class="row">
		<div class="col-lg-8">
			<div class="row">
				<?php
					if($results->directory && !isset($key))
					{
						echo '
							<div class="col-4 col-sm-3 col-xl-2 text-center">
								<a href="' . current_page(null, array('directory' => $results->parent_directory, 'file' => null)) . '" class="--xhr">
									<div class="p-3">
										<img src="data:image/png;base64,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" class="img-fluid rounded" alt="..." />
									</div>
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
										<a href="' . current_page(null, array('directory' => ($results->directory ? $results->directory . DIRECTORY_SEPARATOR : null) . $val->source, 'file' => null)) . '" class="--xhr">
											<div class="p-3">
												<img src="' . get_image('_extension', 'folder.png') . '" class="img-fluid rounded" alt="..." />
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
										<a href="' . current_page(null, array('file' => ($results->directory ? $results->directory . DIRECTORY_SEPARATOR : null) . $val->source)) . '" class="--xhr">
											<div class="p-3">
												<img src="' . $val->icon . '" class="img-fluid rounded" alt="..." />
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
		<div class="col-lg-4">
			<?php
				if($results->description)
				{
					echo '
						<div class="mb-5">
							<a href="' . base_url($results->description->server_path) . '" target="_blank">
								<img src="' . $results->description->icon . '" class="img-fluid rounded" alt="..." />
							</a>
						</div>
						<div class="form-group">
							<label class="d-block text-muted">
								' . phrase('filename') . '
							</label>
							<label class="d-block text-break-word">
								' . $results->description->name . '
							</label>
						</div>
						<div class="form-group">
							<label class="d-block text-muted">
								' . phrase('mime_type') . '
							</label>
							<label class="d-block text-break-word">
								' . $results->description->mime_type . '
							</label>
						</div>
						<div class="form-group">
							<label class="d-block text-muted">
								' . phrase('size') . '
							</label>
							<label class="d-block text-break-word">
								' . $results->description->size . '
							</label>
						</div>
						<div class="form-group">
							<label class="d-block text-muted">
								' . phrase('date_modified') . '
							</label>
							<label class="d-block text-break-word">
								' . date('Y-m-d H:i:s', $results->description->date) . '
							</label>
						</div>
					';
				}
			?>
		</div>
	</div>
</div>
