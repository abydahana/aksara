<div class="bg-light text-secondary pt-5 pb-5">
	<div class="container">
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
					foreach($results as $key => $val)
					{
						echo '
							<blockquote class="blockquote' . ($key ? ' mt-5' : null) . '">
								<a href="' . go_to($val->announcement_slug) . '" class="--xhr">
									<h5 class="mb-0">
										' . $val->title . '
									</h5>
								</a>
								<p class="lead">
									' . truncate($val->content, 160) . '
								</p>
								<footer class="blockquote-footer">
									' . phrase('valid_until') . ' ' . $val->end_date . '
								</footer>
							</blockquote>
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
						<div class="text-muted">
							<i class="mdi mdi-information-outline"></i>
							' . phrase('no_announcement_is_available') . '
						</div>
					';
				}
			?>
		</div>
	</div>
</div>
