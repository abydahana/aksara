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
?>

<div class="leading pt-5 pb-5 lazyload" style="background: url(<?php echo get_image('recipes', 'placeholder.png'); ?>) center center no-repeat; background-size: cover; position: relative" data-original="<?php echo get_image('recipes', 'cover.jpg'); ?>">
	<div style="background:rgba(23,162,184,.5); position: absolute; top: 0; right: 0; bottom: 0; left: 0"></div>
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
			<div class="col-md-6 offset-md-3">
				<h1 class="text-center text-light mb-5">
					The Global Community of Recipes
				</h1>
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

<div class="bg-light full-height-callout pt-5 pb-5">
	<div class="container">
		<a href="<?php echo base_url('travelling'); ?>" class="--xhr">
			<button class="btn btn-outline-warning btn-sm float-right d-none d-md-block">
				<i class="mdi mdi-magnify"></i>
				Lihat Semua
			</button>
			<h3 class="text-center text-md-left text-warning mb-0">
				Top Recipes Story Teller
			</h3>
		</a>
		<p class="text-center text-md-left">
			Discover inspiring bucket lists, exciting destinations and hottest travel trends...
		</p>
		<div class="row">
			<div class="owl-carousel owl-theme" data-nav="1" data-md-items="3" data-lg-items="4">
				<?php
					foreach(range(0, 5) as $key => $val)
					{
						echo '
							<div class="item">
								<div class="card border-0 shadow mr-3 mb-5 ml-3">
									<div class="relative rounded-top lazyload" style="background:url(' . get_image('recipes', 'placeholder.png', 'thumb') . ') center center no-repeat; background-size: cover; height: 256px" data-original="//picsum.photos/id/' . rand(1, 200) . '/256/256">
										<a href="' . base_url('recipes/abc') . '" class="--xhr d-block">
											<div class="clip gradient-top rounded-top"></div>
										</a>
										<div class="absolute bottom p-3">
											<a href="' . base_url('user') . '" class="--xhr d-block">
												<div class="row no-gutters">
													<div class="col-2">
														<img src="' . get_image('users', 'placeholder.png', 'icon') . '" class="rounded-circle img-fluid lazyload" data-original="' . get_image('users', 'user', 'icon') . '" />
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
											<a href="' . base_url('recipes/abc') . '" class="--xhr">
												Cara Membuat Pisang Bakar
											</a>
										</h5>
										<p class="card-text text-secondary">
											<a href="' . base_url('recipes/abc') . '" class="--xhr">
												Some quick example text to build on the card title and make up the bulk of the card\'s content.
											</a>
										</p>
									</div>
								</div>
							</div>
						';
					}
				?>
			</div>
		</div>
	</div>
</div>

<div class="bg-light pb-5">
	<div class="container">
		<a href="<?php echo base_url('travelling'); ?>" class="--xhr">
			<h3 class="text-center text-md-left text-warning mb-0">
				Ada 1,312,636 Destinasi Wisata
			</h3>
		</a>
		<p class="text-center text-md-left">
			Kami telah mengumpulkan jutaan pengalaman berwisata dari para pengguna aplikasi yang dapat Anda coba sendiri di rumah. Seluruh resep yang ada di aplikasi ini dapat Anda simpan ke dalam akun untuk dicoba nanti. Jika pengguna lain bisa menulis resep, begitu juga Anda!
		</p>
	</div>
</div>

<div class="sticky-top bg-light" style="top: 55px">
	<div class="container">
		<ul class="nav">
			<li class="nav-item">
				<a class="nav-link active font-weight-bold" href="#" style="border-bottom:3px solid #ccc">
					Overview
				</a>
			</li>
			<li class="nav-item">
				<a class="nav-link font-weight-bold" href="#">
					Trip
				</a>
			</li>
			<li class="nav-item">
				<a class="nav-link font-weight-bold" href="#">
					Places
				</a>
			</li>
			<li class="nav-item">
				<a class="nav-link font-weight-bold" href="#">
					Hotel
				</a>
			</li>
		</ul>
	</div>
</div>

<div class="bg-light pt-5 pb-5">
	<div class="container">
		<div class="row">
			<?php
				foreach(range(0, 11) as $key => $val)
				{
					echo '
						<div class="col-sm-6 col-md-4 col-lg-3">
							<div class="card border-0 shadow" style="margin-bottom: 30px">
								<div class="relative rounded-top lazyload" style="background:url(' . get_image('recipes', 'placeholder.png', 'thumb') . ') center center no-repeat; background-size: cover; height: 256px" data-original="//picsum.photos/id/' . rand(1, 200) . '/256/256">
									<a href="' . base_url('recipes/abc') . '" class="--xhr d-block">
										<div class="clip gradient-top rounded-top"></div>
									</a>
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
										<a href="' . base_url('recipes/abc') . '" class="--xhr">
											Cara Membuat Pisang Bakar
										</a>
									</h5>
									<p class="card-text text-secondary">
										<a href="' . base_url('recipes/abc') . '" class="--xhr">
											Some quick example text to build on the card title and make up the bulk of the card\'s content.
										</a>
									</p>
								</div>
							</div>
						</div>
					';
				}
			?>
			<div class="col-12 text-center">
				<a href="#" class="d-flex justify-content-center load-more">
					<div class="spinner-border" role="status">
						<span class="sr-only"><?php echo phrase('loading'); ?></span>
					</div>
				</a>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function()
	{
		$(window).off('scroll.load-more'),
		
		require.js('<?php echo asset_url('visible/visible.min.js'); ?>', function()
		{
			var visible				= false;
			$(window).on('scroll.load-more', function()
			{
				if($('.load-more').visible(true))
				{
					if(!visible)
					{
						$.ajax
						({
							url: ''
						})
					}
					visible			= true;
				}
				else if(visible)
				{
					visible			= false;
				}
			})
		})
	})
</script>
