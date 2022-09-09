<div class="bg-light text-secondary">
	<div class="container pt-5 pb-5">
		<div class="row">
			<div class="col-md-8 offset-md-2">
				<h1 class="text-center">
					<?php echo $meta->title; ?>
				</h1>
				<p class="lead text-center">
					<?php echo truncate($meta->description, 256); ?>
				</p>
			</div>
		</div>
	</div>
</div>

<div class="container pt-5 pb-5">
	<div class="row">
		<div class="col-md-10 offset-1">
			<?php
				if($results)
				{
					$items							= null;
					foreach($results as $key => $val)
					{
						$items						.= '
							<div class="row mb-3 align-items-center">
								<div class="col-3 col-md-3 pt-2">
									<img src="' . get_image('testimonials', $val->photo, 'thumb') . '" class="img-fluid rounded-4">
								</div>
								<div class="col-9 col-md-9">
									<h4 class="article fw-bold">
										' . $val->testimonial_title . '
									</h4>
									<div class="article mb-4">
										' . $val->testimonial_content . '
									</div>
									<p class="blockquote-footer">
										<b>' . $val->first_name . ' ' . $val->last_name . '</b>, ' . $val->timestamp . '
									</p>
								</div>
							</div>
						';
					}
					
					echo $items;
					
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
						<div class="text-muted">
							<i class="mdi mdi-information-outline"></i>
							' . phrase('no_testimonial_is_available') . '
						</div>
					';
				}
			?>
		</div>
	</div>
</div>
