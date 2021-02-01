<div class="leading pt-5 pb-5 mb-5 bg-light relative" style="background: url(<?php echo get_image('blogs', 'cover.jpg'); ?>) center center no-repeat; background-size: cover">
	<div class="clip gradient-top"></div>
	<div class="area" >
		<ul class="circles">
			<li></li>
			<li></li>
			<li></li>
			<li></li>
			<li></li>
			<li></li>
			<li></li>
			<li></li>
			<li></li>
			<li></li>
		</ul>
    </div>
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
				<form action="<?php echo base_url('blogs/search', array('per_page' => null)); ?>" method="POST" class="form-horizontal relative --xhr-form">
					<input type="text" name="q" class="form-control form-control-lg pt-4 pr-4 pb-4 pl-4 border-0" placeholder="<?php echo phrase('search_post'); ?>" />
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
					<div class="item">
						<div class="card border-0 shadow mr-3 mb-5 ml-3">
							<a href="' . base_url(array('blogs', $val->category_slug, $val->post_slug)) . '" class="--xhr d-block">
								<div class="relative rounded-top" style="background:url(' . get_image('blogs', $val->featured_image, 'thumb') . ') center center no-repeat; background-size: cover; height: 256px">
									<div class="clip gradient-top rounded-top"></div>
									<div class="absolute bottom p-3">
										<h5 class="text-light" data-toggle="tooltip" title="' . $val->post_title . '">
											' . truncate($val->post_title, 80) . '
										</h5>
									</div>
								</div>
							</a>
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
				<h3 class="text-center text-md-left text-primary pt-3 mb-0 text-uppercase">
					' . phrase('spotlight') . '
				</h3>
				<p class="text-center text-md-left">
					' . phrase('an_article_spotlight_you_may_want_to_know') . '
				</p>
				<div class="row">
					<div class="owl-carousel owl-theme" data-nav="1" data-md-items="3" data-lg-items="3">
						' . $posts . '
					</div>
				</div>
			';
		?>
	</div>
</div>

<div class="">
	<div class="container">
		<?php
			foreach($articles as $key => $val)
			{
				$posts								= null;
				
				foreach($val->posts as $_key => $_val)
				{
					$posts							.= '
						<div class="item">
							<div class="card border-0 shadow mr-3 mb-5 ml-3">
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
					<div class="row">
						<div class="owl-carousel owl-theme" data-nav="1" data-md-items="3" data-lg-items="4">
							' . $posts . '
						</div>
					</div>
				';
			}
		?>
	</div>
</div>
