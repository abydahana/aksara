<div class="bg-light pt-5 pb-5 position-relative" style="background: url(<?php echo get_image('blogs', 'cover.jpg'); ?>) center center no-repeat; background-size: cover">
	<div class="clip gradient-top"></div>
	<div class="position-relative">
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
							<input type="text" name="q" class="form-control border-0" placeholder="<?php echo phrase('search_post'); ?>" value="<?php echo $keywords; ?>" />
							<button type="submit" class="btn btn-light border-0">
								<i class="mdi mdi-magnify"></i>
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="container pt-5 pb-5">
	<div class="row">
		<div class="col-md-10 offset-md-1 col-lg-8 offset-lg-2">
			<?php
				if($results)
				{
					foreach($results as $key => $val)
					{
						if(!$key && $keywords && $total)
						{
							echo '
								<div class="mb-3">
									' . phrase('found') . ' <b>' . number_format($total) . '</b> ' . phrase('for_the_search_keyword') . ' "<b>' . $keywords . '</b>".
								</div>
							';
						}
						
						echo '
							<div class="row align-items-center mb-5">
								<div class="col-9 col-sm-10">
									<a href="' . base_url(array('blogs', $val->category_slug)) . '" class="text-muted d-none d-sm-inline --xhr">
										' . base_url('blogs') . ' &gt; ' . $val->category_title . '
									</a>
									<h5>
										<a href="' . base_url(array('blogs', $val->category_slug, $val->post_slug)) . '" class="text-primary --xhr">
											' . ($keywords ? preg_replace('/' . $keywords . '/i', '<b class="text-success">$0</b>', truncate($val->post_title, 255)) : truncate($val->post_title, 255)). '
										</a>
									</h5>
									<div>
										' . ($keywords ? preg_replace('/' . $keywords . '/i', '<b class="text-success">$0</b>', truncate($val->post_excerpt, 255)) : truncate($val->post_excerpt, 255)). '
									</div>
								</div>
								<div class="col-3 col-sm-2">
									<a href="' . base_url(array('blogs', $val->category_slug, $val->post_slug)) . '" class="--xhr">
										<img src="' . get_image('blogs', $val->featured_image, 'thumb') . '" class="card-img rounded-4" alt="..." />
									</a>
								</div>
							</div>
						';
					}
					
					if($total > $limit)
					{
						echo '
							<div class="pt-3 pb-3">
								' . $template->pagination . '
							</div>
						';
					}
				}
				else
				{
					echo '
						<div class="pt-5 pb-5 text-center">
							<i class="mdi mdi-dropbox mdi-5x text-muted"></i>
							<h2>
								' . phrase('your_keyword_did_not_match_any_result') . '
							</h2>
						</div>
					';
				}
			?>
		</div>
	</div>
</div>
