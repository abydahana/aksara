<div class="bg-secondary text-white pt-5 pb-5" style="background-image:url(<?php echo get_image('blogs', 'cover.jpg'); ?>)">
	<div class="container">
		<div class="row">
			<div class="col-md-8 offset-md-2">
				<h1 class="text-center text-light">
					<?php echo phrase('welcome_to') . ' ' . get_setting('app_name'); ?>!
				</h1>
				<p class="lead text-center text-light mb-5">
					<?php echo get_setting('app_description'); ?>
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
										<b class="text-light" data-toggle="tooltip" title="' . $val->post_title . '">
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
				<a href="' . base_url('blogs') . '" class="--xhr">
					<h3 class="text-center text-sm-start text-primary">
						' . phrase('spotlight') . '
					</h3>
				</a>
				<p class="text-center text-sm-start text-muted">
					' . phrase('an_article_spotlight_you_may_want_to_know') . '
				</p>
				<div class="swiper" data-slide-count-sm="1" data-slide-count-md="2" data-slide-count-lg="3" data-slide-count-xl="3">
					<div class="swiper-wrapper">
						' . $posts . '
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
											<b class="text-light" data-toggle="tooltip" title="' . $_val->post_title . '">
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
					<a href="' . base_url(array('blogs', $val->category_slug)) . '" class="--xhr">
						<h3 class="text-center text-sm-start text-primary">
							' . $val->category_title . '
						</h3>
					</a>
					<p class="text-center text-sm-start text-muted">
						' . $val->category_description . '
					</p>
					<div class="swiper" data-slide-count-sm="2" data-slide-count-md="2" data-slide-count-lg="3" data-slide-count-xl="4">
						<div class="swiper-wrapper">
							' . $posts . '
						</div>
					</div>
				';
			}
		?>
	</div>
</div>

<div class="bg-light">
	<div class="container">
		<?php
			if($galleries)
			{
				$albums								= null;
				
				foreach($galleries as $key => $val)
				{
					$image							= json_decode($val->gallery_images);
					
					if(!$image) continue;
					
					foreach($image as $src => $alt)
					{
						$albums						.= '
							<div class="swiper-slide">
								<div class="card border-0 rounded-4 overflow-hidden shadow-sm mb-5">
									<a href="' . base_url(array('galleries', $val->gallery_slug)) . '" class="--xhr d-block">
										<div class="card-body" style="background:url(' . get_image('galleries', $src, 'thumb') . ') center center no-repeat; background-size: cover; height: 256px">
											<div class="clip gradient-top"></div>
											<div class="position-absolute bottom-0 p-3">
												<b class="text-light">
													' . $val->gallery_title . '
												</b>
											</div>
										</div>
									</a>
								</div>
							</div>
						';
						
						break;
					}
				}
				
				echo '
					<a href="' . base_url('galleries') . '" class="--xhr">
						<h3 class="text-center text-sm-start text-primary">
							' . phrase('galleries') . '
						</h3>
					</a>
					<p class="text-center text-sm-start text-muted">
						' . phrase('new_from_gallery') . '
					</p>
					<div class="swiper" data-slide-count-sm="2" data-slide-count-md="2" data-slide-count-lg="3" data-slide-count-xl="3">
						<div class="swiper-wrapper">
							' . $albums . '
						</div>
					</div>
				';
			}
		?>
	</div>
</div>
