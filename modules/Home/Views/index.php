<div class="leading pt-5 pb-5 mb-5 bg-light relative" style="background: url(<?php echo get_image('blogs', 'cover.jpg'); ?>) center center no-repeat; background-size: cover; position: relative">
	<div class="clip gradient-top"></div>
	<div class="container pt-5 pb-5">
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
				<form action="<?php echo base_url('blogs/search', array('per_page' => null)); ?>" method="POST" class="form-horizontal relative --xhr-form">
					<input type="text" name="q" class="form-control form-control-lg pt-4 pr-4 pb-4 pl-4 border-0 rounded-pill" placeholder="<?php echo phrase('search_post'); ?>" />
					<button type="submit" class="btn btn-lg float-right absolute top right">
						<i class="mdi mdi-magnify font-weight-bold"></i>
					</button>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="">
	<div class="container">
		<?php
			$posts									= null;
			
			foreach($spotlight as $key => $val)
			{
				$posts								.= '
					<div class="swiper-slide">
						<div class="card border-0 shadow-sm rounded-more mb-5">
							<div class="relative rounded-top" style="background:url(' . get_image('blogs', $val->featured_image, 'thumb') . ') center center no-repeat; background-size: cover; height: 256px">
								<a href="' . base_url(array('blogs', $val->category_slug, $val->post_slug)) . '" class="--xhr d-block">
									<div class="clip gradient-top rounded-top"></div>
									<div class="absolute bottom p-3">
										<h5 class="text-light" data-toggle="tooltip" title="' . $val->post_title . '">
											' . truncate($val->post_title, 80) . '
										</h5>
									</div>
								</a>
								<a href="' . base_url(array('blogs', $val->category_slug)) . '" class="--xhr badge badge-primary absolute right">
									' . $val->category_title . '
								</a>
							</div>
							<div class="card-body">
								<p class="card-text">
									<a href="' . base_url(array('blogs', $val->category_slug, $val->post_slug)) . '" class="--xhr d-block">
										' . truncate($val->post_excerpt, 100) . '
									</a>
								</p>
								<p class="card-text">
									<i class="mdi mdi-clock-outline"></i> ' . time_ago($val->updated_timestamp) . '
								</p>
							</div>
						</div>
					</div>
				';
			}
				
			echo '
				<a href="' . base_url('blogs') . '" class="--xhr">
					<h3 class="text-center text-md-left text-primary pt-3 mb-0 text-uppercase">
						' . phrase('spotlight') . '
					</h3>
				</a>
				<p class="text-center text-md-left">
					' . phrase('an_article_spotlight_you_may_want_to_know') . '
				</p>
				<div class="swiper-container swiper" data-sm-items="2" data-md-items="3" data-lg-items="3" data-autoplay="1" data-space-between="30" data-navigation="1">
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
							<div class="card border-0 shadow-sm rounded-more mb-5">
								<a href="' . base_url(array('blogs', $val->category_slug, $_val->post_slug)) . '" class="--xhr d-block">
									<div class="relative rounded-top" style="background:url(' . get_image('blogs', $_val->featured_image, 'thumb') . ') center center no-repeat; background-size: cover; height: 256px">
										<div class="clip gradient-top rounded-top"></div>
										<div class="absolute bottom p-3">
											<b class="text-light" data-toggle="tooltip" title="' . $_val->post_title . '">
												' . truncate($_val->post_title, 80) . '
											</b>
										</div>
									</div>
								</a>
								<div class="card-body">
									<p class="card-text">
										<a href="' . base_url(array('blogs', $val->category_slug, $_val->post_slug)) . '" class="--xhr d-block">
											' . truncate($_val->post_excerpt, 100) . '
										</a>
									</p>
									<p class="card-text text-sm">
										<i class="mdi mdi-clock-outline"></i> ' . time_ago($_val->updated_timestamp) . '
									</p>
								</div>
							</div>
						</div>
					';
				}
				
				echo '
					<a href="' . base_url(array('blogs', $val->category_slug)) . '" class="--xhr">
						<h3 class="text-center text-md-left text-primary pt-3 mb-0">
							' . $val->category_title . '
						</h3>
					</a>
					<p class="text-center text-md-left">
						' . $val->category_description . '
					</p>
					<div class="swiper-container swiper" data-sm-items="2" data-md-items="3" data-lg-items="3" data-autoplay="1" data-space-between="30" data-navigation="1">
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
								<div class="card rounded-more border-0 shadow-sm mb-5">
									<a href="' . base_url(array('galleries', $val->gallery_slug)) . '" class="--xhr d-block">
										<div class="card-body rounded" style="background:url(' . get_image('galleries', $src, 'thumb') . ') center center no-repeat; background-size: cover; height: 256px">
											<div class="clip gradient-top rounded"></div>
											<div class="absolute bottom p-3">
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
						<h3 class="text-center text-md-left text-primary pt-3 mb-0">
							' . phrase('galleries') . '
						</h3>
					</a>
					<p class="text-center text-md-left">
						New from gallery
					</p>
					<div class="swiper-container swiper" data-sm-items="2" data-md-items="3" data-lg-items="3" data-autoplay="1" data-space-between="30" data-navigation="1">
						<div class="swiper-wrapper">
							' . $albums . '
						</div>
					</div>
				';
			}
		?>
	</div>
</div>

<?php
	/**
	 * Latest testimonials
	 */
	$latest_testimonials							= null;
	
	if(isset($testimonials) && $testimonials)
	{
		$indicators									= null;
		$carousel_items								= null;
		
		foreach($testimonials as $key => $val)
		{
			$indicators								.= '<li data-target="#testimonials" data-slide-to="' . $key . '" class="' . ($key == 0 ? 'active bg-dark' : 'bg-dark') . '"></li>';
			$carousel_items							.= '
				<div class="carousel-item' . ($key == 0 ? ' active' : '') . '">
					<div class="row align-items-center">
						<div class="col-sm-4 col-md-3 text-center">
							<img src="' . get_image('testimonials', $val->photo, 'thumb') . '" class="img-fluid rounded-circle" />
						</div>
						<div class="col-sm-8 col-md-9">
							<div class="text-center text-sm-left text-md-left text-lg-left text-xl-left">
								<h4>
									' . $val->testimonial_title . '
								</h4>
								<p>
									' . truncate($val->testimonial_content, 500) . '
								</p>
								<p class="blockquote-footer">
									<b>' . $val->first_name . ' ' . $val->last_name . '</b>
								</p>
							</div>
						</div>
					</div>
				</div>
			';
		}
		
		$latest_testimonials						= '
			<div id="testimonials" class="carousel slide mb-3" data-ride="carousel">
				<div class="carousel-inner pb-5">
					' . $carousel_items . '
				</div>
				<ol class="carousel-indicators">
					' . $indicators . '
				</ol>
			</div>
		';
	}
?>

<?php if($latest_testimonials) { ?>
<div class="container pt-5 pb-5">
	<div class="row">
		<div class="col-lg-10 offset-lg-1">
			<h1 class="text-center font-weight-bold">
				<?php echo phrase('testimonials'); ?>
			</h1>
			<br />
			<br />
			<?php echo $latest_testimonials; ?>
		</div>
	</div>
</div>
<?php } ?>
