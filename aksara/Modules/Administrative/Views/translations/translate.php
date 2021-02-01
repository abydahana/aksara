<div class="container-fluid pt-3 pb-3">
	<div class="row">
		<div class="col-lg-6">
			<div class="row">
				<div class="col-sm-4">
					<label class="text-muted d-block">
						<?php echo phrase('language'); ?>
					</label>
				</div>
				<div class="col">
					<label>
						<?php echo (isset($results->form_data->language->original) ? $results->form_data->language->original : null); ?>
					</label>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-4">
					<label class="text-muted d-block">
						<?php echo phrase('alias'); ?>
					</label>
				</div>
				<div class="col">
					<label>
						<?php echo (isset($results->form_data->description->original) ? $results->form_data->description->original : null); ?>
					</label>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-4">
					<label class="text-muted d-block">
						<?php echo phrase('code'); ?>
					</label>
				</div>
				<div class="col">
					<label>
						<?php echo (isset($results->form_data->code->original) ? $results->form_data->code->original : null); ?>
					</label>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-4">
					<label class="text-muted d-block">
						<?php echo phrase('locale'); ?>
					</label>
				</div>
				<div class="col">
					<label>
						<?php echo (isset($results->form_data->locale->original) ? $results->form_data->locale->original : null); ?>
					</label>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-4">
					<label class="text-muted d-block">
						<?php echo phrase('total_phrase'); ?>
					</label>
				</div>
				<div class="col">
					<label>
						<?php echo (isset($pagination->total_rows) ? number_format($pagination->total_rows) : 0); ?>
					</label>
				</div>
			</div>
		</div>
		<div class="col-lg-6">
			<form action="<?php echo current_page(null, array('per_page' => null)); ?>" method="POST" class="--xhr-form">
				<div class="form-group">
					<div class="input-group">
						<input type="text" name="q" class="form-control" placeholder="<?php echo phrase('search_phrase'); ?>" value="<?php echo service('request')->getGet('q'); ?>" />
						<div class="input-group-append">
							<button type="submit" class="btn btn-primary">
								<i class="mdi mdi-magnify"></i>
							</button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
	
	<hr class="row" />
	
	<?php echo $template->pagination; ?>
	
	<hr class="row" />
	
	<form action="<?php echo current_page(); ?>" method="POST" class="--validate-form" enctype="multipart/form-data">
		<div class="row">
			<?php
				if(isset($phrases))
				{
					foreach($phrases as $key => $val)
					{
						echo '
							<div class="col-md-4">
								<div class="form-group">
									<div class="input-group">
										<input type="text" name="phrase[' . $key . ']" class="form-control" value="' . $val . '" placeholder="' . $key . '" data-toggle="tooltip" title="' . $key . '" autocomplete="off" />
										<div class="input-group-append">
											<a href="' . current_page('delete_phrase', array('phrase' => $key)) . '" class="btn border --open-delete-confirm" data-toggle="tooltip" title="' . phrase('delete_phrase') . '">
												<i class="mdi mdi-trash-can-outline"></i>
											</a>
										</div>
									</div>
								</div>
							</div>
						';
					}
				}
			?>
		</div>
		<div class="--validation-callback mb-0"></div>
		<div class="opt-btn-overlap-fix d-none d-md-block"></div><!-- fix the overlap -->
		<div class="row opt-btn">
			<div class="col-md-12">
				<a href="<?php echo current_page('../', array('id' => null, 'code' => null, 'per_page' => null, 'q' => null)); ?>" class="btn btn-link --xhr">
					<i class="mdi mdi-arrow-left"></i>
					<?php echo phrase('back'); ?>
				</a>
				<button type="submit" class="btn btn-primary float-right">
					<i class="mdi mdi-check"></i>
					<?php echo phrase('translate'); ?>
				</button>
			</div>
		</div>
	</form>
	
	<hr class="row" />
	
	<div class="row">
		<div class="col-md-12">
			<?php echo $template->pagination; ?>
		</div>
	</div>
	<div class="mb-5 d-md-none"><!-- fix mobile overlap --></div>
</div>
