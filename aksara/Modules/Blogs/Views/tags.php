<div class="bg-light pt-5 pb-5 position-relative" style="background: url(<?php echo get_image('blogs', 'tag-cover.jpg'); ?>) center center no-repeat; background-size: cover">
	<div class="clip gradient-top"></div>
	<div class="position-relative">
		<div class="container">
			<div class="row">
				<div class="col-md-8 offset-md-2">
					<h1 class="text-center text-light">
						<?php echo $meta->title; ?>
					</h1>
					<p class="lead text-center text-light mb-5">
						<?php echo truncate($meta->description, 256); ?>
					</p>
				</div>
			</div>
			<div class="row">
				<div class="col-md-8 offset-md-2 col-lg-6 offset-lg-3">
					<form action="<?php echo base_url('blogs/search', array('per_page' => null)); ?>" method="POST" class="form-horizontal position-relative --xhr-form">
						<div class="input-group input-group-lg position-relative rounded-pill overflow-hidden">
							<input type="text" name="q" class="form-control border-0" placeholder="<?php echo phrase('search_post'); ?>" />
							<button type="submit" class="btn btn-light border-0">
								<i class="mdi mdi-magnify"></i>
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="bg-light pt-5 pb-5">
	<div class="container">
		<div class="row">
			<?php
				if($results)
				{
					foreach($results as $key => $val)
					{
						$item_tags					= array_map('trim', explode(',', $val->post_tags));
						$tags						= null;
						
						if(sizeof($item_tags) > 0)
						{
							foreach($item_tags as $label => $badge)
							{
								if($label == 2) break;
								if($badge)
								{
									$tags			.= '
										<a href="' . go_to('../tags', array('q' => $badge)) . '" class="--xhr">
											<span class="badge bg-secondary me-2">
												#' . trim($badge) . '
											</span>
										</a>
									';
								}
							}
						}
						
						echo '
							<div class="col-sm-6 col-md-4 col-lg-3">
								<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
									<a href="' . base_url(array('blogs', $val->category_slug, $val->post_slug)) . '" class="--xhr d-block">
										<div class="position-relative" style="background:url(' . get_image('blogs', $val->featured_image, 'thumb') . ') center center no-repeat; background-size: cover; height: 256px">
											<div class="clip gradient-top"></div>
											<div class="position-absolute bottom-0 p-3">
												<b class="text-light" data-bs-toggle="tooltip" title="' . $val->post_title . '">
													' . truncate($val->post_title, 80) . '
												</b>
											</div>
										</div>
									</a>
									<div class="card-body">
										<p class="card-text">
											<a href="' . base_url(array('blogs', $val->category_slug, $val->post_slug)) . '" class="--xhr d-block">
												' . truncate($val->post_excerpt, 90) . '
											</a>
										</p>
										<div class="row g-0 align-items-center">
											<div class="col-1">
												<a href="' . base_url('user/' . $val->username) . '" class="text-sm text-secondary">
													<img src="' . get_image('users', $val->photo, 'icon') . '" class="img-fluid rounded-circle" alt="..." />
												</a>
											</div>
											<div class="col-6">
												<a href="' . base_url('user/' . $val->username) . '" class="text-sm text-secondary ps-2">
													<b>
														' . $val->first_name . ' ' . $val->last_name . '
													</b>
												</a>
											</div>
											<div class="col-5 text-end">
												<small class="text-muted text-sm">
													<i class="mdi mdi-clock-outline"></i> ' . time_ago($val->updated_timestamp) . '
												</small>
											</div>
										</div>
									</div>
								</div>
							</div>
						';
					}
					
					if($total > $limit)
					{
						echo '
							<div class="col-lg-12 pt-3 pb-3">
								' . $template->pagination . '
							</div>
						';
					}
				}
				else
				{
					echo '
						<div class="col-lg-6 offset-lg-3 text-center">
							<div class="pt-5 pb-5">
								<i class="mdi mdi-dropbox mdi-5x text-muted"></i>
								<h2>
									' . phrase('your_tag_did_not_match_any_result') . '
								</h2>
							</div>
						</div>
					';
				}
			?>
		</div>
	</div>
</div>
