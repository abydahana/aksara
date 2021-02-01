<div class="relative">
	<div role="map" class="bg-light" data-coordinate="<?php echo htmlspecialchars(get_setting('office_map')); ?>" data-zoom="16" data-mousewheel="0" style="height:320px"></div>
</div>

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
	<p class="mb-5">
		Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
	</p>
	<div class="row">
		<div class="col-lg-6">
			<h3 class="mb-3">
				<?php echo get_setting('office_name'); ?>
			</h3>
			<div class="form-group">
				<label class="text-muted d-block">
					<?php echo phrase('address'); ?>
				</label>
				<p class="lead">
					<?php echo get_setting('office_address'); ?>
				</p>
			</div>
			<div class="form-group">
				<label class="text-muted d-block">
					<?php echo phrase('email'); ?>
				</label>
				<p class="lead">
					<a href="mailto:<?php echo get_setting('office_phone'); ?>" target="_blank">
						<?php echo get_setting('office_email'); ?>
					</a>
				</p>
			</div>
			<div class="row">
				<div class="col-sm-6">
					<div class="form-group">
						<label class="text-muted d-block">
							<?php echo phrase('phone'); ?>
						</label>
						<p class="lead">
							<a href="tel:<?php echo get_setting('office_phone'); ?>" target="_blank">
								<?php echo get_setting('office_phone'); ?>
							</a>
						</p>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						<label class="text-muted d-block">
							<?php echo phrase('whatsapp'); ?>
						</label>
						<p class="lead">
							<a href="https://api.whatsapp.com/send?phone=<?php echo str_replace(array('+', '-', ' '), array(null, null, null), get_setting('whatsapp_number')); ?>&text=<?php echo phrase('hello') . '%20' . get_setting('app_name'); ?>..." target="_blank">
								<?php echo get_setting('whatsapp_number'); ?>
							</a>
						</p>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-6">
					<div class="form-group">
						<label class="text-muted d-block">
							<?php echo phrase('twitter'); ?>
						</label>
						<p class="lead">
							<a href="//twitter.com/<?php echo get_setting('twitter_username'); ?>" target="_blank">
								<?php echo get_setting('twitter_username'); ?>
							</a>
						</p>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						<label class="text-muted d-block">
							<?php echo phrase('instagram'); ?>
						</label>
						<p class="lead">
							<a href="//instagram.com/<?php echo get_setting('instagram_username'); ?>" target="_blank">
								<?php echo get_setting('instagram_username'); ?>
							</a>
						</p>
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-6">
			<div class="card rounded-0 shadow">
				<div class="card-body">
					<h3 class="mb-3">
						<?php echo phrase('direct_inquiry'); ?>
					</h3>
					<form action="<?php echo current_page(); ?>" method="POST" class="--validate-form">
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<input type="text" name="full_name" class="form-control" placeholder="<?php echo phrase('full_name'); ?>" />
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<input type="text" name="email" class="form-control" placeholder="<?php echo phrase('email_address'); ?>" />
								</div>
							</div>
						</div>
						<div class="form-group">
							<input type="text" name="subject" class="form-control" placeholder="<?php echo phrase('subject'); ?>" />
						</div>
						<div class="form-group">
							<textarea type="text" name="messages" class="form-control" placeholder="<?php echo phrase('messages'); ?>" rows="1"></textarea>
						</div>
						<div class="form-group">
							<label>
								<input type="checkbox" name="copy" value="1" checked />
								<?php echo phrase('copy_this_message_to_your_email'); ?>
							</label>
						</div>
						
						<div class="--validation-callback mb-0"></div>
						
						<div class="row">
							<div class="col-md-6 offset-md-6">
								<button type="submit" class="btn btn-primary btn-block">
									<i class="mdi mdi-check"></i>
									<?php echo phrase('send_message'); ?>
								</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
