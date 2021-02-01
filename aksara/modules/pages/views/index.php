<?php
	if($results)
	{
		foreach($results as $key => $val)
		{
			$carousels								= json_decode($val->carousel_content);
			$faqs									= json_decode($val->faq_content);
			
			if($carousels)
			{
				$navigation							= null;
				$carousel_items						= null;
				
				foreach($carousels as $_key => $_val)
				{
					$navigation						.= '<li data-target="#carouselExampleIndicators" data-slide-to="' . $_key . '"' . ($_key == 0 ? ' class="active"' : '') . '></li>';
					$carousel_items					.= '
						<div class="carousel-item full-height bg-dark gradient d-flex align-items-center' . ($_key == 0 ? ' active' : '') . '" style="background:#333 url(\'' . get_image('carousels', (isset($_val->background) ? $_val->background : 'placeholder.png')) . '\') center center no-repeat;background-size:cover;background-attachment:fixed">
							<div class="absolute top right bottom left" style="background:rgba(0, 0, 0, .75)"></div>
							<div class="area">
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
							<div class="carousel-caption container-fluid" style="position:inherit">
								<div class="row">
									' . ($_val->thumbnail && $_val->thumbnail != 'placeholder.png' ? '
									<div class="col-lg-4 offset-lg-1 text-center text-lg-left d-none d-md-block">
										<div class="pt-5 w-100">
											<img src="' . get_image('carousels', $_val->thumbnail) . '" class="img-fluid rounded" />
										</div>
									</div>
									' : null) . '
									<div class="' . ($_val->thumbnail && $_val->thumbnail != 'placeholder.png' ? 'col-lg-6 text-center text-lg-left d-flex align-items-center justify-content-center' : 'col-md-10 offset-md-1 col-lg-8 offset-lg-2 text-center') . '">
										<div class="pt-5 w-100">
											<h1 class="font-weight-bold mb-3 text-light">
												' . (isset($_val->title) ? $_val->title : phrase('title_was_not_set')) . '
											</h1>
											<p class="text-light mb-5">
												' . (isset($_val->description) ? truncate($_val->description, 260) : phrase('description_was_not_set')) . '
											</p>
											' . (isset($_val->link) && $_val->link ? '
											<div class="row">
												<div class="col-sm-6 offset-sm-3 col-md-12 offset-md-0">
													<a href="' . $_val->link . '" class="btn btn-outline-light btn-lg rounded-pill" data-animation="animated bounceInLeft" style="border-width:2px">
														' . (isset($_val->label) && $_val->label ? $_val->label : phrase('read_more')) . '
														<i class="mdi mdi-chevron-right"></i>
													</a>
												</div>
											</div>
											' : null) . '
										</div>
									</div>
								</div>
							</div>
						</div>
					';
				}
				
				echo '
					<div id="carouselExampleIndicators" class="carousel slide carousel-fade" data-ride="carousel">
						' . (sizeof($carousels) > 1 ? '
						<ol class="carousel-indicators">
							' . $navigation . '
						</ol>
						' : '') . '
						<div class="carousel-inner">
							' . $carousel_items . '
						</div>
						' . (sizeof($carousels) > 1 ? '
						<a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
							<span class="carousel-control-prev-icon" aria-hidden="true"></span>
							<span class="sr-only">
								' . phrase('previous') . '
							</span>
						</a>
						<a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
							<span class="carousel-control-next-icon" aria-hidden="true"></span>
							<span class="sr-only">
								' . phrase('next') . '
							</span>
						</a>
						' : '') . '
					</div>
				';
			}
			
			if($faqs)
			{
				$output								= null;
				
				foreach($faqs as $_key => $_val)
				{
					if(!isset($_val->question) || !$_val->answer) continue;
					
					$output							.= '
					  <div class="card">
							<div class="card-header" id="heading_' . $_key . '">
								<a href="#" class="d-block font-weight-bold" data-toggle="collapse" data-target="#collapse_' . $_key . '" aria-expanded="' . (!$_key ? 'true' : 'false') . '" aria-controls="collapse_' . $_key . '">
									' . $_val->question . '
								</a>
							</div>
							<div id="collapse_' . $_key . '" class="collapse" aria-labelledby="heading_' . $_key . '" data-parent="#accordionExample">
								<div class="card-body">
									' . $_val->answer . '
								</div>
							</div>
						</div>
					';
				}
				
				$faqs								= '
					<div class="accordion" id="accordionExample">
						' . $output . '
					</div>
				';
			}
			
			echo '
				<div class="jumbotron jumbotron-fluid bg-transparent">
					<div class="container">
						<div class="text-center text-sm-left">
							<h3 class="mb-0' . (!$meta->description ? ' mt-3' : null) . '">
								' . $meta->title . '
							</h3>
							<p class="lead mb-0">
								' . truncate($meta->description, 256) . '
							</p>
						</div>
					</div>
				</div>
				<div class="container">
					<div class="text-justify mb-3">
						' . preg_replace('/(<[^>]+) style=".*?"/i', '$1', preg_replace('/<img src="(.*?)"/i', '<img id="og-image" src="$1" class="img-fluid rounded"', $val->page_content)) . '
					</div>
					<div class="mb-3">
						' . $faqs . '
					</div>
					<p>
						<i class="text-muted text-sm">
							' . phrase('updated_at') . ' ' . phrase(strtolower(date('l', strtotime($val->updated_timestamp)))) . ', ' . $val->updated_timestamp . '
						</i>
					</p>
				</div>
			';
		}
	}
	else
	{
		$link_left									= null;
		$link_right									= null;
		
		if(isset($suggestions) && $suggestions)
		{
			$num									= 1;
			
			foreach($suggestions as $key => $val)
			{
				if($num % 2 == 0)
				{
					$link_right						.= '
						<li>
							<a href="' . base_url('pages/' . $val->page_slug) . '" class="--xhr">
								' . $val->page_title . '
							</a>
						</li>
					';
				}
				else
				{
					$link_left						.= '
						<li>
							<a href="' . base_url('pages/' . $val->page_slug) . '" class="--xhr">
								' . $val->page_title . '
							</a>
						</li>
					';
				}
				
				$num++;
			}
		}
		
		echo '
			<div class="container pt-5">
				<div class="text-center pt-5 pb-5">
					<h1 class="text-muted">
						404
					</h1>
					<i class="mdi mdi-dropbox mdi-5x text-muted"></i>
				</div>
				<div class="row mb-5">
					<div class="col-md-6 offset-md-3">
						<h2 class="text-center">
							' . phrase('page_not_found') . '
						</h2>
						<p class="lead text-center mb-5">
							' . phrase('the_page_you_requested_was_not_found_or_it_is_already_removed') . '
						</p>
						<div class="text-center mt-5">
							<a href="' . base_url() . '" class="btn btn-outline-primary rounded-pill --xhr">
								<i class="mdi mdi-arrow-left"></i>
								' . phrase('back_to_home') . '
							</a>
						</div>
					</div>
				</div>
				<div class="row mb-2">
					<div class="col-md-10 offset-md-1">
						<h5>
							<i class="mdi mdi-lightbulb-on-outline"></i>
							' . phrase('our_suggestions') . '
							<blink>_</blink>
						</h5>
					</div>
				</div>
				<div class="row">
					<div class="col-md-5 offset-md-1">
						<ul>
							' . $link_left . '
						</ul>
					</div>
					<div class="col-md-5">
						<ul>
							' . $link_right . '
						</ul>
					</div>
				</div>
			</div>
		';
	}
