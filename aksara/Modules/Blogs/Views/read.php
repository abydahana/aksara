<div class="jumbotron jumbotron-fluid leading relative" style="background:url(<?php echo get_image('blogs', (isset($results[0]->featured_image) ? $results[0]->featured_image : 'placeholder.png')); ?>) center center no-repeat; background-size:cover">
	<div class="clip gradient-top"></div>
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
	</div>
</div>

<div class="container">
	<div class="row">
		<div class="col-md-8">
			<?php
				$output								= null;
				
				if($results)
				{
					foreach($results as $key => $val)
					{
						$item_tags					= explode(',', $val->post_tags);
						$tags						= null;
						
						if(sizeof($item_tags) > 0)
						{
							foreach($item_tags as $label => $badge)
							{
								if(!$badge) continue;
								
								$tags				.= '
									<a href="' . go_to('../tags', array('q' => trim($badge))) . '" class="--xhr">
										<span class="badge badge-secondary">
											' . trim($badge) . '
										</span>
									</a>
								';
							}
						}
						
						$output						.= '
							<div class="mt-5 mb-5">
								' . str_replace('MsoNormalTable', 'table table-bordered', preg_replace('/(width|height)="\d*"\s/', '', preg_replace('~<p[^>]*>~', '<p class="text-justify article">', preg_replace('/style[^>]*/', '', $val->post_content)))) . '
							</div>
							<p class="mb-5">
								' . $tags . '
							</p>
							
							' . load_comment_plugin(current_page()) . '
						';
					}
				}
				
				if($output)
				{
					/* show author */
					echo '
						<div class="row">
							<div class="col-sm-6 col-md-8 mb-3">
								<div class="row no-gutters">
									<div class="col-2 col-sm-1">
										<a href="' . base_url('user/' . $results[0]->username) . '" class="--xhr">
											<img src="' . get_image('users', $results[0]->photo, 'thumb') . '" class="img-fluid rounded-circle" />
										</a>
									</div>
									<div class="col-10 col-sm-11 pl-3">
										<a href="' . base_url('user/' . $results[0]->username) . '" class="--xhr">
											<h6 class="mb-0">
												' . $results[0]->first_name . ' ' . $results[0]->last_name . '
											</h6>
										</a>
										<p>
											<span class="text-sm text-muted" data-toggle="tooltip" title="' . $results[0]->updated_timestamp . '">
												' . time_ago($results[0]->updated_timestamp) . '
											</span>
										</p>
									</div>
								</div>
							</div>
							<div class="col-sm-6 col-md-4 mb-3">
								<div class="btn-group d-flex">
									<a href="//www.facebook.com/sharer/sharer.php?u=' . current_page() . '" class="btn btn-primary" data-toggle="tooltip" title="' . phrase('share_to_facebook') . '" target="_blank">
										<i class="mdi mdi-facebook"></i>
									</a>
									<a href="//www.twitter.com/share?url=' . current_page() . '" class="btn btn-info" data-toggle="tooltip" title="' . phrase('share_to_twitter') . '" target="_blank">
										<i class="mdi mdi-twitter"></i>
									</a>
									<a href="//wa.me/?text=' . current_page() . '" class="btn btn-success" data-toggle="tooltip" title="' . phrase('send_to_whatsapp') . '" target="_blank">
										<i class="mdi mdi-whatsapp"></i>
									</a>
								</div>
							</div>
						</div>
					';
					
					/* show featured image */
					if($results[0]->featured_image)
					{
						echo '<a href="' . get_image('blogs', $results[0]->featured_image) . '" target="_blank"><img id="og-image" src="' . get_image('blogs', $results[0]->featured_image) . '" class="img-fluid rounded" width="100%" /></a>';
					}
					
					/* show post content */
					echo preg_replace('/<img src="(.*?)"/i', '<img id="og-image" src="$1" class="img-fluid rounded"', $output);
				}
				else
				{
					echo '
						<div class="alert alert-warning">
							<i class="mdi mdi-alert-outline"></i>
							' . phrase('the_post_you_requested_does_not_exists') . '
						</div>
					';
				}
			?>
		</div>
		<div class="col-md-4">
			<div class="sticky-top">
				<?php
					if($similar)
					{
						$similar_article			= null;
						
						foreach($similar as $key => $val)
						{
							$similar_article		.= '
								<div class="row form-group">
									<div class="col-2 col-sm-1 col-md-2 pr-0">
										<a href="' . go_to('../' . $val->category_slug . '/' . $val->post_slug) . '" class="--xhr">
											<img src="' . get_image('blogs', $val->featured_image, 'icon') . '" class="img-fluid rounded" />
										</a>
									</div>
									<div class="col col-sm-11 col-md-10">
										<a href="' . go_to('../' . $val->category_slug . '/' . $val->post_slug) . '" class="--xhr">
											<b>
												' . $val->post_title . '
											</b>
										</a>
									</div>
								</div>
							';
						}
						
						echo '
							<div class="mb-5">
								<h5 class="mb-3">
									' . phrase('similar_articles') . '
								</h5>
								
								' . $similar_article . '
							</div>
						';
					}
					
					if($categories)
					{
						$similar_category			= null;
						
						foreach($categories as $key => $val)
						{
							$similar_category		.= '
								<div class="row form-group">
									<div class="col-2 pr-0">
										<a href="' . go_to('../' . $val->category_slug) . '" class="--xhr">
											<img src="' . get_image('blogs', $val->category_image, 'icon') . '" class="img-fluid rounded" />
										</a>
									</div>
									<div class="col">
										<a href="' . go_to('../' . $val->category_slug) . '" class="--xhr">
											<h5>
												' . $val->category_title . '
											</h5>
											<p class="text-muted mb-0">
												' . number_format($val->total_data) . ' ' . ($val->total_data > 1 ? phrase('articles') : phrase('article')) . '
											</p>
										</a>
									</div>
								</div>
							';
						}
						
						echo '
							<div class="mb-5">
								<h5 class="mb-3">
									' . phrase('similar_categories') . '
								</h5>
								
								' . $similar_category . '
							</div>
						';
					}
				?>
			</div>
		</div>
	</div>
</div>
