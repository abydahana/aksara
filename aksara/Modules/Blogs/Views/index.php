<div class="bg-secondary text-white pt-5 pb-5" style="background-image:url(<?php echo get_image('blogs', 'cover.jpg'); ?>); background-position:center center; background-repeat:no-repeat">
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

<div class="bg-light">
	<div class="container pt-3 pb-3">
		<?php
			$posts									= null;
			
			foreach($spotlight as $key => $val)
			{
				$posts								.= '
					<div class="swiper-slide">
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
				
			echo '
				<div class="pt-3 pb-3">
					<a href="' . base_url('blogs') . '" class="--xhr">
						<h3 class="text-center text-sm-start text-primary">
							' . phrase('spotlight') . '
						</h3>
					</a>
					<p class="text-center text-sm-start text-muted">
						' . phrase('an_article_spotlight_you_may_want_to_know') . '
					</p>
					<div class="swiper" data-slide-count-sm="2" data-slide-count-md="2" data-slide-count-lg="3" data-slide-count-xl="3" data-autoplay="1">
						<div class="swiper-wrapper">
							' . $posts . '
						</div>
					</div>
				</div>
			';
		?>
	</div>
</div>

<div class="bg-light">
	<div class="container">
		<?php
			foreach($articles as $key => $val)
			{
				$posts								= null;
				
				foreach($val->posts as $_key => $_val)
				{
					$posts							.= '
						<div class="swiper-slide">
							<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
								<a href="' . base_url(array('blogs', $val->category_slug, $_val->post_slug)) . '" class="--xhr d-block">
									<div class="position-relative" style="background:url(' . get_image('blogs', $_val->featured_image, 'thumb') . ') center center no-repeat; background-size: cover; height: 256px">
										<div class="clip gradient-top"></div>
										<div class="position-absolute bottom-0 p-3">
											<b class="text-light" data-bs-toggle="tooltip" title="' . $_val->post_title . '">
												' . truncate($_val->post_title, 80) . '
											</b>
										</div>
									</div>
								</a>
								<div class="card-body">
									<p class="card-text">
										<a href="' . base_url(array('blogs', $val->category_slug, $_val->post_slug)) . '" class="--xhr d-block">
											' . truncate($_val->post_excerpt, 90) . '
										</a>
									</p>
									<div class="row g-0 align-items-center">
										<div class="col-1">
											<a href="' . base_url('user/' . $_val->username) . '" class="text-sm text-secondary">
												<img src="' . get_image('users', $_val->photo, 'icon') . '" class="img-fluid rounded-circle" alt="..." />
											</a>
										</div>
										<div class="col-6">
											<a href="' . base_url('user/' . $_val->username) . '" class="text-sm text-secondary ps-2">
												<b>
													' . $_val->first_name . ' ' . $_val->last_name . '
												</b>
											</a>
										</div>
										<div class="col-5 text-end">
											<small class="text-muted text-sm">
												<i class="mdi mdi-clock-outline"></i> ' . time_ago($_val->updated_timestamp) . '
											</small>
										</div>
									</div>
								</div>
							</div>
						</div>
					';
				}
				
				echo '
					<div class="pt-3 pb-3">
						<a href="' . base_url(array('blogs', $val->category_slug)) . '" class="--xhr">
							<h3 class="text-center text-sm-start text-primary">
								' . $val->category_title . '
							</h3>
						</a>
						<p class="text-center text-sm-start">
							' . $val->category_description . '
						</p>
						<div class="swiper" data-slide-count-sm="2" data-slide-count-md="2" data-slide-count-lg="3" data-slide-count-xl="4">
							<div class="swiper-wrapper">
								' . $posts . '
							</div>
						</div>
					</div>
				';
			}
		?>
	</div>
</div>
