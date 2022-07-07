<div class="position-relative">
	<div role="map" class="bg-light" data-coordinate="<?php echo htmlspecialchars(get_setting('office_map')); ?>" data-zoom="16" data-mousewheel="0" style="height:320px"></div>
</div>

<div class="pt-5 pb-5 bg-light gradient">
	<div class="container">
		<h3 class="mb-0<?php echo (!$meta->description ? ' mt-3' : null); ?>">
			<?php echo $meta->title; ?>
		</h3>
		<p class="lead">
			<?php echo truncate($meta->description, 256); ?>
		</p>
	</div>
</div>

<div class="container pt-5 pb-5">
	<div class="row">
		<div class="col-lg-6">
			<h3 class="mb-3">
				<?php echo get_setting('office_name'); ?>
			</h3>
			<div class="mb-3">
				<label class="text-muted d-block mb-0">
					<?php echo phrase('address'); ?>
				</label>
				<p class="lead">
					<?php echo get_setting('office_address'); ?>
				</p>
			</div>
			<div class="mb-3">
				<label class="text-muted d-block mb-0">
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
					<div class="mb-3">
						<label class="text-muted d-block mb-0">
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
					<div class="mb-3">
						<label class="text-muted d-block mb-0">
							<?php echo phrase('whatsapp'); ?>
						</label>
						<p class="lead">
							<a href="https://api.whatsapp.com/send?phone=<?php echo str_replace(array('+', '-', ' '), '', get_setting('whatsapp_number')); ?>&text=<?php echo phrase('hello') . '%20' . get_setting('app_name'); ?>..." target="_blank">
								<?php echo get_setting('whatsapp_number'); ?>
							</a>
						</p>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-6">
					<div class="mb-3">
						<label class="text-muted d-block mb-0">
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
					<div class="mb-3">
						<label class="text-muted d-block mb-0">
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
			<div class="card border-0 rounded-4 shadow">
				<div class="card-body p-4">
					<h3 class="mb-3">
						<?php echo phrase('direct_inquiry'); ?>
					</h3>
					<form action="<?php echo current_page(); ?>" method="POST" class="--validate-form">
						<div class="row">
							<div class="col-md-6">
								<div class="form-group mb-3">
									<input type="text" name="full_name" class="form-control" placeholder="<?php echo phrase('full_name'); ?>" id="full_name_input" />
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group mb-3">
									<input type="text" name="email" class="form-control" placeholder="<?php echo phrase('email_address'); ?>" id="email_input" />
								</div>
							</div>
						</div>
						<div class="form-group mb-3">
							<input type="text" name="subject" class="form-control" placeholder="<?php echo phrase('subject'); ?>" id="subject_input" />
						</div>
						<div class="form-group mb-3">
							<textarea type="text" name="messages" class="form-control" placeholder="<?php echo phrase('messages'); ?>" rows="1" id="messages_input"></textarea>
						</div>
						
						<div class="--validation-callback mb-0"></div>
						
						<div class="row">
							<div class="col-md-6">
								<label>
									<input type="checkbox" name="copy" class="form-check-input" value="1" checked />
									<?php echo phrase('copy_this_message_to_your_email'); ?>
								</label>
							</div>
							<div class="col-md-6">
								<div class="d-grid">
									<button type="submit" class="btn btn-primary">
										<i class="mdi mdi-check"></i>
										<?php echo phrase('send_message'); ?>
									</button>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
