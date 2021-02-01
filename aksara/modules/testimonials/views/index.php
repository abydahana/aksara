<div class="jumbotron jumbotron-fluid bg-transparent">
	<div class="container">
		<div class="text-center text-md-left">
			<h3 class="mb-0<?php echo (!$meta->description ? ' mt-3' : null); ?>">
				<?php echo $meta->title; ?>
			</h3>
			<p class="lead">
				<?php echo truncate($meta->description, 256); ?>
			</p>
		</div>
	</div>
</div>

<div class="container">
	<div class="row">
		<div class="col-md-10 offset-1">
			<?php
				if($results)
				{
					$items							= null;
					foreach($results as $key => $val)
					{
						$items						.= '
							<div class="row mb-3">
								<div class="col-3 col-md-3 pt-2">
									<img src="' . get_image('testimonials', $val->photo, 'thumb') . '" class="img-fluid rounded">
								</div>
								<div class="col-9 col-md-9">
									<h4 class="article font-weight-bold">
										' . $val->testimonial_title . '
									</h4>
									<p class="article">
										' . $val->testimonial_content . '
									</p>
									<p class="blockquote-footer">
										<b>' . $val->first_name . ' ' . $val->last_name . '</b>, ' . $val->timestamp . '
									</p>
								</div>
							</div>
						';
					}
					
					echo $items;
					
					echo $template->pagination;
				}
				else
				{
					echo '
						<div class="text-muted">
							<i class="fa fa-info"></i>
							' . phrase('no_testimonial_is_available') . '
						</div>
					';
				}
			?>
		</div>
	</div>
</div>
