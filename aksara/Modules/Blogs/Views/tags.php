<div class="bg-secondary text-white pt-5 pb-5">
	<div class="container pt-5 pb-5">
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
											<span class="badge bg-secondary mr-2">
												#' . trim($badge) . '
											</span>
										</a>
									';
								}
							}
						}
						
						echo '
							<div class="col-sm-6 col-md-4 col-lg-3">
								<div class="card border-0 rounded-4 overflow-hidden shadow-sm mb-4">
									<div class="position-relative">
										<a href="' . base_url(array('blogs', $val->category_slug)) . '" class="--xhr badge bg-primary position-absolute float-end">
											' . $val->category_title . '
										</a>
										<a href="' . base_url(array('blogs', $val->category_slug, $val->post_slug)) . '" class="--xhr">
											<img src="' . get_image('blogs', $val->featured_image, 'thumb') . '" class="card-img rounded-0" alt="..." />
										</a>
										<div class="w-100 position-absolute bottom p-3">
											<a href="' . base_url(array('blogs', $val->category_slug, $val->post_slug)) . '" class="--xhr">
												<h5 class="text-light" data-bs-toggle="tooltip" title="' . $val->post_title . '">
													' . truncate($val->post_title, 80) . '
												</h5>
											</a>
										</div>
									</div>
									<div class="card-body">
										<p class="card-text">
											<a href="' . base_url(array('blogs', $val->category_slug, $val->post_slug)) . '" class="--xhr">
												' . truncate($val->post_excerpt, 90) . '
											</a>
										</p>
										<p class="card-text">
											<small class="text-muted">
												<i class="mdi mdi-clock-outline"></i> ' . time_ago($val->updated_timestamp) . '
											</small>
										</p>
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
