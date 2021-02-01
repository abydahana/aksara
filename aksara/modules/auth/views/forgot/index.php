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
		<div class="col-md-6">
			<form action="<?php echo current_page(); ?>" method="POST" class="--validate-form">
				<div class="form-group">
					<label class="d-block text-muted" for="username_input">
						<?php echo phrase('enter_your_valid_username_or_email_to_request_a_password_reset_link'); ?>
					</label>
					<input type="text" name="username" class="form-control" id="username_input" placeholder="<?php echo phrase('enter_your_username_or_email'); ?>" />
				</div>
				
				<div class="--validation-callback mb-0"></div>
				
				<div class="form-group">
					<div class="row">
						<div class="col-6">
							<a href="<?php echo current_page('../'); ?>" class="btn btn-light btn-block --xhr">
								<i class="mdi mdi-chevron-left"></i>
								<?php echo phrase('back'); ?>
							</a>
						</div>
						<div class="col-6">
							<button type="submit" class="btn btn-primary btn-block">
								<i class="mdi mdi-check"></i> 
								<?php echo phrase('sign_in'); ?>
							</button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
