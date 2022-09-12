<div class="bg-light pt-5 pb-5">
	<div class="container">
		<div class="row align-items-center">
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
					<label class="d-block text-muted" for="password_input">
						<?php echo phrase('new_password'); ?>
					</label>
					<input type="password" name="password" class="form-control" id="password_input" placeholder="<?php echo phrase('enter_your_new_password'); ?>" />
				</div>
				<div class="mb-3">
					<label class="d-block text-muted" for="confirm_password_input">
						<?php echo phrase('confirm_new_password'); ?>
					</label>
					<input type="password" name="confirm_password" class="form-control" id="confirm_password_input" placeholder="<?php echo phrase('confirm_your_new_password'); ?>" />
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
