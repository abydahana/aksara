<?php
	/**
	 * Carousel
	 */
	$carousel									= null;
	if(isset($carousels) && $carousels)
	{
		$indicators								= null;
		$carousel_items							= null;
		foreach($carousels as $key => $val)
		{
			$indicators							.= '<li data-target="#welcome-sider" data-slide-to="' . $key . '" class="' . ($key == 0 ? 'active bg-dark' : 'bg-dark') . '"></li>';
			$carousel_items						.= '
				<div class="carousel-item rounded' . (!$key ? ' active' : null) . '" style="background:url(' . get_image('carousels', $val->background) . ') center center no-repeat;background-size:cover;height:320px">
					<!--<div class="carousel-caption">
						<h5 class="text-light font-weight-bold">
							' . (isset($val->title) ? $val->title : null) . '
						</h5>
						<p class="text-light">
							' . (isset($val->description) ? truncate($val->description, 160) : null) . '
						</p>
					</div>-->
				</div>
			';
		}
		
		$carousel								= '
			<div id="welcome-sider" class="carousel slide" data-ride="carousel">
				' . (sizeof($carousels) > 1 ? '
				' : '') . '
				<div class="carousel-inner">
					' . $carousel_items . '
				</div>
				<ol class="carousel-indicators">
					' . $indicators . '
				</ol>
			</div>
		';
	}
	
	/**
	 * Latest stories
	 */
	$latest_stories								= null;
	if(isset($stories) && $stories)
	{
		$indicators								= null;
		$carousel_items							= null;
		foreach($stories as $key => $val)
		{
			$indicators							.= '<li data-target="#stories" data-slide-to="' . $key . '" class="' . ($key == 0 ? 'active bg-dark' : 'bg-dark') . '"></li>';
			$carousel_items						.= '
				<div class="carousel-item' . ($key == 0 ? ' active' : '') . '">
					<div class="row">
						' . ($val->background ? '
							<div class="col-md-5 text-center">
								<img src="' . get_image('carousels', $val->background) . '" class="img-fluid rounded" />
							</div>
						' : null) . '
						<div class="col-md-7">
							<div class="text-dark text-sm-center text-md-left text-lg-left text-xl-left">
								<img src="' . get_image('carousels', ($val->thumbnail ? $val->thumbnail : 'placeholder.png'), 'icon') . '" class="img-fluid rounded-circle mt-3" />
								<br />
								<p class="lead">
									' . (isset($val->description) ? $val->description : phrase('description_was_not_set')) . '
								</p>
								<p class="blockquote-footer">
									<b>' . (isset($val->title) ? $val->title : null) . '</b>
								</p>
							</div>
						</div>
					</div>
				</div>
			';
		}
		
		$latest_stories							= '
			<div id="stories" class="carousel slide mb-3" data-ride="carousel">
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
<style type="text/css">
	.no-gutters.menu-icon
	{
		margin-left: -5px;
		margin-right: -5px
	}
	.no-gutters.menu-icon > .col,
	.no-gutters.menu-icon > [class*=col-]
	{
		padding-left: 5px;
		padding-right: 5px
	}
	.no-gutters.menu-icon .btn
	{
		padding-left: 0;
		padding-right: 0
	}
	.no-gutters.menu-icon .card
	{
		border-radius: 20%
	}
	@media(min-width:991px)
	{
		.no-gutters.menu-icon .card h6
		{
			font-size: 22px
		}
		.no-gutters.menu-icon .card .mdi
		{
			font-size: 5rem
		}
	}
	@media(max-width:990px)
	{
		.mdi.mdi-5x
		{
			font-size: 3rem
		}
	}
	#fullscreen-video
	{
		position: absolute;
		right: 0;
		bottom: 0;
		min-width: 100%; 
		min-height: 100%
	}
	.video-overlay
	{
		position: absolute;
		right: 0;
		bottom: 0;
		min-width: 100%; 
		min-height: 100%;
		background: rgba(23, 162, 184, .50)
	}
	.banner-bottom
	{
		position: absolute;
		right: 0;
		bottom: -2px;
		left: 0;
		height: 216px;
		background: url(<?php echo get_image('welcome', 'banner-bottom.png'); ?>) center center no-repeat;
		background-size: cover
	}
</style>
<div class="full-height leading d-flex align-items-center justify-content-center pt-5 pb-5" style="position: relative">
	<video autoplay muted loop id="fullscreen-video">
		<source src="<?php echo get_image('welcome', 'food.webm'); ?>?a" type="video/webm" />
	</video>
	<div class="video-overlay"></div>
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
	<div class="container">
		<div class="row">
			<div class="col-md-7 text-sm-center text-md-left d-flex align-items-center justify-content-center">
				<div class="pt-5">
					<h1 class="font-weight-bold mb-3 text-warning text-uppercase">
						Wisata Mata, Wisata Rasa.
					</h1>
					<p class="lead font-weight-normal text-light">
						CaraMembuat adalah sebuah platform yang digunakan untuk berbagi spot travelling, rekomendasi street food, resep makanan hingga berkebun dan bercocok tanam.
					</p>
					<?php
						if(stripos($this->agent->platform(), 'windows') !== false)
						{
							echo '
								<div class="row">
									<div class="col-8 offset-2 col-sm-6 offset-sm-3 col-md-8 offset-md-0">
										<a href="' . base_url('OutletPintar-Setup.exe') . '" class="btn btn-outline-light btn-lg rounded-pill" style="border-width:2px">
											<i class="mdi mdi-download"></i>
											Download OutletPintar
										</a>
									</div>
								</div>
							';
						}
						elseif(stripos($this->agent->platform(), 'android') !== false)
						{
							echo '
								<div class="row">
									<div class="col-8 offset-2 col-sm-6 offset-sm-3 col-md-6 offset-md-0">
										<a href="' . base_url('OutletPintar.apk') . '">
											<img src="' . base_url('themes/default/img/playstore.png') . '" class="img-fluid" />
										</a>
									</div>
								</div>
							';
						}
					?>
				</div>
			</div>
			<div class="col-md-5 text-sm-center d-none d-md-block">
				<div class="pt-5">
					<img src="<?php echo get_image('welcome', 'shape.png'); ?>" class="img-fluid" />
				</div>
			</div>
		</div>
	</div>
	<div class="banner-bottom"></div>
</div>

<div class="pt-5 pb-5">
	<div class="container">
		<h1 class="text-center pt-5 pb-5 text-warning">
			Apa Yang Dapat Dibagikan?
		</h1>
		<div class="row">
			<div class="col-6 col-md-3 text-center mb-4">
				<a href="<?php echo base_url('travelling'); ?>" class="--xhr d-block">
					<div class="highlight-thumbnail relative rounded d-flex align-items-center lazyload" style="background: url(<?php echo get_image('travelling', 'placeholder.png', 'thumb'); ?>) center center no-repeat; background-size: cover" data-original="<?php echo get_image('welcome', 'travelling.jpg'); ?>">
						<div class="clip gradient-top rounded">
							<h4 class="pt-3 pb-3 mb-0 text-light shadow absolute bottom">
								Rekomendasi Destinasi Wisata
							</h4>
						</div>
					</div>
				</a>
			</div>
			<div class="col-6 col-md-3 text-center mb-4">
				<a href="<?php echo base_url('culinary'); ?>" class="--xhr d-block">
					<div class="highlight-thumbnail relative rounded d-flex align-items-center lazyload" style="background: url(<?php echo get_image('culinary', 'placeholder.png', 'thumb'); ?>) center center no-repeat; background-size: cover" data-original="<?php echo get_image('welcome', 'streetfood.jpg'); ?>">
						<div class="clip gradient-top rounded">
							<h4 class="pt-3 pb-3 mb-0 text-light shadow absolute bottom">
								Rekomendasi Berwisata Kuliner
							</h4>
						</div>
					</div>
				</a>
			</div>
			<div class="col-6 col-md-3 text-center mb-4">
				<a href="<?php echo base_url('recipes'); ?>" class="--xhr d-block">
					<div class="highlight-thumbnail relative rounded d-flex align-items-center lazyload" style="background: url(<?php echo get_image('recipes', 'placeholder.png', 'thumb'); ?>) center center no-repeat; background-size: cover" data-original="<?php echo get_image('welcome', 'food.jpg'); ?>">
						<div class="clip gradient-top rounded">
							<h4 class="pt-3 pb-3 mb-0 text-light shadow absolute bottom">
								Resep Makanan dan Minuman
							</h4>
						</div>
					</div>
				</a>
			</div>
			<div class="col-6 col-md-3 text-center mb-4">
				<a href="<?php echo base_url('gardening'); ?>" class="--xhr d-block">
					<div class="highlight-thumbnail relative rounded d-flex align-items-center lazyload" style="background: url(<?php echo get_image('gardening', 'placeholder.png', 'thumb'); ?>) center center no-repeat; background-size: cover" data-original="<?php echo get_image('welcome', 'gardening.jpg'); ?>">
						<div class="clip gradient-top rounded">
							<h4 class="pt-3 pb-3 mb-0 text-light shadow absolute bottom">
								Berkebun dan Bercocok Tanam
							</h4>
						</div>
					</div>
				</a>
			</div>
		</div>
	</div>
</div>

<div class="pt-5 pb-5 lazyload" style="background: url(<?php echo get_image(null, 'placeholder_thumb.png'); ?>) center center no-repeat; background-size: cover; position: relative" data-original="<?php echo get_image('recipes', 'cover.jpg'); ?>">
	<div style="background:rgba(23,162,184,.5); position: absolute; top: 0; right: 0; bottom: 0; left: 0"></div>
	<div class="container-fluid pt-5 pb-5">
		<div class="row">
			<div class="col-md-6 offset-md-3">
				<form class="form-horizontal relative">
					<input type="text" name="q" class="form-control font-weight-bold pt-4 pr-4 pb-4 pl-4 border-0" placeholder="Cari resep" />
					<button type="submit" class="btn btn-lg float-right absolute top right">
						<i class="mdi mdi-magnify font-weight-bold"></i>
					</button>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="bg-light pt-5 pb-5">
	<div class="container">
		<a href="<?php echo base_url('travelling'); ?>" class="--xhr">
			<h3 class="text-center text-md-left text-warning mb-0">
				Rekomendasi Destinasi Wisata
			</h3>
		</a>
		<p class="text-center text-md-left">
			Discover inspiring bucket lists, exciting destinations and hottest travel trends...
		</p>
		<div class="row">
			<div class="owl-carousel owl-theme" data-nav="1" data-md-items="3" data-lg-items="3">
				<?php
					foreach(range(0, 9) as $key => $val)
					{
						echo '
							<div class="item">
								<a href="' . base_url('travelling/abc') . '" class="--xhr d-block">
									<div class="card border-0 shadow mr-3 mb-5 ml-3">
										<div class="relative rounded-top lazyload" style="background:url(' . get_image('travelling', 'placeholder.png', 'thumb') . ') center center no-repeat; background-size: cover; height: 256px" data-original="//picsum.photos/id/' . rand(1, 200) . '/256/256">
											<div class="clip gradient-top rounded-top"></div>
											<div class="absolute bottom p-3">
												<a href="' . base_url('user') . '" class="--xhr d-block">
													<div class="row no-gutters">
														<div class="col-2">
															<img src="' . get_image('users', 'placeholder.png', 'icon') . '" class="rounded-circle img-fluid lazyload" data-original="' . get_image('users', 'placeholder', 'icon') . '" />
														</div>
														<div class="col-10">
															<h6 class="pt-1 pl-3 text-light text-shadow">
																Diah Utami
															</h6>
														</div>
													</div>
												</a>
											</div>
										</div>
										<div class="card-body">
											<h5 class="card-title mb-0">
												<button class="btn btn-lg p-0 float-right text-warning" data-toggle="tooltip" title="Pin" style="margin-right: -10px">
													<i class="mdi mdi-pin"></i>
												</button>
												Cara Membuat Pisang Bakar
											</h5>
											<p class="card-text text-secondary">
												Some quick example text to build on the card title and make up the bulk of the card\'s content.
											</p>
										</div>
									</div>
								</a>
							</div>
						';
					}
				?>
			</div>
		</div>
		<br />
		<br />
		<a href="<?php echo base_url('travelling'); ?>" class="--xhr">
			<h3 class="text-center text-md-left text-warning pt-3 mb-0">
				Rekomendasi Wisata Kuliner
			</h3>
		</a>
		<p class="text-center text-md-left">
			Discover inspiring bucket lists, exciting destinations and hottest travel trends...
		</p>
		<div class="row">
			<div class="owl-carousel owl-theme" data-nav="1" data-md-items="4" data-lg-items="3">
				<?php
					foreach(range(0, 9) as $key => $val)
					{
						echo '
							<div class="item">
								<a href="' . base_url('culinary/abc') . '" class="--xhr d-block">
									<div class="card border-0 shadow mr-3 mb-5 ml-3">
										<div class="relative rounded-top lazyload" style="background:url(' . get_image('culinary', 'placeholder.png', 'thumb') . ') center center no-repeat; background-size: cover; height: 256px" data-original="//picsum.photos/id/' . rand(1, 200) . '/256/256">
											<div class="clip gradient-top rounded-top"></div>
											<div class="absolute bottom p-3">
												<a href="' . base_url('user') . '" class="--xhr d-block">
													<div class="row no-gutters">
														<div class="col-2">
															<img src="' . get_image('users', 'placeholder.png', 'icon') . '" class="rounded-circle img-fluid lazyload" data-original="' . get_image('users', 'placeholder', 'icon') . '" />
														</div>
														<div class="col-10">
															<h6 class="pt-1 pl-3 text-light text-shadow">
																Diah Utami
															</h6>
														</div>
													</div>
												</a>
											</div>
										</div>
										<div class="card-body">
											<h5 class="card-title mb-0">
												<button class="btn btn-lg p-0 float-right text-warning" data-toggle="tooltip" title="Pin" style="margin-right: -10px">
													<i class="mdi mdi-pin"></i>
												</button>
												Cara Membuat Pisang Bakar
											</h5>
											<p class="card-text text-secondary">
												Some quick example text to build on the card title and make up the bulk of the card\'s content.
											</p>
										</div>
									</div>
								</a>
							</div>
						';
					}
				?>
			</div>
		</div>
		<br />
		<br />
		<a href="<?php echo base_url('travelling'); ?>" class="--xhr">
			<h3 class="text-center text-md-left text-warning pt-3 mb-0">
				Resep Makanan dan Minuman
			</h3>
		</a>
		<p class="text-center text-md-left">
			Discover inspiring bucket lists, exciting destinations and hottest travel trends...
		</p>
		<div class="row">
			<div class="owl-carousel owl-theme" data-nav="1" data-md-items="3" data-lg-items="4">
				<?php
					foreach(range(0, 9) as $key => $val)
					{
						echo '
							<div class="item">
								<a href="' . base_url('recipes/abc') . '" class="--xhr d-block">
									<div class="card border-0 shadow mr-3 mb-5 ml-3">
										<div class="relative rounded-top lazyload" style="background:url(' . get_image('recipes', 'placeholder.png', 'thumb') . ') center center no-repeat; background-size: cover; height: 256px" data-original="//picsum.photos/id/' . rand(1, 200) . '/256/256">
											<div class="clip gradient-top rounded-top"></div>
											<div class="absolute bottom p-3">
												<a href="' . base_url('user') . '" class="--xhr d-block">
													<div class="row no-gutters">
														<div class="col-2">
															<img src="' . get_image('users', 'placeholder.png', 'icon') . '" class="rounded-circle img-fluid lazyload" data-original="' . get_image('users', 'placeholder', 'icon') . '" />
														</div>
														<div class="col-10">
															<h6 class="pt-1 pl-3 text-light text-shadow">
																Diah Utami
															</h6>
														</div>
													</div>
												</a>
											</div>
										</div>
										<div class="card-body">
											<h5 class="card-title mb-0">
												<button class="btn btn-lg p-0 float-right text-warning" data-toggle="tooltip" title="Pin" style="margin-right: -10px">
													<i class="mdi mdi-pin"></i>
												</button>
												Cara Membuat Pisang Bakar
											</h5>
											<p class="card-text text-secondary">
												Some quick example text to build on the card title and make up the bulk of the card\'s content.
											</p>
										</div>
									</div>
								</a>
							</div>
						';
					}
				?>
			</div>
		</div>
		<br />
		<br />
		<a href="<?php echo base_url('travelling'); ?>" class="--xhr">
			<h3 class="text-center text-md-left text-warning pt-3 mb-0">
				Berkebun dan Bercocok Tanam
			</h3>
		</a>
		<p class="text-center text-md-left">
			Discover inspiring bucket lists, exciting destinations and hottest travel trends...
		</p>
		<div class="row">
			<div class="owl-carousel owl-theme" data-nav="1" data-md-items="3" data-lg-items="4">
				<?php
					foreach(range(0, 9) as $key => $val)
					{
						echo '
							<div class="item">
								<a href="' . base_url('gardening/abc') . '" class="--xhr d-block">
									<div class="card border-0 shadow mr-3 mb-5 ml-3">
										<div class="relative rounded-top lazyload" style="background:url(' . get_image('gardening', 'placeholder.png', 'thumb') . ') center center no-repeat; background-size: cover; height: 256px" data-original="//picsum.photos/id/' . rand(1, 200) . '/256/256">
											<div class="clip gradient-top rounded-top"></div>
											<div class="absolute bottom p-3">
												<a href="' . base_url('user') . '" class="--xhr d-block">
													<div class="row no-gutters">
														<div class="col-2">
															<img src="' . get_image('users', 'placeholder.png', 'icon') . '" class="rounded-circle img-fluid lazyload" data-original="' . get_image('users', 'placeholder', 'icon') . '" />
														</div>
														<div class="col-10">
															<h6 class="pt-1 pl-3 text-light text-shadow">
																Diah Utami
															</h6>
														</div>
													</div>
												</a>
											</div>
										</div>
										<div class="card-body">
											<h5 class="card-title mb-0">
												<button class="btn btn-lg p-0 float-right text-warning" data-toggle="tooltip" title="Pin" style="margin-right: -10px">
													<i class="mdi mdi-pin"></i>
												</button>
												Cara Membuat Pisang Bakar
											</h5>
											<p class="card-text text-secondary">
												Some quick example text to build on the card title and make up the bulk of the card\'s content.
											</p>
										</div>
									</div>
								</a>
							</div>
						';
					}
				?>
			</div>
		</div>
	</div>
</div>

<div class="bg-light pt-3 pb-3 talk-to-us">
	<div class="container">
		<div class="row">
			<div class="col-md-7 col-lg-8">
				<h1 class="text-sm-center font-weight-normal text-md-left m-0 p-0 mt-1 mb-2 text-dark">
					<?php echo phrase('need_more_help'); ?>
				</h1>
			</div>
			<div class="col-md-5 col-lg-4">
				<a href="<?php echo base_url('peoples'); ?>" class="btn btn-outline-dark btn-lg btn-block rounded-pill mt-1 mb-2 --xhr">
					<i class="mdi mdi-account-heart-outline"></i>
					&nbsp;
					<?php echo phrase('talk_to_us'); ?>
				</a>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function()
	{
		$('.highlight-thumbnail').each(function()
		{
			$(this).height($(this).outerWidth())
		})
	})
</script>
