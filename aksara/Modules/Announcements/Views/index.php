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
	<?php
		if($results)
		{
			foreach($results as $key => $val)
			{
				echo '
					<blockquote class="blockquote">
						<a href="' . go_to($val->announcement_slug) . '" class="--xhr">
							<h5 class="mb-0">
								' . $val->title . '
							</h5>
							<p>
								' . truncate($val->content, 160) . '
							</p>
							<footer class="blockquote-footer">
								' . phrase('valid_until') . ' ' . $val->end_date . '
							</footer>
						</a>
					</blockquote>
				';
			}
			
			echo $template->pagination;
		}
		else
		{
			echo '
				<div class="text-muted">
					<i class="mdi mdi-information"></i>
					' . phrase('no_announcement_is_available') . '
				</div>
			';
		}
	?>
</div>
