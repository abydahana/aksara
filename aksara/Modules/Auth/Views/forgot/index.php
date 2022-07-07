<div class="bg-light pt-5 pb-5">
	<div class="container">
		<div class="row d-flex align-items-center">
			<div class="col-3 col-sm-2 col-md-1 offset-md-2">
				<i class="<?php echo $meta->icon; ?> mdi-4x"></i>
			</div>
			<div class="col-9 col-sm-10 col-md-6">
				<h3 class="mb-0<?php echo (!$meta->description ? ' mt-3' : null); ?>">
					<?php echo $meta->title; ?>
				</h3>
				<p class="lead">
					<?php echo truncate($meta->description, 256); ?>
				</p>
			</div>
		</div>
	</div>
</div>

<div class="container pt-5 pb-5">
	<div class="row">
		<div class="col-md-6 offset-md-3">
			<form action="<?php echo current_page(); ?>" method="POST" class="--validate-form">
				<div class="mb-3">
					<label class="d-block text-muted" for="username_input">
						<?php echo phrase('enter_your_valid_username_or_email_to_request_a_password_reset_link'); ?>
					</label>
					<input type="text" name="username" class="form-control" id="username_input" placeholder="<?php echo phrase('enter_your_username_or_email'); ?>" />
				</div>
				
				<div class="--validation-callback mb-0"></div>
				
				<div class="mb-3">
					<div class="row">
						<div class="col-6">
							<div class="d-grid">
								<a href="<?php echo current_page('../'); ?>" class="btn btn-light --xhr">
									<i class="mdi mdi-arrow-left"></i>
									<?php echo phrase('back'); ?>
								</a>
							</div>
						</div>
						<div class="col-6">
							<div class="d-grid">
								<button type="submit" class="btn btn-primary">
									<i class="mdi mdi-check"></i> 
									<?php echo phrase('reset_password'); ?>
								</button>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
