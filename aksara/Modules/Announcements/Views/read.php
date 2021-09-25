<?php if($results) { ?>
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
<?php } ?>

<div class="container">
	<?php
		if($results)
		{
			foreach($results as $key => $val)
			{
				echo '
					<p>
						' . $val->content . '
					</p>
					<p class="text-muted mb-5">
						<em>
							' . phrase('this_announcement_will_be_effective_until') . ' <b>' . $val->end_date . '</b>
						</em>
					</p>
					<a href="' . current_page('../') . '" class="btn btn-outline-primary rounded-pill --xhr">
						<i class="mdi mdi-arrow-left"></i>
						' . phrase('back') . '
					</a>
				';
			}
		}
		else
		{
			echo '
				<div class="container pt-5">
					<div class="text-center pt-5 pb-5">
						<h1 class="text-muted">
							404
						</h1>
						<i class="mdi mdi-message-alert-outline mdi-5x text-muted"></i>
					</div>
					<div class="row mb-5">
						<div class="col-md-6 offset-md-3">
							<h2 class="text-center">
								' . phrase('announcement_not_found') . '
							</h2>
							<p class="lead text-center mb-5">
								' . phrase('the_announcement_you_requested_was_not_found_or_its_already_removed') . '
							</p>
							<div class="text-center mt-5">
								<a href="' . go_to('../') . '" class="btn btn-outline-primary rounded-pill --xhr">
									<i class="mdi mdi-arrow-left"></i>
									' . phrase('back_to_announcements') . '
								</a>
							</div>
						</div>
					</div>
				</div>
			';
		}
	?>
</div>
