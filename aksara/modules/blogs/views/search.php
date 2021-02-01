<div class="leading pt-5 pb-5 mb-5 bg-light relative" style="background: url(<?php echo (isset($category->category_image) && $category->category_image != 'placeholder.png' ? get_image('blogs', $category->category_image) : get_image('blogs', 'cover.jpg')); ?>) center center no-repeat; background-size: cover">
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
					<?php echo $meta->description; ?>
				</p>
			</div>
		</div>
		<div class="row">
			<div class="col-md-8 offset-md-2 col-lg-6 offset-lg-3">
				<form action="<?php echo base_url('blogs/search', array('category_id' => null, 'per_page' => null)); ?>" method="POST" class="form-horizontal relative --xhr-form">
					<input type="text" name="q" class="form-control form-control-lg pt-4 pr-4 pb-4 pl-4 border-0" placeholder="<?php echo (service('request')->getGet('category') ? phrase('search_post_under') . ' ' . ($category ? $category->category_title : null) : phrase('search_post')); ?>" value="<?php echo service('request')->getGet('q'); ?>" />
					<button type="submit" class="btn btn-lg float-right absolute top right">
						<i class="mdi mdi-magnify font-weight-bold"></i>
					</button>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="container">
	<div class="row">
		<div class="col-md-8 pb-5">
		
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
											<span class="badge badge-secondary mr-2">
												#' . trim($badge) . '
											</span>
										</a>
									';
								}
							}
						}
						
						echo '
							<div class="row">
								<div class="col-3 col-sm-3">
									<a href="' . go_to('../' . $val->category_slug . '/' . $val->post_slug) . '" class="--xhr">
										<img id="og-image" src="' . get_image('blogs', $val->featured_image, 'thumb') . '" class="img-fluid rounded" />
									</a>
								</div>
								<div class="col-9 col-sm-9">
									<div class="mb-1">
										<a href="' . base_url('user/' . $val->username) . '" class="--xhr">
											<b>
												<i class="mdi mdi-account"></i> ' . $val->first_name . ' ' . $val->last_name . '
											</b>
										</a>
										<span class="text-sm text-muted">
											<i class="mdi mdi-clock-outline"></i> ' . time_ago($val->updated_timestamp) . '
										</span>
									</div>
									<a href="' . go_to('../' . $val->category_slug . '/' . $val->post_slug) . '" class="--xhr">
										<h5>
											' . $val->post_title . '
										</h5>
									</a>
									<p class="mb-0">
										<a href="' . go_to('../' . $val->category_slug . '/' . $val->post_slug) . '" class="--xhr text-muted">
											' . truncate($val->post_excerpt, 128) . '
										</a>
									</p>
									<div>
										' . ($tags ? $tags : null) . '
									</div>
								</div>
							</div>
							<hr />
						';
					}
					
					echo $template->pagination;
				}
				else
				{
					echo '
						<div class="text-muted">
							<i class="mdi mdi-information-outline"></i>
							' . phrase('no_post_found_under_this_category') . '
						</div>
					';
				}
			?>
		
		</div>
		<div class="col-md-4">
			<div class="sticky-top">
				<!-- placeholder -->
			</div>
		</div>
	</div>
</div>
